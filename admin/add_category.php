<?php
$activePage = 'categories';
$pageTitle = 'Add New Category - Gunvani News Admin';

require_once 'auth_check.php';
require_once 'db.php';

function slugify($text) {
    $text = preg_replace('~[^ -127]+~u', '', $text);
    $text = preg_replace('~[^\r\n\t\na-zA-Z0-9_ -]~', '-', $text);
    $text = preg_replace('~-+~', '-', $text);
    $text = trim($text, '-');
    return strtolower($text ?: 'category-' . time());
}

if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = trim($_POST['slug']) ?: slugify($name);

    $stmt = $pdo->prepare('INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)');
    $stmt->execute([$name, $slug, $description]);
    header('Location: categories.php');
    exit;
}

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Add New Category</h1>
        <p>Create a news channel or city category for Gunvani News.</p>
    </div>
    <div>
        <a href="categories.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Categories
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fa-solid fa-layer-group me-2 text-success"></i>Category Information</h5>
            </div>
            <div class="admin-card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-admin" required placeholder="e.g. Agra, Lucknow, Business, Sports">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">URL Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control form-control-admin" placeholder="category-slug">
                        <div class="form-text">Auto-generated from category name if left empty.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Description</label>
                        <textarea name="description" class="form-control form-control-admin" rows="4" placeholder="Short description of stories covered in this category..."></textarea>
                    </div>

                    <button type="submit" name="save" class="btn btn-success px-4 py-2 font-weight-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Save Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
