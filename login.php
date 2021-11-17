<?php

    session_start();
    if (isset($_SESSION['role'])) {
        header('Location: index.php');
        die();
    }

    include 'src/php/database.php';
    include 'src/php/authentication.php';

    // Upon clicking register button
    if (isset($_POST['login-button'])) {

        $user = $_POST['user'];
        $password = $_POST['password'];
        
        if (isset($user) && isset($password)) {
            if (AuthenticateUser($conn, $user, $password)) {
                header('Location: index.php');
                die();
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('error' => 'Unable to authenticate account'));
            }
        } 
        
        return;
        
    }

    $action_type = $_POST['action'];

    switch ($action_type) {
        case 'username_exist':
            // Upon requesting user existance
            $username = $_POST['username'];
            if (isset($username)) {
                echo UserExist($conn, $username);
                return;
            }
            break;

        case 'email_exist':
            // Upon requesting user existance
            $email = $_POST['email'];
            if (isset($email)) {
                echo EmailExist($conn, $email);
                return;
            }
            break;

        case 'acc_auth':
            //Upon requesting user authentication
            $user = $_POST['user'];
            $pass = $_POST['pass'];
            if (isset($user) && isset($pass)) {
                echo json_encode(array('response' => AccountAuth($conn, $user, $pass)));
                return;
            }
            break;
    }

    include 'src/php/login-form.php';

?>