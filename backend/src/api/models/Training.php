<?php
namespace App\Models;

class Training extends BaseModel {
    protected $table = 'trainings';
    
    public function createTraining($title, $description, $startDate, $endDate, $trainerId, $location, $maxParticipants) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO {$this->table}
            (title, description, start_date, end_date, trainer_id, 
             location, max_participants, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'scheduled', NOW())
        ");
        
        return $stmt->execute([
            $title, $description, $startDate, $endDate, 
            $trainerId, $location, $maxParticipants
        ]);
    }
    
    public function updateTraining($trainingId, $title, $description, $startDate, $endDate, $trainerId, $location, $maxParticipants) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET title = ?,
                description = ?,
                start_date = ?,
                end_date = ?,
                trainer_id = ?,
                location = ?,
                max_participants = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $title, $description, $startDate, $endDate, 
            $trainerId, $location, $maxParticipants, $trainingId
        ]);
    }
    
    public function registerEmployee($trainingId, $employeeId) {
        $conn = $this->db->getConnection();
        
        // Check if training exists and has available slots
        $stmt = $conn->prepare("
            SELECT t.max_participants, 
                   COUNT(tr.id) as registered_count
            FROM {$this->table} t
            LEFT JOIN training_registrations tr ON t.id = tr.training_id
            WHERE t.id = ?
            GROUP BY t.id
        ");
        
        $stmt->execute([$trainingId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            throw new \Exception('Training not found');
        }
        
        if ($result['registered_count'] >= $result['max_participants']) {
            throw new \Exception('Training is full');
        }
        
        // Check if employee is already registered
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM training_registrations
            WHERE training_id = ? AND employee_id = ?
        ");
        
        $stmt->execute([$trainingId, $employeeId]);
        if ($stmt->fetch()['count'] > 0) {
            throw new \Exception('Employee is already registered for this training');
        }
        
        // Register employee
        $stmt = $conn->prepare("
            INSERT INTO training_registrations
            (training_id, employee_id, status, registered_at)
            VALUES (?, ?, 'registered', NOW())
        ");
        
        return $stmt->execute([$trainingId, $employeeId]);
    }
    
    public function updateTrainingStatus($trainingId, $status) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $trainingId]);
    }
    
    public function getTrainingDetails($trainingId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT t.*, e.name as trainer_name,
                   COUNT(tr.id) as registered_count
            FROM {$this->table} t
            JOIN employees e ON t.trainer_id = e.id
            LEFT JOIN training_registrations tr ON t.id = tr.training_id
            WHERE t.id = ?
            GROUP BY t.id
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$trainingId]);
        return $stmt->fetch();
    }
    
    public function getEmployeeTrainings($employeeId, $status = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT t.*, e.name as trainer_name, tr.status as registration_status
            FROM {$this->table} t
            JOIN employees e ON t.trainer_id = e.id
            JOIN training_registrations tr ON t.id = tr.training_id
            WHERE tr.employee_id = ?
        ";
        
        $params = [$employeeId];
        
        if ($status) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY t.start_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getDepartmentTrainings($departmentId, $status = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT t.*, e.name as trainer_name,
                   COUNT(tr.id) as registered_count
            FROM {$this->table} t
            JOIN employees e ON t.trainer_id = e.id
            LEFT JOIN training_registrations tr ON t.id = tr.training_id
            WHERE t.department_id = ?
        ";
        
        $params = [$departmentId];
        
        if ($status) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
        }
        
        $sql .= " GROUP BY t.id ORDER BY t.start_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getTrainingParticipants($trainingId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT e.*, d.name as department_name, tr.status as registration_status
            FROM training_registrations tr
            JOIN employees e ON tr.employee_id = e.id
            JOIN departments d ON e.department_id = d.id
            WHERE tr.training_id = ?
            ORDER BY e.name
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$trainingId]);
        return $stmt->fetchAll();
    }
} 