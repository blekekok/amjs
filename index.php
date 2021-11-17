<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';
    include 'src/php/thread-manager.php';

    $action_type = $_POST['action'];

    switch($action_type) {

        case 'get-groups':
            echo getGroups($conn);
            return;
            break;

        case 'get-categories':
            $groupid = $_POST['groupid'];
            if (isset($groupid))
                echo getCategories($conn, $groupid);
            return;
            break;

        case 'get-thread-titles':
            $categoryid = $_POST['categoryid'];
            if (isset($categoryid))
                echo getThreadTitles($conn, $categoryid);
            return;
            break;

        case 'get-thread-content':
            $threadid = $_POST['threadid'];
            if (isset($threadid)) {
                echo getThreadContent($conn, $threadid);
            }
            return;
            break;
    }

    include 'src/php/main-page.php';
?>