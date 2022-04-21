<?php

ini_set('session.save_path', '../../data/sessions');
session_start();

if (isset($_POST['logout'])) {

    if (isset($_COOKIE['user_token'])) {

        setcookie('user_token', "", time() - 1, "/");
    }

    session_destroy();
}

header("location: router.php");
