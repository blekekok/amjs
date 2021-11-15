<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/styles.css">
    <link rel="stylesheet" href="src/style/auth-form.css">
    <script src="src/script/jquery.js"></script>
    <script src="src/script/register.js"></script>
    <title>Metaforums - Sign Up</title>
</head>
<body>
    <header>
        <div class="header-left">
            <a href="login.php">LOG IN</a>
        </div>
        <a class="logo" href="index.php">
            <img src="src/res/logo.png" alt="">
        </a>
        <div class="header-right">

        </div>
    </header>

    <div class="content">
        <form action="<?php $_PHP_SELF ?>" method="post" onsubmit="return validate()">
            <div>
                <input type="email" id="email" name="email" placeholder="E-mail" value="">
                <img src="src/res/mail.svg" alt="">
            </div>
            <div>
                <input type="text" id="username" name="username" placeholder="Username" value="">
                <img src="src/res/user.svg" alt="">
            </div>
            <div>
                <input type="password" id="password" name="password" placeholder="Password" value="">
                <img src="src/res/lock.svg" alt="">
            </div>
            <div>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" value="">
                <img src="src/res/lock.svg" alt="">
            </div>
            <label id="error-message">error!</label>
            <input type="submit" name="register-button" value="SIGN UP">
        </form>
    </div>

</body>
</html>