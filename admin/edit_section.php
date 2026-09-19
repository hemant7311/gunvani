<?php
require_once 'auth_check.php';
require_once 'db.php';

$categories = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
$articles = $pdo->query('SELECT id, title FROM articles ORDER BY published_at DESC')->fetchAll(PDO::FETCH_ASSOC);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM homepage_sections WHERE id = ?');
$stmt->execute([$id]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$section) {
    header('Location: sections.php');
    exit;
}

if (isset($_POST['save'])) {
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $type = in_array($_POST['section_type'], ['latest', 'category', 'featured']) ? $_POST['section_type'] : 'latest';
    $category_id = $type === 'category' ? (int) $_POST['category_id'] : null;
    $article_id = $type === 'featured' ? (int) $_POST['article_id'] : null;
    $display_order = (int) $_POST['display_order'];

    $stmt = $pdo->prepare('UPDATE homepage_sections SET title = ?, subtitle = ?, section_type = ?, category_id = ?, article_id = ?, display_order = ? WHERE id = ?');
    $stmt->execute([$title, $subtitle, $type, $category_id ?: null, $article_id ?: null, $display_order, $id]);
    header('Location: sections.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Section - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function updateFields() {
            const type = document.querySelector('[name="section_type"]').value;
            document.querySelector('#category-row').style.display = type === 'category' ? 'block' : 'none';
            document.querySelector('#article-row').style.display = type === 'featured' ? 'block' : 'none';
        }
        window.addEventListener('DOMContentLoaded', updateFields);
    </script>
</head>
<body>
<div class="container py-5">
    <div class="mb-4">
        <a href="sections.php" class="btn btn-secondary">← Back to Sections</a>
    </div>
    <div class="card p-4 shadow-sm">
        <h2 class="mb-4">Edit Homepage Section</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <textarea name="subtitle" class="form-control" rows="2"><?= htmlspecialchars($section['subtitle'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Section Type</label>
                <select name="section_type" class="form-control" onchange="updateFields()">
                    <option value="latest" <?= $section['section_type'] === 'latest' ? 'selected' : '' ?>>Latest News</option>
                    <option value="category" <?= $section['section_type'] === 'category' ? 'selected' : '' ?>>Category Highlights</option>
                    <option value="featured" <?= $section['section_type'] === 'featured' ? 'selected' : '' ?>>Featured Article</option>
                </select>
            </div>
            <div class="mb-3" id="category-row" style="display:none;">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <option value="">Choose category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $section['category_id'] == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3" id="article-row" style="display:none;">
                <label class="form-label">Featured Article</label>
                <select name="article_id" class="form-control">
                    <option value="">Choose article</option>
                    <?php foreach ($articles as $article): ?>
                        <option value="<?= htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $section['article_id'] == $article['id'] ? 'selected' : '' ?>><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Display Order</label>
                <input type="number" name="display_order" class="form-control" value="<?= htmlspecialchars($section['display_order'], ENT_QUOTES, 'UTF-8') ?>" min="0">
            </div>
            <button type="submit" name="save" class="btn btn-success">Save Section</button>
        </form>
    </div>
</div>
</body>
</html>
