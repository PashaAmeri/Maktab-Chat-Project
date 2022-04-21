<?php

require_once "../../functions/functions.php";

ini_set('session.save_path', '../../../data/sessions');
session_start();

if (!empty($_POST)) {

    $blocked = false;
    $admin_validation = false;

    $username = in_check($_POST['user']);

    $members_json = file_get_contents("../../../data/users/groups/public_1/members.json");
    $members_json = json_decode($members_json, true);

    foreach ($members_json as $member) {

        if ($member['id'] === $_SESSION['id'] and $member['role'] === "admin") {

            $admin_validation = true;
        }

        if ($member['user_name'] === $username) {

            $member['role'] = "block";
            $blocked = true;
        }

        $users_b[] = $member;
    }

    if ($blocked and $admin_validation) {

        $users_b = json_encode($users_b, JSON_PRETTY_PRINT);
        file_put_contents("../../../data/users/groups/public_1/members.json", $users_b);

        echo "done";
    }
}
