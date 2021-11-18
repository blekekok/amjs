<?php

    function canUserCreateThread($conn, $username) {

        $query = $conn->prepare('SELECT username FROM MFUsers A JOIN MFSilences B ON A.id = B.userid WHERE username LIKE ? AND verified = 1;');
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

        $query = $conn->prepare('SELECT title,categoryid,groupid,displayname,username as author, timestampdiff(SECOND, A.lastactivity,NOW()) as lastactivity, DATE_FORMAT(A.creation_date, "%M %D %Y %h:%i %p") as creation_date FROM MFThreads A JOIN MFCategories B ON A.categoryid = B.id JOIN MFUsers C ON A.authorid = C.id WHERE A.id = ?;');
        $query->bind_param('i', $id);
        $query->execute();
        
        $result = $query->get_result();
        if (!$result)
            return json_encode(array('response' => 500)); // Error
        if ($result->num_rows < 1)
            return json_encode(array('response' => 404)); // Not found

        if (!isset($_SESSION['role']))
            return json_encode(array('response' => 401)); // Not authorized

        addThreadViewCount($conn, $id);

        return json_encode(array('response' => 200, 'content' => $result->fetch_assoc())); // Success

    }

    function addThreadViewCount($conn, $id) {
        
        session_start();
        if (!isset($_SESSION['role'])) return;

        $query = $conn->prepare('INSERT IGNORE INTO MFViews (threadid, userid) VALUES (?, (SELECT id FROM MFUsers WHERE username LIKE ?));');
        $query->bind_param('is', $id, $_SESSION['username']);
        $query->execute();
        
    }

?>