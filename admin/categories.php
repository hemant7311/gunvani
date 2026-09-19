<?php
$activePage = 'categories';
$pageTitle = 'Manage Categories - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: categories.php');
    exit;
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Manage Categories</h1>
        <p>Add, edit, or delete city news categories and topic channels.</p>
    </div>
    <div>
        <a href="add_category.php" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i>Add Category
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <div class="table-responsive"><table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Category Name</th>
                        <th>URL Slug</th>
                        <th>Description</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categories) === 0): ?>
                        <tr><td colspan="5" class="text-center text-muted py-5">No categories found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $index => $cat): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><i class="fa-solid fa-folder me-2 text-success"></i><?= htmlspecialchars($cat['name']) ?></div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded text-success"><?= htmlspecialchars($cat['slug']) ?></code>
                            </td>
                            <td class="text-muted small">
                                <?= htmlspecialchars($cat['description'] ?: 'No description provided.') ?>
                            </td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <a href="edit_category.php?id=<?= $cat['id'] ?>" class="btn-icon-action btn-edit" title="Edit Category" aria-label="Edit Category">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="categories.php?delete=<?= $cat['id'] ?>" class="btn-icon-action btn-delete" title="Delete Category" aria-label="Delete Category" onclick="return confirmDeleteModal(this.href)">
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
