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
    $categories = $pdo->query("SELECT id, name FROM menus WHERE status = 'active' ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

// Default fallback categories matching screenshot if DB has few
$defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];

// 1. Fetch Featured Article for Hero (is_featured = 1 or latest published article)
try {
    $featuredArticles = $pdo->query("SELECT a.*, 
       (SELECT m.name FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_name,
       (SELECT m.slug FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_slug
       FROM articles a  WHERE a.status = 'published' AND a.is_trending = 0 AND (a.video_url IS NULL OR a.video_url = '') AND (a.video_file IS NULL OR a.video_file = '') ORDER BY a.is_featured DESC, a.published_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
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
    $featIds = array_column($featuredArticles ?? [], 'id'); $excludeSql = !empty($featIds) ? "AND a.id NOT IN (" . implode(',', $featIds) . ")" : ""; $supportingArticles = $pdo->query("SELECT a.*, 
       (SELECT m.name FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_name,
       (SELECT m.slug FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_slug
       FROM articles a  WHERE a.status = 'published' AND a.is_trending = 0 AND (a.video_url IS NULL OR a.video_url = '') AND (a.video_file IS NULL OR a.video_file = '') $excludeSql ORDER BY a.id DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $supportingArticles = [];
}

// 4. Fetch 5 Most Read Articles
try {
    $mostReadArticles = $pdo->query(
        "SELECT a.*, 
       (SELECT m.name FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_name,
       (SELECT m.slug FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_slug
       FROM articles a  WHERE a.status = 'published'
         ORDER BY a.views_count DESC, a.published_at DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mostReadArticles = [];
}

// 5. Fetch Trending Stories (Max 8)
try {
    $trendingStories = $pdo->query(
        "SELECT a.*, 
       (SELECT m.name FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_name,
       (SELECT m.slug FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_slug
       FROM articles a  WHERE a.status = 'published' AND a.is_trending = 1
         ORDER BY a.id DESC
         LIMIT 8"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $trendingStories = [];
}

// 6. Fetch Latest Headlines for Sidebar
try {
    $latestHeadlines = $pdo->query(
        "SELECT a.*, 
       (SELECT m.name FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_name,
       (SELECT m.slug FROM menus m JOIN article_menu am ON am.menu_id = m.id WHERE am.article_id = a.id LIMIT 1) as category_slug
       FROM articles a  WHERE a.status = 'published'
         ORDER BY a.id DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $latestHeadlines = [];
}

// 7. Dynamic City News per major city (using active submenus)
  $cityNews = [];
  try {
      $submenus = $pdo->query("SELECT id, name, slug FROM menus WHERE parent_id IS NOT NULL AND status = 'active' ORDER BY display_order ASC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
      
      foreach ($submenus as $menu) {
          $stmt = $pdo->prepare("
              SELECT a.* 
              FROM articles a
              JOIN article_menu am ON a.id = am.article_id
              WHERE am.menu_id = ? AND a.status = 'published'
              ORDER BY a.id DESC LIMIT 4
          ");
          $stmt->execute([$menu['id']]);
          $cArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if (!empty($cArticles)) {
              $bullets = [];
              foreach (array_slice($cArticles, 1) as $bArt) {
                  $bullets[] = [
                      'title' => $bArt['title'],
                      'slug' => $bArt['slug']
                  ];
              }
              $cityNews[$menu['name']] = [
                  'main' => $cArticles[0],
                  'slug' => $menu['slug'],
                  'image' => articleImage($cArticles[0]['image'] ?? ''),
                  'bullets' => $bullets
              ];
          }
      }
  } catch (Exception $e) {}

// 8. Dynamic Video News Dataset
try {
    $videoNews = $pdo->query(
        "SELECT a.*, c.name AS category_name
         FROM articles a
         LEFT JOIN menus c ON a.category_id = c.id
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
    <!-- Gunvani Official Favicon & Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

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

    <link rel="stylesheet" href="/css/style.css">
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

<!-- 2. Main Header & Navigation Menu (Dynamic) -->
    <?php include __DIR__ . '/nav.php'; ?>

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
                                        <img src="<?= escape(articleImage($fa['image'])) ?>" alt="<?= escape($fa['title']) ?>" onerror="this.onerror=null; this.src='/images/placeholder/second6.webp'">
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
                            <img src="<?= escape(articleImage($story['image'])) ?>" alt="<?= escape($story['title']) ?>" class="supporting-thumb-img" onerror="this.onerror=null; this.src='/images/placeholder/first8.jpg'">
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
                                        <img src="<?= escape($cData['image'] ?? 'images/placeholder/first8.jpg') ?>" alt="<?= escape($cName) ?>" class="city-box-img" onerror="this.onerror=null; this.src='/images/placeholder/first8.jpg'">
                                    </a>
                                    <h4 class="city-box-title"><a href="/category/<?= escape($cData['slug'] ?? strtolower($cName)) ?>" class="text-dark text-decoration-none"><?= escape($cName) ?></a></h4>
                                    <ul class="city-bullets">
                                          <?php foreach (($cData['bullets'] ?? []) as $bItem): ?>
                                              <?php 
                                              $bTitle = is_array($bItem) ? ($bItem['title'] ?? '') : $bItem;
                                              $bSlug = is_array($bItem) ? ($bItem['slug'] ?? '') : preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($bTitle)));
                                              ?>
                                              <li><a href="/article/<?= escape($bSlug) ?>" class="line-clamp-3">â€¢ <?= escape($bTitle) ?></a></li>
                                          <?php endforeach; ?>
                                      </ul>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <a href="/category/<?= escape($cData['slug'] ?? strtolower($cName)) ?>" class="font-weight-bold text-success text-decoration-none" style="font-size: 0.75rem;">
                                            View more →
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
                                    <img src="<?= escape(articleImage($lh['image'] ?? '')) ?>" alt="Thumb" class="rounded" style="width:65px; height:45px; object-fit:cover;" onerror="this.onerror=null; this.src='/images/placeholder/first8.jpg'">
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
                                            <video src="/uploads/videos/<?= htmlspecialchars($vItem['video_file']) ?>" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;" muted playsinline></video>
                                        <?php else: ?>
                                            <img src="<?= escape(articleImage($vItem['image'] ?? '')) ?>" alt="<?= escape($vItem['title'] ?? '') ?>" onerror="this.onerror=null; this.src='/images/placeholder/first8.jpg'">
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
                                    <img src="<?= escape(articleImage($tArt['image'] ?? '')) ?>" alt="<?= escape($tArt['title'] ?? '') ?>" class="trend-card-img" onerror="this.onerror=null; this.src='/images/placeholder/first8.jpg'">
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
    <?php include __DIR__ . '/footer.php'; ?>
    </body>
</html>




