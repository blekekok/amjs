<?php

    include 'src/php/profile-module.php';

    $query = $conn->prepare('SELECT timestampdiff(DAY, last_username_change, NOW()) >= 30 as usernameChange, verified, about FROM MFUsers WHERE id = ?;');
    $query->bind_param('i', $_SESSION['userid']);
    $query->execute();
    
    $result = $query->get_result();
    if (!$result || $result->num_rows <= 0) {
        header('Location: index.php');
        die();
    }
    $data = $result->fetch_assoc();

    if (strlen($data['usernameChange']) <= 0) $data['usernameChange'] = 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/styles.css">
    <link rel="stylesheet" href="src/style/account.css">
    <link rel="stylesheet" href="src/style/profile.css">
    <script src="src/script/jquery.js"></script>
    <title>Metaforums - Account</title>
</head>
<body>
    <?php include 'src/php/page-header.php'; ?>
    <div id="tab-nav">
        <ul>
            <li id="profile-button" class="active">Profile</li>
            <li id="management-button">Account Managemenent</li>
        </ul>
        <div id="content">
            <div id="profile-tab">
                <div class="profile-notify">
                    This is how your profile page will appear to others in public.
                </div>
                <?php displayProfile($conn, $_SESSION['userid']); ?>
            </div>
            <div id="management-tab">
                <form action="<?php $_PHP_SELF ?>" method="post" enctype="multipart/form-data" onsubmit="return profileValidate()">
                    <div>
                        <label for="username">Display Name (every 30 days)</label>
                        <input type="text" name="username" id="username-input" value="<?php echo $_SESSION['username'] ?>" <?php if (!$data['usernameChange']) echo 'disabled'; ?>>
                    </div>
                    <div>
                        <label for="about">About</label>
                        <input type="text" name="about" id="about" value="<?php echo $data['about'] ?>">
                    </div>
                    <div>
                        <label for="email-visibility">Display e-mail on profile</label>
                        <input type="checkbox" name="email-visibility" id="email-visibility" <?php if ($_SESSION['email_visibility']) echo 'checked'; ?>>
                    </div>
                    <div>
                        <label for="image-upload">Avatar</label>
                        <input name="image-upload" type="file" accept="image/png, image/gif, image/jpeg">
                    </div>
                    <input type="submit" name="save-button" value="Save">
                </form>
                <hr>
                <form action="<?php $_PHP_SELF ?>" method="post" onsubmit="return changeValidate()">
                    <div>
                        <label for="newPassword">Change Password</label>
                        <input type="password" name="newPassword" id="newPassword" placeholder="New Password">
                        <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password">
                    </div>    
                    <div>
                        <label for="email">E-mail Address</label>
                        <input type="email" name="email" id="email" placeholder="E-mail" value="<?php echo $_SESSION['email'] ?>" <?php if(!$data['verified']) echo 'disabled'; ?>>
                    </div>
                    <div>
                        <label for="delete-account">Delete Account</label>
                        <input type="text" name="delete-account" id="delete-account" placeholder="Re-input your username here and submit">
                    </div>
                    <div>
                        <label for="password">Input Password</label>
                        <input type="password" name="password" id="password" placeholder="Input your password to submit changes">
                    </div>
                    <input type="submit" name="submit-button" value="Submit Changes">
                </form>
            </div>
        </div>
        
        <script src="src/script/account-page.js"></script>
</body>
</html>