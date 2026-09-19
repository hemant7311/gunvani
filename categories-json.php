<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$categories = $pdo->query('SELECT name, slug FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($categories);
