<?php
echo "<h2>Debug Information</h2>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

echo "<h3>File Existence Check:</h3>";
$files_to_check = [
    'products.php',
    'categories.php', 
    'about.php',
    'contact.php',
    '.htaccess'
];

foreach ($files_to_check as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "<p><strong>{$file}:</strong> " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . "</p>";
}

echo "<h3>Apache Modules (if available):</h3>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $rewrite_enabled = in_array('mod_rewrite', $modules);
    echo "<p><strong>mod_rewrite:</strong> " . ($rewrite_enabled ? "✅ ENABLED" : "❌ DISABLED") . "</p>";
    echo "<p><strong>All modules:</strong> " . implode(', ', $modules) . "</p>";
} else {
    echo "<p>apache_get_modules() function not available</p>";
}

echo "<h3>Test Links:</h3>";
echo "<p><a href='products.php'>Direct: products.php</a></p>";
echo "<p><a href='products'>Clean URL: products</a></p>";
echo "<p><a href='test'>Test rewrite: test</a></p>";
?>
