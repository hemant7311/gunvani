<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

// If existing static file (CSS, JS, images, icons), serve directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Clean Article Route: /article/slug-name
if (preg_match('#^/article/([^/]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = urldecode($matches[1]);
    require __DIR__ . '/article.php';
    exit;
}

// Clean Category Route: /category/category-name
if (preg_match('#^/category/([^/]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = urldecode($matches[1]);
    require __DIR__ . '/category.php';
    exit;
}

// Static Page Routes
if ($uri === '/categories' || $uri === '/categories/') {
    require __DIR__ . '/categories.php';
    exit;
}

if ($uri === '/contact' || $uri === '/contact/') {
    require __DIR__ . '/contact.php';
    exit;
}

if ($uri === '/verification' || $uri === '/verification/') {
    require __DIR__ . '/verification.php';
    exit;
}

if ($uri === '/about-us' || $uri === '/about-us/') {
    require __DIR__ . '/about-us.html';
    exit;
}

// Default homepage
require __DIR__ . '/index.php';
