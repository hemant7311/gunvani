<?php
$file = 'db.php';
$content = file_get_contents($file);
$marker = "// 1. Users Table (Unified Auth for Admin & Agent)";
$pos = strpos($content, $marker);

if ($pos !== false) {
    $dbLogic = substr($content, 0, $pos);
    $migrationLogic = "<?php\nrequire_once __DIR__ . '/../includes/auth.php';\nrequire_admin();\n// This file contains all the schema creation and migrations.\n?>\n" . substr($content, $pos);
    
    file_put_contents('db.php', $dbLogic);
    file_put_contents('admin/migrate.php', $migrationLogic);
    echo "Done splitting";
} else {
    echo "Marker not found";
}
