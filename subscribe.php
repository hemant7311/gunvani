<?php
require_once __DIR__ . '/db.php';

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, status) VALUES (?, 'active') ON DUPLICATE KEY UPDATE status = 'active'");
    $stmt->execute([$email]);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Successfully subscribed to Gunvani News newsletter!']);
        exit;
    }
} catch (Exception $e) {}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;
