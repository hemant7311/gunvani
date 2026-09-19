<?php
$activePage = 'appearance';
$pageTitle = 'Appearance Settings - Gunvani News Admin';

require_once 'admin_header.php';
require_once 'db.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Appearance & Branding</h1>
        <p>Customize the look and feel of Gunvani News Admin Panel.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-palette me-2 text-success"></i>Brand Colors & Logo</h5>
            </div>
            <div class="admin-card-body">
                <div class="mb-4">
                    <label class="form-label font-weight-bold">Primary Brand Color</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" class="form-control form-control-color" value="#116530" style="width:50px; height:38px;">
                        <span class="font-monospace fw-bold text-success">#116530 (Gunvani Green)</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label font-weight-bold">Active Logo Asset</label>
                    <div class="p-3 border rounded bg-light text-center">
                        <img src="../images/placeholder/logos.png" alt="Gunvani Logo" style="max-height:60px;" onerror="this.src='../icon.png'">
                    </div>
                    <div class="form-text mt-2 text-muted">The existing Gunvani logo asset is preserved and actively used across the site.</div>
                </div>

                <button type="button" class="btn btn-success px-4 py-2 font-weight-bold">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Save Appearance
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-desktop me-2 text-info"></i>Admin Layout Settings</h5>
            </div>
            <div class="admin-card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="darkSidebarSwitch" checked>
                    <label class="form-check-label font-weight-bold" for="darkSidebarSwitch">Dark Emerald Sidebar</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="stickyHeaderSwitch" checked>
                    <label class="form-check-label font-weight-bold" for="stickyHeaderSwitch">Sticky Top Header Bar</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="compactTableSwitch" checked>
                    <label class="form-check-label font-weight-bold" for="compactTableSwitch">Icon-First Action Buttons</label>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
