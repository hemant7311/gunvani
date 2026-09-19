<?php
$activePage = 'members';
$pageTitle = 'Approve / Reject Member - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header("Location: members.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? 'approved';
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');

    if ($status === 'rejected' && empty($rejection_reason)) {
        $error = "Please provide a rejection reason when rejecting credentials.";
    } else {
        $up = $pdo->prepare("UPDATE members SET status = ?, rejection_reason = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
        $up->execute([$status, $status === 'rejected' ? $rejection_reason : null, $_SESSION['user_id'] ?? 1, $id]);
        
        header("Location: members.php?success=status_updated");
        exit();
    }
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Member Verification Decision</h1>
        <p>Review and update press credentials status for <strong><?= htmlspecialchars($member['name']) ?></strong> (<code><?= htmlspecialchars($member['member_id']) ?></code>).</p>
    </div>
    <div>
        <a href="members.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Members
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-user-check me-2 text-success"></i>Verification Status</h5>
            </div>
            <div class="admin-card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 px-3 mb-4 rounded-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <div class="row g-2 small">
                        <div class="col-6"><strong>Member Name:</strong> <?= htmlspecialchars($member['name']) ?></div>
                        <div class="col-6"><strong>Member ID:</strong> <code><?= htmlspecialchars($member['member_id']) ?></code></div>
                        <div class="col-6"><strong>Designation:</strong> <?= htmlspecialchars($member['designation']) ?></div>
                        <div class="col-6"><strong>Location:</strong> <?= htmlspecialchars($member['location']) ?></div>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="id" value="<?= $member['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Select Status <span class="text-danger">*</span></label>
                        <select name="status" id="statusSelect" class="form-select form-control-admin" onchange="toggleRejectionBox()">
                            <option value="approved" <?= $member['status'] === 'approved' ? 'selected' : '' ?>>Approved (Verified Press Staff)</option>
                            <option value="pending" <?= $member['status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                            <option value="rejected" <?= $member['status'] === 'rejected' ? 'selected' : '' ?>>Rejected (Unverified)</option>
                            <option value="expired" <?= $member['status'] === 'expired' ? 'selected' : '' ?>>Expired Credentials</option>
                        </select>
                    </div>

                    <div class="mb-4" id="rejectionBox" style="display: <?= $member['status'] === 'rejected' ? 'block' : 'none' ?>;">
                        <label class="form-label font-weight-bold text-danger">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control form-control-admin" rows="3" placeholder="Enter reason for rejecting this member credential..."><?= htmlspecialchars($member['rejection_reason'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="members.php" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold">
                            <i class="fa-solid fa-save me-1"></i>Save Verification Decision
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRejectionBox() {
    var status = document.getElementById('statusSelect').value;
    document.getElementById('rejectionBox').style.display = (status === 'rejected') ? 'block' : 'none';
}
</script>

<?php require_once 'admin_footer.php'; ?>
