<?php
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();
$redis = new Redis();
$redis->connect('web-redis', 6379);

if (!empty($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $user = $userModel->auth($username, $password);

    if ($user) {
        $_SESSION['id'] = $user[0]['id'];
        $_SESSION['username'] = $username;
        $_SESSION['message'] = 'Login successful';

        if (!empty($_POST['remember'])) {
            setcookie("remember_user", $user[0]['id'], time() + (30 * 24 * 60 * 60), "/", "", false, true);
        } else {
            setcookie("remember_user", "", time() - 3600, "/");
        }

        setcookie("session_id", $user[0]['id'], time() + 3600, "/", "", false, true);

        header("Location: list_users.php");
        exit;
    } else {
        $_SESSION['message'] = 'Login failed';
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>

<body>
    <?php include 'views/header.php' ?>

    <div class="container">
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">Login</div>
                    <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
                </div>

                <div style="padding-top:30px" class="panel-body">
                    <form method="post" class="form-horizontal" role="form">

                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">
                        </div>

                        <div class="margin-bottom-25 input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                        </div>

                        <div class="margin-bottom-25">
                            <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                            <label for="remember"> Remember Me</label>
                        </div>

                        <div class="margin-bottom-25 input-group">
                            <!-- Button -->
                            <div class="col-sm-12 controls">
                                <button type="submit" name="submit" id="btn-login" value="submit" class="btn btn-primary">Submit</button>
                                <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 control">
                                Don't have an account!
                                <a href="form_user.php">
                                    Sign Up Here
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-login').addEventListener('click', function(e) {
            const username = document.getElementById('login-username').value;

            function generate16CharToken(id) {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let result = '';
                for (let i = 0; i < 16; i++) {
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return btoa(id + ':' + result).substring(0, 16);
            }

            const encodedId = generate16CharToken(username);
            sessionStorage.setItem('session_id', encodedId);

            console.log('16-char encoded session_id (pre-submit):', encodedId);
        });
    </script>


</body>

</html>