<?php
$activePage = 'change_password';
$pageTitle = 'Change Password - Gunvani News Admin';

require_once 'auth_check.php';
require_once 'db.php';

$success = '';
$error = '';
$username = $_SESSION['admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($newPassword === '' || $confirmPassword === '' || $currentPassword === '') {
        $error = 'All fields are required.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirm password do not match.';
    } else {
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = 'User account not found.';
        } else {
            $passwordValid = false;
            $hash = $user['password_hash'] ?? '';
            
            if (password_verify($currentPassword, $hash)) {
                $passwordValid = true;
            } elseif ($currentPassword === $hash) {
                // Fallback for plaintext (if any)
                $passwordValid = true;
            }

            if (!$passwordValid) {
                $error = 'Current password is incorrect.';
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE username = ?');
                $updateStmt->execute([$hashedPassword, $username]);
                
                // Keep admins table in sync if it exists
                try {
                    $aUpdate = $pdo->prepare('UPDATE admins SET password = ? WHERE username = ?');
                    $aUpdate->execute([$hashedPassword, $username]);
                } catch (Exception $e) {}
                
                $success = 'Password updated successfully!';
            }
        }
    }
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Change Admin Password</h1>
        <p>Update security credentials for admin account (<?= htmlspecialchars($username) ?>).</p>
    </div>
    <div>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-key me-2 text-warning"></i>Security Credentials</h5>
            </div>
            <div class="admin-card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-4" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        <div><?= htmlspecialchars($success) ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-4" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="current_password" class="form-label font-weight-bold">Current Password</label>
                        <input type="password" class="form-control form-control-admin" id="current_password" name="current_password" required placeholder="Enter current password">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label font-weight-bold">New Password</label>
                        <input type="password" class="form-control form-control-admin" id="new_password" name="new_password" required placeholder="Enter new password (min 6 chars)">
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label font-weight-bold">Confirm New Password</label>
                        <input type="password" class="form-control form-control-admin" id="confirm_password" name="confirm_password" required placeholder="Re-enter new password">
                    </div>
                    <button type="submit" class="btn btn-success px-4 py-2 font-weight-bold">
                        <i class="fa-solid fa-shield-halved me-2"></i>Update Password Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
