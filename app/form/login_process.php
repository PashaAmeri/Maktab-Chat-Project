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

    //database connections

    $db_user = 'root';
    $db_password = '1234';

    $db_host = 'localhost';
    $db_name = 'chat_project';

    $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

    $pdo = new PDO($db_dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stm = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stm->execute([$username]);

    if ($user_db = $stm->fetch()) {

        $user_exist = true;

        $date = date("Y-m-d H:i:s");

        var_dump($user_db);

        if (password_verify($password, $user_db['password'])) {

            // $user_db['last_login'] = $date;

            $new_validator =  hash('sha256', rand_str("validator"));

            $stm = $pdo->prepare("UPDATE token SET validator = :validator WHERE user_id = :id");
            $stm->execute(['validator' => $new_validator, 'id' => $user_db['ID']]);

            $stm = $pdo->prepare("SELECT * FROM token WHERE `user_id` = ?");
            $stm->execute([$user_db['ID']]);

            var_dump($token_db = $stm->fetch());

            if ($remember === "on") {

                setcookie("user_token", $token_db['token'] . ":" . $token_db['validator'], time() + (((60 * 60) * 24) * 30) * 6, "/");
            }

            $_SESSION["username"] = $user_db['username'];
            $_SESSION["id"] = $user_db['ID'];
            $_SESSION["name"] = $user_db['name'];
            $_SESSION["email"] = $user_db['email'];
            $_SESSION["nickname"] = $user_db['nickname'];
            $_SESSION["phone_number"] = $user_db['phone_number'];

            $user_exist = true;
        } else {

            $user_exist = false;
        }
    }

    if ($user_exist) {

        $_SESSION['status'] = true;

        header("location: ../../index.php");
    } else {

        header("location: ../../login.php?error=true&type=user_not_found");
    }
}
