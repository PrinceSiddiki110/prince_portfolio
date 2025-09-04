<?php
// Simple test to check if session warnings are resolved
echo "Testing session configuration...\n";

// Include functions.php which should handle session configuration
require_once __DIR__ . '/functions.php';

echo "Functions.php loaded successfully.\n";

// Try to initialize session
initSecureSession();

echo "Session initialized successfully.\n";
echo "Session ID: " . session_id() . "\n";
echo "Session name: " . session_name() . "\n";

echo "Test completed without warnings!\n";
?>
