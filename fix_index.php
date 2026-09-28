<?php
$file = 'index.php';
$content = file_get_contents($file);

// Fix City News query
$content = str_replace(
    "WHERE parent_id IS NOT NULL",
    "WHERE menu_type = 'city'",
    $content
);

// Add error reporting to featuredArticles
$content = str_replace(
    "} catch (Exception \$e) { \$featuredArticles = []; }",
    "} catch (Exception \$e) { \$featuredArticles = []; echo '<!-- FEATURED ERROR: ' . htmlspecialchars(\$e->getMessage()) . ' -->'; }",
    $content
);

// Add error reporting to supportingArticles
$content = preg_replace(
    "/\} catch \(Exception \\\$e\) \{\s*\\\$supportingArticles = \[\];\s*\}/",
    "} catch (Exception \$e) { \$supportingArticles = []; echo '<!-- SUPPORTING ERROR: ' . htmlspecialchars(\$e->getMessage()) . ' -->'; }",
    $content
);

file_put_contents($file, $content);
echo "Done.";
