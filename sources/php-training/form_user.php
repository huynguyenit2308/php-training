<?php
session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = null;
$_id = null;
$errors = [];

if (!empty($_GET['id'])) {
    $_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($_id === false || $_id === null) {
        $_id = null;
    } else {
        $user = $userModel->findUserById($_id);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Không cho phép ký tự HTML/JS trong name
    $name = preg_replace('/[^\p{L}\p{N}\s]/u', '', $name);

    if ($name === '') {
        $errors[] = 'Tên không được để trống.';
    } elseif (mb_strlen($name, 'UTF-8') > 255) {
        $errors[] = 'Tên quá dài.';
    }

    if (empty($errors)) {
        $payload = ['name' => $name];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            } else {
                $payload['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
        }

        if (empty($errors)) {
            if (!empty($id)) {
                $payload['id'] = $id;
                $userModel->updateUser($payload);
            } else {
                if (empty($payload['password'])) {
                    $errors[] = 'Mật khẩu bắt buộc khi tạo user mới.';
                } else {
                    $userModel->insertUser($payload);
                }
            }

            if (empty($errors)) {
                header('Location: list_users.php');
                exit;
            }
        }
    }

    if (!empty($id)) {
        $user = $userModel->findUserById($id);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
    <?php include 'views/header.php' ?>
    <div class="container" style="max-width:700px; margin-top:20px;">

        <?php
        if (!empty($errors)) {
            echo '<div class="alert alert-danger" role="alert"><ul style="margin:0;padding-left:18px;">';
            foreach ($errors as $err) {
                echo '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            echo '</ul></div>';
        }
        ?>

        <?php if ($user || !isset($_id)) { ?>
            <div class="alert alert-warning" role="alert">
                User form
            </div>
            <form method="POST" novalidate>
                <?php
                $hiddenId = '';
                if (!empty($user) && isset($user[0]['id'])) {
                    $hiddenId = intval($user[0]['id']);
                } elseif (!empty($_id)) {
                    $hiddenId = intval($_id);
                }
                ?>
                <input type="hidden" name="id" value="<?php echo $hiddenId ? $hiddenId : '' ?>">

                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" class="form-control" name="name" placeholder="Name"
                        value="<?php
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
                                echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
                            } elseif (!empty($user) && isset($user[0]['name'])) {
                                echo htmlspecialchars($user[0]['name'], ENT_QUOTES, 'UTF-8');
                            } else {
                                echo '';
                            }
                        ?>">
                </div>

                <div class="form-group">
                    <label for="password">
                        Password <?php if (!empty($hiddenId)) echo '<small>(để trống nếu không thay đổi)</small>'; ?>
                    </label>
                    <input id="password" type="password" name="password" class="form-control" placeholder="Password">
                </div>

                <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php } else { ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars('User not found!', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>
