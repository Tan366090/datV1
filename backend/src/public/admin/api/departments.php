<?php
// Include database connection
require_once '../../config/database.php';

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        if ($id) {
            // Get single department
            $stmt = $conn->prepare("
                SELECT d.*, 
                       m.username as manager_name,
                       m.email as manager_email,
                       COUNT(u.user_id) as employee_count
                FROM departments d
                LEFT JOIN users m ON d.manager_id = m.user_id
                LEFT JOIN users u ON d.id = u.department_id
                WHERE d.id = ?
                GROUP BY d.id
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $department = $result->fetch_assoc();
            
            if ($department) {
                sendResponse($department);
            } else {
                handleError('Department not found', 404);
            }
        } else {
            // Get departments list with filters
            $parent_id = $_GET['parent_id'] ?? null;
            $status = $_GET['status'] ?? null;
            $search = $_GET['search'] ?? null;
            
            $query = "
                SELECT d.*, 
                       m.username as manager_name,
                       m.email as manager_email,
                       COUNT(u.user_id) as employee_count
                FROM departments d
                LEFT JOIN users m ON d.manager_id = m.user_id
                LEFT JOIN users u ON d.id = u.department_id
                WHERE 1=1
            ";
            
            $params = [];
            $types = "";
            
            if ($parent_id) {
                $query .= " AND d.parent_id = ?";
                $params[] = $parent_id;
                $types .= "i";
            }
            
            if ($status) {
                $query .= " AND d.status = ?";
                $params[] = $status;
                $types .= "s";
            }
            
            if ($search) {
                $query .= " AND (d.name LIKE ? OR d.description LIKE ?)";
                $search_param = "%$search%";
                $params[] = $search_param;
                $params[] = $search_param;
                $types .= "ss";
            }
            
            $query .= " GROUP BY d.id ORDER BY d.name ASC";
            
            $stmt = $conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $departments = [];
            while ($row = $result->fetch_assoc()) {
                $departments[] = $row;
            }
            sendResponse($departments);
        }
        break;
        
    case 'POST':
        // Create new department
        $required_fields = ['name', 'description', 'status'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field])) {
                handleError("Missing required field: $field");
            }
        }
        
        $stmt = $conn->prepare("
            INSERT INTO departments (
                name,
                parent_id,
                manager_id,
                description,
                status,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->bind_param(
            "siiss",
            $input['name'],
            $input['parent_id'] ?? null,
            $input['manager_id'] ?? null,
            $input['description'],
            $input['status']
        );
        
        if ($stmt->execute()) {
            sendResponse(['id' => $conn->insert_id], 201);
        } else {
            handleError('Failed to create department');
        }
        break;
        
    case 'PUT':
        // Update department
        if (!$id) {
            handleError('Department ID is required');
        }
        
        $stmt = $conn->prepare("
            UPDATE departments 
            SET name = ?,
                parent_id = ?,
                manager_id = ?,
                description = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param(
            "siissi",
            $input['name'],
            $input['parent_id'] ?? null,
            $input['manager_id'] ?? null,
            $input['description'],
            $input['status'],
            $id
        );
        
        if ($stmt->execute()) {
            sendResponse(['message' => 'Department updated successfully']);
        } else {
            handleError('Failed to update department');
        }
        break;
        
    case 'DELETE':
        // Delete department
        if (!$id) {
            handleError('Department ID is required');
        }
        
        // Check if department has any employees
        $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE department_id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            handleError('Cannot delete department with assigned employees', 400);
        }
        
        // Check if department has any sub-departments
        $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM departments WHERE parent_id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            handleError('Cannot delete department with sub-departments', 400);
        }
        
        $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            sendResponse(['message' => 'Department deleted successfully']);
        } else {
            handleError('Failed to delete department');
        }
        break;
        
    default:
        handleError('Method not allowed', 405);
} 