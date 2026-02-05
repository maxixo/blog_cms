<?php
// Test script to verify .env file loading
require_once __DIR__ . '/config/config.php';

echo "<h1>Environment Variables Test</h1>";

echo "<h2>TinyMCE Configuration:</h2>";
echo "<ul>";
echo "<li><strong>TINYMCE_API_KEY:</strong> " . esc(TINYMCE_API_KEY) . "</li>";
echo "<li><strong>TINYMCE_ALLOW_LOCALHOST:</strong> " . (TINYMCE_ALLOW_LOCALHOST ? 'true' : 'false') . "</li>";
echo "<li><strong>TINYMCE_SCRIPT_URL:</strong> " . esc(TINYMCE_SCRIPT_URL) . "</li>";
echo "</ul>";

echo "<h2>Environment Detection:</h2>";
$tinymceHost = strtolower($_SERVER['HTTP_HOST'] ?? 'localhost');
$tinymceHost = preg_replace('/:\d+$/', '', $tinymceHost);
$isLocalHost = in_array($tinymceHost, ['localhost', '127.0.0.1'], true);
echo "<ul>";
echo "<li><strong>Current Host:</strong> " . esc($tinymceHost) . "</li>";
echo "<li><strong>Is Localhost:</strong> " . ($isLocalHost ? 'Yes' : 'No') . "</li>";
echo "</ul>";

echo "<h2>Status:</h2>";
if (empty(TINYMCE_API_KEY)) {
    // Using jsDelivr CDN (no API key needed)
    echo "<p style='color: green;'>✅ Using free jsDelivr CDN (no API key needed)!</p>";
    if (strpos(TINYMCE_SCRIPT_URL, 'jsdelivr') !== false) {
        echo "<p style='color: green;'>✅ TinyMCE script URL is correctly configured!</p>";
        echo "<p style='color: green;'>✅ TinyMCE should work correctly!</p>";
    } else {
        echo "<p style='color: red;'>❌ Unexpected CDN URL configuration!</p>";
    }
} elseif (TINYMCE_API_KEY === 'no-api-key') {
    echo "<p style='color: red;'>❌ API Key is set to 'no-api-key' - TinyMCE will not work properly!</p>";
    if ($isLocalHost && !TINYMCE_ALLOW_LOCALHOST) {
        echo "<p style='color: orange;'>⚠️ Issue: Running on localhost but TINYMCE_ALLOW_LOCALHOST is false</p>";
    }
} else {
    // Using TinyMCE Cloud with API key
    echo "<p style='color: green;'>✅ API Key is properly configured!</p>";
    if (strpos(TINYMCE_SCRIPT_URL, 'tiny.cloud') !== false) {
        echo "<p style='color: green;'>✅ Using TinyMCE Cloud CDN!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Warning: API key is set but using custom URL</p>";
    }
    echo "<p style='color: green;'>✅ TinyMCE should work correctly!</p>";
}

function esc($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>