<?php

    $configs = include('src/php/config.php');

    $conn = new mysqli($configs['DB_HOST'], $configs['DB_USER'], $configs['DB_PASS'], $configs['DB_db']);
    if ($conn->connect_error) {
        die("Connected failed: " . $conn->connect_error);
    }

    session_start();
    
    if (isset($_SESSION['role'])) {
        // Update user activity
        $query = $conn->prepare('UPDATE MFUsers SET lastactivity=CURRENT_TIMESTAMP WHERE username LIKE ?;');
        $query->bind_param('s', $_SESSION['username']);
        $query->execute();

        $query = $conn->prepare('SELECT deleted FROM MFUsers WHERE id = ? AND deleted = 1;');
        $query->bind_param('i', $_SESSION['userid']);
        $query->execute();

        $result = $query->get_result();
        if ($result && $result->num_rows >= 1) {
            session_unset();
    
            header('Location: index.php');
            die();
        }
    }

    function getActiveUser($conn, $user) {

        $configs = include('src/php/config.php');

        $query = $conn->prepare('SELECT COUNT(lastactivity) AS totaluser FROM MFUsers WHERE timestampdiff(SECOND, lastactivity, NOW()) < ?;');
        $query->bind_param('i', $configs['MAX_ACTIVITY_TIME']);
        $query->execute();

        $result = $query->get_result();
        if (!$result) return 0;

        $data = $result->fetch_assoc();

        return $data['totaluser'];
    }

?>