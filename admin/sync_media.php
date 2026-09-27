<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

echo "<h2>Syncing Legacy Files to Media Database...</h2>";

$uploadDir = realpath(__DIR__ . '/../uploads/news/');
if (!$uploadDir || !is_dir($uploadDir)) {
    die("Upload directory not found.");
}

$files = array_diff(scandir($uploadDir), ['.', '..']);
$added = 0;

foreach ($files as $f) {
    $path = $uploadDir . '/' . $f;
    if (is_file($path)) {
        // Check if exists in DB
        $stmt = $pdo->prepare("SELECT id FROM media WHERE filename = ?");
        $stmt->execute([$f]);
        if (!$stmt->fetch()) {
            // Insert into DB
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            $mime = mime_content_type($path) ?: 'application/octet-stream';
            $size = filesize($path);
            
            $iStmt = $pdo->prepare("INSERT INTO media (filename, original_name, file_path, file_type, file_size, mime_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $iStmt->execute([
                $f,
                $f,
                'uploads/news/' . $f,
                $ext,
                $size,
                $mime,
                $_SESSION['user_id'] ?? 1
            ]);
            $added++;
            echo "Added $f to database.<br>";
        }
    }
}

echo "<h3>Sync Complete! Added $added new media records.</h3>";
echo "<a href='media.php'>Go back to Media Library</a>";
