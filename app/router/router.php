<?php

//this page is for Routing users
$user_exist = false;

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

if (isset($_COOKIE['user_token'])) {

    unset($_SESSION["nickname"]);
    unset($_SESSION["phone_number"]);
    unset($_SESSION["about"]);

    $token = explode(":", $_COOKIE['user_token']);

    $db_user = 'root';
    $db_password = '1234';

    $db_host = 'localhost';
    $db_name = 'chat_project';

    $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

    $pdo = new PDO($db_dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stm = $pdo->prepare("SELECT * FROM `token` WHERE token = :token AND validator = :validator");
    $stm->execute(['token' => $token[0], 'validator' => $token[1]]);

    $token_db = $stm->fetch();

    var_dump($token_db);


    if ($token_db['token'] === $token[0] and $token_db['validator'] === $token[1]) {

        $date = date("Y-m-d H:i:s");

        //get user data from db

        $stm = $pdo->prepare("SELECT * FROM `users` WHERE ID = ?");
        $stm->execute([$token_db['user_id']]);

        $user_db = $stm->fetch();

        var_dump($user_db);

        //get profile pics

        $stm = $pdo->prepare("SELECT profile_pics.address , profile_pics.ID FROM `profile_pics` WHERE user_id = ?");
        $stm->execute([$token_db['user_id']]);

        var_dump($pics_db = $stm->fetchAll());

        $_SESSION["username"] = $user_db['username'];
        $_SESSION["id"] = $user_db['ID'];
        $_SESSION["name"] = $user_db['name'];
        $_SESSION["email"] = $user_db['email'];

        if ($pics_db) {

            foreach ($pics_db as $pic) {

                $_SESSION["profile_pic"][] = [$pic['address'], $pic['ID']];
            }
        } else {

            $_SESSION["profile_pic"] = [];
        }

        if ($user_db['nickname'] !== "") {

            $_SESSION["nickname"] = $user_db['nickname'];
        }

        if ($user_db['phone_number'] !== "") {

            $_SESSION["phone_number"] = $user_db['phone_number'];
        }

        if ($user_db['about'] !== "") {

            $_SESSION["about"] = $user_db['about'];
        }

        // updating last login in db
        $stm = $pdo->prepare("UPDATE `users` SET last_login = ?");
        $stm->execute([$date]);

        $user_exist = true;
    } else {

        $user_exist = false;
    }
}

if ($user_exist) {

    $_SESSION['status'] = true;

    header("location: ../../index.php");
    exit;
} else {

    session_destroy();
    setcookie('user_token', "", time() - 1, "/");

    header("location: ../../login.php");
    exit;
}
