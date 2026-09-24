<?php
$activePage = 'menus';
$pageTitle = 'Edit Menu - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

$id = (int)$_GET['id'];
$menu = $pdo->prepare('SELECT * FROM menus WHERE id = ?');
$menu->execute([$id]);
$menu = $menu->fetch(PDO::FETCH_ASSOC);

if (!$menu) { header('Location: menus.php'); exit; }

if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $menu_type = $_POST['menu_type'];
    $target_url = $_POST['target_url'];
    $display_order = (int)$_POST['display_order'];
    $status = $_POST['status'];
    $open_new_tab = isset($_POST['open_new_tab']) ? 1 : 0;

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    $stmt = $pdo->prepare('UPDATE menus SET name=?, slug=?, parent_id=?, menu_type=?, target_url=?, display_order=?, status=?, open_new_tab=? WHERE id=?');
    $stmt->execute([$name, $slug, $parent_id, $menu_type, $target_url, $display_order, $status, $open_new_tab, $id]);
    header('Location: menus.php');
    exit;
}

$parentMenus = $pdo->query('SELECT id, name FROM menus WHERE parent_id IS NULL AND id != '.$id.' ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

require_once 'admin_header.php';
?>
<div class="page-header">
    <div class="page-title-box">
        <h1>Edit Menu</h1>
    </div>
    <a href="menus.php" class="btn btn-outline-secondary">Back</a>
</div>
<div class="admin-card"><div class="admin-card-body">
<form method="post">
    <div class="mb-3">
        <label class="form-label">Menu Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control form-control-admin" required value="<?= htmlspecialchars($menu['name']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control form-control-admin" value="<?= htmlspecialchars($menu['slug']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Parent Menu</label>
        <select name="parent_id" class="form-select form-control-admin">
            <option value="">None (Top Level)</option>
            <?php foreach ($parentMenus as $pm): ?>
                <option value="<?= $pm['id'] ?>" <?= $menu['parent_id'] == $pm['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pm['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Menu Type</label>
        <select name="menu_type" class="form-select form-control-admin">
            <option value="category" <?= $menu['menu_type'] === 'category' ? 'selected' : '' ?>>Category / Standard</option>                <option value="city" <?= $menu['menu_type'] === 'city' ? 'selected' : '' ?>>City / Location</option>
            <option value="custom" <?= $menu['menu_type'] === 'custom' ? 'selected' : '' ?>>Custom URL</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Custom URL</label>
        <input type="text" name="target_url" class="form-control form-control-admin" value="<?= htmlspecialchars($menu['target_url']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Display Order</label>
        <input type="number" name="display_order" class="form-control form-control-admin" value="<?= (int)$menu['display_order'] ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select form-control-admin">
            <option value="active" <?= $menu['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $menu['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="open_new_tab" class="form-check-input" id="open_new_tab" value="1" <?= $menu['open_new_tab'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="open_new_tab">Open in New Tab</label>
    </div>
    <button type="submit" name="save" class="btn btn-success px-4">Update Menu</button>
</form>
</div></div>
<?php require_once 'admin_footer.php'; ?>

