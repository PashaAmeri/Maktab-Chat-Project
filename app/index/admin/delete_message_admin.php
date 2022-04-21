<?php

require_once "../../functions/functions.php";

ini_set('session.save_path', '../../../data/sessions');
session_start();

if (!empty($_POST)) {

    $admin_validation = true;

    $message_number = in_check($_POST['id']);

    $members_json = file_get_contents("../../../data/users/groups/public_1/members.json");
    $members_json = json_decode($members_json, true);

    foreach ($members_json as $member) {

        if ($member['id'] === $_SESSION['id'] and $member['role'] === "admin") {

            $admin_validation = true;
            continue;
        }

        if ($member['id'] !== $_SESSION['id']) {

            $messages_json = file_get_contents("../../../data/users/groups/public_1/messages.json");
            $messages_json = json_decode($messages_json, true);

            foreach ($messages_json as $message) {

                if ($message['message_number'] != $message_number) {

                    $out_put_messages[] = $message;
                }
            }

            break;
        }
    }

    $out_put_messages = json_encode($out_put_messages, JSON_PRETTY_PRINT);
    file_put_contents("../../../data/users/groups/public_1/messages.json", $out_put_messages);

    echo "done";
}
