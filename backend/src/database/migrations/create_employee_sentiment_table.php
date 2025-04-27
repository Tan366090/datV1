<?php
require_once __DIR__ . '/../../config/database.php';

try {
    // Create employee_sentiment table
    $query = "CREATE TABLE IF NOT EXISTS employee_sentiment (
        sentiment_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        sentiment_score DECIMAL(5,2) NOT NULL,
        analysis_date DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
    )";

    $conn->exec($query);
    echo "Table employee_sentiment created successfully\n";

    // Insert some sample data
    $sampleData = [
        [1, 0.75, '2024-04-27 10:00:00'],
        [2, 0.85, '2024-04-27 10:00:00'],
        [3, 0.65, '2024-04-27 10:00:00'],
        [4, 0.90, '2024-04-27 10:00:00'],
        [5, 0.70, '2024-04-27 10:00:00']
    ];

    $insertQuery = "INSERT INTO employee_sentiment (employee_id, sentiment_score, analysis_date) 
                    VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);

    foreach ($sampleData as $data) {
        $stmt->execute($data);
    }
    echo "Sample data inserted successfully\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 