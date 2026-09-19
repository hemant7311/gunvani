<?php
if (!isset($_COOKIE['googtrans'])) {
    setcookie('googtrans', '/en/hi', time() + (86400 * 30), '/');
    $_COOKIE['googtrans'] = '/en/hi';
}

require_once __DIR__ . '/db.php';

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function memberPhotoPath($photo) {
    if (!$photo) return 'images/placeholder/first8.jpg';
    if (preg_match('#^(images/|uploads/)#', $photo)) return $photo;
    if (file_exists(__DIR__ . '/admin/uploads/' . $photo)) return 'admin/uploads/' . $photo;
    if (file_exists(__DIR__ . '/uploads/' . $photo)) return 'uploads/' . $photo;
    return 'images/placeholder/first8.jpg';
}

function formatDateDisplay($date, $default = 'Dec 31, 2026') {
    if (!$date) return $default;
    $time = strtotime($date);
    return $time ? date('M j, Y', $time) : $default;
}

$found = null;
$message = '';
$member_id_input = trim($_REQUEST['member_id'] ?? $_REQUEST['id'] ?? '');

if ($member_id_input !== '') {
    try {
        $cleanInput = strtolower(trim($member_id_input));
        $cleanInputNoDash = str_replace('-', '', $cleanInput);

        $stmt = $pdo->prepare("SELECT * FROM members 
            WHERE LOWER(member_id) = :q 
            OR id = :id_num 
            OR LOWER(REPLACE(member_id, '-', '')) = :q_clean
            OR LOWER(member_id) LIKE :id_like
            OR mobile = :mobile
            OR LOWER(name) LIKE :name_like 
            LIMIT 1");

        $stmt->execute([
            ':q' => $cleanInput,
            ':id_num' => is_numeric($cleanInput) ? (int)$cleanInput : 0,
            ':q_clean' => $cleanInputNoDash,
            ':id_like' => '%' . $cleanInput . '%',
            ':mobile' => $member_id_input,
            ':name_like' => '%' . $cleanInput . '%'
        ]);
        $found = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $found = null;
    }

    if (!$found) {
        $message = "<strong>MEMBER RECORD NOT FOUND:</strong> No active reporter or member record found matching <strong>" . escape($member_id_input) . "</strong>.";
    }
}

$defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/">
    <meta charset="utf-8">
    <title>Member & Reporter Verification | Gunvani News</title>
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

        /* Hero Banner */
        .verify-hero {
            background: linear-gradient(135deg, var(--gn-green-dark) 0%, var(--gn-green) 100%);
            color: #ffffff;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        @media (max-width: 575px) {
            .verify-hero {
                padding: 24px 0;
                margin-bottom: 20px;
            }
            .verify-hero h1 {
                font-size: 1.5rem !important;
            }
        }

        /* Search Card */
        .search-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--gn-border);
            padding: 28px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            max-width: 600px;
            margin: 0 auto 35px auto;
        }
        @media (max-width: 575px) {
            .search-card {
                padding: 18px;
            }
        }

        /* Verification Result Card */
        .result-card {
            background: #ffffff;
            border-radius: 12px;
            border: 2px solid var(--gn-green);
            padding: 28px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            max-width: 650px;
            margin: 0 auto;
        }
        @media (max-width: 575px) {
            .result-card {
                padding: 16px;
            }
        }
        .member-avatar {
            width: 130px;
            height: 130px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid var(--gn-green);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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

    <!-- 3. Hero Banner -->
    <section class="verify-hero">
        <div class="gn-container text-center">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white text-success fw-bold small mb-2" style="color:var(--gn-green) !important;">
                <i class="fa-solid fa-shield-halved"></i> Official Credentials Directory
            </div>
            <h1 class="display-6 fw-bold mb-2">Member & Reporter Verification</h1>
            <p class="m-0 opacity-85">Verify Gunvani News press credentials, reporter badges, and authorized staff profiles.</p>
        </div>
    </section>

    <!-- 4. Search Form & Results Container -->
    <main class="gn-container mb-5">
        <div class="search-card">
            <form method="POST" action="verification">
                <label class="form-label fw-bold mb-2">Enter Member ID or Badge Number:</label>
                <div class="input-group">
                    <input type="text" name="member_id" class="form-control py-2" placeholder="e.g. GN-1001 or 1001" value="<?= escape($member_id_input) ?>" required>
                    <button type="submit" class="btn btn-success fw-bold px-4" style="background:var(--gn-green); border-color:var(--gn-green);">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Verify Now
                    </button>
                </div>
            </form>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-danger text-center max-w-600 mx-auto border-danger mb-4 shadow-sm" style="max-width:600px;">
                <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i><?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($found): 
            $mStatus = strtolower($found['status'] ?? 'approved');
            $statusBadgeClass = 'bg-success';
            $statusLabel = 'VERIFIED';
            $statusIcon = 'fa-circle-check';
            $borderClass = 'border-success';

            if ($mStatus === 'rejected') {
                $statusBadgeClass = 'bg-danger';
                $statusLabel = 'REJECTED';
                $statusIcon = 'fa-circle-xmark';
                $borderClass = 'border-danger';
            } elseif ($mStatus === 'pending') {
                $statusBadgeClass = 'bg-warning text-dark';
                $statusLabel = 'PENDING VERIFICATION';
                $statusIcon = 'fa-clock';
                $borderClass = 'border-warning';
            } elseif ($mStatus === 'expired') {
                $statusBadgeClass = 'bg-secondary';
                $statusLabel = 'EXPIRED';
                $statusIcon = 'fa-calendar-xmark';
                $borderClass = 'border-secondary';
            }
        ?>
            <div class="result-card shadow-sm <?= $borderClass ?> border-2">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 fw-bold fs-5">
                        <i class="fa-solid <?= $statusIcon ?> fs-4"></i> <?= $statusLabel ?> PRESS CREDENTIAL
                    </div>
                    <span class="badge <?= $statusBadgeClass ?> px-3 py-2 fs-6"><i class="fa-solid fa-shield-halved me-1"></i><?= $statusLabel ?></span>
                </div>

                <?php if ($mStatus === 'rejected' && !empty($found['rejection_reason'])): ?>
                    <div class="alert alert-danger mb-3 py-2 px-3 small border-0">
                        <strong><i class="fa-solid fa-circle-exclamation me-1"></i>Rejection Reason:</strong> <?= escape($found['rejection_reason']) ?>
                    </div>
                <?php endif; ?>

                <div class="row align-items-center g-4">
                    <div class="col-sm-4 text-center">
                        <img src="<?= escape(memberPhotoPath($found['photo'] ?? '')) ?>" alt="<?= escape($found['name']) ?>" class="member-avatar img-thumbnail rounded-circle shadow-sm" style="width:130px; height:130px; object-fit:cover;" onerror="this.src='images/placeholder/first8.jpg'">
                    </div>
                    <div class="col-sm-8">
                        <h3 class="fw-bold mb-1 text-dark"><?= escape($found['name']) ?></h3>
                        <p class="text-success fw-bold mb-3 fs-6" style="color:var(--gn-green) !important;"><i class="fa-solid fa-id-badge me-1"></i><?= escape($found['designation'] ?: 'Press Reporter') ?></p>
                        
                        <div class="bg-light p-3 rounded-3 border">
                            <div class="row g-2 small">
                                <div class="col-12 col-sm-6 text-nowrap overflow-hidden text-truncate" title="Member ID: <?= escape($found['member_id'] ?: $found['id']) ?>"><strong>Member ID:</strong> <code class="bg-white px-2 py-0.5 rounded border text-success fw-bold"><?= escape($found['member_id'] ?: $found['id']) ?></code></div>
                                <div class="col-12 col-sm-6 text-nowrap overflow-hidden text-truncate"><strong>Location:</strong> <?= escape($found['location'] ?: $found['city'] ?? 'Agra, UP') ?></div>
                                <div class="col-12 col-sm-6 text-nowrap overflow-hidden text-truncate"><strong>Mobile:</strong> <?= escape($found['mobile'] ?: 'N/A') ?></div>
                                <div class="col-12 col-sm-6 text-nowrap overflow-hidden text-truncate"><strong>Blood Group:</strong> <span class="fw-semibold text-dark"><?= escape($found['blood_group'] ?: 'N/A') ?></span></div>
                                <div class="col-12 col-sm-6 text-nowrap overflow-hidden text-truncate"><strong>Issue Date:</strong> <?= formatDateDisplay($found['doi'] ?? null, 'Jan 01, 2025') ?></div>
                                <div class="col-12 col-sm-6 text-nowrap overflow-hidden text-truncate"><strong>Valid Until:</strong> <?= formatDateDisplay($found['doe'] ?? null, 'Dec 31, 2026') ?></div>
                                <?php if (!empty($found['address'])): ?>
                                    <div class="col-12 border-top pt-2 mt-1"><strong>Address:</strong> <?= escape($found['address']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($mStatus === 'approved'): ?>
                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            <a href="admin/generate_id.php?id=<?= escape($found['id']) ?>" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
                                <i class="fa-solid fa-file-pdf me-1"></i> View / Download Press ID Card
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
