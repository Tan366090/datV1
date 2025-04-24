<?php
namespace App\Models;

class Salary extends BaseModel {
    protected $table = 'salaries';
    
    public function calculateMonthlySalary($employeeId, $year, $month) {
        $conn = $this->db->getConnection();
        
        // Get base salary
        $stmt = $conn->prepare("
            SELECT base_salary, allowance, bonus
            FROM {$this->table}
            WHERE employee_id = ?
            AND effective_date <= ?
            ORDER BY effective_date DESC
            LIMIT 1
        ");
        
        $endDate = date('Y-m-t', strtotime("{$year}-{$month}-01"));
        $stmt->execute([$employeeId, $endDate]);
        $salaryInfo = $stmt->fetch();
        
        if (!$salaryInfo) {
            throw new \Exception('No salary information found for employee');
        }
        
        // Get attendance data
        $stmt = $conn->prepare("
            SELECT COUNT(*) as working_days,
                   SUM(CASE WHEN TIME(check_in) > '08:30:00' THEN 1 ELSE 0 END) as late_days
            FROM attendance
            WHERE employee_id = ?
            AND date BETWEEN ? AND ?
        ");
        
        $startDate = date('Y-m-01', strtotime("{$year}-{$month}-01"));
        $stmt->execute([$employeeId, $startDate, $endDate]);
        $attendance = $stmt->fetch();
        
        // Calculate salary
        $baseSalary = $salaryInfo['base_salary'];
        $allowance = $salaryInfo['allowance'];
        $bonus = $salaryInfo['bonus'];
        
        $dailySalary = $baseSalary / 22; // Assuming 22 working days per month
        $lateDeduction = $attendance['late_days'] * ($dailySalary * 0.1); // 10% deduction for late days
        
        $totalSalary = $baseSalary + $allowance + $bonus - $lateDeduction;
        
        return [
            'base_salary' => $baseSalary,
            'allowance' => $allowance,
            'bonus' => $bonus,
            'working_days' => $attendance['working_days'],
            'late_days' => $attendance['late_days'],
            'late_deduction' => $lateDeduction,
            'total_salary' => $totalSalary
        ];
    }
    
    public function updateSalary($employeeId, $baseSalary, $allowance, $bonus, $effectiveDate = null) {
        if (!$effectiveDate) {
            $effectiveDate = date('Y-m-d');
        }
        
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO {$this->table} 
            (employee_id, base_salary, allowance, bonus, effective_date, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([$employeeId, $baseSalary, $allowance, $bonus, $effectiveDate]);
    }
    
    public function getSalaryHistory($employeeId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT s.*, e.name as employee_name
            FROM {$this->table} s
            JOIN employees e ON s.employee_id = e.id
            WHERE s.employee_id = ?
            ORDER BY s.effective_date DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }
    
    public function getDepartmentSalaryReport($departmentId, $year, $month) {
        $conn = $this->db->getConnection();
        
        $endDate = date('Y-m-t', strtotime("{$year}-{$month}-01"));
        
        $sql = "
            SELECT e.id as employee_id, e.name as employee_name,
                   s.base_salary, s.allowance, s.bonus,
                   COUNT(a.id) as working_days,
                   SUM(CASE WHEN TIME(a.check_in) > '08:30:00' THEN 1 ELSE 0 END) as late_days
            FROM employees e
            JOIN salaries s ON e.id = s.employee_id
            LEFT JOIN attendance a ON e.id = a.employee_id 
                AND a.date BETWEEN ? AND ?
            WHERE e.department_id = ?
            AND s.effective_date <= ?
            GROUP BY e.id, e.name, s.base_salary, s.allowance, s.bonus
            ORDER BY e.name
        ";
        
        $startDate = date('Y-m-01', strtotime("{$year}-{$month}-01"));
        $stmt = $conn->prepare($sql);
        $stmt->execute([$startDate, $endDate, $departmentId, $endDate]);
        return $stmt->fetchAll();
    }
} 