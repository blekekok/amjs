<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';

    $action_type = $_POST['action'];

    // Reset Password Request
    if ($action_type == 'reset') {
        $email = $_POST['email'];
        if ($email) echo RequestResetPassword($conn, $email);
        return;
    }

    $email = $_GET['email'];
    $token = $_GET['token'];

    // If params doesn't exist
    if (!$email || !$token) {
        include 'src/php/request-resetpassword-form.php';
        return;
    }

    // Reset Password
    if (isset($_POST['resetpassword-button'])) {

        

        return;
    } else if (ResetPassword($conn, $email, $token, true)) {
     
        echo 'hello';

        return;
    } else {

        include 'src/php/invalid-resetpassword.php';

    }

?>