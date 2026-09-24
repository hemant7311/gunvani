<?php
require_once __DIR__ . '/auth_check.php';
$activePage = $activePage ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Admin Panel - Gunvani News';
$adminUser = $_SESSION['admin'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Gunvani Official Favicon & Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css?v=1789755542">
</head>
<body class="admin-body">

    <div class="sidebar-overlay" onclick="toggleMobileSidebar()"></div>

    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <img src="../images/placeholder/logos.png" alt="Gunvani News Logo" class="sidebar-brand-logo" onerror="this.onerror=null; this.src='../icon.png'">
            <div class="sidebar-brand-text">
                Gunvani News
                <small>Admin Panel</small>
            </div>
        </div>

        <div class="sidebar-menu-wrapper">
            <div class="menu-category">Main Menu</div>
            <ul class="nav-sidebar">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" title="Dashboard">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="news.php" class="nav-link <?= $activePage === 'news' ? 'active' : '' ?>" title="News Articles">
                        <i class="fa-solid fa-newspaper"></i>
                        <span>News Articles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="videos.php" class="nav-link <?= $activePage === 'videos' ? 'active' : '' ?>" title="Video News">
                        <i class="fa-solid fa-play-circle"></i>
                        <span>Video News</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="trending.php" class="nav-link <?= $activePage === 'trending' ? 'active' : '' ?>" title="Trending Stories">
                        <i class="fa-solid fa-fire"></i>
                        <span>Trending Stories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="add_news.php" class="nav-link <?= $activePage === 'add_news' ? 'active' : '' ?>" title="Add New Article">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Add New Article</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="menus.php" class="nav-link <?= $activePage === 'menus' ? 'active' : '' ?>" title="Menu Management">
                        <i class="fa-solid fa-bars"></i>
                        <span>Menu Management</span>
                    </a>
                </li>
            </ul>

            <div class="menu-category">Management</div>
            <ul class="nav-sidebar">
                <li class="nav-item">
                    <a href="media.php" class="nav-link <?= $activePage === 'media' ? 'active' : '' ?>" title="Media Library">
                        <i class="fa-solid fa-photo-film"></i>
                        <span>Media Library</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="members.php" class="nav-link <?= $activePage === 'members' ? 'active' : '' ?>" title="Members">
                        <i class="fa-solid fa-address-card"></i>
                        <span>Members Directory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="add_members.php" class="nav-link <?= $activePage === 'add_members' ? 'active' : '' ?>" title="Add Member">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Add Member</span>
                    </a>
                </li>
                <?php if (is_admin()): ?>
                <li class="nav-item">
                    <a href="agents.php" class="nav-link <?= $activePage === 'agents' ? 'active' : '' ?>" title="Press Agents">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Press Agents</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="contact_messages.php" class="nav-link <?= $activePage === 'contact_messages' ? 'active' : '' ?>" title="Contact Messages">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Contact Messages</span>
                    </a>
                </li>
            </ul>

            <div class="menu-category">System</div>
            <ul class="nav-sidebar">
                <li class="nav-item">
                    <a href="settings.php" class="nav-link <?= $activePage === 'settings' ? 'active' : '' ?>" title="Site Settings">
                        <i class="fa-solid fa-gear"></i>
                        <span>Site Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="change_password.php" class="nav-link <?= $activePage === 'change_password' ? 'active' : '' ?>" title="Change Password">
                        <i class="fa-solid fa-key"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a href="logout.php" class="nav-link text-danger" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var activeLink = document.querySelector('.sidebar-menu-wrapper .nav-link.active');
            if (activeLink) {
                activeLink.scrollIntoView({ block: 'center', behavior: 'instant' });
            }
        });
        </script>
    </aside>

    <div class="admin-main-wrapper">

        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="btn-sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search articles, categories, members..." aria-label="Search">
                </div>
            </div>

            <div class="topbar-right">
                <a href="../index.php" target="_blank" class="btn-view-site" title="View Public Website">
                    <i class="fa-solid fa-globe"></i>
                    <span>View Website</span>
                </a>

                <div class="dropdown">
                    <button class="topbar-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications" title="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="badge-dot"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2 mt-2" style="width:280px;">
                        <li class="px-2 py-1 fw-bold border-bottom mb-2 text-success">Notifications</li>
                        <li><a class="dropdown-item rounded small text-wrap py-2" href="verification_requests.php">New member verification request pending</a></li>
                        <li><a class="dropdown-item rounded small text-wrap py-2" href="comments.php">3 new article comments awaiting moderation</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="profile-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($adminUser, 0, 1)) ?>
                        </div>
                        <div class="profile-info">
                            <div class="name"><?= htmlspecialchars($adminUser) ?></div>
                            <div class="role"><?= is_admin() ? 'Administrator' : 'Press Agent' ?></div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-muted ms-1 small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mt-2" style="width:200px;">
                        <li><a class="dropdown-item rounded py-2" href="change_password.php"><i class="fa-solid fa-key me-2 text-warning"></i>Change Password</a></li>
                        <li><a class="dropdown-item rounded py-2" href="settings.php"><i class="fa-solid fa-sliders me-2 text-info"></i>Account Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded py-2 text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="admin-content">
