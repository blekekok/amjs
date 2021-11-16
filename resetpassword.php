<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';

    $action_type = $_POST['action'];

    // Reset Password Request for ajax
    if ($action_type == 'request') {
        $email = $_POST['email'];
        if ($email) echo RequestResetPassword($conn, $email);
        return;
    }

    $email = $_GET['email'];
    $token = $_GET['token'];

    // If params doesn't exist, redirect to password reset request
    if (!$email || !$token) {
        include 'src/php/request-resetpassword-form.php';
        return;
    }

    
    // Password reset POST request
    if (isset($_POST['changepassword-button'])) {
        
        $pass = $_POST['password'];
        if ($pass && ChangeUserPassword($conn, $email, $pass, $token)) {
            header('Location: login.php');
            die();
        } else {
            include 'src/php/invalid-resetpasword.php';
        }
        
        return;
        // Show a password reset page if link is valid
    } else if (CheckResetPasswordToken($conn, $email, $token)) {
        include 'src/php/resetpassword-form.php';
        return;
    } else {
        // Show a invalid link page if token is not found
        include 'src/php/invalid-resetpassword.php';
    }

?>