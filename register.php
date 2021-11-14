<?php

    include 'src/php/database.php';

    if (!empty($_POST)) {

        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];

        if ($email && $username && $password) {
            echo $email;
            echo $username;
            echo $password;
            // PUT IN AUTH CODE HERE
            // PUT IN AUTH CODE HERE
            // PUT IN AUTH CODE HERE
            // PUT IN AUTH CODE HERE
            // PUT IN AUTH CODE HERE
            // PUT IN AUTH CODE HERE
        } 
        
        return;
        
    }

    if ($username = $_REQUEST["user_exist"]) {

        // SQL Injection Prevention
        $query = $conn->prepare('SELECT username FROM users WHERE username LIKE ?');
        $query-> bind_param('s', $username);
        $query->execute();

        $result = $query->get_result();

        header("Content-Type: application/json");

        if (!$result) {
            echo json_encode(array('error' => 'An unknown error occured'));
            return;
        }

        echo json_encode(array('response' => boolval($result->num_rows)));

        return;
    }

    include 'src/php/register-form.php';
?>