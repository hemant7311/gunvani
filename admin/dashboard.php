<?php
$activePage = 'dashboard';
$pageTitle = 'Dashboard - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';

// Fetch KPI statistics from real database
$totalArticles = (int) $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$publishedArticles = (int) $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
$draftArticles = (int) $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'draft'")->fetchColumn();
$totalMembers = (int) $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
$totalCategories = (int) $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Fetch Recent Articles
$recentArticles = $pdo->query(
    "SELECT a.*, c.name AS category_name
     FROM articles a
     LEFT JOIN categories c ON a.category_id = c.id
     ORDER BY a.id DESC
     LIMIT 6"
)->fetchAll(PDO::FETCH_ASSOC);

// Fetch Categories Overview
$categoriesOverview = $pdo->query(
    "SELECT c.*, COUNT(a.id) AS article_count
     FROM categories c
     LEFT JOIN articles a ON c.id = a.category_id
     GROUP BY c.id
     ORDER BY article_count DESC"
)->fetchAll(PDO::FETCH_ASSOC);

function articleThumb($image) {
    if (!$image) return '../images/placeholder/first8.jpg';
    if (preg_match('#^(uploads/|images/)#', $image)) return '../' . $image;
    return '../uploads/news/' . $image;
}
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($adminUser) ?>! Here's what's happening with Gunvani News today.</p>
    </div>
    <div class="badge bg-light text-dark border px-3 py-2 rounded-pill font-weight-bold">
        <i class="fa-regular fa-calendar-days text-success me-2"></i><?= date('F j, Y') ?>
    </div>
</div>

<!-- KPI Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-details">
                <div class="kpi-label">Total Articles</div>
                <div class="kpi-value"><?= number_format($totalArticles) ?></div>
                <div class="kpi-sub text-success"><i class="fa-solid fa-newspaper me-1"></i> All stories</div>
            </div>
            <div class="kpi-icon-badge green">
                <i class="fa-solid fa-newspaper"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-details">
                <div class="kpi-label">Published</div>
                <div class="kpi-value"><?= number_format($publishedArticles) ?></div>
                <div class="kpi-sub text-success"><i class="fa-solid fa-circle-check me-1"></i> Live on portal</div>
            </div>
            <div class="kpi-icon-badge blue">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-details">
                <div class="kpi-label">Drafts</div>
                <div class="kpi-value"><?= number_format($draftArticles) ?></div>
                <div class="kpi-sub text-warning"><i class="fa-solid fa-pen-ruler me-1"></i> In progress</div>
            </div>
            <div class="kpi-icon-badge yellow">
                <i class="fa-solid fa-file-pen"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-details">
                <div class="kpi-label">Total Members</div>
                <div class="kpi-value"><?= number_format($totalMembers) ?></div>
                <div class="kpi-sub text-info"><i class="fa-solid fa-users me-1"></i> Press ID Members</div>
            </div>
            <div class="kpi-icon-badge red">
                <i class="fa-solid fa-id-card"></i>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Layout Grid -->
<div class="row g-4">
    <!-- Main Content Column: Recent Articles Table -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-newspaper me-2 text-success"></i>Recent News Articles</h5>
                <a href="news.php" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="admin-card-body p-0">
                <div class="admin-table-wrap">
                    <div class="table-responsive"><table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Thumbnail</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recentArticles) === 0): ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">No recent articles found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentArticles as $index => $art): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <?php if (!empty($art['video_file']) && empty($art['image'])): ?>
                                            <video src="../uploads/videos/<?= htmlspecialchars($art['video_file']) ?>" class="table-thumb" muted></video>
                                        <?php else: ?>
                                            <img src="<?= htmlspecialchars(articleThumb($art['image'])) ?>" alt="Thumb" class="table-thumb" onerror="this.src='../images/placeholder/first8.jpg'">
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold text-wrap" style="max-width:240px;">
                                        <?= htmlspecialchars($art['title']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($art['category_name'] ?: 'General') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-status <?= htmlspecialchars($art['status']) ?>">
                                            <?= ucfirst($art['status']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('M j, Y', strtotime($art['published_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="action-btn-group">
                                            <a href="../article.php?slug=<?= htmlspecialchars($art['slug']) ?>" target="_blank" class="btn-icon-action btn-view" title="View Article" aria-label="View Article">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a href="edit_news.php?id=<?= $art['id'] ?>" class="btn-icon-action btn-edit" title="Edit Article" aria-label="Edit Article">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                            <a href="news.php?delete=<?= $art['id'] ?>" class="btn-icon-action btn-delete" title="Delete Article" aria-label="Delete Article" onclick="return confirmDeleteModal(this.href)">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side Information Column -->
    <div class="col-lg-4">
        <!-- Quick Actions Card -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            </div>
            <div class="admin-card-body">
                <div class="d-grid gap-2">
                    <a href="add_news.php" class="btn btn-success d-flex align-items-center justify-content-between p-2.5">
                        <span><i class="fa-solid fa-plus me-2"></i>Add New Article</span>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                    <a href="add_members.php" class="btn btn-outline-success d-flex align-items-center justify-content-between p-2.5">
                        <span><i class="fa-solid fa-user-plus me-2"></i>Add New Member</span>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                    <a href="categories.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-2.5">
                        <span><i class="fa-solid fa-layer-group me-2"></i>Manage Categories</span>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                    <a href="media.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-2.5">
                        <span><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Media</span>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Categories Overview Card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-layer-group me-2 text-info"></i>Categories Overview</h5>
                <a href="categories.php" class="small text-success text-decoration-none">Manage</a>
            </div>
            <div class="admin-card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($categoriesOverview as $cat): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <div class="fw-semibold">
                            <i class="fa-solid fa-folder me-2 text-muted"></i><?= htmlspecialchars($cat['name']) ?>
                        </div>
                        <span class="badge bg-success rounded-pill"><?= $cat['article_count'] ?> articles</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
