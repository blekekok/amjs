<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/styles.css">
    <link rel="stylesheet" href="src/style/auth-form.css">
    <script src="src/script/jquery.js"></script>
    <script src="src/script/resetpassword.js"></script>
    <title>Metaforums - Reset Password</title>
</head>
<body>
    <header>
        <div class="header-left">
            <a href="register.php">SIGN UP</a>
        </div>
        <a class="logo" href="index.php">
            <img src="src/res/logo.png" alt="">
        </a>
        <div class="header-right">

        </div>
    </header>

    <div class="content">
        <form action="<?php $_PHP_SELF ?>" method="post" onsubmit="return resetPassword()">
            <div>
                <input type="password" id="password" name="password" placeholder="Password" value="">
                <img src="src/res/lock.svg" alt="">
            </div>
            <div>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" value="">
                <img src="src/res/lock.svg" alt="">
            </div>
            <label id="error-message">error!</label>
            <input type="submit" name="changepassword-button" id="changepassword-button" value="CHANGE PASSWORD">
        </form>
    </div>

</body>
</html>