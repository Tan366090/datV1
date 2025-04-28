<?php
// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        // Get all employees or specific employee
        if (isset($uri[4])) {
            // Get specific employee
            $employee_id = $uri[4];
            $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
            $stmt->execute([$employee_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($employee) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $employee
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ]);
            }
        } else {
            // Get all employees
            $stmt = $conn->query("SELECT * FROM employees");
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'status' => 'success',
                'data' => $employees
            ]);
        }
        break;
        
    case 'POST':
        // Add new employee
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($data)) {
            $stmt = $conn->prepare("INSERT INTO employees (name, email, position, department) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['position'],
                $data['department']
            ]);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Employee added successfully',
                'id' => $conn->lastInsertId()
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid data'
            ]);
        }
        break;
        
    case 'PUT':
        // Update employee
        if (isset($uri[4])) {
            $employee_id = $uri[4];
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!empty($data)) {
                $stmt = $conn->prepare("UPDATE employees SET name = ?, email = ?, position = ?, department = ? WHERE id = ?");
                $stmt->execute([
                    $data['name'],
                    $data['email'],
                    $data['position'],
                    $data['department'],
                    $employee_id
                ]);
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Employee updated successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid data'
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Employee ID is required'
            ]);
        }
        break;
        
    case 'DELETE':
        // Delete employee
        if (isset($uri[4])) {
            $employee_id = $uri[4];
            $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$employee_id]);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Employee deleted successfully'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Employee ID is required'
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Method not allowed'
        ]);
        break;
}
?> 