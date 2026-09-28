<?php
$dir = __DIR__;

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$searchPatterns = [
    '/images\/placeholder\/first8\.jpg/',
    '/images\/placeholder\/second6\.webp/',
    '/images\/placeholder\/first7\.jpg/'
];
$replaceWith = 'icon.png';

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        foreach ($searchPatterns as $pattern) {
            $content = preg_replace($pattern, 'icon.png', $content);
        }
        
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}
echo "Done replacing in code.\n";
