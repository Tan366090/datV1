<?php
namespace App\Models;

class Project extends BaseModel {
    protected $table = 'projects';
    
    public function createProject($name, $description, $startDate, $endDate, $managerId, $departmentId, $priority, $budget) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO {$this->table}
            (name, description, start_date, end_date, manager_id, 
             department_id, priority, budget, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'planning', NOW())
        ");
        
        return $stmt->execute([
            $name, $description, $startDate, $endDate, 
            $managerId, $departmentId, $priority, $budget
        ]);
    }
    
    public function updateProject($projectId, $name, $description, $startDate, $endDate, $managerId, $departmentId, $priority, $budget) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET name = ?,
                description = ?,
                start_date = ?,
                end_date = ?,
                manager_id = ?,
                department_id = ?,
                priority = ?,
                budget = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $name, $description, $startDate, $endDate, 
            $managerId, $departmentId, $priority, $budget, $projectId
        ]);
    }
    
    public function updateProjectStatus($projectId, $status) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $projectId]);
    }
    
    public function assignEmployee($projectId, $employeeId, $role) {
        $conn = $this->db->getConnection();
        
        // Check if employee is already assigned
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM project_members
            WHERE project_id = ? AND employee_id = ?
        ");
        
        $stmt->execute([$projectId, $employeeId]);
        if ($stmt->fetch()['count'] > 0) {
            throw new \Exception('Employee is already assigned to this project');
        }
        
        // Assign employee
        $stmt = $conn->prepare("
            INSERT INTO project_members
            (project_id, employee_id, role, joined_at)
            VALUES (?, ?, ?, NOW())
        ");
        
        return $stmt->execute([$projectId, $employeeId, $role]);
    }
    
    public function removeEmployee($projectId, $employeeId) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            DELETE FROM project_members
            WHERE project_id = ? AND employee_id = ?
        ");
        
        return $stmt->execute([$projectId, $employeeId]);
    }
    
    public function getProjectDetails($projectId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT p.*, e.name as manager_name, 
                   d.name as department_name
            FROM {$this->table} p
            JOIN employees e ON p.manager_id = e.id
            JOIN departments d ON p.department_id = d.id
            WHERE p.id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetch();
    }
    
    public function getProjectMembers($projectId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT e.*, d.name as department_name, pm.role
            FROM project_members pm
            JOIN employees e ON pm.employee_id = e.id
            JOIN departments d ON e.department_id = d.id
            WHERE pm.project_id = ?
            ORDER BY e.name
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }
    
    public function getEmployeeProjects($employeeId, $status = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT p.*, e.name as manager_name, d.name as department_name, pm.role
            FROM {$this->table} p
            JOIN employees e ON p.manager_id = e.id
            JOIN departments d ON p.department_id = d.id
            JOIN project_members pm ON p.id = pm.project_id
            WHERE pm.employee_id = ?
        ";
        
        $params = [$employeeId];
        
        if ($status) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY p.start_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getDepartmentProjects($departmentId, $status = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT p.*, e.name as manager_name,
                   COUNT(pm.id) as member_count
            FROM {$this->table} p
            JOIN employees e ON p.manager_id = e.id
            LEFT JOIN project_members pm ON p.id = pm.project_id
            WHERE p.department_id = ?
        ";
        
        $params = [$departmentId];
        
        if ($status) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.start_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getProjectProgress($projectId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT 
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
                AVG(progress) as avg_progress
            FROM project_tasks
            WHERE project_id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetch();
    }
    
    public function getProjectTimeline($projectId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT 
                pt.*,
                e.name as assigned_to_name
            FROM project_tasks pt
            LEFT JOIN employees e ON pt.assigned_to = e.id
            WHERE pt.project_id = ?
            ORDER BY pt.due_date
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }
} 