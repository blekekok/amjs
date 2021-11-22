<?php

    function canUserCreateThread($conn, $username) {

        $query = $conn->prepare('SELECT username FROM MFUsers A JOIN MFModStatus B ON A.id = B.userid WHERE username LIKE ? AND verified = 1 AND level >= 1 AND timestampdiff(SECOND, NOW(), expirydate) >= 0;');
        $query->bind_param('s', $username);
        $query->execute();

        $result = $query->get_result();
        if (!$result || $result->num_rows >= 1) return false;

        return true;

    }

    function getGroups($conn) {

        $query = $conn->prepare('SELECT * FROM MFGroups;');
        $query->execute();

        $result = $query->get_result();
        if (!$result) return json_encode(array());

        $list = array();
        while ($data = $result->fetch_assoc()) {
            array_push($list, $data);
        }

        return json_encode($list);
    }

    function getCategories($conn, $id) {

        $query = $conn->prepare('SELECT * FROM MFCategories WHERE groupid = ?;');
        $query->bind_param('i', $id);
        $query->execute();

        $result = $query->get_result();
        if (!$result) return json_encode(array());

        $list = array();
        while ($data = $result->fetch_assoc()) {
            array_push($list, $data);
        }

        return json_encode($list);
    }

    function getThreadTitles($conn, $id) {

        $query = $conn->prepare('SELECT A.id,title,username AS author,TIMESTAMPDIFF(SECOND,A.lastactivity,NOW()) AS age,(SELECT COUNT(id) FROM MFPosts WHERE threadid = A.id) AS posts,(SELECT COUNT(*) FROM MFViews WHERE threadid = A.id) AS views, (((SELECT COUNT(*) FROM MFViews WHERE threadid = A.id) + ((SELECT COUNT(*) FROM MFPosts WHERE threadid = A.id) * 10)) / TIMESTAMPDIFF(SECOND, A.lastactivity, NOW())) AS ranking,(SELECT COUNT(*) >= 5 FROM MFPosts WHERE threadid = A.id AND creation_date >= date_sub(NOW(), INTERVAL 5 MINUTE)) as hot FROM MFThreads A JOIN MFUsers B ON A.authorid = B.id WHERE categoryid = ? ORDER BY hot DESC,ranking DESC;');
        $query->bind_param('i', $id);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result) return json_encode(array());

        $list = array();
        while ($data = $result->fetch_assoc()) {
            array_push($list, $data);
        }

        return json_encode($list);
    }

    function getThreadContent($conn, $id) {

        $configs = include('src/php/config.php');

        if (!isset($_SESSION['userid'])) return json_encode(array('response' => 401)); // Not authorized

        $query = $conn->prepare('SELECT A.id, A.id as threadId, title, categoryid as categoryId, groupid as groupId, A.creation_date as postDate, DATE_FORMAT(A.creation_date, "%M %D %Y %h:%i %p") as threadDate, C.displayname as categoryName, timestampdiff(SECOND, A.lastactivity, NOW()) as threadLastActivity, timestampdiff(SECOND, A.creation_date, NOW()) as postDate, avatar as avatarURL, username as author, timestampdiff(SECOND, B.lastactivity, NOW()) <= ? as active, role, ((SELECT COUNT(*) FROM MFThreads WHERE authorid = A.authorid) + (SELECT COUNT(*) FROM MFPosts WHERE authorid = A.authorid)) as totalUserPosts, timestampdiff(SECOND, lastlogin, NOW()) as lastLogin, (SELECT level FROM MFModStatus WHERE userid = A.authorid AND timestampdiff(SECOND, NOW(), expirydate) >= 0) as modStatus, (SELECT COUNT(*) FROM MFThreadLikes WHERE threadid = A.id) as totalPostLikes, (SELECT COUNT(*) FROM MFThreadLikes WHERE threadid = A.id AND userid LIKE ?) >= 1 as isLiked, B.username LIKE ? as isAuthor, body as postBody FROM MFThreads A JOIN MFUsers B ON A.authorid = B.id JOIN MFCategories C ON A.categoryid = C.id WHERE A.id = ?;');
        $query->bind_param('iisi', $configs['MAX_ACTIVITY_TIME'], $_SESSION['userid'], $_SESSION['username'], $id);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result)
            return json_encode(array('response' => 500)); // Error
        if ($result->num_rows < 1)
            return json_encode(array('response' => 404)); // Not found

        addThreadViewCount($conn, $id);

        $data = array('response' => 200, 'content' => $result->fetch_assoc());

        $query = $conn->prepare('SELECT A.id, threadid as threadId, timestampdiff(SECOND, A.creation_date, NOW()) as postDate, avatar as avatarURL, username as author, timestampdiff(SECOND, B.lastactivity, NOW()) <= ? as active, role, ((SELECT COUNT(*) FROM MFThreads WHERE authorid = A.authorid) + (SELECT COUNT(*) FROM MFPosts WHERE authorid = A.authorid)) as totalUserPosts, timestampdiff(SECOND, lastlogin, NOW()) as lastLogin, (SELECT level FROM MFModStatus WHERE userid = A.authorid AND timestampdiff(SECOND, NOW(), expirydate) >= 0) as modStatus, (SELECT COUNT(*) FROM MFPostLikes WHERE postid = A.id) as totalPostLikes, (SELECT COUNT(*) FROM MFPostLikes WHERE postid = A.id AND userid LIKE ?) >= 1 as isLiked, B.username LIKE ? as isAuthor, (SELECT username FROM MFUsers WHERE id = A.targetuserid) as targetUsername, body as postBody FROM MFPosts A JOIN MFUsers B ON A.authorid = B.id WHERE threadid = ? ORDER BY postDate DESC;');
        $query->bind_param('iisi', $configs['MAX_ACTIVITY_TIME'], $_SESSION['userid'], $_SESSION['username'], $id);
        
        $query->execute();
        
        $result = $query->get_result();
        
        $list = array();
        
        while ($item = $result->fetch_assoc()) {
            array_push($list, $item);
        }
        
        $data['posts'] = $list;

        return json_encode($data); // Success

    }

    function likeThreadPost($conn, $id, $isLike) {

        if (!isset($_SESSION['userid'])) return;

        $query = $conn->prepare('SELECT authorid FROM MFThreads WHERE id = ?');
        $query->bind_param('i', $id);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result || $result->num_rows < 1) 
            return json_encode(array('response' => false));

        $data = $result->fetch_assoc();
        if ($data['authorid'] == $_SESSION['userid']) return json_encode(array('response' => false));

        if ($isLike) {
            $query = $conn->prepare('INSERT IGNORE INTO MFThreadLikes (threadid, userid, liked_date) VALUES (?, ?, CURRENT_TIMESTAMP);');
        } else {
            $query = $conn->prepare('DELETE FROM MFThreadLikes WHERE threadid = ? AND userid = ?;');
        }

        $query->bind_param('ii', $id, $_SESSION['userid']);

        if (!$query->execute()) return json_encode(array('response' => false));
        
        return json_encode(array('response' => true));

    }

    function likePost($conn, $id, $isLike) {

        if (!isset($_SESSION['userid'])) return;

        $query = $conn->prepare('SELECT authorid FROM MFPosts WHERE id = ?');
        $query->bind_param('i', $id);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result || $result->num_rows < 1) 
            return json_encode(array('response' => false));

        $data = $result->fetch_assoc();
        if ($data['authorid'] == $_SESSION['userid']) return json_encode(array('response' => false));

        if ($isLike) {
            $query = $conn->prepare('INSERT IGNORE INTO MFPostLikes (postid, userid, liked_date) VALUES (?, ?, CURRENT_TIMESTAMP);');
        } else {
            $query = $conn->prepare('DELETE FROM MFPostLikes WHERE postid = ? AND userid = ?;');
        }

        $query->bind_param('ii', $id, $_SESSION['userid']);

        if (!$query->execute()) return json_encode(array('response' => false));

        return json_encode(array('response' => true));

    }

    function getUserData($conn) {

        $configs = include('src/php/config.php');

        if (!isset($_SESSION['userid'])) return json_encode(array('response' => 401)); // Not authorized

        $query = $conn->prepare('SELECT username as author, avatar as avatarURL, role, ((SELECT COUNT(*) FROM MFThreads WHERE authorid = A.id) + (SELECT COUNT(*) FROM MFPosts WHERE authorid = A.id)) as totalUserPosts, timestampdiff(SECOND, lastlogin, NOW()) as lastLogin, (SELECT level FROM MFModStatus WHERE userid = id AND timestampdiff(SECOND, NOW(), expirydate) >= 0) as modStatus, timestampdiff(SECOND, lastactivity, NOW()) <= ? as active FROM MFUsers A WHERE username = ?;');
        $query->bind_param('is', $configs['MAX_ACTIVITY_TIME'], $_SESSION['username']);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result)
            return json_encode(array('response' => 500)); // Error
        if ($result->num_rows < 1)
            return json_encode(array('response' => 404)); // Not found

        return json_encode(array('response' => 200, 'content' => $result->fetch_assoc()));

    }

    function createThread($conn, $categoryid, $title, $content) {

        if (!isset($_SESSION['userid'])) return json_encode(array('response' => 401)); // Not authorized

        if (!canUserCreateThread($conn, $_SESSION['userid'])) return json_encode(array('resoonse' => 403)); // Forbidden

        $query = $conn->prepare('INSERT INTO MFThreads (categoryid, authorid, creation_date, lastactivity, title, body) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, ?, ?);');
        
        $query->bind_param('iiss', $categoryid, $_SESSION['userid'], $title, $content);

        if (!$query->execute()) return json_encode(array('response' => 404));

        return json_encode(array('response' => 200, 'id' => $query->insert_id));

    }

    function addThreadViewCount($conn, $id) {
    
        if (!isset($_SESSION['userid'])) return;

        $query = $conn->prepare('INSERT IGNORE INTO MFViews (threadid, userid) VALUES (?, ?);');
        $query->bind_param('ii', $id, $_SESSION['userid']);
        $query->execute();
        
    }

?>