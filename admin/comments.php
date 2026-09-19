<?php
$activePage = 'comments';
$pageTitle = 'Comments Moderation - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';

$message = '';

// Handle Status Updates
if (isset($_GET['approve'])) {
    $cId = (int)$_GET['approve'];
    $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$cId]);
    $message = "Comment approved successfully.";
}
if (isset($_GET['reject'])) {
    $cId = (int)$_GET['reject'];
    $pdo->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?")->execute([$cId]);
    $message = "Comment rejected.";
}
if (isset($_GET['delete'])) {
    $cId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$cId]);
    $message = "Comment deleted.";
}

// Status Filter
$filter = trim($_GET['status'] ?? '');
$query = "SELECT c.*, a.title AS article_title, a.slug AS article_slug 
          FROM comments c 
          LEFT JOIN articles a ON c.article_id = a.id 
          WHERE 1=1";
$params = [];
if (!empty($filter)) {
    $query .= " AND c.status = ?";
    $params[] = $filter;
}
$query .= " ORDER BY c.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Counts
$pendingCount = $pdo->query("SELECT COUNT(*) FROM comments WHERE status='pending'")->fetchColumn();
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Comments Moderation</h1>
        <p>Review, approve, or reject user comments on news articles.</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="toolbar-card">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link <?= empty($filter) ? 'active bg-success text-white' : 'text-muted' ?> py-1 px-3 me-1 rounded-pill" href="comments.php">All Comments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter === 'approved' ? 'active bg-success text-white' : 'text-muted' ?> py-1 px-3 me-1 rounded-pill" href="comments.php?status=approved">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter === 'pending' ? 'active bg-warning text-dark' : 'text-muted' ?> py-1 px-3 me-1 rounded-pill" href="comments.php?status=pending">
                Pending <?= $pendingCount > 0 ? "($pendingCount)" : '' ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter === 'rejected' ? 'active bg-danger text-white' : 'text-muted' ?> py-1 px-3 me-1 rounded-pill" href="comments.php?status=rejected">Rejected</a>
        </li>
    </ul>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <div class="table-responsive"><table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>User Name & Email</th>
                        <th>Comment Text</th>
                        <th>Article</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width:140px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comments)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">No comments found matching filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($comments as $idx => $c): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $idx + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($c['name'] ?: 'Anonymous') ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($c['email'] ?: 'N/A') ?></div>
                            </td>
                            <td style="max-width:300px;">
                                <div class="small text-dark text-wrap"><?= htmlspecialchars($c['comment']) ?></div>
                            </td>
                            <td>
                                <a href="../article/<?= htmlspecialchars($c['article_slug'] ?? '') ?>" target="_blank" class="badge bg-light text-dark border text-wrap text-start text-decoration-none" style="max-width:200px;">
                                    <?= htmlspecialchars($c['article_title'] ?: 'Article #' . $c['article_id']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge-status <?= htmlspecialchars($c['status']) ?>">
                                    <?= ucfirst($c['status']) ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <?= date('M j, Y h:i A', strtotime($c['created_at'])) ?>
                            </td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <?php if ($c['status'] !== 'approved'): ?>
                                        <a href="comments.php?approve=<?= $c['id'] ?>" class="btn-icon-action btn-view" title="Approve Comment">
                                            <i class="fa-solid fa-check text-success"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($c['status'] !== 'rejected'): ?>
                                        <a href="comments.php?reject=<?= $c['id'] ?>" class="btn-icon-action btn-edit" title="Reject Comment">
                                            <i class="fa-solid fa-ban text-warning"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="comments.php?delete=<?= $c['id'] ?>" class="btn-icon-action btn-delete" title="Delete Comment" onclick="return confirm('Delete this comment?');">
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
