<?php
// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cấu hình CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Kiểm tra kết nối database
try {
    require_once __DIR__ . '/../config/Database.php';
    $database = new Database();
    $conn = $database->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối database: ' . $e->getMessage()
    ]);
    exit();
}

class EmployeeAPI {
    private $conn;
    private $table_name = "employees";

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Lấy danh sách nhân viên với phân trang và bộ lọc
    public function getEmployees() {
        try {
            // Lấy các tham số từ query string
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $department_id = isset($_GET['department_id']) ? $_GET['department_id'] : '';
            $position_id = isset($_GET['position_id']) ? $_GET['position_id'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';

            // Tính toán offset
            $offset = ($page - 1) * $per_page;

            // Xây dựng câu query với join các bảng cần thiết
            $query = "SELECT 
                        e.id,
                        e.employee_code,
                        e.status,
                        e.hire_date,
                        e.termination_date,
                        u.username,
                        u.email,
                        up.full_name,
                        up.date_of_birth as birth_date,
                        up.phone_number,
                        up.gender,
                        d.name as department_name,
                        p.name as position_name
                     FROM {$this->table_name} e
                     LEFT JOIN users u ON e.user_id = u.user_id
                     LEFT JOIN user_profiles up ON u.user_id = up.user_id
                     LEFT JOIN departments d ON e.department_id = d.id
                     LEFT JOIN positions p ON e.position_id = p.id
                     WHERE 1=1";

            $params = [];

            // Thêm điều kiện tìm kiếm
            if (!empty($search)) {
                $query .= " AND (u.username LIKE ? OR e.employee_code LIKE ? OR u.email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            // Thêm điều kiện phòng ban
            if (!empty($department_id)) {
                $query .= " AND e.department_id = ?";
                $params[] = $department_id;
            }

            // Thêm điều kiện chức vụ
            if (!empty($position_id)) {
                $query .= " AND e.position_id = ?";
                $params[] = $position_id;
            }

            // Thêm điều kiện trạng thái
            if (!empty($status)) {
                $query .= " AND e.status = ?";
                $params[] = $status;
            }

            // Lấy tổng số bản ghi
            $count_query = "SELECT COUNT(*) as total FROM ({$query}) as count_table";
            $stmt = $this->conn->prepare($count_query);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Thêm phân trang
            $query .= " ORDER BY e.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $per_page;
            $params[] = $offset;

            // Thực thi query
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Trả về kết quả
            echo json_encode([
                'success' => true,
                'data' => $employees,
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách nhân viên: ' . $e->getMessage()
            ]);
        }
    }

    // Thêm nhân viên mới
    public function addEmployee() {
        try {
            // Lấy dữ liệu từ request body
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                throw new Exception('Dữ liệu không hợp lệ');
            }

            // Validate dữ liệu
            $required_fields = ['email', 'phone', 'employee_code', 'department_id', 'hire_date', 'contract_type', 'base_salary', 'contract_start_date'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Trường {$field} là bắt buộc");
                }
            }

            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ');
            }

            // Validate phone
            if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
                throw new Exception('Số điện thoại không hợp lệ');
            }

            // Kiểm tra email đã tồn tại chưa
            $check_email = "SELECT user_id FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($check_email);
            $stmt->execute([$data['email']]);
            if ($stmt->rowCount() > 0) {
                throw new Exception('Email đã tồn tại');
            }

            // Kiểm tra mã nhân viên đã tồn tại chưa
            $check_code = "SELECT id FROM employees WHERE employee_code = ?";
            $stmt = $this->conn->prepare($check_code);
            $stmt->execute([$data['employee_code']]);
            if ($stmt->rowCount() > 0) {
                throw new Exception('Mã nhân viên đã tồn tại');
            }

            // Bắt đầu transaction
            $this->conn->beginTransaction();

            try {
                // Thêm user
                $user_query = "INSERT INTO users (email, username, password, role) VALUES (?, ?, ?, 'employee')";
                $stmt = $this->conn->prepare($user_query);
                $stmt->execute([
                    $data['email'],
                    $data['email'],
                    password_hash('123456', PASSWORD_DEFAULT) // Mật khẩu mặc định
                ]);
                $user_id = $this->conn->lastInsertId();

                // Thêm user profile
                $profile_query = "INSERT INTO user_profiles (user_id, full_name, phone_number, date_of_birth, gender, permanent_address) 
                                VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($profile_query);
                $stmt->execute([
                    $user_id,
                    $data['name'],
                    $data['phone'],
                    $data['birthday'] ?? null,
                    $data['gender'] ?? null,
                    $data['address'] ?? null
                ]);

                // Xử lý chức vụ mới nếu có
                $position_id = null;
                if (isset($data['position_name']) && isset($data['department_id'])) {
                    // Thêm chức vụ mới
                    $position_query = "INSERT INTO positions (name, department_id) VALUES (?, ?)";
                    $stmt = $this->conn->prepare($position_query);
                    $stmt->execute([$data['position_name'], $data['department_id']]);
                    $position_id = $this->conn->lastInsertId();
                } else if (isset($data['position_id'])) {
                    $position_id = $data['position_id'];
                }

                // Thêm employee
                $employee_query = "INSERT INTO employees (user_id, employee_code, department_id, position_id, hire_date, 
                                contract_type, base_salary, contract_start_date, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
                $stmt = $this->conn->prepare($employee_query);
                $stmt->execute([
                    $user_id,
                    $data['employee_code'],
                    $data['department_id'],
                    $position_id,
                    $data['hire_date'],
                    $data['contract_type'],
                    $data['base_salary'],
                    $data['contract_start_date']
                ]);
                $employee_id = $this->conn->lastInsertId();

                // Nếu có thông tin gia đình
                if (!empty($data['family_members'])) {
                    foreach ($data['family_members'] as $member) {
                        $family_query = "INSERT INTO family_members (employee_id, name, relationship, date_of_birth, 
                                        occupation, is_dependent) 
                                        VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $this->conn->prepare($family_query);
                        $stmt->execute([
                            $employee_id,
                            $member['name'],
                            $member['relationship'],
                            $member['birthday'] ?? null,
                            $member['occupation'] ?? null,
                            $member['is_dependent'] ? 1 : 0
                        ]);
                    }
                }

                // Commit transaction
                $this->conn->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Thêm nhân viên thành công',
                    'data' => [
                        'id' => $employee_id,
                        'employee_code' => $data['employee_code']
                    ]
                ]);

            } catch (Exception $e) {
                // Rollback transaction nếu có lỗi
                $this->conn->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Xóa nhân viên
    public function deleteEmployee($id) {
        try {
            // Kiểm tra nhân viên có tồn tại không
            $check_query = "SELECT id FROM {$this->table_name} WHERE id = ?";
            $stmt = $this->conn->prepare($check_query);
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Nhân viên không tồn tại'
                ]);
                return;
            }

            // Xóa nhân viên
            $query = "DELETE FROM {$this->table_name} WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            echo json_encode([
                'success' => true,
                'message' => 'Xóa nhân viên thành công'
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi xóa nhân viên: ' . $e->getMessage()
            ]);
        }
    }
}

// Xử lý request
$api = new EmployeeAPI();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $api->getEmployees();
        break;
    case 'POST':
        $api->addEmployee();
        break;
    case 'DELETE':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            $api->deleteEmployee($id);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu tham số id'
            ]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức không được hỗ trợ'
        ]);
        break;
} 