<?php
namespace App\Models;

use PDO;
use PDOException;

class Leave extends BaseModel {
    protected $table = 'leaves';
    protected $primaryKey = 'leave_id';

    public function createLeave($employeeId, $leaveType, $startDate, $endDate, $reason, $status = 'pending', $approvedBy = null) {
        try {
            $data = [
                'employee_id' => $employeeId,
                'leave_type' => $leaveType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reason,
                'status' => $status,
                'approved_by' => $approvedBy,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $leaveId = $this->create($data);
            return [
                'success' => true,
                'leave_id' => $leaveId
            ];
        } catch (PDOException $e) {
            error_log("Create Leave Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Lỗi hệ thống'
            ];
        }
    }

    public function updateLeave($leaveId, $leaveType, $startDate, $endDate, $reason) {
        try {
            $data = [
                'leave_type' => $leaveType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reason,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            return $this->update($leaveId, $data);
        } catch (PDOException $e) {
            error_log("Update Leave Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateLeaveStatus($leaveId, $status, $approvedBy = null) {
        try {
            $data = [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($approvedBy !== null) {
                $data['approved_by'] = $approvedBy;
            }

            return $this->update($leaveId, $data);
        } catch (PDOException $e) {
            error_log("Update Leave Status Error: " . $e->getMessage());
            return false;
        }
    }

    public function getLeaveDetails($leaveId) {
        try {
            $query = "SELECT l.*, e.full_name as employee_name, a.full_name as approver_name 
                     FROM {$this->table} l
                     JOIN employees e ON l.employee_id = e.employee_id
                     LEFT JOIN employees a ON l.approved_by = a.employee_id
                     WHERE l.leave_id = ?";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute([$leaveId]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Leave Details Error: " . $e->getMessage());
            return false;
        }
    }

    public function getEmployeeLeaves($employeeId, $status = null) {
        try {
            $query = "SELECT l.* 
                     FROM {$this->table} l
                     WHERE l.employee_id = ?";
            $params = [$employeeId];

            if ($status) {
                $query .= " AND l.status = ?";
                $params[] = $status;
            }

            $query .= " ORDER BY l.start_date DESC";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Employee Leaves Error: " . $e->getMessage());
            return [];
        }
    }

    public function getDepartmentLeaves($departmentId, $status = null) {
        try {
            $query = "SELECT l.*, e.full_name as employee_name, e.employee_code 
                     FROM {$this->table} l
                     JOIN employees e ON l.employee_id = e.employee_id
                     WHERE e.department_id = ?";
            $params = [$departmentId];

            if ($status) {
                $query .= " AND l.status = ?";
                $params[] = $status;
            }

            $query .= " ORDER BY e.full_name ASC, l.start_date DESC";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Department Leaves Error: " . $e->getMessage());
            return [];
        }
    }

    public function getPendingLeaves($approverId = null) {
        try {
            $query = "SELECT l.*, e.full_name as employee_name, e.employee_code, d.department_name 
                     FROM {$this->table} l
                     JOIN employees e ON l.employee_id = e.employee_id
                     JOIN departments d ON e.department_id = d.department_id
                     WHERE l.status = 'pending'";
            
            $params = [];
            if ($approverId) {
                $query .= " AND d.manager_id = ?";
                $params[] = $approverId;
            }

            $query .= " ORDER BY l.created_at ASC";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Pending Leaves Error: " . $e->getMessage());
            return [];
        }
    }

    public function getLeaveStats($departmentId = null) {
        try {
            $query = "SELECT 
                        COUNT(DISTINCT l.leave_id) as total_leaves,
                        COUNT(DISTINCT l.employee_id) as employees_with_leaves,
                        COUNT(DISTINCT CASE WHEN l.status = 'approved' THEN l.leave_id END) as approved_leaves,
                        COUNT(DISTINCT CASE WHEN l.status = 'rejected' THEN l.leave_id END) as rejected_leaves,
                        COUNT(DISTINCT CASE WHEN l.status = 'pending' THEN l.leave_id END) as pending_leaves,
                        AVG(DATEDIFF(l.end_date, l.start_date)) as average_duration
                     FROM {$this->table} l";
            
            $params = [];
            if ($departmentId) {
                $query .= " JOIN employees e ON l.employee_id = e.employee_id
                          WHERE e.department_id = ?";
                $params[] = $departmentId;
            }

            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute($params);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Leave Stats Error: " . $e->getMessage());
            return false;
        }
    }

    public function searchLeaves($keyword, $departmentId = null) {
        try {
            $query = "SELECT l.*, e.full_name as employee_name, e.employee_code, d.department_name 
                     FROM {$this->table} l
                     JOIN employees e ON l.employee_id = e.employee_id
                     JOIN departments d ON e.department_id = d.department_id
                     WHERE (e.full_name LIKE ? OR l.leave_type LIKE ? OR l.reason LIKE ?)";
            $params = ["%$keyword%", "%$keyword%", "%$keyword%"];

            if ($departmentId) {
                $query .= " AND e.department_id = ?";
                $params[] = $departmentId;
            }

            $query .= " ORDER BY e.full_name ASC, l.start_date DESC";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search Leaves Error: " . $e->getMessage());
            return [];
        }
    }
} 