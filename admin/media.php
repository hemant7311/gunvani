<?php
$activePage = 'media';
$pageTitle = 'Media Library - Gunvani News Admin';

require_once __DIR__ . '/../includes/upload.php';
require_once 'admin_header.php';
require_once 'db.php';

$message = '';
$errorMessage = '';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
    $stmt->execute([$delId]);
    $mFile = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($mFile) {
        $filePath = __DIR__ . '/../' . $mFile['file_path'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        $pdo->prepare("DELETE FROM media WHERE id = ?")->execute([$delId]);
        $message = "Media file deleted successfully.";
    }
}

// Handle Media Upload if posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['media_file']['name'])) {
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
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <div class="admin-card h-100 mb-0 position-relative">
                    <div class="position-relative overflow-hidden" style="height:140px; background:#f1f5f9;">
                        <img src="<?= htmlspecialchars($media['path']) ?>" alt="<?= htmlspecialchars($media['name']) ?>" class="w-100 h-100" style="object-fit:cover;" onerror="this.src='../images/placeholder/first8.jpg'">
                        <?php if (!empty($media['id'])): ?>
                            <a href="media.php?delete=<?= $media['id'] ?>" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 px-2 py-0" title="Delete file" onclick="return confirm('Delete this media file?');">
                                <i class="fa-solid fa-trash-can small"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="p-2 text-center">
                        <div class="small fw-semibold text-truncate mb-1" title="<?= htmlspecialchars($media['name']) ?>">
                            <?= htmlspecialchars($media['name']) ?>
                        </div>
                        <div class="small text-muted" style="font-size:0.75rem;">
                            <?= $media['size'] ?> • <?= $media['time'] ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'admin_footer.php'; ?>
