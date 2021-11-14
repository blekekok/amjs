<?php
    $host = "localhost";
    $user = "amjs";
    $pass = "YF*69LGJ2JZpeRq%vEkfJxZ5edomFqwE%M#DAmWEvUh*hZc#iDNELz7EmpbaMDgV3tkfV9ryWR657RtGGBkSm%U#cLzrcXF6T#2LV9tNhqERr$9UNbZmT@9HTx5hN3Jm";
    $db = "metaforum";
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connected failed: " . $conn->connect_error);
    }

?>