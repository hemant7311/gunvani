<?php
$activePage = 'add_members';
$pageTitle = 'Add Member - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';
require_once __DIR__ . '/../includes/upload.php';

$error = '';
$success = '';

// Generate next suggested Member ID
$lastId = (int)$pdo->query("SELECT MAX(id) FROM members")->fetchColumn();
$suggestedMemberId = 'GN-' . str_pad($lastId + 1001, 4, '0', STR_PAD_LEFT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = trim($_POST['member_id'] ?? '');
    if (empty($member_id)) {
        $member_id = $suggestedMemberId;
    }
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $doi = $_POST['doi'] ?? date('Y-m-d');
    $doe = $_POST['doe'] ?? date('Y-m-d', strtotime('+1 year'));
    $location = trim($_POST['location'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $blood = $_POST['blood'] ?? '';
    $designation = trim($_POST['designation'] ?? 'Press Reporter');
    $status = $_POST['status'] ?? 'approved';

    if (empty($name) || empty($mobile)) {
        $error = "Name and Mobile Number are required fields.";
    } else {
        // Check duplicate member_id
        $chk = $pdo->prepare("SELECT id FROM members WHERE member_id = ?");
        $chk->execute([$member_id]);
        if ($chk->fetch()) {
            $error = "Member ID '$member_id' is already assigned to another member.";
        } else {
            $photoFilename = '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upRes = secure_upload_file($_FILES['photo'], __DIR__ . '/uploads/', ['image']);
                if ($upRes['success']) {
                    $photoFilename = $upRes['filename'];
                } else {
                    $error = "Photo upload error: " . $upRes['error'];
                }
            }

            if (empty($error)) {
                $stmt = $pdo->prepare("INSERT INTO members 
                    (member_id, name, dob, doi, doe, mobile, address, location, blood_group, designation, photo, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $member_id, $name, $dob ?: null, $doi ?: null, $doe ?: null, 
                    $mobile, $address, $location, $blood, $designation, $photoFilename, $status
                ]);

                header("Location: members.php?success=added");
                exit();
            }
        }
    }
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Add New Member</h1>
        <p>Register a new press reporter, journalist, or staff member for credential verification.</p>
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
                <h5><i class="fa-solid fa-user-plus me-2 text-success"></i>Member Credentials Form</h5>
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
                            <label class="form-label font-weight-bold">Member ID <small class="text-muted">(Auto-generated)</small></label>
                            <input type="text" name="member_id" class="form-control form-control-admin fw-bold text-success" value="<?= htmlspecialchars($_POST['member_id'] ?? $suggestedMemberId) ?>" required>
                        </div>
                        <div class="col-md-6">
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
                                <option value="approved">Approved (Verified)</option>
                                <option value="pending">Pending Review</option>
                                <option value="rejected">Rejected</option>
                                <option value="expired">Expired</option>
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
