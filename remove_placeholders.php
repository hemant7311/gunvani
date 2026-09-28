<?php
require_once __DIR__ . '/db.php';

echo "<h2>Cleaning up dummy images from database...</h2>";

$tables = [
    'articles' => ['image', 'video_url'],
    'members' => ['photo'],
    'menus' => ['image']
];

foreach ($tables as $table => $columns) {
    foreach ($columns as $col) {
        try {
            $stmt = $pdo->prepare("UPDATE {$table} SET {$col} = NULL WHERE {$col} LIKE '%first8.jpg%' OR {$col} LIKE '%second6.webp%' OR {$col} LIKE '%first7.jpg%' OR {$col} LIKE '%placeholder%'");
            $stmt->execute();
            echo "Cleaned column {$col} in table {$table}. Rows affected: " . $stmt->rowCount() . "<br>";
        } catch (Exception $e) {
            echo "Error cleaning {$table}.{$col}: " . $e->getMessage() . "<br>";
        }
    }
}

echo "<h3>Cleanup complete!</h3>";
