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

// Fetch ad settings
$adSettings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('ad_home_728x90', 'ad_home_300x250')")->fetchAll(PDO::FETCH_KEY_PAIR);
$adHome728 = $adSettings['ad_home_728x90'] ?? '';
$adHome300 = $adSettings['ad_home_300x250'] ?? '';

// Fetch categories from database
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

// Default fallback categories matching screenshot if DB has few
$defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];

// 1. Fetch Featured Article for Hero (is_featured = 1 or latest published article)
try {
    $featuredArticles = $pdo->query("SELECT a.*, c.name AS category_name, c.slug AS category_slug FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' AND a.is_trending = 0 AND (a.video_url IS NULL OR a.video_url = '') AND (a.video_file IS NULL OR a.video_file = '') ORDER BY a.is_featured DESC, a.published_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $featuredArticles = []; }

if (empty($featuredArticles)) { $featuredArticles = [[ "title" => "Gunvani News CMS Activated", "summary" => "Welcome to Gunvani News. Publish news articles from admin panel to populate homepage.", "category_name" => "NEWS", "image" => "images/placeholder/second6.webp", "published_at" => date("Y-m-d H:i:s"), "slug" => "welcome-to-gunvani-news" ]]; }

// 2. Fetch Breaking News items
try {
    $breakingNews = $pdo->query(
        "SELECT title, slug FROM articles WHERE status = 'published' ORDER BY is_breaking DESC, published_at DESC LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $breakingNews = [];
}

// 3. Fetch 4 Supporting Articles for Hero Middle Column
try {
    $featIds = array_column($featuredArticles ?? [], 'id'); $excludeSql = !empty($featIds) ? "AND a.id NOT IN (" . implode(',', $featIds) . ")" : ""; $supportingArticles = $pdo->query("SELECT a.*, c.name AS category_name, c.slug AS category_slug FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' AND a.is_trending = 0 AND (a.video_url IS NULL OR a.video_url = '') AND (a.video_file IS NULL OR a.video_file = '') $excludeSql ORDER BY a.id DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $supportingArticles = [];
}

// 4. Fetch 5 Most Read Articles
try {
    $mostReadArticles = $pdo->query(
        "SELECT a.*, c.name AS category_name, c.slug AS category_slug
         FROM articles a
         LEFT JOIN categories c ON a.category_id = c.id
         WHERE a.status = 'published'
         ORDER BY a.views_count DESC, a.published_at DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mostReadArticles = [];
}

// 5. Fetch Trending Stories (Max 8)
try {
    $trendingStories = $pdo->query(
        "SELECT a.*, c.name AS category_name, c.slug AS category_slug
         FROM articles a
         LEFT JOIN categories c ON a.category_id = c.id
         WHERE a.status = 'published' AND a.is_trending = 1
         ORDER BY a.id DESC
         LIMIT 8"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $trendingStories = [];
}

// 6. Fetch Latest Headlines for Sidebar
try {
    $latestHeadlines = $pdo->query(
        "SELECT a.*, c.name AS category_name, c.slug AS category_slug
         FROM articles a
         LEFT JOIN categories c ON a.category_id = c.id
         WHERE a.status = 'published'
         ORDER BY a.id DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $latestHeadlines = [];
}

// 7. Dynamic City News per major city
$targetCities = ['AGRA', 'LUCKNOW', 'MATHURA', 'NOIDA'];
$cityNews = [];
foreach ($targetCities as $cityName) {
    try {
        $stmt = $pdo->prepare(
            "SELECT a.*, c.name AS category_name
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             LEFT JOIN cities ct ON a.city_id = ct.id
             WHERE a.status = 'published' AND a.is_trending = 0 AND (a.video_url IS NULL OR a.video_url = '') AND (a.video_file IS NULL OR a.video_file = '') AND (LOWER(ct.name) = ? OR LOWER(c.name) = ?)
             ORDER BY a.id DESC
             LIMIT 4"
        );
        $stmt->execute([strtolower($cityName), strtolower($cityName)]);
        $cArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($cArticles)) {
            $bullets = [];
            foreach (array_slice($cArticles, 1) as $bArt) {
                $bullets[] = [
                    'title' => $bArt['title'],
                    'slug' => $bArt['slug']
                ];
            }
            $cityNews[$cityName] = [
                'main' => $cArticles[0],
                'slug' => strtolower($cityName),
                'image' => articleImage($cArticles[0]['image'] ?? ''),
                'bullets' => $bullets
            ];
        }
    } catch (Exception $e) {}
}

if (empty($cityNews)) {
    try {
        $allPub = $pdo->query("SELECT a.*, c.name AS category_name FROM articles a LEFT JOIN categories c ON a.category_id=c.id WHERE a.status='published' ORDER BY a.id DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
        $chunks = array_chunk($allPub, 3);
        foreach ($targetCities as $idx => $cityName) {
            $cList = $chunks[$idx] ?? $allPub;
            if (!empty($cList)) {
                $bullets = [];
                foreach (array_slice($cList, 1) as $bArt) {
                    $bullets[] = [
                        'title' => $bArt['title'] ?? 'City update',
                        'slug' => $bArt['slug'] ?? 'city-update'
                    ];
                }
                $cityNews[$cityName] = [
                    'main' => $cList[0] ?? [],
                    'slug' => strtolower($cityName),
                    'image' => articleImage($cList[0]['image'] ?? ''),
                    'bullets' => $bullets
                ];
            }
        }
    } catch (Exception $e) {}
}

// 8. Dynamic Video News Dataset
try {
    $videoNews = $pdo->query(
        "SELECT a.*, c.name AS category_name
         FROM articles a
         LEFT JOIN categories c ON a.category_id = c.id
         WHERE a.status = 'published' AND (
             (a.video_url IS NOT NULL AND a.video_url != '') 
             OR (a.video_file IS NOT NULL AND a.video_file != '')
         )
         ORDER BY a.id DESC
         LIMIT 4"
    )->fetchAll(PDO::FETCH_ASSOC);

    
} catch (Exception $e) {
    $videoNews = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/">
    <meta charset="utf-8">
    <title>Gunvani News | Leading News Portal</title>
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
            background-color: #ffffff;
            color: var(--gn-text-dark);
        }

        img, video, iframe {
            max-width: 100% !important;
            height: auto;
        }

        /* Container Limit */
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

        /* 1. Top Utility Bar */
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
            transition: opacity 0.2s ease;
        }
        .gn-utility-bar a:hover {
            opacity: 0.85;
        }
        @media (max-width: 575px) {
            .gn-utility-bar {
                font-size: 0.75rem;
                padding: 5px 0;
            }
        }

        /* Language Switcher Pill Box */
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
            transition: all 0.2s ease;
        }
        .lang-pill-btn.active {
            background-color: var(--gn-green-dark);
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* 2. Main Header & Navigation */
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
            transition: color 0.2s ease;
            white-space: nowrap;
        }
        .gn-nav-link:hover, .gn-nav-link.active {
            color: var(--gn-green);
            font-weight: 700;
        }
        .gn-nav-link.active {
            border-bottom: 2px solid var(--gn-green);
        }

        /* 3. Breaking News Ticker */
        .gn-ticker-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--gn-border);
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            overflow: hidden;
        }
        .gn-ticker-badge {
            background-color: var(--gn-red);
            color: #ffffff;
            font-size: 0.55rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .gn-ticker-content {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            white-space: nowrap;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gn-text-dark);
        }
        .gn-ticker-content a {
            color: var(--gn-text-dark);
            text-decoration: none;
            margin-right: 28px;
        }
        .gn-ticker-content a:hover {
            color: var(--gn-green);
        }
            color: var(--gn-green);
        }
        .gn-ticker-nav-btn {
            background: #ffffff;
            border: 1px solid var(--gn-border);
            color: var(--gn-text-muted);
            width: 22px;
            height: 22px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            cursor: pointer;
        }

        /* 4. Hero Section Grid (Left Main + Middle 4 Cards + Right Most Read) */
        .gn-hero-layout {
            display: grid;
            grid-template-columns: 2fr 1.3fr 1.1fr;
            gap: 18px;
            margin-top: 18px;
            margin-bottom: 24px;
        }
        @media (max-width: 1199px) {
            .gn-hero-layout {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 767px) {
            .gn-hero-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Hero Main Featured Card */
        .hero-main-card {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--gn-border);
            display: flex;
            flex-direction: column;
            position: relative;
            height: 100%;
        }
        .hero-main-img-wrap {
            position: relative;
            height: 240px;
            width: 100%;
            overflow: hidden;
        }
        .hero-main-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-main-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.85) 100%);
            padding: 14px;
            color: #ffffff;
        }
        .cat-badge-red {
            background-color: var(--gn-red);
            color: #ffffff;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 2px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 6px;
        }
        .hero-main-body {
            padding: 14px 16px;
            background: #1e293b;
            color: #ffffff;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .hero-main-body h2 {
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 6px;
        }
        .hero-main-body h2 a {
            color: #ffffff;
            text-decoration: none;
        }
        .hero-main-body p {
            font-size: 0.83rem;
            color: #cbd5e1;
            margin-bottom: 10px;
            line-height: 1.35;
        }
        .hero-meta-row {
            font-size: 0.76rem;
            color: #94a3b8;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Hero Middle Supporting Cards */
        .supporting-card-item {
            display: flex;
            gap: 10px;
            padding-bottom: 8px;
            margin-bottom: 8px;
            border-bottom: 1px solid var(--gn-border);
        }
        .supporting-card-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .supporting-thumb-img {
            width: 90px;
            height: 68px;
            border-radius: 6px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .supporting-text-col {
            flex: 1;
        }
        .supporting-tag {
            font-size: 0.55rem;
            font-weight: 800;
            color: var(--gn-red);
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        .supporting-title {
            font-size: 0.83rem;
            font-weight: 700;
            line-height: 1.25;
            margin: 0 0 3px 0;
        }
        .supporting-title a {
            color: var(--gn-text-dark);
            text-decoration: none;
        }
        .supporting-title a:hover {
            color: var(--gn-green);
        }

        /* Right Sidebar Most Read Box */
        .most-read-container {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid var(--gn-border);
            padding: 14px;
        }
        .most-read-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--gn-border);
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .most-read-top h3 {
            font-size: 1rem;
            font-weight: 800;
            margin: 0;
            color: var(--gn-text-dark);
        }
        .most-read-row {
            display: flex;
            gap: 10px;
            padding: 7px 0;
            border-bottom: 1px dashed var(--gn-border);
        }
        .most-read-row:last-child {
            border-bottom: none;
        }
        .most-read-num {
            font-size: 1.2rem;
            font-weight: 900;
            color: var(--gn-red);
            width: 18px;
            flex-shrink: 0;
            line-height: 1;
        }
        .most-read-title {
            font-size: 0.84rem;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 2px 0;
        }
        .most-read-title a {
            color: var(--gn-text-dark);
            text-decoration: none;
        }
        .most-read-title a:hover {
            color: var(--gn-green);
        }

        /* Section Header Standard */
        .sec-title-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid var(--gn-green);
            padding-left: 10px;
            margin-bottom: 16px;
        }
        .sec-title-bar h3 {
            font-size: 1.2rem;
            font-weight: 800;
            margin: 0;
            color: var(--gn-text-dark);
        }
        .sec-title-bar a {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gn-green);
            text-decoration: none;
        }

        /* News Cards Grid */
        .trending-card-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
        @media (max-width: 991px) {
            .trending-card-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 575px) { .trending-card-grid { grid-template-columns: repeat(2, 1fr); } }
        .trend-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid var(--gn-border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .trend-card-img {
            height: 140px;
            width: 100%;
            object-fit: cover;
        }
        .trend-card-body {
            padding: 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .trend-card-body h4 {
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.35;
            margin: 6px 0 10px 0;
        }
        .trend-card-body h4 a {
            color: var(--gn-text-dark);
            text-decoration: none;
        }

        /* City News 4 Cards Layout */
        .city-box-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid var(--gn-border);
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .city-box-img {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        .city-box-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--gn-green);
            text-transform: uppercase;
            border-bottom: 2px solid var(--gn-green-light);
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .city-bullets {
            list-style: none;
            padding: 0;
            margin: 0 0 14px 0;
        }
        .city-bullets li {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 0;
            border-bottom: 1px dashed var(--gn-border);
        }
        .city-bullets li:last-child {
            border-bottom: none;
        }
        .city-bullets li a {
            color: var(--gn-text-dark);
            text-decoration: none;
        }

        /* Video News Cards Overlay */
        .video-card-wrap {
            position: relative;
            height: 150px;
            border-radius: 6px;
            overflow: hidden;
        }
        .video-card-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .video-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 42px;
            height: 42px;
            background: rgba(0,0,0,0.7);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .video-time-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0,0,0,0.8);
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
        }

        /* Newsletter Card */
        .newsletter-box {
            background: #116530;
            color: #ffffff;
            border-radius: 8px;
            padding: 20px;
        }

        /* Advertisement Placeholders */
        .ad-placeholder {
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            padding: 20px;
        }

        /* Footer */
        .gn-footer {
            background-color: #0b1e13;
            color: #e2e8f0 !important;
            padding: 40px 0 20px 0;
            margin-top: 40px;
            border-top: 3px solid var(--gn-green);
        }
        .gn-footer h5 {
            color: #ffffff !important;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .gn-footer p, 
        .gn-footer span, 
        .gn-footer div, 
        .gn-footer li, 
        .gn-footer .text-muted {
            color: #cbd5e1 !important;
        }
        .gn-footer a {
            color: #e2e8f0 !important;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }
        .gn-footer a:hover {
            color: #4ade80 !important;
        }

        /* Complete Google Translate Top Banner Suppression */
        .goog-te-banner-frame,
        .goog-te-banner-frame.skiptranslate,
        iframe.goog-te-banner-frame,
        #goog-gt-tt,
        .goog-te-balloon-frame,
        .goog-gt-tt-outer,
        .goog-te-spinner-pos {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            border: none !important;
        }
        body {
            top: 0px !important;
            position: static !important;
        }
        .goog-te-combo {
            display: none !important;
        }
        .skiptranslate {
            font-size: 0 !important;
        }
        .skiptranslate * {
            font-size: initial;
        }
            /* --- User Customizations --- */
        /* Mobile Ticker Customization (8px) */
        @media (max-width: 767px) {
            .gn-ticker-badge {
                font-size: 8px !important;
                padding: 3px 6px !important;
            }
            .gn-ticker-nav-btn {
                width: 18px !important;
                height: 18px !important;
                font-size: 8px !important;
            }
        }
        
        
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
            /* Mobile Specific: Supporting/Trending Stories List */
        @media (max-width: 767px) {
            .supporting-title, .supporting-title a {
                font-size: 11px !important;
                line-height: 1.2 !important;
                margin-bottom: 2px !important;
            }
            .supporting-tag {
                font-size: 9px !important;
                margin-bottom: 1px !important;
            }
            .supporting-card-item .text-muted, .supporting-card-item i {
                font-size: 9px !important;
            }
            .supporting-thumb-img {
                width: 60px !important;
                height: 45px !important;
                border-radius: 4px !important;
            }
            .supporting-card-item {
                gap: 6px !important;
                padding-bottom: 6px !important;
                margin-bottom: 6px !important;
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
                    
                    <!-- Language Switcher Pills -->
                    <div class="lang-pill-box">
                        <button type="button" class="lang-pill-btn active" id="btn-lang-hi" onclick="changeLanguage('hi')">हिंदी</button>
                        <button type="button" class="lang-pill-btn" id="btn-lang-en" onclick="changeLanguage('en')">English</button>
                    </div>
                    <div id="google_translate_element" style="display:none;"></div>

                    <span class="opacity-25 d-none d-sm-inline">|</span>
                    <div class="d-none d-sm-flex gap-2">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Main Header & Navigation Menu -->
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

    <!-- 3. Breaking News Ticker Bar -->
    <div class="bg-white border-bottom">
        <div class="gn-container">
            <div class="gn-ticker-bar">
                <div class="gn-ticker-badge">
                    BREAKING NEWS
                </div>
                <div class="gn-ticker-content">
                    <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();">
                        <?php if (!empty($breakingNews)): ?>
                            <?php foreach ($breakingNews as $bn): ?>
                                <a href="/article/<?= escape($bn['slug']) ?>"><span class="text-danger me-1">●</span> <?= escape($bn['title']) ?></a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="/article/<?= escape($featuredArticle['slug']) ?>"><span class="text-danger me-1">●</span> <?= escape($featuredArticle['title']) ?></a>
                        <?php endif; ?>
                    </marquee>
                </div>
                <div class="d-flex gap-1">
                    <button class="gn-ticker-nav-btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="gn-ticker-nav-btn"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="gn-container py-3">

        <!-- 4. Hero Section Grid (3 Columns) -->
        <section class="gn-hero-layout">
            
            <!-- Left Column: Main Featured Article -->
                        <article class="hero-main-card">
                <div id="heroCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="7000">
                    <div class="carousel-inner h-100">
                        <?php if (!empty($featuredArticles)): ?>
                            <?php foreach ($featuredArticles as $idx => $fa): ?>
                                <div class="carousel-item h-100 <?= $idx === 0 ? 'active' : '' ?>">
                                    <a href="/article/<?= escape($fa['slug']) ?>" class="hero-main-img-wrap d-block">
                                        <img src="<?= escape(articleImage($fa['image'])) ?>" alt="<?= escape($fa['title']) ?>" onerror="this.src='images/placeholder/second6.webp'">
                                        <div class="hero-main-overlay">
                                            <span class="cat-badge-red"><?= escape($fa['category_name'] ?: 'LUCKNOW') ?></span>
                                        </div>
                                    </a>
                                    <div class="hero-main-body">
                                        <h2>
                                            <a href="/article/<?= escape($fa['slug']) ?>">
                                                <?= escape($fa['title']) ?>
                                            </a>
                                        </h2>
                                        <p><?= escape($fa['summary']) ?></p>
                                        <div class="hero-meta-row">
                                            <div>
                                                <span class="me-3"><i class="fa-regular fa-user me-1"></i>By Gunvani News</span>
                                                <span><i class="fa-regular fa-clock me-1"></i><?= formatDate($fa['published_at']) ?></span>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <button class="gn-ticker-nav-btn" data-bs-target="#heroCarousel" data-bs-slide="prev"><i class="fa-solid fa-chevron-left"></i></button>
                                                <button class="gn-ticker-nav-btn" data-bs-target="#heroCarousel" data-bs-slide="next"><i class="fa-solid fa-chevron-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </article>

            <!-- Middle Column: 4 Stacked Supporting Stories -->
            <div class="d-flex flex-column justify-content-start bg-white p-2 border rounded-3 h-100">
                <?php foreach ($supportingArticles as $story): ?>
                    <article class="supporting-card-item">
                        <a href="/article/<?= escape($story['slug']) ?>" class="flex-shrink-0">
                            <img src="<?= escape(articleImage($story['image'])) ?>" alt="<?= escape($story['title']) ?>" class="supporting-thumb-img" onerror="this.src='images/placeholder/first8.jpg'">
                        </a>
                        <div class="supporting-text-col">
                            <span class="supporting-tag"><?= escape($story['category_name'] ?: 'CITY') ?></span>
                            <h4 class="supporting-title">
                                <a href="/article/<?= escape($story['slug']) ?>">
                                    <?= escape($story['title']) ?>
                                </a>
                            </h4>
                            <div class="text-muted small" style="font-size:0.74rem;">
                                <i class="fa-regular fa-clock me-1"></i><?= formatDate($story['published_at']) ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Right Column: Most Read Box & Ad -->
            <div class="d-flex flex-column gap-3">
                <aside class="most-read-container">
                    <div class="most-read-top">
                        <h3><i class="fa-solid fa-fire text-danger me-2"></i>Most Read</h3>
                        <a href="/categories" class="small text-muted text-decoration-none fw-semibold">See all</a>
                    </div>

                    <?php foreach ($mostReadArticles as $rank => $mr): ?>
                        <div class="most-read-row">
                            <div class="most-read-num"><?= $rank + 1 ?></div>
                            <div>
                                <h5 class="most-read-title">
                                    <a href="/article/<?= escape($mr['slug']) ?>">
                                        <?= escape($mr['title']) ?>
                                    </a>
                                </h5>
                                <div class="text-muted small" style="font-size:0.72rem;">
                                    <i class="fa-regular fa-clock me-1"></i><?= formatDate($mr['published_at']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </aside>

                <!-- Ad Banner Below Most Read -->
                <?php if (!empty(trim($adHome728))): ?>
                    <div class="ad-container text-center my-3 w-100 overflow-hidden">
                        <img src="<?= escape($adHome728) ?>" class="img-fluid" alt="Advertisement">
                    </div>
                <?php endif; ?>
            </div>
        </section>


        <!-- 5. City News Section & Latest Headlines Sidebar -->
        <section class="my-4">
            <div class="row g-3">
                <!-- Left 9-col City News Cards -->
                <div class="col-lg-9">
                    <div class="sec-title-bar">
                        <h3>City News</h3>
                        <a href="/categories" style="font-size: 0.75rem;">View all cities <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($cityNews as $cName => $cData): ?>
                            <div class="col-6 col-sm-6 col-md-3">
                                <div class="city-box-card">
                                    <a href="/category/<?= escape($cData['slug'] ?? strtolower($cName)) ?>">
                                        <img src="<?= escape($cData['image'] ?? 'images/placeholder/first8.jpg') ?>" alt="<?= escape($cName) ?>" class="city-box-img" onerror="this.src='images/placeholder/first8.jpg'">
                                    </a>
                                    <h4 class="city-box-title"><a href="/category/<?= escape($cData['slug'] ?? strtolower($cName)) ?>" class="text-dark text-decoration-none"><?= escape($cName) ?></a></h4>
                                    <ul class="city-bullets">
                                        <?php foreach (($cData['bullets'] ?? []) as $bItem): ?>
                                            <?php 
                                            $bTitle = is_array($bItem) ? ($bItem['title'] ?? '') : $bItem;
                                            $bSlug = is_array($bItem) ? ($bItem['slug'] ?? '') : preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($bTitle)));
                                            ?>
                                            <li><a href="/article/<?= escape($bSlug) ?>">• <?= escape($bTitle) ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <a href="/category/<?= escape($cData['slug'] ?? strtolower($cName)) ?>" class="font-weight-bold text-success text-decoration-none" style="font-size: 0.75rem;">
                                            View all <?= ucfirst(strtolower($cName)) ?> news →
                                        </a>
                                        <button class="btn btn-sm btn-link text-muted p-0" title="Bookmark"><i class="fa-regular fa-bookmark"></i></button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Right 3-col Latest Headlines Sidebar -->
                <div class="col-lg-3">
                    <div class="sec-title-bar">
                        <h3>Latest Headlines</h3>
                        <a href="/categories">See all</a>
                    </div>

                    <div class="bg-white border rounded-3 p-3">
                        <?php foreach ($latestHeadlines as $lh): ?>
                            <div class="d-flex gap-2 py-2 border-bottom">
                                <a href="/article/<?= escape($lh['slug'] ?? '') ?>" class="flex-shrink-0">
                                    <img src="<?= escape(articleImage($lh['image'] ?? '')) ?>" alt="Thumb" class="rounded" style="width:65px; height:45px; object-fit:cover;" onerror="this.src='images/placeholder/first8.jpg'">
                                </a>
                                <div>
                                    <h5 class="small fw-bold mb-1" style="font-size:0.8rem; line-height:1.25;">
                                        <a href="/article/<?= escape($lh['slug'] ?? '') ?>" class="text-dark text-decoration-none">
                                            <?= escape($lh['title'] ?? '') ?>
                                        </a>
                                    </h5>
                                    <div class="text-muted small" style="font-size:0.7rem;">
                                        <i class="fa-regular fa-clock me-1"></i><?= formatDate($lh['published_at'] ?? 'now') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Video News & Get Daily Updates Newsletter Section -->
        <section class="my-4">
            <div class="row g-3">
                <!-- Left 9-col Video News -->
                <div class="col-lg-9">
                    <div class="sec-title-bar">
                        <h3>Video News</h3>
                        <a href="/categories">See all videos <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>

                    <div class="trending-card-grid">
                        <?php foreach ($videoNews as $vItem): ?>
                            <?php $vSlug = !empty($vItem['slug']) ? $vItem['slug'] : preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($vItem['title'] ?? 'video'))); ?>
                            <article class="trend-card">
                                <a href="/article/<?= escape($vSlug) ?>" class="text-decoration-none text-dark d-flex flex-column h-100">
                                    <div class="video-card-wrap">
                                        <?php if (!empty($vItem['video_file']) && empty($vItem['image'])): ?>
                                            <video src="uploads/videos/<?= htmlspecialchars($vItem['video_file']) ?>" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;" muted playsinline></video>
                                        <?php else: ?>
                                            <img src="<?= escape(articleImage($vItem['image'] ?? '')) ?>" alt="<?= escape($vItem['title'] ?? '') ?>" onerror="this.src='images/placeholder/first8.jpg'">
                                        <?php endif; ?>
                                        <div class="video-play-btn"><i class="fa-solid fa-play"></i></div>
                                        <span class="video-time-badge"><?= !empty($vItem['duration']) ? escape($vItem['duration']) : '01:30' ?></span>
                                    </div>
                                    <div class="trend-card-body">
                                        <h4><?= escape($vItem['title'] ?? '') ?></h4>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Right 3-col Newsletter Card -->
                <div class="col-lg-3">
                    <div class="newsletter-box h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-regular fa-envelope display-6"></i>
                            <h4 class="m-0 fw-bold fs-5">Get Daily Updates</h4>
                        </div>
                        <p class="small opacity-75 mb-3">Subscribe to Gunvani News for the latest news, directly in your inbox.</p>
                        <form method="POST" action="#" onsubmit="alert('Thank you for subscribing to Gunvani News!'); return false;">
                            <div class="mb-2">
                                <input type="email" class="form-control form-control-sm py-2" placeholder="Enter your email" required>
                            </div>
                            <button type="submit" class="btn btn-light text-success w-100 font-weight-bold py-2">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>



    <!-- 7. Trending Stories Section -->
        <section class="my-4">
            <div class="sec-title-bar">
                <h3>Trending Stories</h3>
                <a href="/categories" style="font-size: 0.75rem;">View all <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>

            <div class="row g-3">
                <div class="col-lg-9">
                    <div class="trending-card-grid">
                        <?php if (empty($trendingStories)): ?>
                            <div class="alert alert-light border w-100 text-center py-5">
                                <i class="fa-solid fa-chart-line fs-3 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No Trending Stories</h5>
                                <p class="text-muted small mb-0">Check "Trending Story" when adding a new article in the admin panel to show it here.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($trendingStories as $tArt): ?>
                            <article class="trend-card">
                                <a href="/article/<?= escape($tArt['slug'] ?? '') ?>">
                                    <img src="<?= escape(articleImage($tArt['image'] ?? '')) ?>" alt="<?= escape($tArt['title'] ?? '') ?>" class="trend-card-img" onerror="this.src='images/placeholder/first8.jpg'">
                                </a>
                                <div class="trend-card-body">
                                    <span class="supporting-tag"><?= escape($tArt['category_name'] ?? 'NEWS') ?></span>
                                    <h4>
                                        <a href="/article/<?= escape($tArt['slug'] ?? '') ?>">
                                            <?= escape($tArt['title'] ?? '') ?>
                                        </a>
                                    </h4>
                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 text-muted small" style="font-size:0.74rem;">
                                        <span><i class="fa-regular fa-clock me-1"></i><?= formatDate($tArt['published_at'] ?? 'now') ?></span>
                                        <button class="btn btn-sm btn-link text-muted p-0" title="Bookmark"><i class="fa-regular fa-bookmark"></i></button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar Ad 300x250 -->
                <div class="col-lg-3">
                    <?php if (!empty(trim($adHome300))): ?>
                        <div class="ad-container h-100 d-flex align-items-center justify-content-center m-0" style="min-height:220px; overflow:hidden;">
                            <img src="<?= escape($adHome300) ?>" class="img-fluid" alt="Advertisement">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </main>

    <!-- 8. Footer -->
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
                        <li class="mb-2"><a href="about-us">About Us</a></li>
                        <li class="mb-2"><a href="/categories">Categories</a></li>
                        <li class="mb-2"><a href="/verification">Verification</a></li>
                        <li class="mb-2"><a href="/contact">Contact</a></li>
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

    <!-- Bootstrap 5.3 & Language Switcher Script -->
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
