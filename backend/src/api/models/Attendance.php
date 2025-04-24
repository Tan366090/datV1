<?php
namespace App\Models;

class Attendance extends BaseModel {
    protected $table = 'attendance';
    
    public function checkIn($employeeId, $date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $conn = $this->db->getConnection();
        
        // Check if already checked in
        $stmt = $conn->prepare("
            SELECT * FROM {$this->table} 
            WHERE employee_id = ? AND date = ? AND check_in IS NOT NULL
        ");
        $stmt->execute([$employeeId, $date]);
        if ($stmt->fetch()) {
            throw new \Exception('Already checked in for today');
        }
        
        // Insert or update check-in
        $stmt = $conn->prepare("
            INSERT INTO {$this->table} (employee_id, date, check_in, created_at)
            VALUES (?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE check_in = NOW()
        ");
        
        return $stmt->execute([$employeeId, $date]);
    }
    
    public function checkOut($employeeId, $date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $conn = $this->db->getConnection();
        
        // Check if already checked out
        $stmt = $conn->prepare("
            SELECT * FROM {$this->table} 
            WHERE employee_id = ? AND date = ? AND check_out IS NOT NULL
        ");
        $stmt->execute([$employeeId, $date]);
        if ($stmt->fetch()) {
            throw new \Exception('Already checked out for today');
        }
        
        // Update check-out
        $stmt = $conn->prepare("
            UPDATE {$this->table} 
            SET check_out = NOW()
            WHERE employee_id = ? AND date = ?
        ");
        
        return $stmt->execute([$employeeId, $date]);
    }
    
    public function getMonthlyReport($employeeId, $year, $month) {
        $conn = $this->db->getConnection();
        
        $startDate = date('Y-m-01', strtotime("{$year}-{$month}-01"));
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $sql = "
            SELECT a.*, e.name as employee_name
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            WHERE a.employee_id = ?
            AND a.date BETWEEN ? AND ?
            ORDER BY a.date
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$employeeId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function getDepartmentReport($departmentId, $startDate, $endDate) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT a.*, e.name as employee_name, d.name as department_name
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            JOIN departments d ON e.department_id = d.id
            WHERE e.department_id = ?
            AND a.date BETWEEN ? AND ?
            ORDER BY a.date, e.name
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$departmentId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function getLateEmployees($date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT a.*, e.name as employee_name, d.name as department_name
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            JOIN departments d ON e.department_id = d.id
            WHERE a.date = ?
            AND TIME(a.check_in) > '08:30:00'
            ORDER BY a.check_in
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }
} 