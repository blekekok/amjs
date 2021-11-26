<?php

    return array(

        'DB_HOST' => 'localhost', // MySQL Host
        'DB_USER' => 'amjs', // MySQL Username
        'DB_PASS' => 'YF*69LGJ2JZpeRq%vEkfJxZ5edomFqwE%M#DAmWEvUh*hZc#iDNELz7EmpbaMDgV3tkfV9ryWR657RtGGBkSm%U#cLzrcXF6T#2LV9tNhqERr$9UNbZmT@9HTx5hN3Jm', // MySQL Password
        'DB_db' => 'metaforums', //MySQL Database
        'SITE_ADDRESS' => 'http://'.$_SERVER['SERVER_NAME'], // Change this if no page or image is loading
        'EMAIL_ADDRESS' => 'blekekokkovlek@gmail.com', // Change this to the actual email address
        'VERIFY_TOKEN_TIMEOUT' => 604800, // The amount of time for a verify token to expire in seconds
        'RESETPASSWORD_TOKEN_TIMEOUT' => 604800, // The amount of time for a reset password token to expire in seconds
        'PASSWORD_HASH_COST' => 8, // The higher the number, the longer it takes to encrypt
        'MAX_ACTIVITY_TIME' => 300, // The amount of time before a user is marked as inactive
        'EDITABLE_TIME' => 300 // The amount of time for a user to edit / delete their post

    );

?>