<?php
$activePage = 'pages';
$pageTitle = 'Manage Pages - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';

// Static site pages list
$pages = [
    ['id' => 1, 'title' => 'About Us', 'slug' => 'about-us', 'status' => 'published', 'updated' => '2026-09-10'],
    ['id' => 2, 'title' => 'Contact Us', 'slug' => 'contact', 'status' => 'published', 'updated' => '2026-09-12'],
    ['id' => 3, 'title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'status' => 'published', 'updated' => '2026-08-15'],
    ['id' => 4, 'title' => 'Terms of Use', 'slug' => 'terms-of-use', 'status' => 'published', 'updated' => '2026-08-15'],
    ['id' => 5, 'title' => 'Member Verification', 'slug' => 'verification', 'status' => 'published', 'updated' => '2026-09-14'],
];
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Manage Pages</h1>
        <p>Create and edit static informational pages on Gunvani News.</p>
    </div>
    <div>
        <button class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i>Add New Page
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Page Title</th>
                        <th>URL Slug</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $p['id'] ?></td>
                        <td>
                            <div class="fw-bold text-dark"><i class="fa-solid fa-file-lines me-2 text-success"></i><?= htmlspecialchars($p['title']) ?></div>
                        </td>
                        <td>
                            <code class="bg-light px-2 py-1 rounded text-success">/<?= htmlspecialchars($p['slug']) ?>.html</code>
                        </td>
                        <td>
                            <span class="badge-status published">Published</span>
                        </td>
                        <td class="small text-muted">
                            <?= date('M j, Y', strtotime($p['updated'])) ?>
                        </td>
                        <td>
                            <div class="action-btn-group justify-content-center">
                                <a href="../<?= htmlspecialchars($p['slug']) ?>.html" target="_blank" class="btn-icon-action btn-view" title="Preview Page" aria-label="Preview Page">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="#" class="btn-icon-action btn-edit" title="Edit Page" aria-label="Edit Page">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <a href="#" class="btn-icon-action btn-delete" title="Delete Page" aria-label="Delete Page" onclick="return confirmDeleteModal('#')">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
