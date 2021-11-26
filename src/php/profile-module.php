<?php 



    function displayProfile($conn, $id) {

        $configs = include('src/php/config.php');

        if (!isset($_SESSION['userid'])) return json_encode(array('response' => 401)); // Not authorized

        $query = $conn->prepare('SELECT username, IF(A.email_visibility, A.email, "Hidden") as email, about, avatar as avatarURL, role, ((SELECT COUNT(*) FROM MFThreads WHERE authorid = A.id) + (SELECT COUNT(*) FROM MFPosts WHERE authorid = A.id)) as totalUserPosts, timestampdiff(SECOND, lastlogin, NOW()) as lastLogin, (SELECT level FROM MFModStatus WHERE userid = id AND timestampdiff(SECOND, NOW(), expirydate) >= 0) as modStatus, timestampdiff(SECOND, lastactivity, NOW()) <= ? as active, ((SELECT COUNT(*) FROM MFThreads X JOIN MFThreadLikes Y ON X.id = Y.threadid WHERE X.authorid = A.id) + (SELECT COUNT(*) FROM MFPosts X JOIN MFPostLikes Y ON X.id = Y.postid WHERE X.authorid = A.id)) as totalLikes, deleted FROM MFUsers A WHERE id = ?;');
        $query->bind_param('ii', $configs['MAX_ACTIVITY_TIME'], $id);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result || $result->num_rows < 1) {
            header('Location: index.php');
            die();
        }

        $data = $result->fetch_assoc();

        if ($data['deleted']) {
            header('Location: index.php');
            die();
        }

        $query = $conn->prepare('SELECT C.displayname as categoryName, D.displayname as groupName, categoryid , COUNT(*) as count FROM MFThreads A LEFT OUTER JOIN MFPosts B ON A.id = B.threadid JOIN MFCategories C ON A.categoryid = C.id JOIN MFGroups D ON C.groupid = D.id WHERE A.authorid = ? GROUP BY categoryid ORDER BY count DESC LIMIT 1;');
        $query->bind_param('i', $id);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result || $result->num_rows < 1) {
            header('Location: index.php');
            die();
        }

        $mostActiveData = $result->fetch_assoc();

        $query = $conn->prepare('SELECT id, title, username, lastActivity FROM (SELECT  A.id, A.title, B.username, timestampdiff(SECOND, A.lastactivity, NOW()) as lastActivity, A.creation_date as selfActivity FROM MFThreads A JOIN MFUsers B ON A.authorid = B.id WHERE authorid = ? UNION SELECT B.id, B.title, C.username, timestampdiff(SECOND, B.lastactivity, NOW()) as lastActivity, A.creation_date as selfActivity FROM MFPosts A JOIN MFThreads B ON A.threadid = B.id JOIN MFUsers C ON B.authorid = C.id WHERE A.authorid = ? ORDER BY selfActivity DESC) as SUB GROUP BY id, title, username, lastActivity LIMIT 5;');
        $query->bind_param('ii', $id, $id);
        $query->execute();
        
        $postResults = $query->get_result();
        if (!$postResults) {
            header('Location: index.php');
            die();
        }

        if (!isset($data['avatarURL']) || strlen($data['avatarURL']) <= 0) $data['avatarURL'] = 'src/res/default-user-icon.jpg';
        $data['active'] = $data['active'] ? 'Online' : 'Offline';

        switch ($data['role']) {
            case 'user':
                $data['role'] = 'User';
                break;

            case 'mod':
                $data['role'] = 'Moderator';
                break;

            case 'siteadmin':
                $data['role'] = 'Site Admin';
                break;
        }

        switch ($data['modStatus'])  {
            case 1:
                $data['modStatus'] = 'Active';
                break;

            case 2:
                $data['modStatus'] = 'Banned';
                break;

            default: 
                $data['modStatus'] = 'Active';
                break;
        }

        $data['lastLogin'] = formatTimeAsText($data['lastLogin'], true);
        

        echo "
            <div id=\"profile-wrapper\">
                <div class=\"profile-header\">
                    <span>{$data['username']}'s Profile</span>
                </div>

                <div class=\"profile-content\">
                    <div class=\"user-info\">
                        <div class=\"user-profile\">
                            <a class=\"avatar\"><img src=\"{$data['avatarURL']}\" alt=\"\"></a>
                            <a class=\"username\">{$data['username']}</a>
                            <span class=\"status\">{$data['active']}</span>
                        </div>
                        <div class=\"user-data\">
                            <div><img src=\"src/res/user-dark.svg\" alt=\"\"><span>{$data['role']}</span></div>
                            <div><img src=\"src/res/pencil.svg\" alt=\"\"><span>{$data['totalUserPosts']} posts</span></div>
                            <div><img src=\"src/res/login.svg\" alt=\"\"><span>{$data['lastLogin']}</span></div>
                            <div><img src=\"src/res/info.svg\" alt=\"\"><span>{$data['modStatus']}</span></div>
                        </div>
                    </div>
                    <div class=\"profile-body\">
                        <div class=\"about-wrapper\">
                            <span class=\"about-me\">About me</span>
                            <span class=\"about-content\">{$data['about']}</span>
                        </div>
                        <div class=\"profile-data\">
                            <div class=\"extra-info\">
                                <span>Additional Information</span>
                                <div>
                                    <span>Username          : {$data['username']}</span>
                                    <span>Email             : {$data['email']}</span>
                                    <span>Most Active In    : {$mostActiveData['categoryName']} ({$mostActiveData['groupName']})</span>
                                    <span>Number of Hearts  : {$data['totalLikes']}</span>
                                </div>
                            </div>
                            <div class=\"recent-posts\">
                                <span>Recent Post On</span>
                                <div class=\"post-list\">
                                    <ul>
                                    ";   
                                    
        if ($postResults->num_rows >= 1) {
            while ($post = $postResults->fetch_assoc()) {
                $post['lastActivity'] = formatTimeAsText($post['lastActivity'], true);
                echo "<li><a href=\"/index.php?thread={$post['id']}\">{$post['title']}</a><div><span>by {$post['username']}</span><span>{$post['lastActivity']}</span></div></li>";
            }
        }

        echo '                      </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ';
    }

    function formatTimeAsText($time, $short) {

        // Years
        if ($time >= 31536000) {
            $time = ($time / 31536000) | 0;
            if ($time == 1)
                return '1 year ago';
    
            return "{$time} years";
        }
    
        // Seconds
        if ($time < 60)
            return 'Moments ago';
            
        // Minutes
        $time =  ($time / 60) | 0;
        if ($time < 60)
            if ($short) {
                return "{$time}m ago";
            } else {
                if ($time == 1)
                    return "{$time} minute ago";
                
                return "{$time} minutes ago";
            }
    
        // Hours
        $time = ($time / 60) | 0;
        if ($time < 24) {
            if ($time == 1)
                return '1 hour ago';
    
            return "{$time} hours ago";
        }
    
        // Days
        $time =  ($time / 24) | 0;
        if ($time < 7) {
            if ($time == 1)
                return '1 day ago';
    
            return "{$time} days ago";
        }
    
        // Weeks
        $time =  ($time / 7) | 0;
        if ($time < 4) {
            if ($time == 1)
                return '1 week ago';
    
            return "{$time} weeks ago";
        }
    
        // Months
        $time = ($time / 4) | 0;
        if ($time < 12) {
            if ($time == 1)
                return '1 month ago';
    
            return "{$time} months ago";
        }
    
        return '1 year ago';
    }

?>