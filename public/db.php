<?php
/* Database connection configuration and initialization */
$host = 'localhost';
$db   = 'ticketing_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass);
    
    $conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
    $conn->select_db($db);
    $conn->set_charset($charset);

} catch (\mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
