<?php
$activePage = 'videos';
$pageTitle = 'Manage Video News - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: news.php');
    exit;
}

// Fetch Filter Values
$search = trim($_GET['search'] ?? '');
$catFilter = (int) ($_GET['category_id'] ?? 0);
$statusFilter = trim($_GET['status'] ?? '');

// Build Query
$query = "SELECT a.*, c.name AS category_name, ct.name AS city_name
          FROM articles a
          LEFT JOIN categories c ON a.category_id = c.id
          LEFT JOIN cities ct ON a.city_id = ct.id
          WHERE (a.video_url IS NOT NULL AND a.video_url != '') OR (a.video_file IS NOT NULL AND a.video_file != '')";
$params = [];

if ($search !== '') {
    $query .= " AND (a.title LIKE ? OR a.summary LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($catFilter > 0) {
    $query .= " AND a.category_id = ?";
    $params[] = $catFilter;
}
if ($statusFilter !== '') {
    $query .= " AND a.status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY a.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for dropdown filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

function articleThumb($image) {
    if (!$image) return '../images/placeholder/first8.jpg';
    if (preg_match('#^(uploads/|images/)#', $image)) return '../' . $image;
    return '../uploads/news/' . $image;
}
require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Manage Video News</h1>
        <p>View, edit, or delete published video news reports.</p>
    </div>
    <div>
        <a href="add_news.php" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
            <i class="fa-solid fa-video me-2"></i>Add Video News
        </a>
    </div>
</div>

<!-- Search & Filter Toolbar -->
<form method="GET" class="toolbar-card">
    <div class="filter-group flex-grow-1">
        <div class="topbar-search" style="width:260px;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search articles by title..." aria-label="Search articles">
        </div>

        <select name="category_id" class="form-select-admin">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $catFilter == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="status" class="form-select-admin">
            <option value="">All Status</option>
            <option value="published" <?= $statusFilter === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draft</option>
        </select>

        <button type="submit" class="btn btn-sm btn-outline-success px-3 py-2">
            <i class="fa-solid fa-filter me-1"></i>Filter
        </button>
        
        <?php if ($search || $catFilter || $statusFilter): ?>
            <a href="news.php" class="btn btn-sm btn-outline-secondary px-3 py-2">Reset</a>
        <?php endif; ?>
    </div>

    <div class="text-muted small">
        Showing <strong><?= count($articles) ?></strong> articles
    </div>
</form>

<!-- Data Table Card -->
<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <div class="table-responsive"><table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:40px;"><input type="checkbox" aria-label="Select all"></th>
                        <th style="width:50px;">#</th>
                        <th style="width:75px;">Thumb</th>
                        <th>Title & Summary</th>
                        <th>Category / City</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th class="text-center" style="width:110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($articles) === 0): ?>
                        <tr><td colspan="9" class="text-center text-muted py-5">No news articles found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($articles as $index => $article): ?>
                        <tr>
                            <td><input type="checkbox" aria-label="Select row"></td>
                            <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                            <td>
                                <?php if (!empty($article['video_file']) && empty($article['image'])): ?>
                                    <video src="../uploads/videos/<?= htmlspecialchars($article['video_file']) ?>" class="table-thumb" muted></video>
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars(articleThumb($article['image'])) ?>" alt="Thumb" class="table-thumb" onerror="this.src='../images/placeholder/first8.jpg'">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark mb-1">
                                    <?= htmlspecialchars($article['title']) ?>
                                    <?php if (!empty($article['is_featured'])): ?>
                                        <span class="badge bg-primary ms-1" style="font-size:0.65rem;">FEATURED</span>
                                    <?php endif; ?>
                                    <?php if (!empty($article['is_breaking'])): ?>
                                        <span class="badge bg-danger ms-1" style="font-size:0.65rem;">BREAKING</span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted text-truncate" style="max-width:360px;"><?= htmlspecialchars($article['summary']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($article['category_name'] ?: 'General') ?></span>
                                <?php if (!empty($article['city_name'])): ?>
                                    <span class="badge bg-info text-dark ms-1"><?= htmlspecialchars($article['city_name']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status <?= htmlspecialchars($article['status']) ?>">
                                    <?= ucfirst($article['status']) ?>
                                </span>
                            </td>
                            <td class="small fw-bold text-muted">
                                <i class="fa-regular fa-eye me-1"></i><?= number_format($article['views'] ?? 0) ?>
                            </td>
                            <td class="small text-muted">
                                <?= date('M j, Y', strtotime($article['published_at'])) ?>
                            </td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <a href="../article/<?= htmlspecialchars($article['slug']) ?>" target="_blank" class="btn-icon-action btn-view" title="View Article" aria-label="View Article">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="edit_news.php?id=<?= $article['id'] ?>" class="btn-icon-action btn-edit" title="Edit Article" aria-label="Edit Article">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="news.php?delete=<?= $article['id'] ?>" class="btn-icon-action btn-delete" title="Delete Article" aria-label="Delete Article" onclick="return confirm('Delete this news article?');">
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

<?php require_once 'admin_footer.php'; ?>
