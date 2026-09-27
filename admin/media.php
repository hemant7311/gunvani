<?php
$activePage = 'media';
$pageTitle = 'Media Library - Gunvani News Admin';

require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/auth.php';
require_once 'db.php';

require_login();

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ---------------------------------------------------------
// AJAX SECURE DELETE HANDLER
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    header('Content-Type: application/json');
    
    // 1. Authorization: Only allow admins OR agents to delete
    $currentUser = get_logged_user();
    if (!is_admin() && !is_agent()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'You do not have permission to delete media.']);
        exit;
    }
    
    // 2. CSRF Verification
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Security verification failed. Please refresh the page and try again.']);
        exit;
    }
    
    // 3. Media ID Validation
    $delId = (int)($_POST['media_id'] ?? 0);
    if ($delId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid media ID.']);
        exit;
    }
    
    try {
        // 4. Fetch Media Record
        $stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
        $stmt->execute([$delId]);
        $mFile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$mFile) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Media not found in database.']);
            exit;
        }
        
        // Ensure Agents can only delete their own uploaded media, while Admins can delete any
        if (is_agent() && !is_admin()) {
            if ($mFile['uploaded_by'] != $currentUser['id']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'You do not have permission to delete media uploaded by others.']);
                exit;
            }
        }
        
        $filename = $mFile['filename'];
        $filePath = $mFile['file_path'];
        
        // 5. Shared File Safety Checks
        $isReferenced = false;
        
        // Check articles
        $stmtRef = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE image = ? OR video_url = ?");
        $stmtRef->execute([$filename, $filename]);
        if ($stmtRef->fetchColumn() > 0) $isReferenced = true;
        
        // Check members
        $stmtRef = $pdo->prepare("SELECT COUNT(*) FROM members WHERE photo = ? OR photo = ?");
        $stmtRef->execute([$filename, $filePath]);
        if ($stmtRef->fetchColumn() > 0) $isReferenced = true;
        
        // Check menus
        $stmtRef = $pdo->prepare("SELECT COUNT(*) FROM menus WHERE image = ? OR image = ?");
        $stmtRef->execute([$filename, $filePath]);
        if ($stmtRef->fetchColumn() > 0) $isReferenced = true;
        
        // 6. article_media Relation Cleanup
        try {
            $stmtAm = $pdo->prepare("DELETE FROM article_media WHERE media_id = ?");
            $stmtAm->execute([$delId]);
        } catch (PDOException $e) { }
        
        // 7. Physical File Deletion (Path Traversal Safe)
        if (!$isReferenced) {
            $uploadDir = realpath(__DIR__ . '/../uploads');
            if ($uploadDir) {
                $targetFile = realpath(__DIR__ . '/../' . $filePath);
                if ($targetFile && strpos($targetFile, $uploadDir) === 0 && file_exists($targetFile)) {
                    @unlink($targetFile);
                }
            }
        }
        
        // 8. Delete Database Record
        $pdo->prepare("DELETE FROM media WHERE id = ?")->execute([$delId]);
        
        echo json_encode(['success' => true, 'message' => 'Media deleted successfully.']);
        exit;
    } catch (Exception $e) {
        error_log("[Media Delete Error] " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'An internal server error occurred. Please try again.']);
        exit;
    }
}
// End AJAX Handler
// ---------------------------------------------------------

require_once 'admin_header.php';

$message = '';
$errorMessage = '';

// Handle Media Upload if posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['media_file']['name']) && !isset($_POST['action'])) {
    $uploadRes = secure_upload_file($_FILES['media_file'], __DIR__ . '/../uploads/news/', ['image', 'video']);
    if ($uploadRes['success']) {
        $loggedUser = get_logged_user();
        $userId = $loggedUser['id'] ?? null;
        
        $stmt = $pdo->prepare("INSERT INTO media (filename, original_name, file_path, file_type, file_size, mime_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $uploadRes['filename'],
            $uploadRes['original_name'],
            'uploads/news/' . $uploadRes['filename'],
            $uploadRes['ext'],
            $uploadRes['file_size'],
            $uploadRes['mime_type'],
            $userId
        ]);
        $message = "File uploaded and saved to Media Library!";
    } else {
        $errorMessage = $uploadRes['error'];
    }
}

// Fetch images from media table
$dbMedia = $pdo->query("SELECT * FROM media ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$mediaFiles = [];
if (!empty($dbMedia)) {
    foreach ($dbMedia as $m) {
        $mediaFiles[] = [
            'id' => $m['id'],
            'name' => $m['original_name'] ?: $m['filename'],
            'path' => '../' . $m['file_path'],
            'size' => round(($m['file_size'] ?: 0) / 1024, 1) . ' KB',
            'time' => date('M j, Y', strtotime($m['created_at'] ?? 'now'))
        ];
    }
} else {
    // Fallback: fetch images from uploads/ directory
    $uploadDir = __DIR__ . '/../uploads/news/';
    if (is_dir($uploadDir)) {
        $files = array_diff(scandir($uploadDir), ['.', '..']);
        foreach ($files as $f) {
            $path = $uploadDir . $f;
            if (is_file($path)) {
                $mediaFiles[] = [
                    'id' => 0,
                    'name' => $f,
                    'path' => '../uploads/news/' . $f,
                    'size' => round(filesize($path) / 1024, 1) . ' KB',
                    'time' => date('M j, Y', filemtime($path))
                ];
            }
        }
    }
}
?>

<style>
/* 1. FIX HOVER CSS */
.media-card-img-wrap {
    position: relative;
    overflow: hidden;
    height: 140px;
    background: #f1f5f9;
}

.media-card-img-wrap .btn-delete-media {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 100;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
    transform: scale(0.9);
}

.media-card-img-wrap:hover .btn-delete-media {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: scale(1);
}

/* 16. MOBILE ACCESSIBILITY */
@media (hover: none), (pointer: coarse) {
    .media-card-img-wrap .btn-delete-media {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: scale(1);
    }
}
</style>

<div class="page-header">
    <div class="page-title-box">
        <h1>Media Library</h1>
        <p>Upload and manage news images, story banners, videos and documents.</p>
    </div>
    <div>
        <button class="btn btn-success px-4 py-2 font-weight-bold shadow-sm" data-bs-toggle="collapse" data-bs-target="#uploadCollapse">
            <i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Media
        </button>
    </div>
</div>

<!-- Upload Collapse Container -->
<div class="collapse mb-4" id="uploadCollapse">
    <div class="admin-card">
        <div class="admin-card-body text-center p-4">
            <form method="POST" enctype="multipart/form-data" class="d-inline-block w-100" style="max-width:500px;">
                <div class="border border-2 border-dashed rounded-3 p-4 bg-light mb-3">
                    <i class="fa-solid fa-images display-4 text-success mb-3"></i>
                    <h5>Select Media File to Upload</h5>
                    <p class="text-muted small mb-3">Supports JPG, PNG, WEBP, GIF, PDF</p>
                    <input type="file" name="media_file" class="form-control form-control-admin mb-3" required>
                    <button type="submit" class="btn btn-success w-100 font-weight-bold py-2">
                        <i class="fa-solid fa-upload me-2"></i>Upload Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="media-alerts-container">
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Tabs & Search Toolbar -->
<div class="toolbar-card">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active bg-success text-white py-1 px-3 me-1 rounded-pill" href="#">All Media</a>
        </li>
    </ul>

    <div class="topbar-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search media files..." aria-label="Search media">
    </div>
</div>

<!-- Media Grid Layout -->
<div class="row g-3">
    <?php if (count($mediaFiles) === 0): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="fa-regular fa-folder-open display-3 mb-3 text-muted"></i>
            <h5>No media files found</h5>
            <p>Click Upload Media to add photos or documents.</p>
        </div>
    <?php else: ?>
        <?php foreach ($mediaFiles as $media): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2 media-grid-item">
                <div class="admin-card h-100 mb-0 position-relative">
                    <div class="media-card-img-wrap">
                        <img src="<?= htmlspecialchars($media['path']) ?>" alt="<?= htmlspecialchars($media['name']) ?>" class="w-100 h-100" style="object-fit:cover;" onerror="this.src='../images/placeholder/first8.jpg'">
                        <?php if (!empty($media['id'])): ?>
                            <button type="button" class="btn btn-sm btn-danger btn-delete-media" aria-label="Delete Media" title="Delete Media" data-id="<?= $media['id'] ?>" data-csrf="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="p-2 text-center">
                        <div class="small fw-semibold text-truncate mb-1" title="<?= htmlspecialchars($media['name']) ?>">
                            <?= htmlspecialchars($media['name']) ?>
                        </div>
                        <div class="small text-muted" style="font-size:0.75rem;">
                            <?= $media['size'] ?> ? <?= $media['time'] ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete-media').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const mediaId = this.dataset.id;
            const csrfToken = this.dataset.csrf;
            const gridItem = this.closest('.media-grid-item');
            
            if (confirm('Are you sure you want to delete this media file?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('media_id', mediaId);
                formData.append('csrf_token', csrfToken);
                
                fetch('media.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    const text = await response.text();
                    let data;
                    
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        throw new Error('Server returned an invalid response. HTTP ' + response.status);
                    }
                    
                    if (!response.ok || !data.success) {
                        throw new Error(data.error || 'Delete request failed.');
                    }
                    
                    return data;
                })
                .then(data => {
                    // Remove from DOM without refreshing
                    gridItem.remove();
                    
                    // Show success alert
                    const alertHtml = `<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa-solid fa-circle-check me-2"></i>Media deleted successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
                    const container = document.getElementById('media-alerts-container');
                    container.innerHTML = alertHtml;
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert(error.message || 'Unable to delete media. Please try again.');
                });
            }
        });
    });
});
</script>

<?php require_once 'admin_footer.php'; ?>
