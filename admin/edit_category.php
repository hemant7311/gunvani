<?php
$activePage = 'categories';
$pageTitle = 'Edit Menu Item - Gunvani News Admin';

require_once 'auth_check.php';
require_once 'db.php';

function slugify($text) {
    $text = preg_replace('~[^ -127]+~u', '', $text);
    $text = preg_replace('~[^\r\n\t\na-zA-Z0-9_ -]~', '-', $text);
    $text = preg_replace('~-+~', '-', $text);
    $text = trim($text, '-');
    return strtolower($text ?: 'category-' . time());
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$category) {
    header('Location: categories.php');
    exit;
}

if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = trim($_POST['slug']) ?: slugify($name);

    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $show_in_menu = isset($_POST['show_in_menu']) ? 1 : 0;
    $stmt = $pdo->prepare('UPDATE categories SET name = ?, slug = ?, description = ?, parent_id = ?, display_order = ?, show_in_menu = ? WHERE id = ?');
    $stmt->execute([$name, $slug, $description, $parent_id, $display_order, $show_in_menu, $id]);
    header('Location: categories.php');
    exit;
}

$allCategories = $pdo->query('SELECT id, name FROM categories WHERE id != '.(int)$id.' AND parent_id IS NULL ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

require_once 'admin_header.php';
?>

<div class="page-header">
    <div class="page-title-box">
        <h1>Edit Menu Item</h1>
        <p>Update category name, URL slug or description.</p>
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
                <h5><i class="fa-solid fa-pencil me-2 text-warning"></i>Edit Menu Item Information</h5>
            </div>
            <div class="admin-card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Menu Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-admin" value="<?= htmlspecialchars($category['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">URL Slug</label>
                        <input type="text" name="slug" class="form-control form-control-admin" value="<?= htmlspecialchars($category['slug']) ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Description</label>
                        <textarea name="description" class="form-control form-control-admin" rows="4"><?= htmlspecialchars($category['description']) ?></textarea>
                    </div>

                    <button type="submit" name="save" class="btn btn-success px-4 py-2 font-weight-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Update Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
