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
            if (isset($_SESSION['role']) && canUserCreateThread($conn, $_SESSION['username'])) {
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
        <div id="content-header">

        </div>
        <div id="thread-content">
            <div class="post-wrapper">
                <div class="post-header">
                    <span>Main Post</span>
                    <div>
                        <img src="src/res/clock.svg" alt="">
                        <span>17 minutes ago</span>
                    </div>
                </div>
                <div class="post-content">
                    <div class="user-info">
                        <div class="user-profile">
                            <img src="src/res/temp-image.jpg" alt="">
                            <span class="username">RusticKey</span>
                            <span class="status">Online</span>
                        </div>
                        <div class="user-data">
                            <div><img src="src/res/user-dark.svg" alt=""><span>User</span></div>
                            <div><img src="src/res/pencil.svg" alt=""><span>5 posts</span></div>
                            <div><img src="src/res/login.svg" alt=""><span>37 minutes ago</span></div>
                            <div><img src="src/res/info.svg" alt=""><span>Active</span></div>
                        </div>
                    </div>
                    <div class="post-body">
                        <p>
                        
                        </p>
                    </div>
                </div>
                <div class="post-footer">
                    <div class="favorite">
                        <img src="src/res/heart.svg" alt="">
                        <span>1 user favorited this post</span>
                    </div>
                    <div class="post-buttons">
                        <button>
                            <img src="src/res/heart.svg" alt="">
                        </button>
                        <button>
                            <img src="src/res/reply.svg" alt="">
                        </button>
                        <button>
                            <img src="src/res/trash-bin.svg" alt="">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="src/script/main-page.js"></script>
</body>
</html>

