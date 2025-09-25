<?php
session_start();
require_once 'models/UserModel.php';

if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Lỗi CSRF!!!");
}

if (isset($_GET['id'])) {
    $userModel = new UserModel();
    $id = intval($_GET['id']);
    $userModel->deleteUserById($id);
    header("Location: list_users.php");
    exit;
}
