<?php
require_once __DIR__ . '/../../../config/autoload.php';

use App\Middleware\CorsMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\ValidationMiddleware;
use App\Handlers\Response;
use App\Handlers\ErrorHandler;
use App\Services\DataStore;

// Apply CORS middleware
$cors = new CorsMiddleware();
$cors->handle();

// Apply Auth middleware
$auth = new AuthMiddleware();
$user = $auth->handle();

if (!$user) {
    ErrorHandler::handleUnauthorized();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Get resource ID if exists
$id = isset($pathParts[3]) ? (int)$pathParts[3] : null;

// Get action if exists
$action = isset($pathParts[4]) ? $pathParts[4] : null;

// Initialize DataStore
$dataStore = DataStore::getInstance();

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                if ($action === 'details') {
                    // Get employee details with related data
                    $employee = $dataStore->getData('employees', ['id' => $id]);
                    if (!$employee) {
                        ErrorHandler::handleNotFound();
                    }
                    
                    $employee = $employee[0];
                    
                    // Get related data
                    $employee['department'] = $dataStore->getData('departments', ['id' => $employee['department_id']])[0] ?? null;
                    $employee['position'] = $dataStore->getData('positions', ['id' => $employee['position_id']])[0] ?? null;
                    $employee['contracts'] = $dataStore->getData('contracts', ['employee_id' => $id]);
                    $employee['certificates'] = $dataStore->getData('certificates', ['employee_id' => $id]);
                    $employee['family_members'] = $dataStore->getData('family_members', ['employee_id' => $id]);
                    
                    Response::success($employee);
                } else {
                    $employee = $dataStore->getData('employees', ['id' => $id]);
                    if (!$employee) {
                        ErrorHandler::handleNotFound();
                    }
                    Response::success($employee[0]);
                }
            } else {
                $page = $_GET['page'] ?? 1;
                $perPage = $_GET['per_page'] ?? 10;
                $departmentId = $_GET['department_id'] ?? null;
                $positionId = $_GET['position_id'] ?? null;
                $status = $_GET['status'] ?? null;
                $search = $_GET['search'] ?? null;

                $conditions = [];
                if ($departmentId) {
                    $conditions['department_id'] = $departmentId;
                }
                if ($positionId) {
                    $conditions['position_id'] = $positionId;
                }
                if ($status) {
                    $conditions['status'] = $status;
                }
                if ($search) {
                    $conditions['search'] = $search;
                }

                $employees = $dataStore->getAllData('employees', [
                    'page' => $page,
                    'per_page' => $perPage,
                    'conditions' => $conditions
                ]);
                Response::paginated($employees['data'], $employees['total'], $page, $perPage);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate input
            $rules = [
                'first_name' => 'required|min:2|max:50',
                'last_name' => 'required|min:2|max:50',
                'email' => 'required|email|unique:employees',
                'phone' => 'required|min:10|max:15',
                'address' => 'required|min:5|max:255',
                'gender' => 'required|in:male,female,other',
                'birth_date' => 'required|date',
                'hire_date' => 'required|date',
                'department_id' => 'required|integer|exists:departments,id',
                'position_id' => 'required|integer|exists:positions,id',
                'status' => 'required|in:active,inactive,on_leave,terminated',
                'salary' => 'required|numeric|min:0',
                'emergency_contact' => 'required|min:5|max:255',
                'emergency_phone' => 'required|min:10|max:15',
                'national_id' => 'required|min:9|max:20',
                'tax_code' => 'required|min:10|max:20',
                'bank_account' => 'required|min:10|max:20',
                'bank_name' => 'required|min:2|max:100'
            ];
            
            $validator = new ValidationMiddleware($rules);
            if (!$validator->validate($data)) {
                ErrorHandler::handleValidationError($validator->getErrors());
            }

            $employeeId = $dataStore->insertData('employees', $data);
            $employee = $dataStore->getData('employees', ['id' => $employeeId]);
            Response::created($employee[0]);
            break;

        case 'PUT':
            if (!$id) {
                ErrorHandler::handleNotFound();
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate input
            $rules = [
                'first_name' => 'min:2|max:50',
                'last_name' => 'min:2|max:50',
                'email' => 'email|unique:employees',
                'phone' => 'min:10|max:15',
                'address' => 'min:5|max:255',
                'gender' => 'in:male,female,other',
                'birth_date' => 'date',
                'hire_date' => 'date',
                'department_id' => 'integer|exists:departments,id',
                'position_id' => 'integer|exists:positions,id',
                'status' => 'in:active,inactive,on_leave,terminated',
                'salary' => 'numeric|min:0',
                'emergency_contact' => 'min:5|max:255',
                'emergency_phone' => 'min:10|max:15',
                'national_id' => 'min:9|max:20',
                'tax_code' => 'min:10|max:20',
                'bank_account' => 'min:10|max:20',
                'bank_name' => 'min:2|max:100'
            ];
            
            $validator = new ValidationMiddleware($rules);
            if (!$validator->validate($data)) {
                ErrorHandler::handleValidationError($validator->getErrors());
            }

            $dataStore->updateData('employees', $data, ['id' => $id]);
            $employee = $dataStore->getData('employees', ['id' => $id]);
            Response::updated($employee[0]);
            break;

        case 'DELETE':
            if (!$id) {
                ErrorHandler::handleNotFound();
            }

            // Check if employee has related records
            $hasRelatedRecords = false;
            $relatedTables = ['contracts', 'certificates', 'family_members', 'attendance', 'salaries'];
            
            foreach ($relatedTables as $table) {
                $records = $dataStore->getData($table, ['employee_id' => $id]);
                if (!empty($records)) {
                    $hasRelatedRecords = true;
                    break;
                }
            }

            if ($hasRelatedRecords) {
                ErrorHandler::handle('Cannot delete employee with related records', 400);
            }

            $dataStore->deleteData('employees', ['id' => $id]);
            Response::deleted();
            break;

        default:
            ErrorHandler::handle('Method not allowed', 405);
    }
} catch (\Exception $e) {
    ErrorHandler::handle($e);
}
?> 