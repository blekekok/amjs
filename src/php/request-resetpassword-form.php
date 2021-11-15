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
        <form onsubmit="return requestPasswordReset()">
            <div>
                <input type="email" id="email" name="email" placeholder="E-mail" value="">
                <img src="src/res/mail.svg" alt="">
            </div>
            <label id="success-message">error!</label>
            <input type="submit" name="request-resetpassword-button" id="request-resetpassword-button" value="SEND PASSWORD RESET LINK">
        </form>
    </div>

</body>
</html>