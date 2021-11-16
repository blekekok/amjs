<?php
    $host = "localhost";
    $user = "amjs";
    $pass = "YF*69LGJ2JZpeRq%vEkfJxZ5edomFqwE%M#DAmWEvUh*hZc#iDNELz7EmpbaMDgV3tkfV9ryWR657RtGGBkSm%U#cLzrcXF6T#2LV9tNhqERr$9UNbZmT@9HTx5hN3Jm";
    $db = "metaforums";
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connected failed: " . $conn->connect_error);
    }

    session_start();
    
    // Update user activity
    if (isset($_SESSION['role'])) {
        $query = $conn->prepare('UPDATE users SET lastactivity=CURRENT_TIMESTAMP WHERE username LIKE ?;');
        $query->bind_param('s', $_SESSION['username']);
        $query->execute();
    }

    function getActiveUser($conn, $user) {

        $configs = include('src/php/config.php');

        $query = $conn->prepare('SELECT COUNT(lastactivity) AS totaluser FROM users WHERE timestampdiff(SECOND, lastactivity, NOW()) < ?;');
        $query->bind_param('i', $configs['MAX_ACTIVITY_TIME']);
        $query->execute();

        $result = $query->get_result();
        if (!$result) return 0;

        $data = $result->fetch_assoc();

        return $data['totaluser'];
    }

?>