<?php
$activePage = 'menus';
$pageTitle = 'Menu Management - Gunvani News Admin';

require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once 'db.php';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare('DELETE FROM menus WHERE id = ?')->execute([$id]);
    header('Location: menus.php');
    exit;
}

$sql = "SELECT m.*, p.name as parent_name 
        FROM menus m 
        LEFT JOIN menus p ON m.parent_id = p.id 
        ORDER BY m.parent_id ASC, m.display_order ASC, m.id ASC";
$menus = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

require_once 'admin_header.php';
?>
<div class="page-header">
    <div class="page-title-box">
        <h1>Menu Management</h1>
        <p>Control the frontend header menu hierarchy.</p>
    </div>
    <div>
        <a href="add_menu.php" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i>Add Menu
        </a>
    </div>
</div>
<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Parent</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $index => $menu): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="font-weight-bold"><?= htmlspecialchars($menu['name']) ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst($menu['menu_type']) ?></span></td>
                            <td><?= $menu['parent_name'] ? '<span class="badge bg-info text-dark">'.htmlspecialchars($menu['parent_name']).'</span>' : '-' ?></td>
                            <td><?= (int)$menu['display_order'] ?></td>
                            <td>
                                <?php if ($menu['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="edit_menu.php?id=<?= $menu['id'] ?>" class="btn btn-light border"><i class="fa-solid fa-pen text-primary"></i></a>
                                    <a href="menus.php?delete=<?= $menu['id'] ?>" class="btn btn-light border" onclick="return confirm('Are you sure?');"><i class="fa-solid fa-trash text-danger"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'admin_footer.php'; ?>