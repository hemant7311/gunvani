<?php
require_once __DIR__ . '/db.php';

// Handle CSV Export
if (isset($_GET['export'])) {
    $rows = $pdo->query("SELECT id, email, status, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Email Address', 'Status', 'Subscribed At']);
    foreach ($rows as $r) {
        fputcsv($output, $r);
    }
    fclose($output);
    exit;
}

$activePage = 'newsletter';
$pageTitle = 'Newsletter Subscribers - Gunvani News Admin';

// Handle Delete Request
$message = '';
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?")->execute([$delId]);
    $message = "Subscriber deleted successfully.";
}

require_once 'admin_header.php';

// Fetch newsletter subscribers list
$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Newsletter Subscribers</h1>
        <p>Manage daily news subscribers and export mailing lists.</p>
    </div>
    <div>
        <a href="newsletter.php?export=1" class="btn btn-outline-success px-4 py-2 font-weight-bold">
            <i class="fa-solid fa-file-export me-2"></i>Export List (CSV)
        </a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Subscriber Email</th>
                        <th>Status</th>
                        <th>Subscribed Date</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-5">No newsletter subscribers found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($subscribers as $idx => $sub): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $idx + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><i class="fa-solid fa-envelope me-2 text-success"></i><?= htmlspecialchars($sub['email']) ?></div>
                            </td>
                            <td>
                                <span class="badge-status <?= htmlspecialchars($sub['status']) ?>">
                                    <?= ucfirst($sub['status']) ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <?= date('M j, Y h:i A', strtotime($sub['subscribed_at'])) ?>
                            </td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <a href="newsletter.php?delete=<?= $sub['id'] ?>" class="btn-icon-action btn-delete" title="Delete Subscriber" onclick="return confirm('Delete this subscriber?');">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
