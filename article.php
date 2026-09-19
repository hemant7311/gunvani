<?php
if (!isset($_COOKIE['googtrans'])) {
    setcookie('googtrans', '/en/hi', time() + (86400 * 30), '/');
    $_COOKIE['googtrans'] = '/en/hi';
}

require_once __DIR__ . '/db.php';

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function articleImage($image) {
    if (!$image) {
        return 'images/placeholder/first8.jpg';
    }
    if (preg_match('#^(uploads/|images/)#', $image)) {
        return $image;
    }
    return 'uploads/news/' . $image;
}

function formatDate($date) {
    if (!$date) return 'Sep 14, 2026';
    return date('M j, Y', strtotime($date));
}

$id = $_GET['id'] ?? '';
$slug = $_GET['slug'] ?? '';

$article = false;

if ($id) {
    try {
        $stmt = $pdo->prepare(
            'SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.id = ? AND a.status = "published"'
        );
        $stmt->execute([$id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $article = false;
    }
}

if (!$article && $slug) {
    try {
        $stmt = $pdo->prepare(
            'SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE (a.slug = ? OR a.id = ?) AND a.status = "published"'
        );
        $stmt->execute([$slug, $slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $article = false;
    }
}

// 404 Response if article not found in database
if (!$article) {
    http_response_code(404);
    $defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <base href="/">
        <meta charset="utf-8">
        <title>404 Page Not Found | Gunvani News</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root { --gn-green: #116530; }
            body { font-family: 'Inter', sans-serif; background:#f8faf9; }
            .gn-logo { height: 60px; }
                /* --- Responsive Nav Sizes --- */
        @media (min-width: 768px) {
            .gn-desktop-nav-container {
                justify-content: center !important;
                gap: 30px !important;
            }
            .gn-desktop-nav-container .gn-nav-link {
                font-size: 16px !important;
            }
        }
        @media (max-width: 767px) {
            .gn-nav-link { font-size: 14px !important; }
        }
            /* Mobile Headings & Category Sidebar */
        @media (max-width: 767px) {
            h1, h2, h3, .sec-title-bar h2, .sec-title-bar h3 {
                font-size: 19px !important;
                margin-bottom: 6px !important;
            }
            h4, h5, h6, .subheading, p.lead {
                font-size: 12px !important;
            }
            .list-group-item, .list-group-item a {
                font-size: 12px !important;
                padding: 6px 12px !important;
            }
            .list-group-item i {
                font-size: 12px !important;
            }
        }
            /* Mobile Newsletter Widget & Forms */
        @media (max-width: 767px) {
            .form-control, .btn {
                font-size: 11px !important;
                padding: 6px 10px !important;
                height: auto !important;
            }
            .fa-envelope.display-6 {
                font-size: 14px !important;
            }
            .p-4 h4.fs-5 {
                font-size: 14px !important;
            }
            .p-4 p.opacity-85 {
                font-size: 12px !important;
                line-height: 1.3 !important;
            }
            .p-4.rounded-3 {
                padding: 12px !important;
            }
        }
            /* Mobile Footer Typography */
        @media (max-width: 767px) {
            .gn-footer h5 {
                font-size: 14px !important;
                margin-bottom: 8px !important;
            }
            .gn-footer p, .gn-footer a, .gn-footer li, .gn-footer div, .gn-footer span {
                font-size: 12px !important;
                line-height: 1.4 !important;
            }
            .gn-footer {
                padding: 20px 0 10px 0 !important;
            }
            .gn-footer img {
                height: 36px !important;
            }
        }
            /* Mobile Circular Buttons Fix */
        @media (max-width: 767px) {
            .btn.rounded-circle {
                width: 32px !important;
                height: 32px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
        }
                        /* Mobile Hero Slider Fix */
        @media (max-width: 767px) {
            .hero-main-img-wrap, .hero-main-img-wrap img {
                height: auto !important;
                aspect-ratio: 16/9 !important;
            }
        }
            /* Mobile Article Page Video UI Fix */
        @media (max-width: 767px) {
            /* Fix overly tall video/images */
            .mb-4.rounded.overflow-hidden.shadow-sm video,
            .article-video-wrapper iframe,
            .article-featured-img {
                height: 200px !important;
                object-fit: cover !important;
                width: 100% !important;
                max-height: 200px !important;
            }
            
            /* Share Buttons Fix */
            .share-bar {
                flex-wrap: wrap !important;
                gap: 6px !important;
                padding: 12px !important;
            }
            .share-bar > span {
                width: 100% !important;
                display: block !important;
                margin-bottom: 4px !important;
                font-size: 13px !important;
            }
            .btn-share {
                flex: 1 1 calc(50% - 6px) !important;
                font-size: 10px !important;
                padding: 6px 10px !important;
                justify-content: center !important;
                white-space: nowrap !important;
            }
            
            /* Related News Cards to List View */
            .related-card {
                display: flex !important;
                flex-direction: row !important;
                gap: 10px !important;
                border: none !important;
                border-bottom: 1px dashed var(--gn-border) !important;
                padding-bottom: 10px !important;
                border-radius: 0 !important;
            }
            .related-card > a {
                flex-shrink: 0 !important;
            }
            .related-card img {
                width: 100px !important;
                height: 70px !important;
                border-radius: 4px !important;
            }
            .related-card-body {
                padding: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
            }
            .related-card-body h4, .related-card-body h4 a {
                font-size: 13px !important;
                line-height: 1.3 !important;
                margin-bottom: 4px !important;
                margin-top: 0 !important;
            }
            .related-card-body .small {
                font-size: 10px !important;
            }
        }
            @media (max-width: 767px) {
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100vw !important;
            }
            /* Fix for Bootstrap rows causing horizontal scroll on mobile */
            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .container, .container-fluid, .gn-container {
                padding-left: 12px !important;
                padding-right: 12px !important;
                overflow-x: hidden !important;
            }
            .news-card, .trend-card-item, .city-box-card {
                max-width: 100% !important;
            }
        }
    </style>
        <script>
        var savedLang = localStorage.getItem('gunvani_lang') || 'hi';
        var cookieVal = (savedLang === 'hi') ? '/en/hi' : '/en/en';
        
        document.cookie = "googtrans=" + cookieVal + "; path=/;";
        if (location.hostname && location.hostname !== 'localhost') {
            document.cookie = "googtrans=" + cookieVal + "; domain=." + location.hostname.replace(/^www\./, '') + "; path=/;";
            document.cookie = "googtrans=" + cookieVal + "; domain=" + location.hostname.replace(/^www\./, '') + "; path=/;";
        }
    </script>
</head>
    <body>
        <div class="container text-center py-5 my-5">
            <a href="/"><img src="images/placeholder/logos.png" alt="Gunvani News" class="gn-logo mb-4" onerror="this.src='icon.png'"></a>
            <div class="display-1 fw-bold text-danger">404</div>
            <h2 class="fw-bold text-dark mb-3">Article Not Found</h2>
            <p class="text-muted max-w-500 mx-auto mb-4" style="max-width:500px;">The news article or story you are looking for does not exist, has been removed, or the link is invalid.</p>
            <a href="/" class="btn btn-success font-weight-bold px-4 py-2" style="background:var(--gn-green); border-color:var(--gn-green);">
                <i class="fa-solid fa-house me-2"></i>Return to Homepage
            </a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Increment view count
try {
    $pdo->prepare("UPDATE articles SET views_count = views_count + 1 WHERE id = ?")->execute([$article['id']]);
} catch (Exception $e) {}

// Fetch multi-image gallery photos from article_media
$galleryMedia = [];
try {
    $gStmt = $pdo->prepare("SELECT m.* FROM media m JOIN article_media am ON m.id = am.media_id WHERE am.article_id = ?");
    $gStmt->execute([$article['id']]);
    $galleryMedia = $gStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $galleryMedia = [];
}

// Handle Comment Submission
$commentMsg = '';
$commentErr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $cName = trim($_POST['name'] ?? '');
    $cEmail = trim($_POST['email'] ?? '');
    $cComment = trim($_POST['comment'] ?? '');

    if (empty($cName) || empty($cEmail) || empty($cComment)) {
        $commentErr = 'Please fill out all required fields (Name, Email, Comment).';
    } else {
        try {
            $pdo->prepare("INSERT INTO comments (article_id, name, email, comment, status) VALUES (?, ?, ?, ?, 'pending')")
                ->execute([$article['id'], $cName, $cEmail, $cComment]);
            $commentMsg = 'Your comment has been submitted and is awaiting admin approval!';
        } catch (Exception $e) {
            $commentErr = 'Error submitting comment. Please try again.';
        }
    }
}

// Fetch Approved Comments
$approvedComments = [];
try {
    $cFetch = $pdo->prepare("SELECT * FROM comments WHERE article_id = ? AND status = 'approved' ORDER BY created_at DESC");
    $cFetch->execute([$article['id']]);
    $approvedComments = $cFetch->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $approvedComments = [];
}

// Fetch 3 Related Articles
$relatedArticles = [];
try {
    $rStmt = $pdo->prepare(
        "SELECT a.*, c.name AS category_name, c.slug AS category_slug
         FROM articles a
         LEFT JOIN categories c ON a.category_id = c.id
         WHERE a.status = 'published' AND a.id != ? AND (a.category_id = ? OR 1=1)
         ORDER BY a.published_at DESC
         LIMIT 3"
    );
    $rStmt->execute([$article['id'], $article['category_id']]);
    $relatedArticles = $rStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $relatedArticles = [];
}

// Fetch Sidebar Latest Headlines
$sidebarHeadlines = [];
try {
    $sidebarHeadlines = $pdo->query(
        "SELECT a.*, c.name AS category_name, c.slug AS category_slug
         FROM articles a
         LEFT JOIN categories c ON a.category_id = c.id
         WHERE a.status = 'published'
         ORDER BY a.published_at DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $sidebarHeadlines = [];
}

$defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/">
    <meta charset="utf-8">
    <title><?= escape($article['title']) ?> | Gunvani News</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" href="icon.png">
    <meta name="theme-color" content="#116530">

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --gn-green: #116530;
            --gn-green-dark: #0b4520;
            --gn-green-light: #eaf4ed;
            --gn-red: #dc2626;
            --gn-bg: #ffffff;
            --gn-border: #e2e8f0;
            --gn-text-dark: #1e293b;
            --gn-text-muted: #64748b;
            --container-max-w: 1360px;
        }

        html, body {
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: clip !important;
            position: relative;
            margin: 0;
            padding: 0;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            max-width: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gn-bg);
            color: var(--gn-text-dark);
        }

        img, video, iframe {
            max-width: 100% !important;
            height: auto;
        }

        .gn-container {
            width: 100%;
            max-width: var(--container-max-w);
            margin: 0 auto;
            padding: 0 16px;
        }
        @media (max-width: 575px) {
            .gn-container {
                padding: 0 12px;
            }
        }

        /* Top Utility Bar */
        .gn-utility-bar {
            background-color: var(--gn-green);
            color: #ffffff;
            font-size: 0.82rem;
            padding: 6px 0;
            width: 100%;
            overflow: hidden;
        }
        .gn-utility-bar a {
            color: #ffffff;
            text-decoration: none;
        }
        @media (max-width: 575px) {
            .gn-utility-bar {
                font-size: 0.75rem;
                padding: 5px 0;
            }
        }

        .lang-pill-box {
            background: rgba(0,0,0,0.25);
            border-radius: 4px;
            padding: 2px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            margin-top: -2px;
            margin-bottom: -2px;
        }
        .lang-pill-btn {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 3px;
            border: none;
            color: #ffffff;
            background: transparent;
            cursor: pointer;
        }
        .lang-pill-btn.active {
            background-color: var(--gn-green-dark);
            color: #ffffff;
        }

        .gn-main-header {
            background: #ffffff;
            border-bottom: 1px solid var(--gn-border);
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .gn-logo {
            height: 80px;
            max-height: 80px;
            width: auto;
            object-fit: contain;
            transition: height 0.3s ease;
        }
        @media (max-width: 991px) {
            .gn-logo {
                height: 60px;
                max-height: 60px;
            }
        }
        @media (max-width: 575px) {
            .gn-logo {
                height: 46px;
                max-height: 46px;
            }
        }

        .gn-nav-link {
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            padding: 8px 12px;
            text-decoration: none;
            white-space: nowrap;
        }
        .gn-nav-link:hover, .gn-nav-link.active {
            color: var(--gn-green);
            font-weight: 700;
        }

        /* Breadcrumb */
        .article-breadcrumb {
            font-size: 0.82rem;
            color: var(--gn-text-muted);
            padding: 12px 0;
            border-bottom: 1px solid var(--gn-border);
            margin-bottom: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .article-breadcrumb a {
            color: var(--gn-text-muted);
            text-decoration: none;
        }
        .article-breadcrumb a:hover {
            color: var(--gn-green);
        }

        .cat-badge-red {
            background-color: var(--gn-red);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 3px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 12px;
        }
        .article-title-main {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.28;
            color: var(--gn-text-dark);
            margin-bottom: 16px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        @media (max-width: 767px) {
            .article-title-main {
                font-size: 1.45rem !important;
                line-height: 1.3 !important;
            }
        }
        .article-meta-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 0;
            border-top: 1px solid var(--gn-border);
            border-bottom: 1px solid var(--gn-border);
            margin-bottom: 24px;
            font-size: 0.84rem;
            color: var(--gn-text-muted);
        }
        .article-featured-img {
            width: 100%;
            max-height: 480px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 24px;
        }
        .article-body-text {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #334155;
            margin-bottom: 30px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .article-body-text p {
            margin-bottom: 1.4rem;
        }

        .share-bar {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 12px 14px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid var(--gn-border);
            margin-bottom: 36px;
        }
        .btn-share {
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex: 1 1 auto;
            justify-content: center;
            min-width: 105px;
        }

        .sec-title-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid var(--gn-green);
            padding-left: 10px;
            margin-bottom: 16px;
        }
        .sec-title-bar h3 {
            font-size: 1.15rem;
            font-weight: 800;
            margin: 0;
        }

        .related-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid var(--gn-border);
            overflow: hidden;
            height: 100%;
        }
        .related-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .related-card-body {
            padding: 14px;
        }
        .related-card-body h4 {
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.35;
            margin: 6px 0;
        }
        .related-card-body h4 a {
            color: var(--gn-text-dark);
            text-decoration: none;
        }

        .gn-footer {
            background-color: #0b1e13;
            color: #e2e8f0 !important;
            padding: 40px 0 20px 0;
            margin-top: 50px;
            border-top: 3px solid var(--gn-green);
        }
        .gn-footer h5 {
            color: #ffffff !important;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .gn-footer p, .gn-footer span, .gn-footer div, .gn-footer li, .gn-footer .text-muted {
            color: #cbd5e1 !important;
        }
        .gn-footer a {
            color: #e2e8f0 !important;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .gn-footer a:hover {
            color: #4ade80 !important;
        }

        .goog-te-banner-frame.skiptranslate, .goog-te-banner-frame, .goog-gt-tt, .goog-te-balloon-frame, #goog-gt-tt { display: none !important; }
        body { top: 0px !important; }
        .goog-te-combo { display: none !important; }
        .skiptranslate { font-size: 0 !important; }
        .skiptranslate * { font-size: initial; }
            /* --- Responsive Nav Sizes --- */
        @media (min-width: 768px) {
            .gn-desktop-nav-container {
                justify-content: center !important;
                gap: 30px !important;
            }
            .gn-desktop-nav-container .gn-nav-link {
                font-size: 16px !important;
            }
        }
        @media (max-width: 767px) {
            .gn-nav-link { font-size: 14px !important; }
        }
            /* Mobile Headings & Category Sidebar */
        @media (max-width: 767px) {
            h1, h2, h3, .sec-title-bar h2, .sec-title-bar h3 {
                font-size: 19px !important;
                margin-bottom: 6px !important;
            }
            h4, h5, h6, .subheading, p.lead {
                font-size: 12px !important;
            }
            .list-group-item, .list-group-item a {
                font-size: 12px !important;
                padding: 6px 12px !important;
            }
            .list-group-item i {
                font-size: 12px !important;
            }
        }
            /* Mobile Newsletter Widget & Forms */
        @media (max-width: 767px) {
            .form-control, .btn {
                font-size: 11px !important;
                padding: 6px 10px !important;
                height: auto !important;
            }
            .fa-envelope.display-6 {
                font-size: 14px !important;
            }
            .p-4 h4.fs-5 {
                font-size: 14px !important;
            }
            .p-4 p.opacity-85 {
                font-size: 12px !important;
                line-height: 1.3 !important;
            }
            .p-4.rounded-3 {
                padding: 12px !important;
            }
        }
            /* Mobile Footer Typography */
        @media (max-width: 767px) {
            .gn-footer h5 {
                font-size: 14px !important;
                margin-bottom: 8px !important;
            }
            .gn-footer p, .gn-footer a, .gn-footer li, .gn-footer div, .gn-footer span {
                font-size: 12px !important;
                line-height: 1.4 !important;
            }
            .gn-footer {
                padding: 20px 0 10px 0 !important;
            }
            .gn-footer img {
                height: 36px !important;
            }
        }
            /* Mobile Circular Buttons Fix */
        @media (max-width: 767px) {
            .btn.rounded-circle {
                width: 32px !important;
                height: 32px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
        }
                        /* Mobile Hero Slider Fix */
        @media (max-width: 767px) {
            .hero-main-img-wrap, .hero-main-img-wrap img {
                height: auto !important;
                aspect-ratio: 16/9 !important;
            }
        }
            /* Mobile Article Page Video UI Fix */
        @media (max-width: 767px) {
            /* Fix overly tall video/images */
            .mb-4.rounded.overflow-hidden.shadow-sm video,
            .article-video-wrapper iframe,
            .article-featured-img {
                height: 200px !important;
                object-fit: cover !important;
                width: 100% !important;
                max-height: 200px !important;
            }
            
            /* Share Buttons Fix */
            .share-bar {
                flex-wrap: wrap !important;
                gap: 6px !important;
                padding: 12px !important;
            }
            .share-bar > span {
                width: 100% !important;
                display: block !important;
                margin-bottom: 4px !important;
                font-size: 13px !important;
            }
            .btn-share {
                flex: 1 1 calc(50% - 6px) !important;
                font-size: 10px !important;
                padding: 6px 10px !important;
                justify-content: center !important;
                white-space: nowrap !important;
            }
            
            /* Related News Cards to List View */
            .related-card {
                display: flex !important;
                flex-direction: row !important;
                gap: 10px !important;
                border: none !important;
                border-bottom: 1px dashed var(--gn-border) !important;
                padding-bottom: 10px !important;
                border-radius: 0 !important;
            }
            .related-card > a {
                flex-shrink: 0 !important;
            }
            .related-card img {
                width: 100px !important;
                height: 70px !important;
                border-radius: 4px !important;
            }
            .related-card-body {
                padding: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
            }
            .related-card-body h4, .related-card-body h4 a {
                font-size: 13px !important;
                line-height: 1.3 !important;
                margin-bottom: 4px !important;
                margin-top: 0 !important;
            }
            .related-card-body .small {
                font-size: 10px !important;
            }
        }
            @media (max-width: 767px) {
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100vw !important;
            }
            /* Fix for Bootstrap rows causing horizontal scroll on mobile */
            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .container, .container-fluid, .gn-container {
                padding-left: 12px !important;
                padding-right: 12px !important;
                overflow-x: hidden !important;
            }
            .news-card, .trend-card-item, .city-box-card {
                max-width: 100% !important;
            }
        }
    </style>
    <script>
        var savedLang = localStorage.getItem('gunvani_lang') || 'hi';
        var cookieVal = (savedLang === 'hi') ? '/en/hi' : '/en/en';
        
        document.cookie = "googtrans=" + cookieVal + "; path=/;";
        if (location.hostname && location.hostname !== 'localhost') {
            document.cookie = "googtrans=" + cookieVal + "; domain=." + location.hostname.replace(/^www\./, '') + "; path=/;";
            document.cookie = "googtrans=" + cookieVal + "; domain=" + location.hostname.replace(/^www\./, '') + "; path=/;";
        }
    </script>
</head>
<body>

    <!-- 1. Top Utility Bar -->
    <div class="gn-utility-bar">
        <div class="gn-container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-regular fa-calendar-days"></i>
                    <span id="top-date-display" class="fw-semibold">Monday, September 14, 2026</span>
                </div>

                <div class="d-flex align-items-center flex-wrap gap-2 gap-sm-3 ms-auto ms-sm-0">
                    <a href="/verification"><i class="fa-regular fa-circle-check me-1"></i>Verification</a>
                    <span class="opacity-25 d-none d-sm-inline">|</span>
                    <a href="admin/login.php"><i class="fa-solid fa-lock me-1"></i>Admin</a>
                    <span class="opacity-25 d-none d-sm-inline">|</span>
                    
                    <div class="lang-pill-box">
                        <button type="button" class="lang-pill-btn active" id="btn-lang-hi" onclick="changeLanguage('hi')">हिंदी</button>
                        <button type="button" class="lang-pill-btn" id="btn-lang-en" onclick="changeLanguage('en')">English</button>
                    </div>
                    <div id="google_translate_element" style="display:none;"></div>

                    <span class="opacity-25 d-none d-sm-inline">|</span>
                    <div class="d-none d-sm-flex gap-2">
                        <a href="#" class="text-white" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="text-white" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="text-white" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-white" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Navigation Header -->
                <header class="gn-main-header">
        <div class="gn-container">
            <nav class="navbar navbar-light py-2 d-flex flex-row flex-nowrap align-items-center" style="gap: 12px;">
                <!-- Logo -->
                <a class="navbar-brand m-0 p-0 flex-shrink-0" href="/">
                    <img src="images/placeholder/logos.png" alt="Gunvani News Logo" class="gn-logo" onerror="this.src='icon.png'">
                </a>

                <!-- Nav Links (Wraps next to logo) -->
                                <?php $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
                <div class="d-flex flex-wrap align-items-center flex-grow-1 gn-desktop-nav-container" style="gap: 6px 10px; font-weight: 600;">
                    <a href="/" class="gn-nav-link <?= ($currentUri === '/' || $currentUri === '/index.php' || $currentUri === '') ? 'active' : '' ?> text-nowrap p-0" >Home</a>
                    <?php foreach ($defaultNavCategories as $catName): ?>
                        <?php 
                        $catSlug = strtolower(str_replace(' ', '-', $catName));
                        $isActiveCat = (strpos($currentUri, '/category/' . $catSlug) !== false) ? 'active' : '';
                        ?>
                        <a href="/category/<?= $catSlug ?>" class="gn-nav-link <?= $isActiveCat ?> text-nowrap p-0" >
                            <?= $catName ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="/contact" class="gn-nav-link <?= (strpos($currentUri, '/contact') !== false) ? 'active' : '' ?> text-nowrap p-0" >Contact Us</a>
                </div>
                            <!-- Search Bar (Laptop Only) -->
                <form action="/search" method="GET" class="d-none d-md-flex align-items-center m-0 p-0 position-relative flex-shrink-0">
                    <input type="text" name="q" class="form-control rounded-pill pe-4" placeholder="Search..." style="width: 180px; height: 32px; font-size: 0.85rem; border-color: #dee2e6;" required>
                    <button type="submit" class="btn btn-link text-secondary position-absolute end-0 top-0 bottom-0 text-decoration-none d-flex align-items-center justify-content-center" style="padding: 0 12px; height: 32px;" title="Search">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 0.85rem;"></i>
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="gn-container mb-5">
        <!-- Breadcrumb -->
        <div class="article-breadcrumb">
            <a href="/">Home</a>
            <span class="mx-2"><i class="fa-solid fa-angle-right small opacity-50"></i></span>
            <a href="/category/<?= escape($article['category_slug'] ?: 'news') ?>"><?= escape($article['category_name'] ?: 'News') ?></a>
            <span class="mx-2"><i class="fa-solid fa-angle-right small opacity-50"></i></span>
            <span class="text-dark fw-semibold"><?= escape(mb_strimwidth($article['title'], 0, 50, '...')) ?></span>
        </div>

        <div class="row g-4">
            <!-- Left Column: Article Detail -->
            <div class="col-lg-8">
                <article>
                    <span class="cat-badge-red"><?= escape($article['category_name'] ?: 'NEWS') ?></span>
                    <h1 class="article-title-main"><?= escape($article['title']) ?></h1>

                    <div class="article-meta-bar">
                        <div class="d-flex align-items-center gap-3">
                            <span><i class="fa-regular fa-user me-1 text-success"></i>By <strong><?= escape($article['author'] ?: 'Gunvani News Bureau') ?></strong></span>
                            <span><i class="fa-regular fa-clock me-1 text-success"></i><?= formatDate($article['published_at']) ?></span>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark border"><i class="fa-regular fa-eye me-1"></i><?= number_format($article['views_count'] ?? 0) ?> Views</span>
                        </div>
                    </div>

                    <?php if (!empty($article['video_url'])): ?>
                        <div class="ratio ratio-16x9 mb-4 rounded overflow-hidden shadow-sm">
                            <?php 
                            $vUrl = $article['video_url'];
                            if (strpos($vUrl, 'youtube.com/watch') !== false) {
                                parse_str(parse_url($vUrl, PHP_URL_QUERY), $vars);
                                $vUrl = 'https://www.youtube.com/embed/' . ($vars['v'] ?? '');
                            } elseif (strpos($vUrl, 'youtu.be/') !== false) {
                                $vUrl = 'https://www.youtube.com/embed/' . basename(parse_url($vUrl, PHP_URL_PATH));
                            }
                            ?>
                            <iframe src="<?= escape($vUrl) ?>" title="News Video" allowfullscreen></iframe>
                        </div>
                    <?php elseif (!empty($article['video_file'])): ?>
                        <div class="mb-4 rounded overflow-hidden shadow-sm">
                            <video controls class="w-100" style="max-height:450px;">
                                <source src="uploads/videos/<?= escape($article['video_file']) ?>" type="video/mp4">
                                Your browser does not support HTML video.
                            </video>
                        </div>
                    <?php elseif (!empty($article['image'])): ?>
                        <img src="<?= escape(articleImage($article['image'])) ?>" alt="<?= escape($article['title']) ?>" class="article-featured-img" onerror="this.src='images/placeholder/second6.webp'">
                    <?php endif; ?>

                    <div class="article-body-text">
                        <?php if (!empty($article['content'])): ?>
                            <?= nl2br($article['content']) ?>
                        <?php else: ?>
                            <p class="lead font-weight-semibold"><?= escape($article['summary']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Article Multi-Image Gallery -->
                    <?php if (!empty($galleryMedia)): ?>
                        <div class="mt-4 mb-4">
                            <h4 class="fw-bold fs-5 mb-3 border-bottom pb-2"><i class="fa-solid fa-images me-2 text-success"></i>Story Photo Gallery</h4>
                            <div class="row g-2">
                                <?php foreach ($galleryMedia as $gm): ?>
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <a href="uploads/news/<?= escape($gm['filename'] ?? '') ?>" target="_blank" class="d-block border rounded overflow-hidden">
                                            <img src="uploads/news/<?= escape($gm['filename'] ?? '') ?>" alt="Gallery Photo" class="w-100" style="height:120px; object-fit:cover;">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Share Bar -->
                    <div class="share-bar">
                        <span class="fw-bold text-dark me-2 small">Share Story:</span>
                        <?php $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/article/' . $article['slug']; ?>
                        <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" class="btn-share" style="background:#1877f2;"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title']) ?>&url=<?= urlencode($currentUrl) ?>" target="_blank" class="btn-share" style="background:#000;"><i class="fa-brands fa-twitter"></i> X / Twitter</a>
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title'].' - '.$currentUrl) ?>" target="_blank" class="btn-share" style="background:#25d366;"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                    </div>
                </article>

                <!-- Comments Moderation & Display Section -->
                <section class="mt-4 mb-5 p-4 bg-white border rounded-3 shadow-sm">
                    <h3 class="fw-bold fs-5 mb-4 text-dark border-bottom pb-2"><i class="fa-solid fa-comments text-success me-2"></i>Reader Comments</h3>

                    <?php if ($commentMsg): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i><?= escape($commentMsg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($commentErr): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i><?= escape($commentErr) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($approvedComments)): ?>
                        <div class="mb-4">
                            <?php foreach ($approvedComments as $ac): ?>
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark"><i class="fa-solid fa-user-circle me-1 text-success"></i><?= escape($ac['name']) ?></strong>
                                        <span class="small text-muted"><?= date('M j, Y h:i A', strtotime($ac['created_at'])) ?></span>
                                    </div>
                                    <p class="mb-0 text-secondary small"><?= escape($ac['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-4">No comments posted yet. Be the first to share your thoughts!</p>
                    <?php endif; ?>

                    <!-- Leave a Comment Form -->
                    <form method="POST" action="article/<?= escape($article['slug']) ?>">
                        <h5 class="fw-bold text-dark mb-3 fs-6">Leave a Comment</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name *" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your Email *" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Write your comment..." required></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="btn btn-success fw-bold px-4" style="background:var(--gn-green); border-color:var(--gn-green);">
                            <i class="fa-solid fa-paper-plane me-1"></i> Submit Comment
                        </button>
                    </form>
                </section>

                <!-- Related Articles Section -->
                <?php if (!empty($relatedArticles)): ?>
                <section class="mt-4">
                    <div class="sec-title-bar">
                        <h3>Related Stories</h3>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($relatedArticles as $rel): ?>
                            <div class="col-md-4">
                                <article class="related-card">
                                    <a href="/article/<?= escape($rel['slug']) ?>">
                                        <img src="<?= escape(articleImage($rel['image'])) ?>" alt="<?= escape($rel['title']) ?>" onerror="this.src='images/placeholder/first8.jpg'">
                                    </a>
                                    <div class="related-card-body">
                                        <h4><a href="/article/<?= escape($rel['slug']) ?>"><?= escape($rel['title']) ?></a></h4>
                                        <div class="small text-muted" style="font-size:0.72rem;"><i class="fa-regular fa-clock me-1"></i><?= formatDate($rel['published_at']) ?></div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <!-- Right Column Sidebar -->
            <div class="col-lg-4">
                <div class="sec-title-bar">
                    <h3>Latest Headlines</h3>
                </div>

                <div class="bg-white border rounded-3 p-3 mb-4">
                    <?php foreach ($sidebarHeadlines as $sbh): ?>
                        <div class="d-flex gap-2 py-2 border-bottom align-items-center">
                            <a href="/article/<?= escape($sbh['slug']) ?>" class="flex-shrink-0">
                                <img src="<?= escape(articleImage($sbh['image'])) ?>" alt="<?= escape($sbh['title']) ?>" class="rounded" style="width:65px; height:48px; object-fit:cover;" onerror="this.src='images/placeholder/first8.jpg'">
                            </a>
                            <div class="overflow-hidden">
                                <h5 class="small fw-bold mb-1" style="font-size:0.8rem; line-height:1.25;">
                                    <a href="/article/<?= escape($sbh['slug']) ?>" class="text-dark text-decoration-none">
                                        <?= escape($sbh['title']) ?>
                                    </a>
                                </h5>
                                <div class="small text-muted" style="font-size:0.7rem;"><i class="fa-regular fa-clock me-1"></i><?= formatDate($sbh['published_at']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Ad Placeholder Sidebar -->
                <div class="bg-light border rounded-3 p-4 text-center text-muted small fw-semibold">
                    <span>Advertisement Banner<br>300 × 250</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="gn-footer">
        <div class="gn-container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a href="/"><img src="images/placeholder/logos.png" alt="Gunvani News" style="height:46px; margin-bottom:14px;" onerror="this.src='icon.png'"></a>
                    <p class="small text-muted pe-lg-4">Gunvani News ek vishwasniya aur nishpaksh samachar manch hai jo desh-duniya ki mahatvapurn khabrein tezi aur satyata ke saath pahunchata hai.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/">Home</a></li>
                        <li class="mb-2"><a href="/categories">Categories</a></li>
                        <li class="mb-2"><a href="/verification">Verification</a></li>
                        <li class="mb-2"><a href="/contact">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h5>News Categories</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/category/agra">Agra</a></li>
                        <li class="mb-2"><a href="/category/lucknow">Lucknow</a></li>
                        <li class="mb-2"><a href="/category/mathura">Mathura</a></li>
                        <li class="mb-2"><a href="/category/noida">Noida</a></li>
                        <li class="mb-2"><a href="/category/uttar-pradesh">Uttar Pradesh</a></li>
                        <li class="mb-2"><a href="/category/india">India</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Follow Us</h5>
                    <p class="small text-muted mb-3">Janta ki awaaz, Gunvani News ke saath</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary opacity-25 my-4"></div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                <div>© <?= date('Y') ?> Gunvani News — All Rights Reserved.</div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Use</a>
                    <a href="#" class="text-muted text-decoration-none">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,hi',
            autoDisplay: false
        }, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <script>
    function changeLanguage(lang) {
        localStorage.setItem('gunvani_lang', lang);
        updateLangBtnUI(lang);
        setCookieLanguage(lang);
        
        var select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = lang;
            select.dispatchEvent(new Event('change'));
        }
        
        setTimeout(function() {
            location.reload();
        }, 150);
    }

    function setCookieLanguage(lang) {
        var cookieVal = (lang === 'hi') ? '/en/hi' : '/en/en';
        
        document.cookie = "googtrans=" + cookieVal + "; path=/;";
        if (location.hostname && location.hostname !== 'localhost') {
            var domain = location.hostname.replace(/^www\./, '');
            document.cookie = "googtrans=" + cookieVal + "; domain=." + domain + "; path=/;";
            document.cookie = "googtrans=" + cookieVal + "; domain=" + domain + "; path=/;";
        }
    }

    function updateLangBtnUI(lang) {
        var btnHi = document.getElementById('btn-lang-hi');
        var btnEn = document.getElementById('btn-lang-en');
        if (!btnHi || !btnEn) return;
        if (lang === 'hi') {
            btnHi.classList.add('active');
            btnEn.classList.remove('active');
        } else {
            btnEn.classList.add('active');
            btnHi.classList.remove('active');
        }
    }

    function hideGoogleTranslateFrames() {
        document.documentElement.style.top = '0px';
        document.body.style.top = '0px';
        document.body.style.marginTop = '0px';
        
        var bannerFrames = document.querySelectorAll('iframe.goog-te-banner-frame, .goog-te-banner-frame, #goog-gt-tt, .goog-te-balloon-frame');
        bannerFrames.forEach(function(f) {
            f.style.setProperty('display', 'none', 'important');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('gunvani_visited')) {
            localStorage.setItem('gunvani_visited', '1');
            localStorage.setItem('gunvani_lang', 'hi');
        } else if (!localStorage.getItem('gunvani_lang')) {
            localStorage.setItem('gunvani_lang', 'hi');
        }

        var savedLang = localStorage.getItem('gunvani_lang');
        
        updateLangBtnUI(savedLang);
        setCookieLanguage(savedLang);
        
        setTimeout(function() {
            var select = document.querySelector('.goog-te-combo');
            if (select && select.value !== savedLang) {
                select.value = savedLang;
                select.dispatchEvent(new Event('change'));
            }
            hideGoogleTranslateFrames();
        }, 400);
        
        setInterval(hideGoogleTranslateFrames, 300);
    });
    </script>
</body>
</html>
