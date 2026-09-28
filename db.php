<?php
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'u831226226_gunvaniupdate';
$DB_USER = getenv('DB_USER') ?: 'u831226226_newgunvani';
$DB_PASS = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'Gunvani@123';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Fallback for local XAMPP environment if live credentials are not present locally
    if ($DB_HOST === 'localhost' || $DB_HOST === '127.0.0.1') {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=gunvani;charset=utf8mb4", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e2) {
            die("Database connection failed: " . $e->getMessage());
        }
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}

