<?php
$activePage = 'ads';
$pageTitle = 'Manage Advertisements - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

$uploadDir = __DIR__ . '/../uploads/ads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ads'])) {
    
    // Fetch current to delete old if needed
    $currentSettings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('ad_home_728x90', 'ad_home_300x250')")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $adsToProcess = ['ad_home_728x90', 'ad_home_300x250'];
    
    foreach ($adsToProcess as $key) {
        $currentFile = $currentSettings[$key] ?? '';
        
        // Check if delete requested
        if (isset($_POST['delete_' . $key]) && $_POST['delete_' . $key] === '1') {
            if ($currentFile && file_exists(__DIR__ . '/../' . $currentFile)) {
                unlink(__DIR__ . '/../' . $currentFile);
            }
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, '') ON DUPLICATE KEY UPDATE setting_value = ''");
            $stmt->execute([$key]);
            continue;
        }

        // Handle upload
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES[$key]['tmp_name'];
            $name = basename($_FILES[$key]['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowedExts)) {
                $newName = $key . '_' . time() . '.' . $ext;
                $targetFile = $uploadDir . $newName;
                
                if (move_uploaded_file($tmpName, $targetFile)) {
                    // Delete old file
                    if ($currentFile && file_exists(__DIR__ . '/../' . $currentFile)) {
                        unlink(__DIR__ . '/../' . $currentFile);
                    }
                    
                    $dbPath = 'uploads/ads/' . $newName;
                    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$key, $dbPath, $dbPath]);
                }
            }
        }
    }
    
    $successMessage = "Advertisements updated successfully!";
}

// Fetch current ads
$settings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('ad_home_728x90', 'ad_home_300x250')")->fetchAll(PDO::FETCH_KEY_PAIR);
$adHome728 = $settings['ad_home_728x90'] ?? '';
$adHome300 = $settings['ad_home_300x250'] ?? '';

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Manage Advertisements</h1>
        <p>Upload ad images here. If no image is uploaded, the ad section will be completely hidden from the website.</p>
    </div>
</div>

<?php if (isset($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($successMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h5><i class="fa-solid fa-rectangle-ad me-2 text-primary"></i>Homepage Ads</h5>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data">
            
            <div class="mb-5 border-bottom pb-4">
                <label class="form-label font-weight-bold">Horizontal Ad (728x90) - Shown below Most Read section</label>
                <?php if (!empty($adHome728)): ?>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Current Image:</p>
                        <img src="../<?= htmlspecialchars($adHome728) ?>" class="img-thumbnail" style="max-height: 120px;" alt="Current Ad">
                        <div class="form-check mt-2">
                            <input class="form-check-input text-danger" type="checkbox" name="delete_ad_home_728x90" value="1" id="del728">
                            <label class="form-check-label text-danger" for="del728">Delete this Ad</label>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="ad_home_728x90" class="form-control form-control-admin" accept="image/*">
                <small class="text-muted mt-1 d-block">Recommended size: 728x90 pixels. Max file size: 2MB.</small>
            </div>
            
            <div class="mb-4">
                <label class="form-label font-weight-bold">Sidebar Ad (300x250) - Shown in the right sidebar</label>
                <?php if (!empty($adHome300)): ?>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Current Image:</p>
                        <img src="../<?= htmlspecialchars($adHome300) ?>" class="img-thumbnail" style="max-height: 150px;" alt="Current Ad">
                        <div class="form-check mt-2">
                            <input class="form-check-input text-danger" type="checkbox" name="delete_ad_home_300x250" value="1" id="del300">
                            <label class="form-check-label text-danger" for="del300">Delete this Ad</label>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="ad_home_300x250" class="form-control form-control-admin" accept="image/*">
                <small class="text-muted mt-1 d-block">Recommended size: 300x250 pixels. Max file size: 2MB.</small>
            </div>
            
            <button type="submit" name="save_ads" class="btn btn-success"><i class="fa-solid fa-save me-2"></i>Save Advertisements</button>
        </form>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
