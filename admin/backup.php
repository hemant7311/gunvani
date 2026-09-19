<?php
$activePage = 'backup';
$pageTitle = 'Backup & Restore - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';

// Backup files list
$backups = [
    ['id' => 1, 'filename' => 'gunvani_backup_2026-09-14.sql', 'size' => '5.4 MB', 'date' => '2026-09-14 02:38'],
    ['id' => 2, 'filename' => 'gunvani_backup_2026-09-07.sql', 'size' => '5.2 MB', 'date' => '2026-09-07 04:15'],
];
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Backup & Restore</h1>
        <p>Create or restore database backups of your website.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="admin-card h-100 mb-0">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-database me-2 text-success"></i>Create New Backup</h5>
            </div>
            <div class="admin-card-body">
                <p class="text-muted small">Generate a complete SQL backup dump of all articles, categories, members, and site settings.</p>
                <button type="button" class="btn btn-success px-4 py-2 font-weight-bold">
                    <i class="fa-solid fa-cloud-arrow-down me-2"></i>Create Database Backup Now
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="admin-card h-100 mb-0">
            <div class="admin-card-header">
                <h5><div class="fa-solid fa-rotate-left me-2 text-warning"></div>Restore Backup</h5>
            </div>
            <div class="admin-card-body">
                <p class="text-muted small">Restore your website database from a previously generated .sql backup file.</p>
                <input type="file" class="form-control form-control-admin mb-3">
                <button type="button" class="btn btn-outline-warning px-4 py-2 font-weight-bold">
                    <i class="fa-solid fa-upload me-2"></i>Restore Backup File
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Available Backups Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5><i class="fa-solid fa-list me-2 text-info"></i>Available Backups</h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Backup Filename</th>
                        <th>File Size</th>
                        <th>Created Date</th>
                        <th style="width:140px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $b): ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $b['id'] ?></td>
                        <td>
                            <div class="fw-bold text-dark"><i class="fa-solid fa-file-code me-2 text-primary"></i><?= htmlspecialchars($b['filename']) ?></div>
                        </td>
                        <td class="small text-muted"><?= $b['size'] ?></td>
                        <td class="small text-muted"><?= $b['date'] ?></td>
                        <td>
                            <div class="action-btn-group justify-content-center">
                                <a href="../u831226226_gunvani.sql" download class="btn-icon-action btn-download" title="Download Backup" aria-label="Download Backup">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <a href="#" class="btn-icon-action btn-delete" title="Delete Backup" aria-label="Delete Backup" onclick="return confirmDeleteModal('#')">
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
