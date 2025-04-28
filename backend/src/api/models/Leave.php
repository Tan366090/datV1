<?php
namespace App\Models;

class Leave extends BaseModel {
    protected $table = 'leaves';
    
    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'leave_duration_days',
        'reason',
        'status',
        'approved_by_user_id',
        'approver_comments',
        'attachment_url'
    ];
    
    public function getWithDetails($id = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT l.*, 
                   e.employee_code,
                   up.full_name,
                   d.name as department_name,
                   up2.full_name as approver_name
            FROM leaves l
            JOIN employees e ON l.employee_id = e.id
            JOIN user_profiles up ON e.user_id = up.user_id
            JOIN departments d ON e.department_id = d.id
            LEFT JOIN users u ON l.approved_by_user_id = u.id
            LEFT JOIN user_profiles up2 ON u.id = up2.user_id
            WHERE l.status != 'deleted'
        ";
        
        if ($id) {
            $sql .= " AND l.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
        
        $sql .= " ORDER BY l.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByEmployee($employeeId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT l.*, 
                   e.employee_code,
                   up.full_name,
                   d.name as department_name,
                   up2.full_name as approver_name
            FROM leaves l
            JOIN employees e ON l.employee_id = e.id
            JOIN user_profiles up ON e.user_id = up.user_id
            JOIN departments d ON e.department_id = d.id
            LEFT JOIN users u ON l.approved_by_user_id = u.id
            LEFT JOIN user_profiles up2 ON u.id = up2.user_id
            WHERE l.employee_id = ? AND l.status != 'deleted'
            ORDER BY l.created_at DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }
    
    public function getPendingRequests() {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT l.*, 
                   e.employee_code,
                   up.full_name,
                   d.name as department_name
            FROM leaves l
            JOIN employees e ON l.employee_id = e.id
            JOIN user_profiles up ON e.user_id = up.user_id
            JOIN departments d ON e.department_id = d.id
            WHERE l.status = 'pending'
            ORDER BY l.created_at DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function approve($id, $userId, $comments = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            UPDATE {$this->table}
            SET status = 'approved',
                approved_by_user_id = ?,
                approver_comments = ?,
                updated_at = NOW()
            WHERE id = ? AND status = 'pending'
        ";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$userId, $comments, $id]);
    }
    
    public function reject($id, $userId, $comments) {
        $conn = $this->db->getConnection();
        
        $sql = "
            UPDATE {$this->table}
            SET status = 'rejected',
                approved_by_user_id = ?,
                approver_comments = ?,
                updated_at = NOW()
            WHERE id = ? AND status = 'pending'
        ";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$userId, $comments, $id]);
    }
    
    public function cancel($id, $employeeId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            UPDATE {$this->table}
            SET status = 'cancelled',
                updated_at = NOW()
            WHERE id = ? AND employee_id = ? AND status = 'pending'
        ";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$id, $employeeId]);
    }
    
    public function checkOverlap($employeeId, $startDate, $endDate, $excludeId = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT COUNT(*) as count
            FROM {$this->table}
            WHERE employee_id = ?
            AND status IN ('pending', 'approved')
            AND ((start_date BETWEEN ? AND ?)
                OR (end_date BETWEEN ? AND ?))
        ";
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$employeeId, $startDate, $endDate, $startDate, $endDate, $excludeId]);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$employeeId, $startDate, $endDate, $startDate, $endDate]);
        }
        
        return $stmt->fetchColumn() > 0;
    }
    
    public function calculateDuration($startDate, $endDate) {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $days = ($end - $start) / (60 * 60 * 24) + 1;
        return round($days, 1);
    }
} 