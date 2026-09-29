<?php
require_once __DIR__ . '/db.php';

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM members LIKE 'rni_no'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE members ADD COLUMN rni_no VARCHAR(100) DEFAULT NULL AFTER blood_group");
        echo "Column 'rni_no' added successfully.\n";
    } else {
        echo "Column 'rni_no' already exists.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
