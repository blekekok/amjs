<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';

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

    $action_type = $_POST['action'];

    switch ($action_type) {
        case 'username_exist':
            // Upon requesting user existance
            $username = $_POST['username'];
            if ($username) {
                echo UserExist($conn, $username);
                return;
            }
            break;

        case 'email_exist':
            // Upon requesting user existance
            $email = $_POST['email'];
            if ($email) {
                echo EmailExist($conn, $email);
                return;
            }
            break;
    }

    include 'src/php/register-form.php';
?>