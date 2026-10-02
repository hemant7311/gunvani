<?php
$activePage = 'add_members';
$pageTitle = 'Add Member - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';
require_once __DIR__ . '/../includes/upload.php';

$error = '';
$success = '';

// Generate next suggested Member ID safely
try {
    $lastIdQuery = $pdo->query("SELECT MAX(id) FROM members");
    $lastId = (int)$lastIdQuery->fetchColumn();
    $suggestedMemberId = 'GN-' . str_pad($lastId + 1001, 4, '0', STR_PAD_LEFT);
} catch (Exception $e) {
    error_log("add_members.php error: " . $e->getMessage());
    $error = "Database connection error. Please contact administrator.";
    $suggestedMemberId = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $member_id = trim($_POST['member_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $designation = trim($_POST['designation'] ?? 'Press Reporter');
    $dob = trim($_POST['dob'] ?? '');
    $doi = trim($_POST['doi'] ?? date('Y-m-d'));
    $doe = trim($_POST['doe'] ?? date('Y-m-d', strtotime('+1 year')));
    $location = trim($_POST['location'] ?? '');
    $blood = trim($_POST['blood'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status = trim($_POST['status'] ?? 'approved');
    $rni_no = trim($_POST['rni_no'] ?? '');

    if (empty($member_id) || empty($name) || empty($mobile)) {
        $error = "Member ID, Name, and Mobile Number are required.";
    } else {
        try {
            // Check duplicate member_id
            $chk = $pdo->prepare("SELECT id FROM members WHERE member_id = ?");
            $chk->execute([$member_id]);
            if ($chk->fetch()) {
                $error = "Member ID '$member_id' already exists. Please choose a different one.";
            }
        } catch (Exception $e) {
            error_log("add_members.php duplicate check error: " . $e->getMessage());
            $error = "Database query failed.";
        }
    }

    if (empty($error)) {
        $photoFilename = null;
        if (!empty($_FILES['photo']['name'])) {
            $upRes = secure_upload_file($_FILES['photo'], __DIR__ . '/uploads/', ['image']);
            if ($upRes['success']) {
                $photoFilename = $upRes['filename'];
            } else {
                $error = "Photo upload error: " . $upRes['error'];
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO members 
                    (member_id, name, dob, doi, doe, mobile, address, location, blood_group, designation, photo, status, rni_no) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $member_id, 
                    $name, 
                    $dob ?: null, 
                    $doi ?: null, 
                    $doe ?: null, 
                    $mobile, 
                    $address, 
                    $location, 
                    $blood, 
                    $designation, 
                    $photoFilename, 
                    $status, 
                    $rni_no
                ]);
                
                header("Location: members.php?success=added");
                exit();
            } catch (Exception $e) {
                error_log("add_members.php INSERT error: " . $e->getMessage());
                $error = "Failed to save member. Please try again later.";
            }
        }
    }
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Add New Member</h1>
        <p>Register a new press reporter or staff member.</p>
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
                <h5><i class="fa-solid fa-user-plus me-2 text-primary"></i>Member Information</h5>
            </div>
            <div class="admin-card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 px-3 mb-4 rounded-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Member ID <small class="text-muted">(Auto-generated)</small></label>
                            <input type="text" name="member_id" class="form-control form-control-admin fw-bold text-success" value="<?= htmlspecialchars($_POST['member_id'] ?? $suggestedMemberId) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">RNI Number</label>
                            <input type="text" name="rni_no" class="form-control form-control-admin" placeholder="e.g. UPHIN/2023/12345" value="<?= htmlspecialchars($_POST['rni_no'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-admin" required placeholder="e.g. Hemant Rathore" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control form-control-admin" required placeholder="+91 7088824006" value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Designation</label>
                            <input type="text" name="designation" class="form-control form-control-admin" placeholder="e.g. Bureau Chief / Senior Correspondent" value="<?= htmlspecialchars($_POST['designation'] ?? 'Press Reporter') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control form-control-admin" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Date of Issue</label>
                            <input type="date" name="doi" class="form-control form-control-admin" value="<?= htmlspecialchars($_POST['doi'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Date of Expiry</label>
                            <input type="date" name="doe" class="form-control form-control-admin" value="<?= htmlspecialchars($_POST['doe'] ?? date('Y-m-d', strtotime('+1 year'))) ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">City / Location</label>
                            <input type="text" name="location" class="form-control form-control-admin" placeholder="e.g. Agra, UP" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Blood Group</label>
                            <select name="blood" class="form-select form-control-admin">
                                <option value="">Select Blood Group</option>
                                <?php foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg): ?>
                                    <option value="<?= $bg ?>" <?= ($_POST['blood'] ?? '') === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Full Address</label>
                        <input type="text" name="address" class="form-control form-control-admin" placeholder="e.g. Civil Lines, Agra" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Member Photo</label>
                            <input type="file" name="photo" class="form-control form-control-admin" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Verification Status</label>
                            <select name="status" class="form-select form-control-admin">
                                <option value="approved" <?= ($_POST['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved (Verified)</option>
                                <option value="pending" <?= ($_POST['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                                <option value="rejected" <?= ($_POST['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="expired" <?= ($_POST['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="members.php" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 font-weight-bold">
                            <i class="fa-solid fa-save me-1"></i>Save Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
