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

$slug = strtolower($_GET['slug'] ?? 'lucknow');
$cleanSlug = str_replace('-', ' ', $slug);

$category = false;
try {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE LOWER(slug) = ? OR LOWER(name) = ?');
    $stmt->execute([$slug, $cleanSlug]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $category = false;
}

$city = false;
try {
    $stmt = $pdo->prepare('SELECT * FROM cities WHERE LOWER(slug) = ? OR LOWER(name) = ?');
    $stmt->execute([$slug, $cleanSlug]);
    $city = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $city = false;
}

$catName = $category['name'] ?? ($city['name'] ?? ucwords($cleanSlug));
$catDesc = $category['description'] ?? 'Latest news, breaking headlines, and regional coverage from ' . $catName . '.';

$articles = [];
try {
    $query = "SELECT a.*, c.name AS category_name, c.slug AS category_slug, ct.name AS city_name
              FROM articles a
              LEFT JOIN categories c ON a.category_id = c.id
              LEFT JOIN cities ct ON a.city_id = ct.id
              WHERE a.status = 'published'";
              
    $params = [];

    if ($slug === 'india') {
        // Show all news
    } elseif ($slug === 'uttar-pradesh') {
        // Show news from UP cities (Agra, Lucknow, Mathura, Noida)
        $query .= " AND (LOWER(ct.slug) IN ('agra', 'lucknow', 'mathura', 'noida') OR LOWER(c.slug) IN ('agra', 'lucknow', 'mathura', 'noida'))";
    } else {
        $query .= " AND (LOWER(c.slug) = :slug OR LOWER(c.name) = :clean OR LOWER(ct.slug) = :slug OR LOWER(ct.name) = :clean";
        if ($category) {
            $query .= " OR a.category_id = " . (int)$category['id'];
        }
        if ($city) {
            $query .= " OR a.city_id = " . (int)$city['id'];
        }
        $query .= ")";
        $params[':slug'] = $slug;
        $params[':clean'] = $cleanSlug;
    }
    
    $query .= " ORDER BY a.id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $articles = [];
}
$defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/">
    <meta charset="utf-8">
    <title><?= escape($catName) ?> News | Gunvani News</title>
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
            --gn-bg: #f8faf9;
            --gn-card-bg: #ffffff;
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

        /* Navigation Header */
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

        /* Hide Google Translate Top Banner Frame & prevent body push down */
        iframe.goog-te-banner-frame,
        .goog-te-banner-frame,
        #goog-gt-tt,
        .goog-gt-tt,
        .goog-te-balloon-frame {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
        }

        html, body {
            top: 0px !important;
            position: static !important;
            margin-top: 0px !important;
            padding-top: 0px !important;
        }

        .goog-te-combo {
            display: none !important;
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
        .gn-nav-link.active {
            border-bottom: 2px solid var(--gn-green);
        }

        /* Category Hero Banner */
        .category-hero {
            background: linear-gradient(135deg, var(--gn-green-dark) 0%, var(--gn-green) 100%);
            color: #ffffff;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        @media (max-width: 575px) {
            .category-hero {
                padding: 24px 0;
                margin-bottom: 20px;
            }
            .category-hero h1 {
                font-size: 1.5rem !important;
            }
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
            margin-bottom: 10px;
        }

        /* News Cards */
        .news-card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        @media (max-width: 991px) {
            .news-card-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 575px) { .news-card-grid { grid-template-columns: repeat(2, 1fr); } }
        .news-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid var(--gn-border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .news-card-img {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }
        .news-card-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .news-card-body h4 {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.35;
            margin: 8px 0 10px 0;
        }
        .news-card-body h4 a {
            color: var(--gn-text-dark);
            text-decoration: none;
        }
        .news-card-body h4 a:hover {
            color: var(--gn-green);
        }

        /* Section Title Bar */
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

        /* Footer */
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

        /* Google Translate frame hide */
        .goog-te-banner-frame.skiptranslate, .goog-te-banner-frame, .goog-gt-tt, .goog-te-balloon-frame, #goog-gt-tt { display: none !important; }
        body { top: 0px !important; }
        .goog-te-combo { display: none !important; }
        .skiptranslate { font-size: 0 !important; }
        .skiptranslate * { font-size: initial; }
            /* --- User Customizations --- */
        
        
        @media (max-width: 767px) {
            .gn-nav-link { font-size: 14px !important; }
        }
        .card-title {
            font-size: 0.9rem !important;
            line-height: 1.3 !important;
        }
        .card-text {
            font-size: 0.78rem !important;
        }
        .gn-container {
            padding: 0 15px !important;
        }
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
            /* Mobile Global Card Adjustments (10px Headings, 8px Text, 4px Padding) */
        @media (max-width: 767px) {
            .news-card-body, .trend-card-body, .city-box-card {
                padding: 4px !important;
            }
            .news-card-body h4, .news-card-body h4 a,
            .trend-card-body h4, .trend-card-body h4 a,
            .city-box-title, .city-box-title a,
            .supporting-title, .supporting-title a,
            .most-read-title, .most-read-title a {
                font-size: 13px !important;
                line-height: 1.3 !important;
                margin-bottom: 4px !important;
                margin-top: 2px !important;
            }
            .news-card-body p, .news-card-body div, .news-card-body span, .news-card-body a,
            .trend-card-body p, .trend-card-body div, .trend-card-body span, .trend-card-body a,
            .city-bullets li a, .city-box-card .small, .city-box-card span, .city-box-card a,
            .supporting-tag, .hero-meta-row, .most-read-row .text-muted, .most-read-num {
                font-size: 10px !important;
            }
            .news-card-body i, .trend-card-body i, .city-box-card i, .most-read-row i {
                font-size: 8px !important;
            }
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
                        /* Mobile Card Image Heights */
        @media (max-width: 767px) {
            .news-card-img, .trend-card-img, .city-box-img {
                height: 80px !important;
            }
            .video-card-wrap, .video-card-wrap video, .video-card-wrap img {
                height: 75px !important;
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

    <!-- 3. Category Banner -->
    <section class="category-hero">
        <div class="gn-container">
            <span class="cat-badge-red"><?= escape(strtoupper($catName)) ?></span>
            <h1 class="display-6 fw-bold mb-2"><?= escape($catName) ?> News & Updates</h1>
            <p class="m-0 opacity-85"><?= escape($catDesc ?? 'Coverage and breaking news for '.$catName) ?></p>
        </div>
    </section>

    <!-- 4. Category News Grid & Sidebar -->
    <main class="gn-container mb-5">
        <div class="row" style="--bs-gutter-x: 10px; --bs-gutter-y: 10px;">
            <!-- Left 8-col News Grid -->
            <div class="col-lg-8">
                <div class="sec-title-bar">
                    <h3>Latest Stories in <?= escape($catName) ?></h3>
                </div>

                <div class="news-card-grid">
                    <?php foreach ($articles as $art): ?>
                        <article class="news-card">
                            <a href="/article/<?= escape($art['slug']) ?>">
                                <img src="<?= escape(articleImage($art['image'])) ?>" alt="<?= escape($art['title']) ?>" class="news-card-img" onerror="this.src='images/placeholder/first8.jpg'">
                            </a>
                            <div class="news-card-body">
                                <h4>
                                    <a href="/article/<?= escape($art['slug']) ?>">
                                        <?= escape($art['title']) ?>
                                    </a>
                                </h4>
                                <p class="small text-muted mb-3" style="font-size:0.8rem; line-height:1.35;"><?= escape(mb_strimwidth($art['summary'] ?? '', 0, 90, '...')) ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 small text-muted" style="font-size:0.74rem;">
                                    <span><i class="fa-regular fa-clock me-1"></i><?= formatDate($art['published_at']) ?></span>
                                    <a href="/article/<?= escape($art['slug']) ?>" class="fw-bold text-success text-decoration-none">Read →</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right 4-col Sidebar -->
            <div class="col-lg-4">
                <div class="sec-title-bar">
                    <h3>Explore Categories</h3>
                </div>

                <div class="bg-white border rounded-3 p-3 mb-4">
                    <div class="list-group list-group-flush">
                        <?php 
                        $sidebarCats = array_filter($defaultNavCategories, function($c) {
                            return !in_array($c, ['Uttar Pradesh', 'India']);
                        });
                        foreach ($sidebarCats as $cItem): 
                        ?>
                            <a href="/category/<?= strtolower(str_replace(' ', '-', $cItem)) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center fw-semibold py-2">
                                <?= $cItem ?>
                                <i class="fa-solid fa-angle-right small opacity-50"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Newsletter Widget -->
                <div class="p-4 rounded-3 text-white" style="background:var(--gn-green);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-regular fa-envelope display-6"></i>
                        <h4 class="m-0 fw-bold fs-5">Get Updates</h4>
                    </div>
                    <p class="small opacity-85 mb-3">Subscribe to receive breaking news alerts for <?= escape($catName) ?> directly in your inbox.</p>
                    <form method="POST" action="#" onsubmit="alert('Subscribed!'); return false;">
                        <input type="email" class="form-control form-control-sm mb-2 py-2" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-light text-success w-100 fw-bold py-2">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="gn-footer">
        <div class="gn-container">
            <div class="row" style="--bs-gutter-x: 10px; --bs-gutter-y: 10px;">
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
