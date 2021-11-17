<?php 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style/styles.css">
    <link rel="stylesheet" href="src/style/main.css">
    <script src="src/script/jquery.js"></script>
    <title>Metaforums</title>
</head>
<body>
    <?php include 'src/php/page-header.php' ?>
    <div class="seperator">
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
        <ul id="group-list" class="list group"></ul>
        <ul id="category-list" class="list category"></ul>
        <ul id="thread-list" class="thread"></ul>
    </div>
        
    <div id="bottom-seperator">
        <div class="seperator">
            <h1>Site</h1>
            <div class="line"></div>
        </div>
        <h2 class="active-user">Currently Active Users: <?php echo getActiveUser($conn, 'hello'); ?></h2>
    </div>

    <div id="content">
    </div>

    <script src="src/script/main-page.js"></script>
</body>
</html>

