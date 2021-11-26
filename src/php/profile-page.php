<?php

    include 'src/php/profile-module.php';

    $query = $conn->prepare('SELECT id FROM MFUsers WHERE username = ?;');
    $query->bind_param('s', $user);
    $query->execute();
    
    $result = $query->get_result();
    if (!$result || $result->num_rows < 1) {
        header('Location: index.php');
        die();
    }

    $data = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/styles.css">
    <link rel="stylesheet" href="src/style/profile.css">
    <title>Metaforums - Profile</title>
</head>
<body>
    <?php 
        include 'src/php/page-header.php'; 
        displayProfile($conn, $data['id']);
    ?>
</body>
</html>