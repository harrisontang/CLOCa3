<?php
    include('tools.php');

    // First check if email already exists: 
    // if true -> email already exists
    // if false -> function = post, store user in dynamo
    //      -> then upload default profile image to s3
    function registerCheck() {
        $_SESSION['imagePath'] = 'assets/images/default-profile.jpg';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            // checking if email already exists
            $function = 'get';
            $result = userHandler($email, $password, $function);

            if (isset($result['body']['Item'])) {
                echo "Email already exists";
            } else {

                // creating new user entry in dynamo table
                $function = 'post';
                $result = userHandler($email, $password, $function);
                echo $result['body'];   // message: successfully uploaded

                // supply email and filepath to postImage method for api processing
                $result = profileImageHandler($email, $function);

                header("refresh:1; url=index.php");
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/styles.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
                <p><a href="index.php">Already have an account? Click here</a></p>

                <?php registerCheck(); ?>

            </div>
        </form>
    </div>
</body>
</html>

<!-- debug area -->
<!-- <div style="width: 80%; margin: 0 auto; background-color: lightgray; padding: 20px;">
<?php
    // debugModule();
?>
</div> -->
