<?php
session_start();

// Lưu 1 giá trị vào session
if (!isset($_SESSION['redis_test'])) {
    $_SESSION['redis_test'] = "Hello from Redis at " . date('H:i:s');
}

// Hiển thị
echo "<h2>🔌 PHP Session + Redis Test</h2>";
echo "<p><b>Session ID:</b> " . session_id() . "</p>";
echo "<p><b>Stored Value:</b> " . $_SESSION['redis_test'] . "</p>";