<?php
    session_start();
    $role = $_SESSION['role'];
?>

<header>
    <div class="header-left">
        <?php 
            if (!isset($role)) {
                echo '
                    <a href="login.php">LOG IN</a>
                    <a href="register.php">SIGN UP</a>
                ';        
            } else {
                echo '
                    <a href="logout.php">LOG OUT</a>
                ';
            }

        ?>
    </div>
    <a class="logo" href="index.php">
        <img src="src/res/logo.png" alt="">
    </a>
    <div class="header-right">
        <?php

            if ($role == 'mod' || $role == 'admin')
                echo '<a href="manage.php">USER MANAGEMENT</a>';

            if (isset($role)) 
                echo '<a href="account.php">ACCOUNT</a>';

        ?>
    </div>
</header>