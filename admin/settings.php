<?php
$activePage = 'settings';
$pageTitle = 'Site Settings - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Site Settings</h1>
        <p>Manage website settings, preferences, contact info, and admin configurations.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-sliders me-2 text-success"></i>General Configuration</h5>
            </div>
            <div class="admin-card-body">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Website Name</label>
                            <input type="text" class="form-control form-control-admin" value="Gunvani News">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Website URL</label>
                            <input type="text" class="form-control form-control-admin" value="http://localhost:8000">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Admin Email</label>
                            <input type="email" class="form-control form-control-admin" value="admin@gunvani.in">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Timezone</label>
                            <select class="form-select form-select-admin">
                                <option>Asia/Kolkata (IST)</option>
                                <option>UTC</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="commentsSwitch" checked>
                            <label class="form-check-label font-weight-bold" for="commentsSwitch">Enable User Comments on Articles</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="regSwitch" checked>
                            <label class="form-check-label font-weight-bold" for="regSwitch">Allow Member Registration</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="maintSwitch">
                            <label class="form-check-label font-weight-bold text-danger" for="maintSwitch">Maintenance Mode</label>
                        </div>
                    </div>

                    <button type="button" class="btn btn-success px-4 py-2 font-weight-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-shield-halved me-2 text-info"></i>System Information</h5>
            </div>
            <div class="admin-card-body">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">PHP Version:</span>
                        <strong class="text-dark"><?= phpversion() ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Database:</span>
                        <strong class="text-dark">MySQL (gunvani)</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Server Software:</span>
                        <strong class="text-dark">PHP Dev Server</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Upload Max Size:</span>
                        <strong class="text-dark">128MB</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
