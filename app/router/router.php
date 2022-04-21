<?php

//this page is for Routing user
$user_exist = false;

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

if (isset($_COOKIE['user_token'])) {

    unset($_SESSION["nickname"]);
    unset($_SESSION["phone_number"]);
    unset($_SESSION["about"]);

    $token = explode(":", $_COOKIE['user_token']);

    $users_json = file_get_contents("../../data/users/users.json");
    $users_json = json_decode($users_json, true);

    for ($i = 0; $i < sizeof($users_json); $i++) {

        if ($users_json[$i]['token']['usertoken'] === $token[0] and $users_json[$i]['token']['validator'] === $token[1]) {

            $_SESSION["username"] = $users_json[$i]['user_name'];
            $_SESSION["id"] = $users_json[$i]['id'];
            $_SESSION["name"] = $users_json[$i]['name'];
            $_SESSION["email"] = $users_json[$i]['email'];
            $_SESSION["profile_pic"] = $users_json[$i]['profile_pic'];

            if ($users_json[$i]['nickname'] !== "") {

                $_SESSION["nickname"] = $users_json[$i]['nickname'];
            }

            if ($users_json[$i]['phone_number'] !== "") {

                $_SESSION["phone_number"] = $users_json[$i]['phone_number'];
            }

            if ($users_json[$i]['about'] !== "") {

                $_SESSION["about"] = $users_json[$i]['about'];
            }

            $users_json[$i]['last_login'] = date("Y-m-d H:i:s");

            $users_json = json_encode($users_json, JSON_PRETTY_PRINT);
            file_put_contents("../../data/users/users.json", $users_json);

            $user_exist = true;

            break;
        } else {

            $user_exist = false;
        }
    }
}

if ($user_exist) {

    $_SESSION['status'] = true;

    header("location: ../../index.php");
} else {

    session_destroy();
    setcookie('user_token', "", time() - 1, "/");

    header("location: ../../login.php");
}
