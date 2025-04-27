<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'qlnhansu';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create employee_sentiment table
    $sql = "CREATE TABLE employee_sentiment (
        sentiment_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        sentiment_score DECIMAL(5,2) NOT NULL,
        analysis_date DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_employee (employee_id),
        CONSTRAINT fk_employee FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
    ) ENGINE=InnoDB";
    $conn->exec($sql);
    echo "Table employee_sentiment created successfully\n";

    // Insert sample data
    $sql = "INSERT INTO employee_sentiment (employee_id, sentiment_score, analysis_date) VALUES 
        (1, 0.75, '2024-04-27 10:00:00'),
        (2, 0.85, '2024-04-27 10:00:00'),
        (3, 0.65, '2024-04-27 10:00:00'),
        (4, 0.90, '2024-04-27 10:00:00'),
        (5, 0.70, '2024-04-27 10:00:00')";
    $conn->exec($sql);
    echo "Sample sentiment data inserted successfully\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 