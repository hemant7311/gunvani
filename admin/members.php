<?php
$activePage = 'members';
$pageTitle = 'Manage Members & Reporters - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

// Handle Delete Member Request
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: members.php?success=deleted");
    exit();
}

$members = $pdo->query("SELECT * FROM members ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

function memberPhoto($photo) {
    if (!$photo) return '../images/placeholder/first8.jpg';
    if (preg_match('#^(images/|uploads/)#', $photo)) return '../' . $photo;
    if (file_exists(__DIR__ . '/uploads/' . $photo)) return 'uploads/' . $photo;
    if (file_exists(__DIR__ . '/../uploads/' . $photo)) return '../uploads/' . $photo;
    return '../images/placeholder/first8.jpg';
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Manage Members Directory</h1>
        <p>Manage press members, journalists, reporters, verify status, and generate PDF ID Cards.</p>
    </div>
    <div>
        <a href="add_members.php" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
            <i class="fa-solid fa-user-plus me-2"></i>Add Member
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-4 rounded-3" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        <div>
            <?php 
                if ($_GET['success'] === 'added') echo "New member added successfully!";
                elseif ($_GET['success'] === 'status_updated') echo "Member verification status updated successfully!";
                elseif ($_GET['success'] === 'deleted') echo "Member record deleted successfully.";
                else echo "Operation completed successfully.";
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <div class="table-responsive"><table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:70px;">Photo</th>
                        <th>Member ID</th>
                        <th>Name & Address</th>
                        <th>Mobile</th>
                        <th>Designation</th>
                        <th>Location</th>
                        <th>Blood Group</th>
                        <th>Status</th>
                        <th style="width:160px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($members) === 0): ?>
                        <tr><td colspan="10" class="text-center text-muted py-5">No members found. Click "Add Member" to create a new member record.</td></tr>
                    <?php else: ?>
                        <?php foreach ($members as $index => $row): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                            <td>
                                <img src="<?= htmlspecialchars(memberPhoto($row['photo'])) ?>" alt="Photo" class="table-thumb rounded-circle" style="width:42px; height:42px; object-fit:cover;" onerror="this.src='../images/placeholder/first8.jpg'">
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded text-success font-weight-bold"><?= htmlspecialchars($row['member_id']) ?></code>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($row['address'] ?: 'N/A') ?></div>
                            </td>
                            <td class="small fw-semibold text-muted">
                                <?= htmlspecialchars($row['mobile'] ?: 'N/A') ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['designation'] ?: 'Press Member') ?></span>
                            </td>
                            <td class="small text-muted">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($row['location'] ?: 'N/A') ?>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($row['blood_group'] ?: 'N/A') ?></span>
                            </td>
                            <td>
                                <?php 
                                    $st = strtolower($row['status'] ?? 'approved');
                                    if ($st === 'approved' || $st === 'active'): 
                                ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i>Approved</span>
                                <?php elseif ($st === 'rejected'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" title="<?= htmlspecialchars($row['rejection_reason'] ?? '') ?>"><i class="fa-solid fa-xmark me-1"></i>Rejected</span>
                                <?php elseif ($st === 'pending'): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i>Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Expired</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btn-group justify-content-center">
                                    <a href="edit_members.php?id=<?= $row['id'] ?>" class="btn-icon-action btn-edit" title="Edit Member">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="approve_member.php?id=<?= $row['id'] ?>" class="btn-icon-action btn-view" title="Verify / Approve / Reject Status">
                                        <i class="fa-solid fa-user-check"></i>
                                    </a>
                                    <a href="generate_id.php?id=<?= $row['id'] ?>" target="_blank" class="btn-icon-action btn-download" title="Download PDF ID Card">
                                        <i class="fa-solid fa-id-card"></i>
                                    </a>
                                    <a href="members.php?delete=<?= $row['id'] ?>" class="btn-icon-action btn-delete" title="Delete Member" onclick="return confirm('Are you sure you want to delete this member?');">
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
