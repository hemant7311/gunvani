<?php
$file = 'index.php';
$content = file_get_contents($file);

$content = str_replace(
    "echo '<!-- FEATURED ERROR: ' . htmlspecialchars(\$e->getMessage()) . ' -->';",
    "echo '<div class=\"alert alert-danger\">FEATURED ERROR: ' . htmlspecialchars(\$e->getMessage()) . '</div>';",
    $content
);
$content = str_replace(
    "echo '<!-- SUPPORTING ERROR: ' . htmlspecialchars(\$e->getMessage()) . ' -->';",
    "echo '<div class=\"alert alert-danger\">SUPPORTING ERROR: ' . htmlspecialchars(\$e->getMessage()) . '</div>';",
    $content
);
file_put_contents($file, $content);
echo "Done.";
