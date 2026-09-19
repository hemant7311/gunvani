<?php
$activePage = 'agents';
$pageTitle = 'Add New Press Agent - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';
require_admin(); // Server-side admin role check

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = strtolower(trim($_POST['username'] ?? ''));
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if (empty($name) || empty($username) || empty($password)) {
        $error = "Name, Username, and Password are required fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check duplicate username
        $chk = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $chk->execute([$username]);
        if ($chk->fetch()) {
            $error = "Username '$username' is already taken. Please choose another.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, password_hash, role, mobile, status) VALUES (?, ?, ?, ?, ?, 'agent', ?, ?)");
            $stmt->execute([$name, $username, $email, $hash, $hash, $mobile, $status]);
            
            header("Location: agents.php?success=1");
            exit();
        }
    }
}
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Create Press Agent</h1>
        <p>Add a new press agent account for news submissions and reporting.</p>
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
                <h5><i class="fa-solid fa-user-plus me-2 text-success"></i>Agent Account Credentials</h5>
            </div>
            <div class="admin-card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 px-3 mb-4 rounded-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-admin" required placeholder="e.g. Rahul Sharma" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control form-control-admin" required placeholder="e.g. agent_rahul" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-admin" placeholder="rahul@gunvani.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control form-control-admin" placeholder="+91 9876543210" value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-admin" required placeholder="Min 6 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Account Status</label>
                            <select name="status" class="form-select form-control-admin">
                                <option value="active">Active (Can Login)</option>
                                <option value="inactive">Inactive (Disabled)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="agents.php" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold">
                            <i class="fa-solid fa-save me-1"></i>Save Agent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
