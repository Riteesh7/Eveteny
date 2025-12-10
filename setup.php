<?php
/* Database table creation script */
require 'db.php';

$tableSql = "CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    sale_start DATETIME NOT NULL,
    sale_end DATETIME NOT NULL,
    is_public TINYINT(1) DEFAULT 1,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $conn->query($tableSql);
    echo "Database and table 'tickets' setup successfully.";
} catch (Exception $e) {
    echo "Error setting up table: " . $e->getMessage();
}
?>
