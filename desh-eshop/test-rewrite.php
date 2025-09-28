<?php
echo "URL Rewrite Test - This page is working!";
echo "<br>Current URL: " . $_SERVER['REQUEST_URI'];
echo "<br>Script Name: " . $_SERVER['SCRIPT_NAME'];
echo "<br>Query String: " . ($_SERVER['QUERY_STRING'] ?? 'None');
?>
