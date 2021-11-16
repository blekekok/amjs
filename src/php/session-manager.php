<?php

    if (session_status() != 3) session_start();

    if (!$_SESSION['role']) $_SESSION['role'] = 'guest';

?>