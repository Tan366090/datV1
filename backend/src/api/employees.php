<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/Database.php';

class EmployeeAPI {
    private $conn;
    private $table_name = "employees";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
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