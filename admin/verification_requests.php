<?php
$activePage = 'verification';
$pageTitle = 'Verification Requests - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';

// Verification requests moderation list
$requests = [
    [
        'id' => 1,
        'member' => 'Ramesh Kumar',
        'email' => 'ramesh@gunvani.com',
        'document' => 'aadhar_card.pdf',
        'status' => 'pending',
        'date' => '2026-09-14'
    ],
    [
        'id' => 2,
        'member' => 'Sunita Patel',
        'email' => 'sunita@gunvani.com',
        'document' => 'press_id.pdf',
        'status' => 'approved',
        'date' => '2026-09-13'
    ],
    [
        'id' => 3,
        'member' => 'Vikash Singh',
        'email' => 'vikash@gunvani.com',
        'document' => 'id_card.pdf',
        'status' => 'pending',
        'date' => '2026-09-12'
    ],
];
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Verification Requests</h1>
        <p>Review and approve press member verification applications and identity documents.</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Member Name</th>
                        <th>Identity Document</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th style="width:140px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $req['id'] ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($req['member']) ?></div>
                            <div class="small text-muted"><?= htmlspecialchars($req['email']) ?></div>
                        </td>
                        <td>
                            <a href="#" class="badge bg-light text-primary border text-decoration-none py-1.5 px-2.5">
                                <i class="fa-solid fa-file-pdf me-1 text-danger"></i><?= htmlspecialchars($req['document']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge-status <?= $req['status'] ?>">
                                <?= ucfirst($req['status']) ?>
                            </span>
                        </td>
                        <td class="small text-muted">
                            <?= date('M j, Y', strtotime($req['date'])) ?>
                        </td>
                        <td>
                            <div class="action-btn-group justify-content-center">
                                <a href="#" class="btn-icon-action btn-view" title="Approve Request" aria-label="Approve Request">
                                    <i class="fa-solid fa-check text-success"></i>
                                </a>
                                <a href="#" class="btn-icon-action btn-delete" title="Reject Request" aria-label="Reject Request">
                                    <i class="fa-solid fa-xmark text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
