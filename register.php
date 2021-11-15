<?php

    include 'src/php/authentication.php';
    include 'src/php/database.php';

    if (isset($_SESSION['session_active'])) {
        header('Location: index.php');
        die();
    }

    // Upon clicking register button
    if (isset($_POST['register-button'])) {

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($username && $email && $password) {
            if (CreateNewUser($conn, $username, $email, $password)) {
                header('Location: login.php');
                die();
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('error' => 'Unable to create account'));
            }
        } 

        return;
        
    }

    // Upon requestion user existance
    if ($username = $_REQUEST['user_exist']) {
        echo UserExist($conn, $username);
        return;
    }

    include 'src/php/register-form.php';
?>