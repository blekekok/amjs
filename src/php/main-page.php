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
    <link rel="stylesheet" href="src/style/quill.css">
    <script src="src/script/jquery.js"></script>
    <title>Metaforums</title>
</head>
<body>
    <div id="error-message">
        <div>
            <span>asdasdads</span>
            <button>
                <img src="src/res/close.svg" alt="">
            </button>
        </div>
    </div>
    <?php include 'src/php/page-header.php' ?>
    <div class="seperator">
        <h1>Forum Groups</h1>
        <div class="line"></div>
        <?php 
            if (isset($_SESSION['role']) && canUserCreateThread($conn, $_SESSION['username'])) {
                echo '
                    <button id="create-thread-button">
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
        <div id="content-header">

        </div>
        <div id="thread-content">

        </div>
    </div>

    <script src="src/script/quill.js"></script>
    <script src="src/script/main-page.js"></script>
</body>
</html>

