<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';

    if (!isset($_SESSION['userid'])) {
        header('Location: login.php');
        die();
    }
    
    if (isset($_POST['save-button'])) {
        $usernameChange = 0;
        if (isset($_POST['username']) &&
            $_POST['username'] != $_SESSION['username']) {
                $usernameChange = 1;
                $_SESSION['username'] = $_POST['username'];
        }

        if (isset($_POST['email-visibility'])) {
            $_SESSION['email_visibility'] = 1;
        } else {
            $_SESSION['email_visibility'] = 0;
        }
        
        if ($_FILES['image-upload']['error'] === 0) {
            $target_dir = 'public_res/'; 
            $newdir = $target_dir . $_SESSION['userid'] . '.' . end(explode('.', $_FILES['image-upload']['name']));
            if (move_uploaded_file($_FILES['image-upload']['tmp_name'], $newdir)) {
                $_SESSION['avatar'] = $newdir;
            }
        }

        if ($usernameChange) {
            $usernameChange = ', last_username_change=CURRENT_TIMESTAMP';
        } else {
            $usernameChange = '';
        }

        $query = $conn->prepare('UPDATE MFUsers SET username=?, email_visibility=?, avatar=?, about=? '. $usernameChange . ' WHERE id = ?;');
        $query->bind_param('sissi', $_SESSION['username'], $_SESSION['email_visibility'], $_SESSION['avatar'], $_POST['about'], $_SESSION['userid']);
        $query->execute();
    }

    if (isset($_POST['submit-button'])) {

        if (!empty($_POST['newPassword']) || 
            (!empty($_POST['delete-account']) && 
                $_POST['delete-account'] == $_SESSION['username']) || 
            (isset($_POST['email']) && $_POST['email'] != $_SESSION['email']))  {

                $newPassword = '';
                $newEmail = '';
                $deleteAccount = 0;

                if (!empty($_POST['newPassword'])) {
                    $newPassword = getPasswordHash($_POST['newPassword']);
                }

                if ($_POST['email'] != $_SESSION['email']) {
                    $newEmail = $_POST['email'];
                }

                if (!empty($_POST['delete-account']) && $_POST['delete-account'] == $_SESSION['username']) {
                    $deleteAccount = 1;
                }

                $token = bin2hex(openssl_random_pseudo_bytes(32));

                $query = $conn->prepare('REPLACE INTO MFAccChange (userid, token, new_password, new_email, delete_account) VALUES (?, ?, ?, ?, ?);');
                $query->bind_param('isssi', $_SESSION['userid'], $token, $newPassword, $newEmail, $deleteAccount);
                $query->execute();

                SendAccountChangeEmail($_SESSION['email'], $token);
        }

    }

    $action_type = $_POST['action'];
    
    switch ($action_type) {
        case 'username_exist':
            // Upon requesting user existance
            $username = $_POST['username'];
            if (isset($username)) {
                if ($username == $_SESSION['username']) {
                    echo json_encode(array('response' => false));
                    return;
                }
                echo UserExist($conn, $username);
                return;
            }
            break;

        case 'email_exist':
            // Upon requesting user existance
            $email = $_POST['email'];
            if (isset($email)) {
                if ($email == $_SESSION['email']) {
                    echo json_encode(array('response' => false));
                    return;
                }
                echo EmailExist($conn, $email);
                return;
            }
            break;
        
        case 'acc_auth':
            //Upon requesting user authentication
            $pass = $_POST['pass'];
            if (isset($pass)) {
                echo json_encode(array('response' => AccountAuth($conn, $_SESSION['username'], $pass)));
                return;
            }
            break;
    }

    include 'src/php/account-page.php';

?>