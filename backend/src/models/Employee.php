<?php
require_once __DIR__ . '/../config/database.php';

class Employee {
    private $db;
    private $table = 'employees';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll($page = 1, $pageSize = 10, $search = '', $department = '', $status = '') {
        $offset = ($page - 1) * $pageSize;
        $query = "SELECT e.*, d.name as department_name, p.name as position_name 
                 FROM {$this->table} e 
                 LEFT JOIN departments d ON e.department_id = d.id 
                 LEFT JOIN positions p ON e.position_id = p.id 
                 WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (e.full_name LIKE ? OR e.employee_id LIKE ? OR e.email LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($department)) {
            $query .= " AND e.department_id = ?";
            $params[] = $department;
        }

        if (!empty($status)) {
            $query .= " AND e.status = ?";
            $params[] = $status;
        }

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM ($query) as count_table";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Add pagination
        $query .= " ORDER BY e.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $pageSize;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'employees' => $employees,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => ceil($total / $pageSize)
        ];
    }

    public function getById($id) {
        $query = "SELECT e.*, d.name as department_name, p.name as position_name 
                 FROM {$this->table} e 
                 LEFT JOIN departments d ON e.department_id = d.id 
                 LEFT JOIN positions p ON e.position_id = p.id 
                 WHERE e.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $query = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                 VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = array_map(function($field) {
            return "$field = ?";
        }, array_keys($data));
        
        $query = "UPDATE {$this->table} 
                 SET " . implode(', ', $fields) . " 
                 WHERE id = ?";
        
        $params = array_values($data);
        $params[] = $id;
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
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