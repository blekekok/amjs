<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';
    include 'src/php/thread-manager.php';

    $action_type = $_POST['action'];

    if (isset($action_type)) {

        switch($action_type) {
    
            case 'get-groups':
                echo getGroups($conn);
                break;
    
            case 'get-categories':
                $groupid = $_POST['groupid'];
                if (isset($groupid))
                    echo getCategories($conn, $groupid);
                break;
    
            case 'get-thread-titles':
                $categoryid = $_POST['categoryid'];
                if (isset($categoryid))
                    echo getThreadTitles($conn, $categoryid);
                break;
    
            case 'get-thread-content':
                $threadid = $_POST['threadid'];
                if (isset($threadid)) {
                    echo getThreadContent($conn, $threadid);
                }
                break;

            case 'post-like':
                $id = $_POST['id'];
                $isLike = $_POST['islike'];
                $isThread = $_POST['isthread'];
                if (isset($id) && isset($isLike) && isset($isThread)) {
                    if ($isThread) {
                        echo likeThreadPost($conn, $id, $isLike);
                    } else {
                        echo likePost($conn, $id, $isLike);
                    }
                }
                break;

            case 'get-userdata':
                echo getUserData($conn);
                break;

            case 'thread-create':
                $categoryid = $_POST['categoryid'];
                $title = $_POST['title'];
                $content = $_POST['content'];
                if (isset($categoryid) && isset($title) && isset($content)) {
                    echo createThread($conn, $categoryid, $title, $content);
                }
                break;

            case 'thread-reply':
                $threadid = $_POST['threadid'];
                $content = $_POST['content'];
                if (isset($threadid) && isset($content)) {
                    echo createReply($conn, $threadid, $content);
                }
                break;
            
            case 'post-edit':
                $id = $_POST['id'];
                $isThread = $_POST['isThread'];
                $content = $_POST['content'];
                if (isset($id) && isset($isThread) && isset($content)) {
                    echo postEdit($conn, $id, $isThread, $content);
                }
                break;

            case 'post-delete':
                $id = $_POST['id'];
                $isThread = $_POST['isThread'];
                if (isset($id) && isset($isThread)) {
                    echo postDelete($conn, $id, $isThread);
                }
                break;
        }

        return;
    }


    include 'src/php/main-page.php';
?>