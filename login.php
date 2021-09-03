<?php 
 if(file_exists('todo.json')) {
    session_start();

    $json = file_get_contents('users.json');
    $users = json_decode($json, true);

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    foreach($users as $key => $value) {
        if($value['email'] == $email && $value['password'] == $password) {
            header('Location: index.php');
            $_SESSION['user_current'] = $key;
        }
    }
    // echo '<pre>';
    // var_dump($value);
    // echo '</pre>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link rel="stylesheet" href="./dist/css/login.css">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="new_user.php" method="post">
                <h1>Create Account</h1>
                <span style="display: block; margin-bottom: 2rem">or use your email for registration</span>
               
                <input type="text" style="display: none" value="1" name="user_id" class="user-id" placeholder="Name" />
                <input type="text" name="user_name" class="user-name" placeholder="Name" />
                <input type="email" name="user_email" placeholder="Email" />
                <input type="password" name="user_password" placeholder="Password" />
                <button>Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="login.php" method="post">
                <h1>Sign in</h1>
                <span style="display: block; margin-bottom: 2rem">or use your account</span>
                <input type="email" name="email" placeholder="Email" />
                <input type="password" name="password" placeholder="Password" />
                <button style="margin-top: 2rem;">Sign In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>Sign in to manage your tasks </p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start task management</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>
            Design ideas from 
            <a target="_blank" href="https://florin-pop.com" style="color: #fff;">Florin Pop</a>,
            <a target="_blank" href="https://www.youtube.com/channel/UClb90NQQcskPUGDIXsQEz5Q" style="color: #fff;">Dev Ed</a>,
            <a target="_blank" href="https://www.facebook.com/longdang24/">Me</a>.
        </p>
    </footer>

    <script src="./dist/js/jquery-3.6.0.min.js"></script>
    <script src="./dist/js/login.js"></script>
</body>
</html>
