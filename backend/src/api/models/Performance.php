<?php
namespace App\Models;

class Performance extends BaseModel {
    protected $table = 'performance_reviews';
    
    public function createReview($employeeId, $reviewerId, $year, $quarter, $kpiScore, $attendanceScore, $teamworkScore, $comments) {
        $conn = $this->db->getConnection();
        
        // Check if review already exists for this period
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM {$this->table}
            WHERE employee_id = ?
            AND year = ?
            AND quarter = ?
        ");
        
        $stmt->execute([$employeeId, $year, $quarter]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            throw new \Exception('Performance review already exists for this period');
        }
        
        // Calculate overall score
        $overallScore = ($kpiScore * 0.4) + ($attendanceScore * 0.3) + ($teamworkScore * 0.3);
        
        // Insert new review
        $stmt = $conn->prepare("
            INSERT INTO {$this->table}
            (employee_id, reviewer_id, year, quarter, kpi_score, attendance_score, 
             teamwork_score, overall_score, comments, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $employeeId, $reviewerId, $year, $quarter, $kpiScore, 
            $attendanceScore, $teamworkScore, $overallScore, $comments
        ]);
    }
    
    public function updateReview($reviewId, $kpiScore, $attendanceScore, $teamworkScore, $comments) {
        $conn = $this->db->getConnection();
        
        // Calculate new overall score
        $overallScore = ($kpiScore * 0.4) + ($attendanceScore * 0.3) + ($teamworkScore * 0.3);
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET kpi_score = ?,
                attendance_score = ?,
                teamwork_score = ?,
                overall_score = ?,
                comments = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $kpiScore, $attendanceScore, $teamworkScore, 
            $overallScore, $comments, $reviewId
        ]);
    }
    
    public function getEmployeeReviews($employeeId, $year = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT p.*, e.name as employee_name, r.name as reviewer_name
            FROM {$this->table} p
            JOIN employees e ON p.employee_id = e.id
            JOIN employees r ON p.reviewer_id = r.id
            WHERE p.employee_id = ?
        ";
        
        $params = [$employeeId];
        
        if ($year) {
            $sql .= " AND p.year = ?";
            $params[] = $year;
        }
        
        $sql .= " ORDER BY p.year DESC, p.quarter DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getDepartmentReviews($departmentId, $year, $quarter) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT p.*, e.name as employee_name, r.name as reviewer_name, d.name as department_name
            FROM {$this->table} p
            JOIN employees e ON p.employee_id = e.id
            JOIN employees r ON p.reviewer_id = r.id
            JOIN departments d ON e.department_id = d.id
            WHERE e.department_id = ?
            AND p.year = ?
            AND p.quarter = ?
            ORDER BY p.overall_score DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$departmentId, $year, $quarter]);
        return $stmt->fetchAll();
    }
    
    public function getPendingReviews($reviewerId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT p.*, e.name as employee_name, d.name as department_name
            FROM {$this->table} p
            JOIN employees e ON p.employee_id = e.id
            JOIN departments d ON e.department_id = d.id
            WHERE p.reviewer_id = ?
            AND p.overall_score IS NULL
            ORDER BY p.created_at DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reviewerId]);
        return $stmt->fetchAll();
    }
    
    public function getPerformanceSummary($employeeId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT 
                AVG(overall_score) as avg_score,
                MAX(overall_score) as max_score,
                MIN(overall_score) as min_score,
                COUNT(*) as total_reviews
            FROM {$this->table}
            WHERE employee_id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$employeeId]);
        return $stmt->fetch();
    }
} 