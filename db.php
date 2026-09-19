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

// 1. Users Table (Unified Auth for Admin & Agent)
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','agent') NOT NULL DEFAULT 'agent',
    mobile VARCHAR(50) DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// Legacy Admins table safety
$pdo->exec("CREATE TABLE IF NOT EXISTS admins (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 2. Cities Table
$pdo->exec("CREATE TABLE IF NOT EXISTS cities (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 3. Media Library Table
$pdo->exec("CREATE TABLE IF NOT EXISTS media (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT(11) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_by INT(11) DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 4. Article Media Gallery Junction Table
$pdo->exec("CREATE TABLE IF NOT EXISTS article_media (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    article_id INT(11) NOT NULL,
    media_id INT(11) NOT NULL,
    display_order INT(11) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 5. Contact Messages Table
$pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('unread','read','archived') NOT NULL DEFAULT 'unread',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 6. Newsletter Subscribers Table
$pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
    subscribed_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 7. Comments Table
$pdo->exec("CREATE TABLE IF NOT EXISTS comments (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    article_id INT(11) NOT NULL,
    author_name VARCHAR(255) NOT NULL,
    author_email VARCHAR(255) NOT NULL,
    comment_text TEXT NOT NULL,
    status ENUM('pending','approved','spam','deleted') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// Safely alter articles table for new CMS features
$articleColumns = [
    "ADD COLUMN city_id INT(11) DEFAULT NULL",
    "ADD COLUMN author VARCHAR(255) DEFAULT 'Gunvani Editor'",
    "ADD COLUMN created_by INT(11) DEFAULT NULL",
    "ADD COLUMN video_url VARCHAR(500) DEFAULT NULL",
    "ADD COLUMN video_file VARCHAR(255) DEFAULT NULL",
    "ADD COLUMN is_featured TINYINT(1) DEFAULT 0",
    "ADD COLUMN is_trending TINYINT(1) DEFAULT 0",
    "ADD COLUMN is_breaking TINYINT(1) DEFAULT 0",
    "ADD COLUMN seo_title VARCHAR(255) DEFAULT NULL",
    "ADD COLUMN seo_description VARCHAR(500) DEFAULT NULL",
    "ADD COLUMN seo_keywords VARCHAR(255) DEFAULT NULL",
    "ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

foreach ($articleColumns as $colSql) {
    try {
        $pdo->exec("ALTER TABLE articles " . $colSql);
    } catch (Exception $e) {}
}

// Safely alter members table for approval/rejection details
$memberColumns = [
    "ADD COLUMN status VARCHAR(20) DEFAULT 'approved'",
    "ADD COLUMN rejection_reason TEXT DEFAULT NULL",
    "ADD COLUMN reviewed_by INT(11) DEFAULT NULL",
    "ADD COLUMN reviewed_at DATETIME DEFAULT NULL"
];

foreach ($memberColumns as $colSql) {
    try {
        $pdo->exec("ALTER TABLE members " . $colSql);
    } catch (Exception $e) {}
}

// Create Settings Table (if not exists) for ad banners
$pdo->exec("CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// Safely alter users table so both password and password_hash columns exist and stay in sync
$userColumns = [
    "ADD COLUMN password_hash VARCHAR(255) NULL AFTER email",
    "ADD COLUMN password VARCHAR(255) NULL AFTER password_hash"
];

foreach ($userColumns as $colSql) {
    try {
        $pdo->exec("ALTER TABLE users " . $colSql);
    } catch (Exception $e) {}
}

try {
    $pdo->exec("UPDATE users SET password_hash = password WHERE (password_hash IS NULL OR password_hash = '') AND (password IS NOT NULL AND password != '')");
    $pdo->exec("UPDATE users SET password = password_hash WHERE (password IS NULL OR password = '') AND (password_hash IS NOT NULL AND password_hash != '')");
} catch (Exception $e) {}

// Seed Default Admin in users table if empty
$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($userCount === 0) {
    $adminHash = password_hash('gunvani@2025##', PASSWORD_DEFAULT);
    $uStmt = $pdo->prepare('INSERT INTO users (name, username, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)');
    $uStmt->execute(['Administrator', 'gunvani', 'admin@gunvani.com', $adminHash, 'admin', 'active']);
    
    // Also seed sample agent
    $agentHash = password_hash('agent@123', PASSWORD_DEFAULT);
    $uStmt->execute(['Press Agent Rahul', 'agent_rahul', 'rahul@gunvani.com', $agentHash, 'agent', 'active']);
}

// Ensure default admin in legacy admins table
$adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if ($adminCount === 0) {
    $adminHash = password_hash('gunvani@2025##', PASSWORD_DEFAULT);
    $pdo->prepare('INSERT INTO admins (username, password) VALUES (?, ?)')->execute(['gunvani', $adminHash]);
}





// Ensure members table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS members (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    dob DATE DEFAULT NULL,
    doi DATE DEFAULT NULL,
    doe DATE DEFAULT NULL,
    mobile VARCHAR(50) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    blood_group VARCHAR(10) DEFAULT NULL,
    designation VARCHAR(255) DEFAULT 'Press Reporter',
    photo VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'approved',
    rejection_reason TEXT DEFAULT NULL,
    reviewed_by INT(11) DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$mCheck = (int) $pdo->query("SELECT COUNT(*) FROM members WHERE member_id = 'GN-1001'")->fetchColumn();
if ($mCheck === 0) {
    $mStmt = $pdo->prepare('INSERT IGNORE INTO members (member_id, name, dob, doi, doe, mobile, location, address, blood_group, designation, photo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $mStmt->execute(['GN-1001', 'Hemant Rathore', '1990-05-15', '2025-01-01', '2026-12-31', '7088824006', 'Agra, UP', 'Civil Lines, Agra', 'O+', 'Bureau Chief', 'images/placeholder/first8.jpg', 'approved']);
    $mStmt->execute(['GN-1002', 'Rahul Sharma', '1994-08-20', '2025-01-01', '2026-12-31', '9876543210', 'Lucknow, UP', 'Hazratganj, Lucknow', 'B+', 'Senior Press Correspondent', 'images/placeholder/second6.webp', 'approved']);
    $mStmt->execute(['GN-1003', 'Amit Verma', '1992-11-10', '2025-01-01', '2026-12-31', '9123456789', 'Noida, UP', 'Sector 62, Noida', 'A+', 'Photojournalist', 'images/placeholder/first7.jpg', 'approved']);
}



