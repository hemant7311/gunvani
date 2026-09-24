<?php
$activePage = 'news';
$pageTitle = 'Edit Article - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/upload.php';
require_once 'db.php';
// Fetch dynamic authors
$authorList = ['Gunvani News Bureau'];
$dbAdmins = $pdo->query("SELECT fullname FROM admins")->fetchAll(PDO::FETCH_COLUMN);
$dbMembers = $pdo->query("SELECT name FROM members WHERE status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
$authorList = array_unique(array_merge($authorList, $dbAdmins, $dbMembers));


function slugify($text) {
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    if (empty($text)) {
        $text = 'news-' . time() . '-' . rand(100, 999);
    }
    return mb_strtolower($text, 'UTF-8');
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) {
    header('Location: news.php');
    exit;
}

$categories = $pdo->query("SELECT id, name FROM menus WHERE status = 'active' ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
$cities = $pdo->query("SELECT id, name FROM menus WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing gallery media for this article
$galleryMedia = $pdo->prepare("SELECT m.* FROM media m JOIN article_media am ON m.id = am.media_id WHERE am.article_id = ?");
$galleryMedia->execute([$id]);
$existingGallery = $galleryMedia->fetchAll(PDO::FETCH_ASSOC);

$errorMessage = '';

if (isset($_POST['save'])) {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    
    // Ensure slug is unique
    $slugBase = $slug;
    $counter = 1;
    while(true) {
        $chk = $pdo->prepare('SELECT id FROM articles WHERE slug = ? AND id != ?');
        $chk->execute([$slug, $id]);
        if (!$chk->fetch()) {
            break;
        }
        $slug = $slugBase . '-' . $counter;
        $counter++;
    }
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $city_id = !empty($_POST['city_id']) ? (int) $_POST['city_id'] : null;
    $summary = trim($_POST['summary'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? '') ?: 'Gunvani News Bureau';
    $video_url = trim($_POST['video_url'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $status = ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published';
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : date('Y-m-d H:i:s');

    $image = $article['image'];
    $video_file = $article['video_file'];

    // Featured Media Upload (Image or Video)
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
            $vidUpload = secure_upload_file($_FILES['image'], __DIR__ . '/../uploads/videos/', ['video']);
            if ($vidUpload['success']) {
                $video_file = $vidUpload['filename'];
                $image = null; // Clear image if a new video is uploaded as main featured media
            } else {
                $errorMessage = "Video upload failed: " . $vidUpload['error'];
            }
        } else {
            $imgUpload = secure_upload_file($_FILES['image'], __DIR__ . '/../uploads/news/', ['image']);
            if ($imgUpload['success']) {
                $image = $imgUpload['filename'];
                $video_file = null; // Clear video if a new image is uploaded as main featured media
            } else {
                $errorMessage = "Image upload failed: " . $imgUpload['error'];
            }
        }
    }

    // Video File Upload
    if (!$errorMessage && !empty($_FILES['video_file']['name'])) {
        $vidUpload = secure_upload_file($_FILES['video_file'], __DIR__ . '/../uploads/videos/', ['video']);
        if ($vidUpload['success']) {
            $video_file = $vidUpload['filename'];
        } else {
            $errorMessage = "Video upload failed: " . $vidUpload['error'];
        }
    }

    if (!$errorMessage) {
        $is_trending = 0;
        $stmt = $pdo->prepare('UPDATE articles SET 
            category_id = ?, city_id = ?, title = ?, slug = ?, summary = ?, content = ?, image = ?, video_url = ?, video_file = ?, author = ?, is_featured = ?, is_trending = ?, is_breaking = ?, status = ?, published_at = ? 
            WHERE id = ?');
        $stmt->execute([
            $category_id, $city_id, $title, $slug, $summary, $content, $image, $video_url, $video_file, $author, $is_featured, $is_trending, $is_breaking, $status, $published_at, $id
        ]);

        // Handle Gallery Uploads (article_media)
        if (!empty($_FILES['gallery']['name'][0])) {
            $loggedUser = get_logged_user();
            $galleryFiles = $_FILES['gallery'];
            for ($i = 0; $i < count($galleryFiles['name']); $i++) {
                if (empty($galleryFiles['name'][$i])) continue;
                $singleFile = [
                    'name' => $galleryFiles['name'][$i],
                    'type' => $galleryFiles['type'][$i],
                    'tmp_name' => $galleryFiles['tmp_name'][$i],
                    'error' => $galleryFiles['error'][$i],
                    'size' => $galleryFiles['size'][$i]
                ];
                $gUpload = secure_upload_file($singleFile, __DIR__ . '/../uploads/news/', ['image']);
                if ($gUpload['success']) {
                    $mStmt = $pdo->prepare("INSERT INTO media (filename, original_name, file_type, file_size, mime_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
                    $mStmt->execute([
                        $gUpload['filename'], $gUpload['original_name'], 'image', $gUpload['file_size'], $gUpload['mime_type'], $loggedUser['id'] ?? null
                    ]);
                    $mediaId = $pdo->lastInsertId();
                    $pdo->prepare("INSERT INTO article_media (article_id, media_id) VALUES (?, ?)")->execute([$id, $mediaId]);
                }
            }
        }

        header('Location: news.php');
        exit;
    }
}

function articleThumb($image) {
    if (!$image) return '../images/placeholder/first8.jpg';
    if (preg_match('#^(uploads/|images/)#', $image)) return '../' . $image;
    return '../uploads/news/' . $image;
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Edit News Article</h1>
        <p>Update headline, category, video embeds, status, or story photo.</p>
    </div>
    <div>
        <a href="news.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Articles
        </a>
    </div>
</div>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($errorMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5><i class="fa-solid fa-pencil me-2 text-warning"></i>Edit Article Details</h5>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Article Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-admin form-control-lg" value="<?= htmlspecialchars($article['title']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">URL Slug</label>
                        <input type="text" name="slug" class="form-control form-control-admin" value="<?= htmlspecialchars($article['slug']) ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Author / Reporter Name</label>
                            <select name="author" class="form-select form-control-admin">
                                <?php
                                $currentAuthor = $article['author'] ?? ($loggedUser['fullname'] ?? 'Gunvani News Bureau');
                                foreach ($authorList as $aName): 
                                    $sel = ($currentAuthor === $aName) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($aName) ?>" <?= $sel ?>><?= htmlspecialchars($aName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Excerpt / Short Summary <span class="text-danger">*</span></label>
                        <textarea name="summary" class="form-control form-control-admin" rows="3" required><?= htmlspecialchars($article['summary']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Full Article Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control form-control-admin" rows="12" required><?= htmlspecialchars($article['content']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Video & Multi-Image Gallery Options -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fa-solid fa-photo-film me-2 text-primary"></i>Video Embed & Photo Gallery</h5>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Video URL (YouTube / Vimeo / MP4 link)</label>
                        <input type="url" name="video_url" class="form-control form-control-admin" value="<?= htmlspecialchars($article['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Or Upload Video File (MP4, WebM)</label>
                        <?php if (!empty($article['video_file'])): ?>
                            <div class="small text-success mb-1"><i class="fa-solid fa-circle-check me-1"></i>Current video: <?= htmlspecialchars($article['video_file']) ?></div>
                        <?php endif; ?>
                        <input type="file" name="video_file" class="form-control form-control-admin" accept="video/mp4,video/webm">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Add Additional Gallery Photos</label>
                        <input type="file" name="gallery[]" class="form-control form-control-admin" multiple accept="image/*">
                    </div>

                    <?php if (!empty($existingGallery)): ?>
                        <div class="border-top pt-3 mt-3">
                            <label class="form-label font-weight-bold small text-muted">Attached Gallery Photos:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($existingGallery as $gm): ?>
                                    <img src="../<?= htmlspecialchars($gm['file_path']) ?>" alt="Gallery" class="rounded border" style="width:70px; height:60px; object-fit:cover;">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Sidebar Options -->
        <div class="col-lg-4">
            <!-- Featured Media Card -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5><i class="fa-solid fa-photo-film me-2 text-primary"></i>Main Featured Media</h5>
                </div>
                <div class="admin-card-body text-center">
                    <div id="mediaPreviewContainer" class="mb-3 <?= empty($article['image']) && empty($article['video_file']) ? 'd-none' : '' ?>">
                        <?php if (!empty($article['video_file'])): ?>
                            <video src="../uploads/videos/<?= htmlspecialchars($article['video_file']) ?>" controls class="img-fluid rounded border shadow-sm" style="max-height: 250px; width: 100%; object-fit: contain; background: #000;"></video>
                        <?php elseif (!empty($article['image'])): ?>
                            <img src="<?= htmlspecialchars(articleThumb($article['image'])) ?>" alt="Current Media" class="img-fluid rounded border shadow-sm" style="max-height:250px; object-fit: cover; width: 100%;">
                        <?php endif; ?>
                    </div>
                    <div class="border rounded p-3 bg-light text-start">
                        <label class="form-label small font-weight-bold text-muted mb-1">Upload Photo or MP4 Video to replace</label>
                        <input type="file" name="image" id="featuredMediaInput" class="form-control form-control-admin form-control-sm" accept="image/*,video/mp4,video/webm">
                    </div>
                </div>
            </div>

            <!-- Publishing Settings Card -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fa-solid fa-sliders me-2 text-warning"></i>Publishing Settings</h5>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select form-select-admin" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $article['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">City Channel (Optional)</label>
                        <select name="city_id" class="form-select form-select-admin">
                            <option value="">No Specific City</option>
                            <?php foreach ($cities as $ct): ?>
                                <option value="<?= $ct['id'] ?>" <?= ($article['city_id'] ?? 0) == $ct['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ct['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= !empty($article['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-primary" for="is_featured">
                                <i class="fa-solid fa-star me-1"></i> Featured News (Hero Banner)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_breaking" id="is_breaking" value="1" <?= !empty($article['is_breaking']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-danger" for="is_breaking">
                                <i class="fa-solid fa-bolt me-1"></i> Breaking News Ticker
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="status" class="form-select form-select-admin">
                            <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Publish Date & Time</label>
                        <input type="datetime-local" name="published_at" class="form-control form-control-admin" value="<?= date('Y-m-d\TH:i', strtotime($article['published_at'])) ?>">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="save" class="btn btn-success py-2.5 font-weight-bold">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('featuredMediaInput');
    const previewContainer = document.getElementById('mediaPreviewContainer');
    
    if(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                // If user cancels selection, keep existing preview (do not hide)
                return; 
            }
            
            previewContainer.innerHTML = '';
            previewContainer.classList.remove('d-none');
            const fileURL = URL.createObjectURL(file);
            
            if (file.type.startsWith('video/')) {
                previewContainer.innerHTML = `<video src="${fileURL}" controls class="img-fluid rounded border shadow-sm" style="max-height: 250px; width: 100%; object-fit: contain; background: #000;"></video>`;
            } else if (file.type.startsWith('image/')) {
                previewContainer.innerHTML = `<img src="${fileURL}" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: cover; width: 100%;">`;
            } else {
                previewContainer.innerHTML = `<div class="alert alert-warning py-2 mb-0">Preview not available</div>`;
            }
        });
    }
});
</script>

<?php require_once 'admin_footer.php'; ?>
