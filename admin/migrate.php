<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

// Only admins can run migrations
require_admin();

echo "<h2>Database Migration Status</h2>";

function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}

function columnExists($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        return false;
    }
}

function safeAddColumn($pdo, $table, $column, $definition) {
    if (!tableExists($pdo, $table)) {
        echo "<p style='color:orange;'>Table <b>$table</b> does not exist, skipping $column.</p>";
        return;
    }
    if (!columnExists($pdo, $table, $column)) {
        try {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            echo "<p style='color:green;'>Added column <b>$column</b> to <b>$table</b>.</p>";
        } catch (PDOException $e) {
            error_log("Migration Error ($table.$column): " . $e->getMessage());
            echo "<p style='color:red;'>Error adding column <b>$column</b> to <b>$table</b>: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

function safeModifyColumn($pdo, $table, $column, $definition) {
    if (tableExists($pdo, $table) && columnExists($pdo, $table, $column)) {
        try {
            $pdo->exec("ALTER TABLE `$table` MODIFY COLUMN `$column` $definition");
            echo "<p style='color:green;'>Modified column <b>$column</b> in <b>$table</b>.</p>";
        } catch (PDOException $e) {
            error_log("Migration Error ($table.$column): " . $e->getMessage());
            echo "<p style='color:red;'>Error modifying column <b>$column</b> in <b>$table</b>.</p>";
        }
    }
}

// 1. Users Table
try {
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
} catch (PDOException $e) {
    error_log("Migration error creating users: " . $e->getMessage());
}

// Ensure Legacy Admins table exists (for auth fallback)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
} catch (PDOException $e) {}

// Cities Table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS cities (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        status ENUM('active','inactive') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
} catch (PDOException $e) {}

// Media Table
try {
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
} catch (PDOException $e) {}

// Members Table
try {
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
} catch (PDOException $e) {}

// Menus Table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS menus (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        parent_id INT(11) DEFAULT NULL,
        menu_type ENUM('category','city','state','custom') NOT NULL DEFAULT 'category',
        target_url VARCHAR(500) DEFAULT NULL,
        category_id INT(11) DEFAULT NULL,
        city_id INT(11) DEFAULT NULL,
        state_id INT(11) DEFAULT NULL,
        display_order INT(11) NOT NULL DEFAULT 0,
        status ENUM('active','inactive') NOT NULL DEFAULT 'active',
        open_new_tab TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_parent (parent_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
} catch (PDOException $e) {}

// Settings Table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
} catch (PDOException $e) {}

// Article_Menu table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS article_menu (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        article_id INT(11) NOT NULL,
        menu_id INT(11) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_art_menu (article_id, menu_id),
        INDEX idx_menu (menu_id),
        INDEX idx_article (article_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
} catch (PDOException $e) {}


// Safe Column Migrations

// Articles Columns
safeAddColumn($pdo, 'articles', 'city_id', 'INT(11) DEFAULT NULL');
safeAddColumn($pdo, 'articles', 'video_type', "ENUM('none','upload','youtube','url') DEFAULT 'none'");
safeAddColumn($pdo, 'articles', 'video_url', 'VARCHAR(500) DEFAULT NULL');
safeAddColumn($pdo, 'articles', 'author_id', 'INT(11) DEFAULT NULL');
safeAddColumn($pdo, 'articles', 'tags', 'VARCHAR(255) DEFAULT NULL');
safeAddColumn($pdo, 'articles', 'is_featured', 'TINYINT(1) DEFAULT 0');
safeAddColumn($pdo, 'articles', 'is_trending', 'TINYINT(1) DEFAULT 0');
safeAddColumn($pdo, 'articles', 'is_breaking', 'TINYINT(1) DEFAULT 0');
safeAddColumn($pdo, 'articles', 'seo_title', 'VARCHAR(255) DEFAULT NULL');
safeAddColumn($pdo, 'articles', 'seo_description', 'VARCHAR(500) DEFAULT NULL');
safeAddColumn($pdo, 'articles', 'seo_keywords', 'VARCHAR(255) DEFAULT NULL');
safeAddColumn($pdo, 'articles', 'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

// Members Columns
safeAddColumn($pdo, 'members', 'status', "VARCHAR(20) DEFAULT 'approved'");
safeAddColumn($pdo, 'members', 'rejection_reason', 'TEXT DEFAULT NULL');
safeAddColumn($pdo, 'members', 'reviewed_by', 'INT(11) DEFAULT NULL');
safeAddColumn($pdo, 'members', 'reviewed_at', 'DATETIME DEFAULT NULL');

// Users Columns
safeAddColumn($pdo, 'users', 'password_hash', 'VARCHAR(255) NULL AFTER email');
safeAddColumn($pdo, 'users', 'password', 'VARCHAR(255) NULL AFTER password_hash');

// Sync passwords in users safely
try {
    $pdo->exec("UPDATE users SET password_hash = password WHERE (password_hash IS NULL OR password_hash = '') AND (password IS NOT NULL AND password != '')");
    $pdo->exec("UPDATE users SET password = password_hash WHERE (password IS NULL OR password = '') AND (password_hash IS NOT NULL AND password_hash != '')");
} catch (PDOException $e) {
    error_log("Password sync error: " . $e->getMessage());
}

// Menus Columns
safeAddColumn($pdo, 'menus', 'open_new_tab', 'TINYINT(1) NOT NULL DEFAULT 0');
safeAddColumn($pdo, 'menus', 'target_url', 'VARCHAR(500) DEFAULT NULL');
safeAddColumn($pdo, 'menus', 'category_id', 'INT(11) DEFAULT NULL');
safeAddColumn($pdo, 'menus', 'city_id', 'INT(11) DEFAULT NULL');
safeAddColumn($pdo, 'menus', 'state_id', 'INT(11) DEFAULT NULL');
safeAddColumn($pdo, 'menus', 'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
safeModifyColumn($pdo, 'menus', 'menu_type', "ENUM('category','city','state','custom') NOT NULL DEFAULT 'category'");


// Database Seeding
try {
    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($userCount === 0) {
        $adminHash = password_hash('Gunvani@2025', PASSWORD_DEFAULT);
        $uStmt = $pdo->prepare('INSERT INTO users (name, username, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)');
        $uStmt->execute(['Administrator', 'admin', 'admin@gunvani.com', $adminHash, 'admin', 'active']);
        echo "<p>Seeded default admin user.</p>";
    }
} catch (PDOException $e) {}

try {
    $menuCount = (int) $pdo->query('SELECT COUNT(*) FROM menus')->fetchColumn();
    if ($menuCount === 0) {
        $defaultMenus = [
            ['India', 'india', null, 'category', 1],
            ['Foreign', 'foreign', null, 'category', 2],
            ['Uttar Pradesh', 'uttar-pradesh', null, 'category', 3],
            ['mainpuri', 'mainpuri', null, 'city', 4]
        ];
        $menuStmt = $pdo->prepare('INSERT INTO menus (name, slug, parent_id, menu_type, display_order, status) VALUES (?, ?, ?, ?, ?, "active")');
        foreach ($defaultMenus as $dm) {
            $menuStmt->execute($dm);
        }
        echo "<p>Seeded default menus.</p>";
    }
} catch (PDOException $e) {}

echo "<p><b>Migration script completed safely.</b></p>";
