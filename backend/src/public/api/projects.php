<?php
header('Content-Type: application/json');
require_once '../config/config.php';

class ProjectsAPI {
    private $conn;
    private $table_projects = 'projects';
    private $table_tasks = 'project_tasks';
    private $table_members = 'project_members';
    private $table_resources = 'project_resources';
    private $table_progress = 'project_progress';
    private $table_reports = 'project_reports';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Project management
    public function getProjects() {
        $sql = "SELECT * FROM {$this->table_projects}";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addProject($data) {
        $sql = "INSERT INTO {$this->table_projects} (name, description, start_date, end_date, status, budget) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssd", $data['name'], $data['description'], $data['start_date'], $data['end_date'], $data['status'], $data['budget']);
        return $stmt->execute();
    }

    public function updateProject($id, $data) {
        $sql = "UPDATE {$this->table_projects} SET name = ?, description = ?, start_date = ?, end_date = ?, status = ?, budget = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssdi", $data['name'], $data['description'], $data['start_date'], $data['end_date'], $data['status'], $data['budget'], $id);
        return $stmt->execute();
    }

    public function deleteProject($id) {
        $sql = "DELETE FROM {$this->table_projects} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Task management
    public function getTasks($project_id) {
        $sql = "SELECT * FROM {$this->table_tasks} WHERE project_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addTask($data) {
        $sql = "INSERT INTO {$this->table_tasks} (project_id, name, description, assigned_to, start_date, end_date, status, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ississss", $data['project_id'], $data['name'], $data['description'], $data['assigned_to'], $data['start_date'], $data['end_date'], $data['status'], $data['priority']);
        return $stmt->execute();
    }

    public function updateTask($id, $data) {
        $sql = "UPDATE {$this->table_tasks} SET name = ?, description = ?, assigned_to = ?, start_date = ?, end_date = ?, status = ?, priority = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssissssi", $data['name'], $data['description'], $data['assigned_to'], $data['start_date'], $data['end_date'], $data['status'], $data['priority'], $id);
        return $stmt->execute();
    }

    // Member management
    public function getMembers($project_id) {
        $sql = "SELECT * FROM {$this->table_members} WHERE project_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addMember($data) {
        $sql = "INSERT INTO {$this->table_members} (project_id, employee_id, role, join_date) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiss", $data['project_id'], $data['employee_id'], $data['role'], $data['join_date']);
        return $stmt->execute();
    }

    public function removeMember($project_id, $employee_id) {
        $sql = "DELETE FROM {$this->table_members} WHERE project_id = ? AND employee_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $project_id, $employee_id);
        return $stmt->execute();
    }

    // Resource management
    public function getResources($project_id) {
        $sql = "SELECT * FROM {$this->table_resources} WHERE project_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addResource($data) {
        $sql = "INSERT INTO {$this->table_resources} (project_id, name, type, quantity, cost, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issids", $data['project_id'], $data['name'], $data['type'], $data['quantity'], $data['cost'], $data['status']);
        return $stmt->execute();
    }

    public function updateResource($id, $data) {
        $sql = "UPDATE {$this->table_resources} SET name = ?, type = ?, quantity = ?, cost = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssidss", $data['name'], $data['type'], $data['quantity'], $data['cost'], $data['status'], $id);
        return $stmt->execute();
    }

    // Progress tracking
    public function getProgress($project_id) {
        $sql = "SELECT * FROM {$this->table_progress} WHERE project_id = ? ORDER BY date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateProgress($data) {
        $sql = "INSERT INTO {$this->table_progress} (project_id, task_id, completion_rate, quality_score, deadline_met, comments) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iidiss", $data['project_id'], $data['task_id'], $data['completion_rate'], $data['quality_score'], $data['deadline_met'], $data['comments']);
        $success = $stmt->execute();
        
        if ($success) {
            // Update performance data
            $this->updatePerformanceData($data);
        }
        return $success;
    }

    private function updatePerformanceData($data) {
        $performance_data = [
            'employee_id' => $data['employee_id'],
            'project_id' => $data['project_id'],
            'task_id' => $data['task_id'],
            'completion_rate' => $data['completion_rate'],
            'quality_score' => $data['quality_score'],
            'deadline_met' => $data['deadline_met'],
            'comments' => $data['comments']
        ];

        // Send to performance API
        $ch = curl_init('http://localhost/qlnhansu_V2/backend/src/public/api/performance/updates');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($performance_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }

    // Report management
    public function getReports($project_id) {
        $sql = "SELECT * FROM {$this->table_reports} WHERE project_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function generateReport($data) {
        $sql = "INSERT INTO {$this->table_reports} (project_id, type, period, content) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $content = json_encode($this->compileReportData($data));
        $stmt->bind_param("isss", $data['project_id'], $data['type'], $data['period'], $content);
        return $stmt->execute();
    }

    private function compileReportData($data) {
        $report = [];
        switch($data['type']) {
            case 'progress':
                $report = $this->getProgress($data['project_id']);
                break;
            case 'resources':
                $report = $this->getResources($data['project_id']);
                break;
            case 'tasks':
                $report = $this->getTasks($data['project_id']);
                break;
            case 'members':
                $report = $this->getMembers($data['project_id']);
                break;
        }
        return $report;
    }
}

// Initialize API
$projectsAPI = new ProjectsAPI($conn);

// Handle requests
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch($method) {
    case 'GET':
        switch($action) {
            case 'projects':
                echo json_encode($projectsAPI->getProjects());
                break;
            case 'tasks':
                $project_id = $_GET['project_id'] ?? 0;
                echo json_encode($projectsAPI->getTasks($project_id));
                break;
            case 'members':
                $project_id = $_GET['project_id'] ?? 0;
                echo json_encode($projectsAPI->getMembers($project_id));
                break;
            case 'resources':
                $project_id = $_GET['project_id'] ?? 0;
                echo json_encode($projectsAPI->getResources($project_id));
                break;
            case 'progress':
                $project_id = $_GET['project_id'] ?? 0;
                echo json_encode($projectsAPI->getProgress($project_id));
                break;
            case 'reports':
                $project_id = $_GET['project_id'] ?? 0;
                echo json_encode($projectsAPI->getReports($project_id));
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Invalid action']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        switch($action) {
            case 'add_project':
                echo json_encode(['success' => $projectsAPI->addProject($data)]);
                break;
            case 'update_project':
                echo json_encode(['success' => $projectsAPI->updateProject($data['id'], $data)]);
                break;
            case 'delete_project':
                echo json_encode(['success' => $projectsAPI->deleteProject($data['id'])]);
                break;
            case 'add_task':
                echo json_encode(['success' => $projectsAPI->addTask($data)]);
                break;
            case 'update_task':
                echo json_encode(['success' => $projectsAPI->updateTask($data['id'], $data)]);
                break;
            case 'add_member':
                echo json_encode(['success' => $projectsAPI->addMember($data)]);
                break;
            case 'remove_member':
                echo json_encode(['success' => $projectsAPI->removeMember($data['project_id'], $data['employee_id'])]);
                break;
            case 'add_resource':
                echo json_encode(['success' => $projectsAPI->addResource($data)]);
                break;
            case 'update_resource':
                echo json_encode(['success' => $projectsAPI->updateResource($data['id'], $data)]);
                break;
            case 'update_progress':
                echo json_encode(['success' => $projectsAPI->updateProgress($data)]);
                break;
            case 'generate_report':
                echo json_encode(['success' => $projectsAPI->generateReport($data)]);
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Invalid action']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?> 