<?php

require_once "../../functions/functions.php";

ini_set('session.save_path', '../../../data/sessions');
session_start();

if (!empty($_POST)) {

    $dir = "../../../data/users/users_files/$_SESSION[id]";

    $image_number = in_check($_POST['image_id']);

    $users_json = file_get_contents("../../../data/users/users.json");
    $users_json = json_decode($users_json, true);

    foreach ($users_json as $user) {

        if ($user['id'] === $_SESSION['id']) {

            unset($user['profile_pic'][$image_number]);
            unset($_SESSION['profile_pic'][$image_number]);
        }

        $users[] = $user;
    }

    $users = json_encode($users, JSON_PRETTY_PRINT);
    $users = file_put_contents("../../../data/users/users.json", $users);

    echo "1";
}

var_dump($_POST);
