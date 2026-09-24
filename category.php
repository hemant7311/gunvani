<?php
session_start();
require_once __DIR__ . '/db.php';

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
function articleImage($image) {
    if (!$image) return '/images/placeholder/first8.jpg';
    return (strpos($image, 'http') === 0) ? $image : '/uploads/' . ltrim($image, '/');
}
function formatDate($dateStr) {
    if (!$dateStr) return '';
    return date('M d, Y', strtotime($dateStr));
}


$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$slug = end($segments);

if (empty($slug) || $slug === 'category') {
    header('Location: /');
    exit;
}

$cleanSlug = strtolower(urldecode($slug));

// Fetch menu
$menu = false;
try {
    $stmt = $pdo->prepare('SELECT * FROM menus WHERE LOWER(slug) = ? OR LOWER(name) = ?');
    $stmt->execute([$slug, $cleanSlug]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $menu = false;
}

if (!$menu) {
    // 404 Fallback
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "<p>The menu or category you are looking for does not exist.</p>";
    echo "<a href='/'>Return to Homepage</a>";
    exit;
}

// Fetch articles for this menu
$articles = [];
try {
    $stmt = $pdo->prepare("
        SELECT a.* 
        FROM articles a
        JOIN article_menu am ON a.id = am.article_id
        WHERE am.menu_id = ? AND a.status = 'published'
        ORDER BY a.id DESC
    ");
    $stmt->execute([$menu['id']]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$pageTitle = htmlspecialchars($menu['name']) . ' News - Gunvani';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <!-- Gunvani Official Favicon & Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <!-- Google Fonts: Mukta -->
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .category-hero {
            background: linear-gradient(135deg, var(--gn-green) 0%, #0d4a22 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>

    <div class="container my-4">
        <div class="category-hero text-center shadow-sm">
            <h1 class="display-5 fw-bold mb-2"><?= htmlspecialchars($menu['name']) ?></h1>
            <p class="lead mb-0">Latest news and updates from <?= htmlspecialchars($menu['name']) ?></p>
        </div>

        <div class="row g-4">
            <?php if (empty($articles)): ?>
                <div class="col-12 text-center py-5">
                    <h3 class="text-muted">No news found for this category.</h3>
                    <p>Please check back later.</p>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $art): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm border-0 news-card">
                            <a href="/article/<?= escape($art['slug']) ?>">
                                <img src="<?= escape(articleImage($art['image'])) ?>" class="card-img-top cat-card-img" alt="<?= escape($art['title']) ?>" onerror="this.onerror=null; this.src='/images/placeholder/first8.jpg'">
                            </a>
                            <div class="card-body cat-card-body d-flex flex-column">
                                <h5 class="card-title cat-card-title fw-bold">
                                    <a href="/article/<?= escape($art['slug']) ?>" class="text-dark text-decoration-none hover-green">
                                        <?= escape($art['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?= escape(mb_substr($art['summary'], 0, 100)) ?>...
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i><?= formatDate($art['published_at']) ?></span>
                                    <a href="/article/<?= escape($art['slug']) ?>" class="btn btn-sm btn-outline-success">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/main.js"></script>
</body>
</html>