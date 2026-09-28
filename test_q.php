<?php
require 'db.php';
try {
    $q = "SELECT a.*, m.name as category_name, m.slug as category_slug FROM articles a LEFT JOIN menus m ON m.id = a.category_id WHERE a.status = 'published' AND a.is_trending = 0 AND (a.video_url IS NULL OR a.video_url = '') AND (a.video_file IS NULL OR a.video_file = '') ORDER BY a.is_featured DESC, a.published_at DESC LIMIT 5";
    $stmt = $pdo->query($q);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
