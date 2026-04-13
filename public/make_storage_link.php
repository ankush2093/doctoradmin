<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$target = realpath(__DIR__ . '/../storage/app/public');
$link = __DIR__ . '/storage';

echo "<pre>";
echo "Target: $target\n";
echo "Link: $link\n\n";

if (!is_dir($target)) {
    echo "❌ Target directory does not exist: $target\n";
    exit;
}

if (file_exists($link)) {
    echo "⚠️ Link already exists at: $link\n";
    unlink($link); // remove if it's a file or broken link
    echo "🧹 Old link removed.\n";
}

if (symlink($target, $link)) {
    echo "✅ Storage link created successfully!\n";
} else {
    echo "❌ Still failed — check file manager for a leftover 'storage' item.\n";
}
?>
