<?php
if (!isset($_COOKIE['googtrans'])) {
    setcookie('googtrans', '/en/hi', time() + (86400 * 30), '/');
    $_COOKIE['googtrans'] = '/en/hi';
}

require_once 'includes/auth.php';
require_once 'db.php';
$defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];

// Fetch dynamic authors
$authorList = ['Gunvani News Bureau'];
$dbAdmins = $pdo->query("SELECT fullname FROM admins")->fetchAll(PDO::FETCH_COLUMN);
$dbMembers = $pdo->query("SELECT name FROM members WHERE status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
$authorList = array_unique(array_merge($authorList, $dbAdmins, $dbMembers));

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    header("Location: /");
    exit;
}

$pageTitle = "Search Results for '" . escape($q) . "' | Gunvani News";
$pageDesc = "Search results for " . escape($q) . " on Gunvani News.";
$catName = "Search: " . $q;

$articles = [];
try {
    $stmt = $pdo->prepare("SELECT a.*, c.name AS category_name FROM articles a LEFT JOIN menus c ON a.category_id = c.id WHERE a.status = 'published' AND (a.title LIKE ? OR a.summary LIKE ? OR a.content LIKE ? OR c.name LIKE ?) ORDER BY a.id DESC LIMIT 50");
    $stmt->execute(["%$q%", "%$q%", "%$q%", "%$q%"]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

$catDesc = "Found " . count($articles) . " results for your query.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Gunvani Official Favicon & Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

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

    
    </div>

    <!-- 2. Navigation Header -->
                <!-- Dynamic Header Menu -->
    <?php include 'nav.php'; ?>

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
        <div class="row g-4">
            <!-- Left 8-col News Grid -->
            <div class="col-lg-8">
                <div class="sec-title-bar">
                    <h3>Search Results for: "<?= escape($query) ?>" (<?= count($articles) ?>)</h3>
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
        <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
