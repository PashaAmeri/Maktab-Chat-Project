<?php

require_once "../functions/functions.php";

ini_set('session.save_path', '../../data/sessions');
session_start();

if (!empty($_POST)) {

    $message_number = in_check($_POST['id']);

    $messages_json = file_get_contents("../../data/users/groups/public_1/messages.json");
    $messages_json = json_decode($messages_json, true);

    foreach ($messages_json as $message) {

        if ($_SESSION['id'] === $message['id']) {

            if ($message['message_number'] != $message_number) {

                $out_put_messages[] = $message;
            }
        } else {

            $out_put_messages[] = $message;
        }
    }

    $out_put_messages = json_encode($out_put_messages, JSON_PRETTY_PRINT);
    file_put_contents("../../data/users/groups/public_1/messages.json", $out_put_messages);
}
