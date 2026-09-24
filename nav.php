<?php
if (!isset($breakingNews)) {
    try {
        $breakingNews = $pdo->query(
            "SELECT title, slug FROM articles WHERE status = 'published' ORDER BY is_breaking DESC, published_at DESC LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $breakingNews = [];
    }
}
if (!function_exists('escape')) {
    function escape($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>
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
                    <a href="/admin/login.php"><i class="fa-solid fa-lock me-1"></i>Admin</a>
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
</div> <!-- MISSING DIV FIXED -->
<?php
// Make sure this doesn't crash if included multiple times, though it shouldn't be.
if (!isset($allMenus)) {
    $allMenus = [];
    $parentCats = [];
    $childCats = [];
    try {
        if (isset($pdo)) {
            $sql = "SELECT id, name as title, parent_id, slug 
                    FROM menus 
                    WHERE status = 'active'
                    ORDER BY parent_id ASC, display_order ASC, id ASC";
            $stmt = $pdo->query($sql);
            $allMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($allMenus as $m) {
                $item = [
                    'id' => $m['id'],
                    'name' => $m['title'],
                    'slug' => $m['slug'] ? $m['slug'] : '#',
                    'parent_id' => $m['parent_id']
                ];
                
                if (empty($m['parent_id'])) {
                    $parentCats[] = $item;
                } else {
                    $childCats[$m['parent_id']][] = $item;
                }
            }
        }
    } catch (Exception $e) {
        $allMenus = [];
        $parentCats = [
            ['id' => 1, 'name' => 'India', 'slug' => 'india', 'parent_id' => null],
            ['id' => 2, 'name' => 'Foreign', 'slug' => 'foreign', 'parent_id' => null],
            ['id' => 3, 'name' => 'Uttar Pradesh', 'slug' => 'uttar-pradesh', 'parent_id' => null],
            ['id' => 4, 'name' => 'Bihar', 'slug' => 'bihar', 'parent_id' => null],
            ['id' => 5, 'name' => 'Madhya Pradesh', 'slug' => 'madhya-pradesh', 'parent_id' => null],
            ['id' => 6, 'name' => 'Business', 'slug' => 'business', 'parent_id' => null],
            ['id' => 7, 'name' => 'Sports', 'slug' => 'sports', 'parent_id' => null]
        ];
        $childCats = [];
    }
    $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
}
?>

<link rel="stylesheet" href="/css/style.css">

<header class="gn-main-header text-center">
    <!-- Huge Centered Logo (Ureshiya Style) -->
    <div class="py-2 text-center border-bottom">
        <a href="/">
            <img src="/images/gunvani_main_logo.webp" alt="Gunvani News Logo" class="gn-logo-center" onerror="this.onerror=null; this.src='/icon.png'">
        </a>
    </div>

    <!-- Centered Nav Bar -->
    <div class="gn-container py-2">
        <div class="d-flex align-items-center justify-content-between">
            
            <div class="nav-center-wrapper flex-grow-1">
                <!-- Home Link with notranslate class to prevent "Ghar" -->
                <a href="/" class="gn-nav-link <?= ($currentUri === '/' || $currentUri === '/index.php' || $currentUri === '') ? 'active' : '' ?> text-nowrap p-0">
                    <span class="notranslate">HOME</span>
                </a>

                <!-- Dynamic Categories -->
                <?php foreach ($parentCats as $pCat): ?>
                    <?php 
                    $pSlug = $pCat['slug'];
                    $isActive = (strpos($currentUri, '/category/' . $pSlug) !== false) ? 'active' : '';
                    $hasChildren = isset($childCats[$pCat['id']]);
                    ?>
                    
                    <?php if ($hasChildren): ?>
                        <div class="gn-dropdown">
                            <a href="/category/<?= $pSlug ?>" class="gn-nav-link <?= $isActive ?> text-nowrap p-0">
                                <?= htmlspecialchars($pCat['name']) ?> <i class="fa-solid fa-chevron-down gn-dropdown-icon"></i>
                            </a>
                            <div class="gn-dropdown-content text-start">
                                <?php foreach ($childCats[$pCat['id']] as $cCat): ?>
                                    <a href="/category/<?= $cCat['slug'] ?>"><?= htmlspecialchars($cCat['name']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/category/<?= $pSlug ?>" class="gn-nav-link <?= $isActive ?> text-nowrap p-0">
                            <?= htmlspecialchars($pCat['name']) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <a href="/contact" class="gn-nav-link <?= (strpos($currentUri, '/contact') !== false) ? 'active' : '' ?> text-nowrap p-0">Contact Us</a>
            </div>

            <!-- Search Bar (Right aligned, desktop only) -->
            <form action="/search" method="GET" class="d-none d-md-flex align-items-center m-0 p-0 position-relative flex-shrink-0 ms-3">
                <input type="text" name="q" class="form-control rounded-pill pe-4" placeholder="Search..." style="width: 180px; height: 32px; font-size: 0.85rem; border-color: #dee2e6;" required>
                <button type="submit" class="btn btn-link text-secondary position-absolute end-0 top-0 bottom-0 text-decoration-none d-flex align-items-center justify-content-center" style="padding: 0 12px; height: 32px;" title="Search">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 0.85rem;"></i>
                </button>
            </form>

        </div>
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
                        <?php else: ?><a href="#">No breaking news at the moment</a>
                        <?php endif; ?>
                    </marquee>
                </div>
                <div class="d-flex gap-1">
                    <button class="gn-ticker-nav-btn"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="gn-ticker-nav-btn"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
        </div>