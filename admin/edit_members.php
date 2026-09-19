<?php
$activePage = 'members';
$pageTitle = 'Edit Member - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';
require_once __DIR__ . '/../includes/upload.php';

$id = (int)($_GET['id'] ?? 0);
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
    $member_id = trim($_POST['member_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $doi = $_POST['doi'] ?? null;
    $doe = $_POST['doe'] ?? null;
    $location = trim($_POST['location'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $blood = $_POST['blood'] ?? '';
    $designation = trim($_POST['designation'] ?? 'Press Reporter');
    $status = $_POST['status'] ?? 'approved';
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');

    if (empty($name) || empty($mobile) || empty($member_id)) {
        $error = "Member ID, Name, and Mobile Number are required fields.";
    } else {
        // Check duplicate member_id for other members
        $chk = $pdo->prepare("SELECT id FROM members WHERE member_id = ? AND id != ?");
        $chk->execute([$member_id, $id]);
        if ($chk->fetch()) {
            $error = "Member ID '$member_id' is already assigned to another member.";
        } else {
            $photoFilename = $member['photo'];
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upRes = secure_upload_file($_FILES['photo'], __DIR__ . '/uploads/', ['image']);
                if ($upRes['success']) {
                    $photoFilename = $upRes['filename'];
                } else {
                    $error = "Photo upload error: " . $upRes['error'];
                }
            }

            if (empty($error)) {
                $upStmt = $pdo->prepare("UPDATE members SET 
                    member_id = ?, name = ?, dob = ?, doi = ?, doe = ?, mobile = ?, 
                    address = ?, location = ?, blood_group = ?, designation = ?, 
                    photo = ?, status = ?, rejection_reason = ? WHERE id = ?");
                
                $upStmt->execute([
                    $member_id, $name, $dob ?: null, $doi ?: null, $doe ?: null, 
                    $mobile, $address, $location, $blood, $designation, 
                    $photoFilename, $status, $status === 'rejected' ? $rejection_reason : null, $id
                ]);

                header("Location: members.php?success=status_updated");
                exit();
            }
        }
    }
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Edit Member Details</h1>
        <p>Update credentials for <strong><?= htmlspecialchars($member['name']) ?></strong> (<code><?= htmlspecialchars($member['member_id']) ?></code>).</p>
    </div>
    <div>
        <a href="members.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Members
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-user-pen me-2 text-primary"></i>Member Information</h5>
            </div>
            <div class="admin-card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 px-3 mb-4 rounded-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Member ID <span class="text-danger">*</span></label>
                            <input type="text" name="member_id" class="form-control form-control-admin fw-bold text-success" value="<?= htmlspecialchars($member['member_id']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-admin" required value="<?= htmlspecialchars($member['name']) ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control form-control-admin" required value="<?= htmlspecialchars($member['mobile']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Designation</label>
                            <input type="text" name="designation" class="form-control form-control-admin" value="<?= htmlspecialchars($member['designation']) ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control form-control-admin" value="<?= htmlspecialchars($member['dob'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Date of Issue</label>
                            <input type="date" name="doi" class="form-control form-control-admin" value="<?= htmlspecialchars($member['doi'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Date of Expiry</label>
                            <input type="date" name="doe" class="form-control form-control-admin" value="<?= htmlspecialchars($member['doe'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">City / Location</label>
                            <input type="text" name="location" class="form-control form-control-admin" value="<?= htmlspecialchars($member['location'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Blood Group</label>
                            <select name="blood" class="form-select form-control-admin">
                                <option value="">Select Blood Group</option>
                                <?php foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg): ?>
                                    <option value="<?= $bg ?>" <?= ($member['blood_group'] ?? '') === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Full Address</label>
                        <input type="text" name="address" class="form-control form-control-admin" value="<?= htmlspecialchars($member['address'] ?? '') ?>">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Update Photo (Optional)</label>
                            <input type="file" name="photo" class="form-control form-control-admin" accept="image/*">
                            <?php if (!empty($member['photo'])): ?>
                                <small class="text-muted">Current: <?= htmlspecialchars($member['photo']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Verification Status</label>
                            <select name="status" class="form-select form-control-admin">
                                <option value="approved" <?= ($member['status'] ?? '') === 'approved' || ($member['status'] ?? '') === 'active' ? 'selected' : '' ?>>Approved (Verified)</option>
                                <option value="pending" <?= ($member['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                                <option value="rejected" <?= ($member['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="expired" <?= ($member['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="members.php" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold">
                            <i class="fa-solid fa-save me-1"></i>Update Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
