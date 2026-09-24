<?php
$activePage = 'menus';
$pageTitle = 'Add Menu - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

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

    $stmt = $pdo->prepare('INSERT INTO menus (name, slug, parent_id, menu_type, target_url, display_order, status, open_new_tab) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $slug, $parent_id, $menu_type, $target_url, $display_order, $status, $open_new_tab]);
    header('Location: menus.php');
    exit;
}

$parentMenus = $pdo->query('SELECT id, name FROM menus WHERE parent_id IS NULL ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

require_once 'admin_header.php';
?>
<div class="page-header">
    <div class="page-title-box">
        <h1>Add Menu</h1>
    </div>
    <a href="menus.php" class="btn btn-outline-secondary">Back</a>
</div>
<div class="admin-card"><div class="admin-card-body">
<form method="post">
    <div class="mb-3">
        <label class="form-label">Menu Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control form-control-admin" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Slug (Optional)</label>
        <input type="text" name="slug" class="form-control form-control-admin">
    </div>
    <div class="mb-3">
        <label class="form-label">Parent Menu</label>
        <select name="parent_id" class="form-select form-control-admin">
            <option value="">None (Top Level)</option>
            <?php foreach ($parentMenus as $pm): ?>
                <option value="<?= $pm['id'] ?>"><?= htmlspecialchars($pm['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Menu Type</label>
        <select name="menu_type" class="form-select form-control-admin">
            <option value="category">Category / Standard</option>                <option value="city">City / Location</option>
            <option value="custom">Custom URL</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Custom URL (if Type is Custom URL)</label>
        <input type="text" name="target_url" class="form-control form-control-admin">
    </div>
    <div class="mb-3">
        <label class="form-label">Display Order</label>
        <input type="number" name="display_order" class="form-control form-control-admin" value="0">
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select form-control-admin">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="open_new_tab" class="form-check-input" id="open_new_tab" value="1">
        <label class="form-check-label" for="open_new_tab">Open in New Tab</label>
    </div>
    <button type="submit" name="save" class="btn btn-success px-4">Save Menu</button>
</form>
</div></div>
<?php require_once 'admin_footer.php'; ?>

