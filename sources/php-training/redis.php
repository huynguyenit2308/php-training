<?php
session_start();
echo "<h2>🔌 Redis Connection Test</h2>";

try {
    $redis = new Redis();
    // Host = tên service trong docker-compose
    $redis->connect('web-redis', 6379);

    // Test set/get
    $redis->set("test_key", "Hello from Redis!");
    $value = $redis->get("test_key");

    echo "<p><b>✅ Connected to Redis successfully!</b></p>";
    echo "<p>Stored value: <code>{$value}</code></p>";
} catch (Exception $e) {
    echo "<p><b>❌ Could not connect to Redis.</b></p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
echo '<p><a href="login.php" style="display:inline-block;padding:8px 16px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px;">⬅️ Quay lại Login</a></p>';


// Lưu 1 giá trị vào session
if (!isset($_SESSION['redis_test'])) {
    $_SESSION['redis_test'] = "Hello from Redis at " . date('H:i:s');
}

// Hiển thị
echo "<h2>🔌 PHP Session + Redis Test</h2>";
echo "<p><b>Session ID:</b> " . session_id() . "</p>";
echo "<p><b>Stored Value:</b> " . $_SESSION['redis_test'] . "</p>";
