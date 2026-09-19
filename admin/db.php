<?php
$DB_HOST = 'localhost';
$DB_NAME = 'gunvani';
$DB_USER = 'root';
$DB_PASS = ''; // change if you set MySQL password

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
