<?php
$activePage = 'cities';
$pageTitle = 'Manage Cities - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';

// Fetch cities from categories table or city list
$cities = $pdo->query("SELECT * FROM cities ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Manage Cities</h1>
        <p>Add, edit or delete city locations for localized news coverage.</p>
    </div>
    <div>
        <a href="add_category.php" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i>Add City
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
                        <th>City Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($cities) === 0): ?>
                        <tr><td colspan="6" class="text-center text-muted py-5">No city locations found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($cities as $index => $city): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><i class="fa-solid fa-location-dot me-2 text-success"></i><?= htmlspecialchars($city['name']) ?></div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded text-success"><?= htmlspecialchars($city['slug']) ?></code>
                            </td>
                            <td class="text-muted small">
                                <?= htmlspecialchars($city['description'] ?? 'City headlines and local announcements.') ?>
                            </td>
                            <td>
                                <span class="badge-status active">Active</span>
                            </td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <a href="edit_category.php?id=<?= $city['id'] ?>" class="btn-icon-action btn-edit" title="Edit City" aria-label="Edit City">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="categories.php?delete=<?= $city['id'] ?>" class="btn-icon-action btn-delete" title="Delete City" aria-label="Delete City" onclick="return confirmDeleteModal(this.href)">
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
