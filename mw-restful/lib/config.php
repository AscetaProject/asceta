<?php
    // prevent file from being accessed directly
    if ('config.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
        die('Please do not access this file directly. Thanks!');
    }

    //Data base fields
    $dbf = array('pages' => array('page_id','page_title','text','summary'),
                 'users' => array('user_id', 'user_name', 'user_real_name', 'user_password', 'user_email')
    );

    //json fields
    $jf = array('pages' => array(),
                'users' => array('name'=>'name', 'realname'=>'realname', 'password'=>'password', 'email'=>'email')

    );
?>