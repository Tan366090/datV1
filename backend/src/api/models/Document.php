<?php
namespace App\Models;

class Document extends BaseModel {
    protected $table = 'documents';
    
    public function createDocument($title, $description, $filePath, $category, $departmentId, $createdBy) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO {$this->table}
            (title, description, file_path, category, department_id, 
             created_by, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())
        ");
        
        return $stmt->execute([
            $title, $description, $filePath, $category, 
            $departmentId, $createdBy
        ]);
    }
    
    public function updateDocument($documentId, $title, $description, $category, $departmentId) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET title = ?,
                description = ?,
                category = ?,
                department_id = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $title, $description, $category, 
            $departmentId, $documentId
        ]);
    }
    
    public function updateFile($documentId, $filePath) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET file_path = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$filePath, $documentId]);
    }
    
    public function updateDocumentStatus($documentId, $status) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            UPDATE {$this->table}
            SET status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $documentId]);
    }
    
    public function getDocumentDetails($documentId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT d.*, e.name as created_by_name, 
                   dep.name as department_name
            FROM {$this->table} d
            JOIN employees e ON d.created_by = e.id
            JOIN departments dep ON d.department_id = dep.id
            WHERE d.id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$documentId]);
        return $stmt->fetch();
    }
    
    public function getDepartmentDocuments($departmentId, $category = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT d.*, e.name as created_by_name
            FROM {$this->table} d
            JOIN employees e ON d.created_by = e.id
            WHERE d.department_id = ?
            AND d.status = 'active'
        ";
        
        $params = [$departmentId];
        
        if ($category) {
            $sql .= " AND d.category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY d.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function searchDocuments($keyword, $category = null, $departmentId = null) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT d.*, e.name as created_by_name, dep.name as department_name
            FROM {$this->table} d
            JOIN employees e ON d.created_by = e.id
            JOIN departments dep ON d.department_id = dep.id
            WHERE d.status = 'active'
            AND (d.title LIKE ? OR d.description LIKE ?)
        ";
        
        $params = ["%{$keyword}%", "%{$keyword}%"];
        
        if ($category) {
            $sql .= " AND d.category = ?";
            $params[] = $category;
        }
        
        if ($departmentId) {
            $sql .= " AND d.department_id = ?";
            $params[] = $departmentId;
        }
        
        $sql .= " ORDER BY d.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getDocumentCategories() {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT DISTINCT category
            FROM {$this->table}
            WHERE status = 'active'
            ORDER BY category
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getDocumentHistory($documentId) {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT dh.*, e.name as updated_by_name
            FROM document_history dh
            JOIN employees e ON dh.updated_by = e.id
            WHERE dh.document_id = ?
            ORDER BY dh.updated_at DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$documentId]);
        return $stmt->fetchAll();
    }
} 