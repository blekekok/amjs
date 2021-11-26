<?php

    include 'src/php/database.php';

    $user = $_GET['user'];

    if (!isset($user)) {
        header('Location: index.php');
        die();
    }

    include 'src/php/profile-page.php';

?>