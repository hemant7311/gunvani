<?php
$activePage = 'agents';
$pageTitle = 'Manage Press Agents - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';
require_admin(); // Server-side admin role check

$success = '';
$error = '';

// Handle Status Toggle Action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $agentId = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'toggle') {
        $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active', 'inactive', 'active') WHERE id = ? AND role = 'agent'");
        $stmt->execute([$agentId]);
        $success = "Agent status updated successfully.";
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'agent'");
        $stmt->execute([$agentId]);
        $success = "Agent deleted successfully.";
    }
}

// Fetch all agents
$agents = $pdo->query("SELECT * FROM users WHERE role = 'agent' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Manage Press Agents</h1>
        <p>Create and manage authorized press agents, reporters, and news contributors.</p>
    </div>
    <div>
        <a href="add_agent.php" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
            <i class="fa-solid fa-user-plus me-2"></i>Create New Agent
        </a>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-4 rounded-3" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        <div><?= htmlspecialchars($success) ?></div>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Agent Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th style="width:160px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($agents) === 0): ?>
                        <tr><td colspan="8" class="text-center text-muted py-5">No press agents registered yet. Click "Create New Agent" to add one.</td></tr>
                    <?php else: ?>
                        <?php foreach ($agents as $index => $row): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                <span class="badge bg-light text-muted border">Press Agent</span>
                            </td>
                            <td><code><?= htmlspecialchars($row['username']) ?></code></td>
                            <td class="small text-muted"><?= htmlspecialchars($row['email'] ?: 'N/A') ?></td>
                            <td class="small fw-semibold"><?= htmlspecialchars($row['mobile'] ?: 'N/A') ?></td>
                            <td>
                                <?php if ($row['status'] === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <a href="edit_agent.php?id=<?= $row['id'] ?>" class="btn-icon-action btn-edit" title="Edit Agent / Reset Password">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="agents.php?action=toggle&id=<?= $row['id'] ?>" class="btn-icon-action btn-view" title="Toggle Status">
                                        <i class="fa-solid fa-power-off"></i>
                                    </a>
                                    <a href="agents.php?action=delete&id=<?= $row['id'] ?>" class="btn-icon-action btn-delete" title="Delete Agent" onclick="return confirm('Are you sure you want to delete this press agent?');">
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
