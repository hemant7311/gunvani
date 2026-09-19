<?php
require_once 'auth_check.php';
require_once 'db.php';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM homepage_sections WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: sections.php');
    exit;
}

$sections = $pdo->query(
    "SELECT s.*, c.name AS category_name, a.title AS article_title
     FROM homepage_sections s
     LEFT JOIN categories c ON s.category_id = c.id
     LEFT JOIN articles a ON s.article_id = a.id
     ORDER BY s.display_order ASC"
)->fetchAll(PDO::FETCH_ASSOC);

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Sections - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div style="background:#198754;color:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;">
    <div><strong>Gunvani News Admin</strong> / Homepage Sections</div>
    <div>
        <a href="dashboard.php" class="btn btn-light btn-sm">Dashboard</a>
        <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
</div>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Homepage Sections</h2>
        <a href="add_section.php" class="btn btn-success">Add Section</a>
    </div>
    <div class="card p-4 shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Category / Article</th>
                        <th>Order</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sections) === 0): ?>
                        <tr><td colspan="6" class="text-center">No homepage sections defined yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sections as $section): ?>
                            <tr>
                                <td><?= escape($section['id']) ?></td>
                                <td><?= escape($section['title']) ?></td>
                                <td><?= escape(ucfirst($section['section_type'])) ?></td>
                                <td>
                                    <?php if ($section['section_type'] === 'category'): ?>
                                        <?= escape($section['category_name'] ?: 'Category removed') ?>
                                    <?php elseif ($section['section_type'] === 'featured'): ?>
                                        <?= escape($section['article_title'] ?: 'Article removed') ?>
                                    <?php else: ?>
                                        Latest stories
                                    <?php endif; ?>
                                </td>
                                <td><?= escape($section['display_order']) ?></td>
                                <td>
                                    <a href="edit_section.php?id=<?= escape($section['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="sections.php?delete=<?= escape($section['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this homepage section?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
