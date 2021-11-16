<?php

    session_start();

    if (!isset($_SESSION['role'])) {
        header('Location: index.php');
        die();
    }

    //Clear all sessions
    session_unset();
    
    header('Location: index.php');
    die();

?>