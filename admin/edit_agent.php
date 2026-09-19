<?php
$activePage = 'agents';
$pageTitle = 'Edit Press Agent - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';
require_admin(); // Server-side admin role check

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'agent'");
$stmt->execute([$id]);
$agent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agent) {
    header("Location: agents.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $newPassword = trim($_POST['new_password'] ?? '');

    if (empty($name)) {
        $error = "Name is a required field.";
    } else {
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                $error = "New password must be at least 6 characters long.";
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $up = $pdo->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, status = ?, password = ?, password_hash = ? WHERE id = ?");
                $up->execute([$name, $email, $mobile, $status, $hash, $hash, $id]);
                $success = "Agent profile and password updated successfully!";
            }
        } else {
            $up = $pdo->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, status = ? WHERE id = ?");
            $up->execute([$name, $email, $mobile, $status, $id]);
            $success = "Agent details updated successfully!";
        }

        // Refresh agent data
        $stmt->execute([$id]);
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Edit Press Agent</h1>
        <p>Update credentials for <strong><?= htmlspecialchars($agent['name']) ?></strong> (<code><?= htmlspecialchars($agent['username']) ?></code>).</p>
    </div>
    <div>
        <a href="agents.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Agents
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-user-pen me-2 text-primary"></i>Update Agent Details</h5>
            </div>
            <div class="admin-card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success py-2 px-3 mb-4 rounded-3" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 px-3 mb-4 rounded-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-admin" required value="<?= htmlspecialchars($agent['name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Username</label>
                            <input type="text" class="form-control form-control-admin bg-light" disabled value="<?= htmlspecialchars($agent['username']) ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-admin" value="<?= htmlspecialchars($agent['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control form-control-admin" value="<?= htmlspecialchars($agent['mobile'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Reset Password (Optional)</label>
                            <input type="password" name="new_password" class="form-control form-control-admin" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Account Status</label>
                            <select name="status" class="form-select form-control-admin">
                                <option value="active" <?= $agent['status'] === 'active' ? 'selected' : '' ?>>Active (Can Login)</option>
                                <option value="inactive" <?= $agent['status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Disabled)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="agents.php" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold">
                            <i class="fa-solid fa-save me-1"></i>Update Agent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
