<?php
    session_start();
    session_unset();
    include('tools.php');


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        loginCheck();
    }

    // WORKS
    // getImage();

    // Gets item form users table by email (pk) then 
    // checks item password against supplied password
    function loginCheck() {    
        $email = $_POST['email'];
        $password = $_POST['password'];
        $function = 'get';

        $result = userHandler($email, $password, $function);
        

        if (isset($result['body']['Item'])) {
            if ($password === $result['body']['Item']['password']) {
                $_SESSION['user'] = cleanResultGetUser($result);
                header("Location:main.php");
            } else {
                echo "Invalid password";
            }

        } else {
            echo "Invalid email";
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="username">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
                <p><a href="register.php">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>