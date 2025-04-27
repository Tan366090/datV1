<?php
// Include database connection
require_once '../../config/database.php';

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        if ($id) {
            // Get single position
            $stmt = $conn->prepare("
                SELECT p.*, 
                       d.name as department_name,
                       COUNT(u.user_id) as employee_count
                FROM positions p
                LEFT JOIN departments d ON p.department_id = d.id
                LEFT JOIN users u ON p.id = u.position_id
                WHERE p.id = ?
                GROUP BY p.id
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $position = $result->fetch_assoc();
            
            if ($position) {
                sendResponse($position);
            } else {
                handleError('Position not found', 404);
            }
        } else {
            // Get positions list with filters
            $department_id = $_GET['department_id'] ?? null;
            $status = $_GET['status'] ?? null;
            $search = $_GET['search'] ?? null;
            
            $query = "
                SELECT p.*, 
                       d.name as department_name,
                       COUNT(u.user_id) as employee_count
                FROM positions p
                LEFT JOIN departments d ON p.department_id = d.id
                LEFT JOIN users u ON p.id = u.position_id
                WHERE 1=1
            ";
            
            $params = [];
            $types = "";
            
            if ($department_id) {
                $query .= " AND p.department_id = ?";
                $params[] = $department_id;
                $types .= "i";
            }
            
            if ($status) {
                $query .= " AND p.status = ?";
                $params[] = $status;
                $types .= "s";
            }
            
            if ($search) {
                $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
                $search_param = "%$search%";
                $params[] = $search_param;
                $params[] = $search_param;
                $types .= "ss";
            }
            
            $query .= " GROUP BY p.id ORDER BY p.name ASC";
            
            $stmt = $conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $positions = [];
            while ($row = $result->fetch_assoc()) {
                $positions[] = $row;
            }
            sendResponse($positions);
        }
        break;
        
    case 'POST':
        // Create new position
        $required_fields = ['name', 'department_id', 'description', 'status'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field])) {
                handleError("Missing required field: $field");
            }
        }
        
        $stmt = $conn->prepare("
            INSERT INTO positions (
                name,
                department_id,
                description,
                status,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->bind_param(
            "siss",
            $input['name'],
            $input['department_id'],
            $input['description'],
            $input['status']
        );
        
        if ($stmt->execute()) {
            sendResponse(['id' => $conn->insert_id], 201);
        } else {
            handleError('Failed to create position');
        }
        break;
        
    case 'PUT':
        // Update position
        if (!$id) {
            handleError('Position ID is required');
        }
        
        $stmt = $conn->prepare("
            UPDATE positions 
            SET name = ?,
                department_id = ?,
                description = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param(
            "sissi",
            $input['name'],
            $input['department_id'],
            $input['description'],
            $input['status'],
            $id
        );
        
        if ($stmt->execute()) {
            sendResponse(['message' => 'Position updated successfully']);
        } else {
            handleError('Failed to update position');
        }
        break;
        
    case 'DELETE':
        // Delete position
        if (!$id) {
            handleError('Position ID is required');
        }
        
        // Check if position has any employees
        $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE position_id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            handleError('Cannot delete position with assigned employees', 400);
        }
        
        $stmt = $conn->prepare("DELETE FROM positions WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            sendResponse(['message' => 'Position deleted successfully']);
        } else {
            handleError('Failed to delete position');
        }
        break;
        
    default:
        handleError('Method not allowed', 405);
} 