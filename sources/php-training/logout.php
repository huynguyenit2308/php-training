<?php
session_start();

// Kết nối Redis
$redis = new Redis();
$redis->connect('web-redis', 6379);

// Xóa dữ liệu Redis liên quan đến user
if (!empty($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    $redis->del("user_session:$userId");
}

// Xóa session PHP
session_unset();
session_destroy();

// Xóa cookie
setcookie("session_id", "", time() - 3600, "/");
setcookie("remember_user", "", time() - 3600, "/");

// Trả về trang logout tạm để xóa sessionStorage bằng JS
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
</head>
<body>
<script>
    // Xóa sessionStorage
    sessionStorage.removeItem('session_id');

    // Redirect về login
    window.location.href = 'login.php';
</script>
</body>
</html>
