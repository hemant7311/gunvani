<?php
$activePage = 'contact_messages';
$pageTitle = 'Contact Messages - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

$message = '';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$delId]);
    $message = "Contact message deleted successfully.";
}

// Handle Mark Read / Unread
if (isset($_GET['toggle_read'])) {
    $toggleId = (int)$_GET['toggle_read'];
    $pdo->prepare("UPDATE contact_messages SET is_read = IF(is_read=1, 0, 1) WHERE id = ?")->execute([$toggleId]);
    header("Location: contact_messages.php");
    exit;
}

// Fetch Messages from Database
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Contact Messages</h1>
        <p>View, read, and manage user inquiries sent via the Contact Us page.</p>
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
            <div class="table-responsive"><table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Sender Name & Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message Content</th>
                        <th>Date Received</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">No contact messages received yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $idx => $msg): ?>
                        <tr class="<?= empty($msg['is_read']) ? 'table-warning fw-semibold' : '' ?>">
                            <td class="fw-bold text-muted"><?= $idx + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($msg['name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($msg['email']) ?></div>
                            </td>
                            <td class="small text-dark"><?= htmlspecialchars($msg['phone'] ?: 'N/A') ?></td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($msg['subject'] ?: 'General Inquiry') ?></span>
                            </td>
                            <td style="max-width:320px;">
                                <div class="small text-dark text-wrap"><?= htmlspecialchars($msg['message']) ?></div>
                            </td>
                            <td class="small text-muted">
                                <?= date('M j, Y h:i A', strtotime($msg['created_at'])) ?>
                            </td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <a href="contact_messages.php?toggle_read=<?= $msg['id'] ?>" class="btn-icon-action btn-edit" title="<?= empty($msg['is_read']) ? 'Mark as Read' : 'Mark as Unread' ?>">
                                        <i class="fa-solid <?= empty($msg['is_read']) ? 'fa-envelope-open' : 'fa-envelope' ?>"></i>
                                    </a>
                                    <a href="contact_messages.php?delete=<?= $msg['id'] ?>" class="btn-icon-action btn-delete" title="Delete Message" onclick="return confirm('Delete this contact message?');">
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
