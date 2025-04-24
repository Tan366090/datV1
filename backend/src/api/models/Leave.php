<?php
namespace App\Models;

class Leave extends BaseModel {
    protected $table = 'leaves';
    
    public function requestLeave($employeeId, $startDate, $endDate, $reason, $type) {
        $conn = $this->db->getConnection();
        
        // Check if there are any overlapping leave requests
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM {$this->table}
            WHERE employee_id = ?
            AND (
                (start_date BETWEEN ? AND ?)
                OR (end_date BETWEEN ? AND ?)
                OR (? BETWEEN start_date AND end_date)
                OR (? BETWEEN start_date AND end_date)
            )
            AND status != 'rejected'
        ");
        
        $stmt->execute([$employeeId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            throw new \Exception('There is an overlapping leave request');
        }
        
        // Insert new leave request
        $stmt = $conn->prepare("
            INSERT INTO {$this->table}
            (employee_id, start_date, end_date, reason, type, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        return $stmt->execute([$employeeId, $startDate, $endDate, $reason, $type]);
    }
    
    public function approveLeave($leaveId, $approvedBy) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET status = 'approved',
                approved_by = ?,
                approved_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$approvedBy, $leaveId]);
    }
    
    public function rejectLeave($leaveId, $rejectedBy, $rejectionReason) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET status = 'rejected',
                rejected_by = ?,
                rejection_reason = ?,
                rejected_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$rejectedBy, $rejectionReason, $leaveId]);
    }
    
    public function getEmployeeLeaves($employeeId, $year = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT l.*, e.name as employee_name,
                   a.name as approved_by_name, r.name as rejected_by_name
            FROM {$this->table} l
            JOIN employees e ON l.employee_id = e.id
            LEFT JOIN employees a ON l.approved_by = a.id
            LEFT JOIN employees r ON l.rejected_by = r.id
            WHERE l.employee_id = ?
        ";
        
        $params = [$employeeId];
        
        if ($year) {
            $sql .= " AND YEAR(l.start_date) = ?";
            $params[] = $year;
        }
        
        $sql .= " ORDER BY l.start_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getDepartmentLeaves($departmentId, $startDate, $endDate) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT l.*, e.name as employee_name, d.name as department_name,
                   a.name as approved_by_name, r.name as rejected_by_name
            FROM {$this->table} l
            JOIN employees e ON l.employee_id = e.id
            JOIN departments d ON e.department_id = d.id
            LEFT JOIN employees a ON l.approved_by = a.id
            LEFT JOIN employees r ON l.rejected_by = r.id
            WHERE e.department_id = ?
            AND l.start_date BETWEEN ? AND ?
            ORDER BY l.start_date DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$departmentId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function getPendingLeaves() {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT l.*, e.name as employee_name, d.name as department_name
            FROM {$this->table} l
            JOIN employees e ON l.employee_id = e.id
            JOIN departments d ON e.department_id = d.id
            WHERE l.status = 'pending'
            ORDER BY l.created_at DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
} 