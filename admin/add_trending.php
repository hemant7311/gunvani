<?php
$activePage = 'trending';
$pageTitle = 'Add Trending Story - Gunvani News Admin';

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
    // Retain Unicode letters, numbers, spaces and hyphens
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    if (empty($text)) {
        $text = 'news-' . time() . '-' . rand(100, 999);
    }
    return mb_strtolower($text, 'UTF-8');
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
$cities = $pdo->query('SELECT * FROM cities ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
$loggedUser = get_logged_user();

$errorMessage = '';

if (isset($_POST['save'])) {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    
    // Ensure slug is unique
    $slugBase = $slug;
    $counter = 1;
    while(true) {
        $chk = $pdo->prepare('SELECT id FROM articles WHERE slug = ?');
        $chk->execute([$slug]);
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
    $author = trim($_POST['author'] ?? '') ?: ($loggedUser['name'] ?? 'Gunvani News Bureau');
    $video_url = trim($_POST['video_url'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $status = ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published';
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : date('Y-m-d H:i:s');
    
    $image = null;
    $video_file = null;

    // Featured Media Upload (Image or Video)
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
            $vidUpload = secure_upload_file($_FILES['image'], __DIR__ . '/../uploads/videos/', ['video']);
            if ($vidUpload['success']) {
                $video_file = $vidUpload['filename'];
            } else {
                $errorMessage = "Video upload failed: " . $vidUpload['error'];
            }
        } else {
            $imgUpload = secure_upload_file($_FILES['image'], __DIR__ . '/../uploads/news/', ['image']);
            if ($imgUpload['success']) {
                $image = $imgUpload['filename'];
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
        $is_trending = 1;
        $stmt = $pdo->prepare('INSERT INTO articles 
            (category_id, city_id, title, slug, summary, content, image, video_url, video_file, author, created_by, is_featured, is_trending, is_breaking, status, published_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $category_id, $city_id, $title, $slug, $summary, $content, $image, $video_url, $video_file, $author, $loggedUser['id'] ?? null, $is_featured, $is_trending, $is_breaking, $status, $published_at
        ]);
        $articleId = $pdo->lastInsertId();

        // Handle Gallery Uploads (article_media)
        if (!empty($_FILES['gallery']['name'][0])) {
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
                    // Save to media table
                    $mStmt = $pdo->prepare("INSERT INTO media (filename, original_name, file_type, file_size, mime_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
                    $mStmt->execute([
                        $gUpload['filename'], $gUpload['original_name'], 'image', $gUpload['file_size'], $gUpload['mime_type'], $loggedUser['id'] ?? null
                    ]);
                    $mediaId = $pdo->lastInsertId();

                    // Link to article_media
                    $pdo->prepare("INSERT INTO article_media (article_id, media_id) VALUES (?, ?)")->execute([$articleId, $mediaId]);
                }
            }
        }

        header('Location: trending.php');
        exit;
    }
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Add Trending Story</h1>
        <p>Create and publish a new news story or video report on Gunvani News.</p>
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
                    <h5><i class="fa-solid fa-pen-to-square me-2 text-success"></i>Article Details</h5>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Article Title / Headline <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-admin form-control-lg" required placeholder="Enter headline title here...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">URL Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control form-control-admin" placeholder="headline-title-slug">
                        <div class="form-text">Will be auto-generated from title if left empty. Supports Hindi / Devanagari text.</div>
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
                        <textarea name="summary" class="form-control form-control-admin" rows="3" required placeholder="Brief summary of the story (1-2 sentences)..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Full Article Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control form-control-admin" rows="12" required placeholder="Write the complete news article text here..."></textarea>
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
                        <input type="url" name="video_url" class="form-control form-control-admin" placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Or Upload Video File (MP4, WebM)</label>
                        <input type="file" name="video_file" class="form-control form-control-admin" accept="video/mp4,video/webm">
                    </div>

                    <div class="mb-0">
                        <label class="form-label font-weight-bold">Article Multi-Image Gallery</label>
                        <input type="file" name="gallery[]" class="form-control form-control-admin" multiple accept="image/*">
                        <div class="form-text">Select multiple images to attach to this article's media gallery.</div>
                    </div>
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
                    <div id="mediaPreviewContainer" class="mb-3 d-none">
                        <!-- Preview will be injected here via JS -->
                    </div>
                    <div class="border rounded p-4 bg-light mb-3">
                        <i class="fa-solid fa-cloud-arrow-up display-5 text-muted mb-2"></i>
                        <p class="small text-muted mb-2">Upload photo (JPG, PNG) or Video (MP4)</p>
                        <input type="file" name="image" id="featuredMediaInput" class="form-control form-control-admin" accept="image/*,video/mp4,video/webm">
                    </div>
                </div>
            </div>

            <!-- Publishing Options Card -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fa-solid fa-sliders me-2 text-warning"></i>Publishing & Flags</h5>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select form-select-admin" required>
                            <option value="">Select category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">City Channel (Optional)</label>
                        <select name="city_id" class="form-select form-select-admin">
                            <option value="">No Specific City</option>
                            <?php foreach ($cities as $ct): ?>
                                <option value="<?= $ct['id'] ?>"><?= htmlspecialchars($ct['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
                            <label class="form-check-label fw-bold text-primary" for="is_featured">
                                <i class="fa-solid fa-star me-1"></i> Featured News (Hero Banner)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_breaking" id="is_breaking" value="1">
                            <label class="form-check-label fw-bold text-danger" for="is_breaking">
                                <i class="fa-solid fa-bolt me-1"></i> Breaking News Ticker
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="status" class="form-select form-select-admin">
                            <option value="published" selected>Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Publish Date & Time</label>
                        <input type="datetime-local" name="published_at" class="form-control form-control-admin" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="save" class="btn btn-success py-2.5 font-weight-bold">
                            <i class="fa-solid fa-paper-plane me-2"></i>Publish Article
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
            previewContainer.innerHTML = '';
            const file = e.target.files[0];
            if (!file) {
                previewContainer.classList.add('d-none');
                return;
            }
            
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
