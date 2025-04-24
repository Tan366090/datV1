<?php
namespace App\Models;

class Employee extends BaseModel {
    protected $table = 'employees';
    
    public function getWithDetails($id = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT e.*, 
                   d.name as department_name,
                   p.name as position_name,
                   u.username as created_by_username
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.status = 'active'
        ";
        
        if ($id) {
            $sql .= " AND e.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
        
        $sql .= " ORDER BY e.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByDepartment($departmentId) {
        return $this->findAll(['department_id' => $departmentId, 'status' => 'active']);
    }
    
    public function getByPosition($positionId) {
        return $this->findAll(['position_id' => $positionId, 'status' => 'active']);
    }
    
    public function search($keyword) {
        $conn = $this->db->getConnection();
        $keyword = "%{$keyword}%";
        
        $sql = "
            SELECT e.*, d.name as department_name, p.name as position_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            WHERE e.status = 'active'
            AND (e.name LIKE ? OR e.email LIKE ? OR e.phone LIKE ?)
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$keyword, $keyword, $keyword]);
        return $stmt->fetchAll();
    }
    
    public function getAttendance($employeeId, $startDate, $endDate) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT a.*, e.name as employee_name
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            WHERE a.employee_id = ?
            AND a.date BETWEEN ? AND ?
            ORDER BY a.date DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$employeeId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function getSalaryHistory($employeeId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT s.*, e.name as employee_name
            FROM salary_history s
            JOIN employees e ON s.employee_id = e.id
            WHERE s.employee_id = ?
            ORDER BY s.effective_date DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }
} 