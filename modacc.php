<?php

    include 'src/php/database.php';
    include 'src/php/authentication.php';

    $email = $_GET['email'];
    $token = $_GET['token'];

    // If params doesn't exist
    if (!$email || !$token) {
        header('Location: login.php');
        die();
    }

    // Verify user
    $query = $conn->prepare('SELECT * FROM MFAccChange A JOIN MFUsers B ON A.userid = B.id WHERE B.email = ? AND A.token = ?;');
    $query->bind_param('ss', $email, $token);
    $query->execute();
    
    $result = $query->get_result();
    if (!$result || $result->num_rows < 1) {
        header('Location: index.php');
        die();
    }
    
    $data = $result->fetch_assoc();

    $password = $data['password_hash'];
    if (!empty($data['new_password'])) $password = $data['new_password'];

    $email = $data['email'];
    if (!empty($data['new_email'])) {
        $email = $data['new_email'];

        $query = $conn->prepare('UPDATE MFUsers SET email = ?, verified = 0, verification_token = ?, verification_timestamp = CURRENT_TIMESTAMP WHERE id = ?;');

        $verification_token = bin2hex(openssl_random_pseudo_bytes(32));
        
        $query->bind_param('ssi', $email, $verification_token, $data['userid']);
        $query->execute();    
        
        SendVerificationEmail($email, $data['username'], $verification_token);
    }

    $query = $conn->prepare('UPDATE MFUsers SET password_hash = ?, deleted = ? WHERE id = ?;');
    $query->bind_param('sii', $password, $data['delete_account'], $data['userid']);
    $query->execute();

    $query = $conn->prepare('DELETE FROM MFAccChange WHERE userid = ?;');
    $query->bind_param('i', $data['userid']);
    $query->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/verify.css">
    <title>Metaforums - Confirm Changes</title>
</head>
<body>
    <div class="content">
        <img class="mail-icon" src="src/res/mail-check.svg" alt="">
        <h1>
        <?php echo 'Account Changes Confirmed'; ?>
        </h1>
        <a class="logo" href="index.php">
            <img src="src/res/logo.png" alt="">
        </a>
    </div>
</body>
</html>