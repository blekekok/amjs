<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';

    $username = $_REQUEST['user'];
    $token = $_REQUEST['token'];

    // If params doesn't exist
    if (!$username || !$token) {
        header('Location: login.php');
        die();
    }

    // Verify user
    $verifiedSuccess = false;
    if (VerifyUser($conn, $username, $token)) $verifiedSuccess = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/verify.css">
    <title>Metaforums - Verify</title>
</head>
<body>
    <div class="content">
        <img class="mail-icon" src="src/res/mail-<?php echo $verifiedSuccess ? 'check' : 'close' ?>.svg" alt="">
        <h1>
        <?php 
            if ($verifiedSuccess) {
                echo 'Account Verified';
            } else {
                echo 'The link is invalid or has expired';
            }
            ?>
        </h1>
        <a class="logo" href="index.php">
            <img src="src/res/logo.png" alt="">
        </a>
    </div>
</body>
</html>