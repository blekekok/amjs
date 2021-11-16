<?php 

    include 'src/php/database.php';
    include 'src/php/authentication.php';

    $_GET['test']  = 'hello';

    session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/styles.css">
    <link rel="stylesheet" href="src/style/main.css">
    <title>Metaforums</title>
</head>
<body>
    <?php include 'src/php/page-header.php' ?>
    <div class="top-seperator">
        <h1>Forum Groups</h1>
        <div class="line"></div>
        <?php 
            //ADD HERE IF USER IS SILENCED
            if (isset($_SESSION['role']) && isUserVerified($conn, $_SESSION['username'])) {
                echo '
                    <button class="create-thread-button">
                        <img src="src/res/note.svg" alt="">
                        CREATE THREAD
                    </button>
                ';
            }

        ?>
    </div>

    <div class="thread-browser">
        <ul class="list group">
            <li><span>GENERAL</span></li>
            <li><span>WORLD</span></li>
            <li><span>ART</span></li>
            <li><span>ENTERTAINMENT</span></li>
            <li class="active"><span>VIDEO GAMES</span></li>
            <li><span>POLITICS</span></li>
            <li><span>OFF-TOPIC</span></li>
        </ul>
        <ul class="list category">
            <li><span>FIRST-PERSON SHOOTERS</span></li>
            <li><span>REAL-TEAM STRATEGY</span></li>
            <li class="active"><span>RPG</span></li>
            <li><span>MOBAGE</span></li>
            <li><span>BOARD GAMES</span></li>
            <li><span>MOBA</span></li>
            <li><span>HORROR</span></li>
            <li><span>SURVIVAL</span></li>
            <li><span>STORY GAMES</span></li>
            <li><span>ARCADE</span></li>
            <li><span>HOMEBREW</span></li>
            <li><span>OTHERS</span></li>
        </ul>
        <ul class="thread">
            <li>
                <span class="badge">[HOT]</span>
                <span class="title">Why you should give Outer Worlds a change (In-depth review)</span>
                <div class="meta">
                    <div class="sub-meta">
                        <span class="author">by Frixs</span>
                        <div class="views">
                            <img src="src/res/eye.svg" alt="">
                            <span>183</span>
                    </div>
                    </div>
                    <div class="posts">
                        <img src="src/res/comment.svg" alt="">
                        <span>999</span>
                    </div>
                    <span class="time">Moments ago</span>
                </div>
            </li>
        </ul>
    </div>
</body>
</html>

