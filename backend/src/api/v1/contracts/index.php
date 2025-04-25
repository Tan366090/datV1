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
                if ($action === 'history') {
                    $history = $dataStore->getData('contracts', ['employee_id' => $id], 'start_date DESC');
                    Response::success($history);
                } else {
                    $contract = $dataStore->getData('contracts', ['id' => $id]);
                    if (!$contract) {
                        ErrorHandler::handleNotFound();
                    }
                    Response::success($contract[0]);
                }
            } else {
                $page = $_GET['page'] ?? 1;
                $perPage = $_GET['per_page'] ?? 10;
                $employeeId = $_GET['employee_id'] ?? null;
                $type = $_GET['type'] ?? null;
                $status = $_GET['status'] ?? null;
                $startDate = $_GET['start_date'] ?? null;
                $endDate = $_GET['end_date'] ?? null;

                $conditions = [];
                if ($employeeId) {
                    $conditions['employee_id'] = $employeeId;
                }
                if ($type) {
                    $conditions['type'] = $type;
                }
                if ($status) {
                    $conditions['status'] = $status;
                }
                if ($startDate && $endDate) {
                    $conditions['start_date'] = ['BETWEEN', $startDate, $endDate];
                }

                $contracts = $dataStore->getAllData('contracts', [
                    'page' => $page,
                    'per_page' => $perPage,
                    'conditions' => $conditions
                ]);
                Response::paginated($contracts['data'], $contracts['total'], $page, $perPage);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate input
            $rules = [
                'employee_id' => 'required|integer|exists:employees,id',
                'type' => 'required|in:full_time,part_time,internship,probation',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'salary' => 'required|numeric|min:0',
                'status' => 'required|in:active,expired,terminated',
                'probation_period' => 'required|integer|min:0',
                'notice_period' => 'required|integer|min:0',
                'working_hours' => 'required|integer|min:1|max:24',
                'benefits' => 'required|array',
                'terms' => 'required|array',
                'document_url' => 'required|max:255'
            ];
            
            $validator = new ValidationMiddleware($rules);
            if (!$validator->validate($data)) {
                ErrorHandler::handleValidationError($validator->getErrors());
            }

            // Check if employee has active contract
            $activeContract = $dataStore->getData('contracts', [
                'employee_id' => $data['employee_id'],
                'status' => 'active'
            ]);
            if (!empty($activeContract)) {
                ErrorHandler::handle('Employee already has an active contract', 400);
            }

            $contractId = $dataStore->insertData('contracts', $data);
            $contract = $dataStore->getData('contracts', ['id' => $contractId]);
            Response::created($contract[0]);
            break;

        case 'PUT':
            if (!$id) {
                ErrorHandler::handleNotFound();
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate input
            $rules = [
                'type' => 'in:full_time,part_time,internship,probation',
                'start_date' => 'date',
                'end_date' => 'date|after:start_date',
                'salary' => 'numeric|min:0',
                'status' => 'in:active,expired,terminated',
                'probation_period' => 'integer|min:0',
                'notice_period' => 'integer|min:0',
                'working_hours' => 'integer|min:1|max:24',
                'benefits' => 'array',
                'terms' => 'array',
                'document_url' => 'max:255'
            ];
            
            $validator = new ValidationMiddleware($rules);
            if (!$validator->validate($data)) {
                ErrorHandler::handleValidationError($validator->getErrors());
            }

            // If updating status to active, check for other active contracts
            if (isset($data['status']) && $data['status'] === 'active') {
                $activeContract = $dataStore->getData('contracts', [
                    'employee_id' => $data['employee_id'],
                    'status' => 'active',
                    'id' => ['!=', $id]
                ]);
                if (!empty($activeContract)) {
                    ErrorHandler::handle('Employee already has an active contract', 400);
                }
            }

            $dataStore->updateData('contracts', $data, ['id' => $id]);
            $contract = $dataStore->getData('contracts', ['id' => $id]);
            Response::updated($contract[0]);
            break;

        case 'DELETE':
            if (!$id) {
                ErrorHandler::handleNotFound();
            }

            // Check if contract is active
            $contract = $dataStore->getData('contracts', ['id' => $id]);
            if ($contract[0]['status'] === 'active') {
                ErrorHandler::handle('Cannot delete active contract', 400);
            }

            $dataStore->deleteData('contracts', ['id' => $id]);
            Response::deleted();
            break;

        default:
            ErrorHandler::handle('Method not allowed', 405);
    }
} catch (\Exception $e) {
    ErrorHandler::handle($e);
}
?> 