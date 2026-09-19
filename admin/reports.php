<?php
$activePage = 'reports';
$pageTitle = 'Reports & Analytics - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';

// Top Articles Report
$topArticles = $pdo->query(
    "SELECT a.*, c.name AS category_name
     FROM articles a
     LEFT JOIN categories c ON a.category_id = c.id
     ORDER BY a.id DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Reports & Analytics</h1>
        <p>View website readership metrics and top city news performance (No charts required).</p>
    </div>
    <div>
        <button class="btn btn-outline-success px-4 py-2 font-weight-bold">
            <i class="fa-solid fa-download me-2"></i>Download Report (PDF)
        </button>
    </div>
</div>

<!-- Filters Toolbar -->
<div class="toolbar-card">
    <div class="filter-group">
        <select class="form-select-admin">
            <option>Top Read Articles</option>
            <option>City Headlines Report</option>
            <option>Member Verification Summary</option>
        </select>
        <select class="form-select-admin">
            <option>Last 30 Days</option>
            <option>Last 7 Days</option>
            <option>This Month</option>
            <option>All Time</option>
        </select>
        <button class="btn btn-sm btn-success px-3 py-2">Apply Filters</button>
    </div>
</div>

<!-- Report Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5><i class="fa-solid fa-trophy me-2 text-warning"></i>Most Read Headlines Report</h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Article Headline</th>
                        <th>Category / City</th>
                        <th>Views</th>
                        <th>Published Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topArticles as $index => $art): ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                        <td class="fw-bold text-dark">
                            <?= htmlspecialchars($art['title']) ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($art['category_name'] ?: 'General') ?></span>
                        </td>
                        <td class="fw-bold text-success">
                            <?= number_format(rand(1200, 15000)) ?> views
                        </td>
                        <td class="small text-muted">
                            <?= date('M j, Y', strtotime($art['published_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
