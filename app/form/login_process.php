<?php

require_once "../functions/functions.php";
require_once "../functions/login-signup-fn.php";

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = $_POST['remember'] ? $_POST['remember'] : "";

    $username = strtolower(in_check($username));
    $password = in_check($password);
    $remember = in_check($remember);

    $users_json = file_get_contents("../../data/users/users.json");
    $users_json = json_decode($users_json, true);

    for ($i = 0; $i < sizeof($users_json); $i++) {

        if ($users_json[$i]['user_name'] === $username) {

            $user_exist = true;

            if (password_verify($password, $users_json[$i]['password'])) {

                $users_json[$i]['token']['validator'] = rand_str("validator");
                $users_json[$i]['last_login'] = date("Y-m-d H:i:s");

                if ($remember === "on") {

                    setcookie("user_token", $users_json[$i]['token']['usertoken'] . ":" . $users_json[$i]['token']['validator'], time() + (((60 * 60) * 24) * 30) * 6, "/");
                }

                $_SESSION["username"] = $users_json[$i]['user_name'];
                $_SESSION["id"] = $users_json[$i]['id'];
                $_SESSION["name"] = $users_json[$i]['name'];
                $_SESSION["email"] = $users_json[$i]['email'];
                $_SESSION["nickname"] = $users_json[$i]['nickname'];
                $_SESSION["phone_number"] = $users_json[$i]['phone_number'];

                $users_json = json_encode($users_json, JSON_PRETTY_PRINT);
                file_put_contents("../../data/users/users.json", $users_json);

                $user_exist = true;
                break;
            } else {

                $user_exist = false;
            }

            break;
        }
    }

    if ($user_exist) {

        $_SESSION['status'] = true;

        header("location: ../../index.php");
    } else {

        header("location: ../../login.php?error=true&type=user_not_found");
    }
}
