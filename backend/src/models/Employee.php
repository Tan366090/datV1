<?php
require_once __DIR__ . '/../config/database.php';

class Employee {
    private $conn;
    private $table_name = "employees";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($page = 1, $limit = 10, $search = '', $department = '', $status = '') {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT 
                    e.id,
                    e.employee_code,
                    e.full_name,
                    d.name as department_name,
                    p.name as position_name,
                    e.salary,
                    e.join_date,
                    e.birth_date,
                    e.phone,
                    e.email,
                    e.address,
                    e.status,
                    e.created_at
                FROM " . $this->table_name . " e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN positions p ON e.position_id = p.id
                WHERE 1=1";
        
        $params = [];
        
        if($search) {
            $query .= " AND (e.full_name LIKE ? OR e.employee_code LIKE ? OR e.email LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if($department) {
            $query .= " AND d.name = ?";
            $params[] = $department;
        }
        
        if($status) {
            $query .= " AND e.status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY e.id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Lấy tổng số bản ghi
        $countQuery = "SELECT COUNT(*) as total FROM " . $this->table_name . " e
                      LEFT JOIN departments d ON e.department_id = d.id
                      WHERE 1=1";
        
        if($search) {
            $countQuery .= " AND (e.full_name LIKE ? OR e.employee_code LIKE ? OR e.email LIKE ?)";
        }
        
        if($department) {
            $countQuery .= " AND d.name = ?";
        }
        
        if($status) {
            $countQuery .= " AND e.status = ?";
        }
        
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return [
            'data' => $employees,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }

    public function getById($id) {
        $query = "SELECT 
                    e.*,
                    d.name as department_name,
                    p.name as position_name
                FROM " . $this->table_name . " e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN positions p ON e.position_id = p.id
                WHERE e.id = ?";
                
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                (employee_code, full_name, department_id, position_id, salary, 
                join_date, birth_date, phone, email, address, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $this->conn->prepare($query);
        
        try {
            $stmt->execute([
                $data['employee_code'],
                $data['full_name'],
                $data['department_id'],
                $data['position_id'],
                $data['salary'],
                $data['join_date'],
                $data['birth_date'],
                $data['phone'],
                $data['email'],
                $data['address'],
                $data['status']
            ]);
            
            return [
                'success' => true,
                'id' => $this->conn->lastInsertId()
            ];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . "
                SET full_name = ?,
                    department_id = ?,
                    position_id = ?,
                    salary = ?,
                    join_date = ?,
                    birth_date = ?,
                    phone = ?,
                    email = ?,
                    address = ?,
                    status = ?
                WHERE id = ?";
                
        $stmt = $this->conn->prepare($query);
        
        try {
            $stmt->execute([
                $data['full_name'],
                $data['department_id'],
                $data['position_id'],
                $data['salary'],
                $data['join_date'],
                $data['birth_date'],
                $data['phone'],
                $data['email'],
                $data['address'],
                $data['status'],
                $id
            ]);
            
            return ['success' => true];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        try {
            $stmt->execute([$id]);
            return ['success' => true];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function validate($data) {
        $errors = [];

        // Validate required fields
        $requiredFields = ['full_name', 'gender', 'birth_date', 'phone', 'email', 'address', 'department_id', 'position_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Trường $field là bắt buộc";
            }
        }

        // Validate email format
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ";
        }

        // Validate phone format (Vietnamese phone number)
        if (!empty($data['phone']) && !preg_match('/^(0|\+84)[0-9]{9,10}$/', $data['phone'])) {
            $errors[] = "Số điện thoại không hợp lệ";
        }

        // Validate birth date
        if (!empty($data['birth_date'])) {
            $birthDate = new DateTime($data['birth_date']);
            $now = new DateTime();
            $age = $now->diff($birthDate)->y;
            
            if ($age < 18) {
                $errors[] = "Nhân viên phải đủ 18 tuổi";
            }
        }

        return $errors;
    }
} 