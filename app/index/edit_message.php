<?php

require_once "../functions/functions.php";

ini_set('session.save_path', '../../data/sessions');
session_start();

var_dump($_POST['id']);

if (isset($_POST['edit_to'])) {
    
    $i = 0;

    $message_number = in_check($_POST['id']);
    $new_message = in_check($_POST['edit_to']);

    $messages_json = file_get_contents("../../data/users/groups/public_1/messages.json");
    $messages_json = json_decode($messages_json, true);


    foreach ($messages_json as $message) {

        if ($message['message_number'] == $message_number and $message['id'] == $_SESSION['id']) {

            $edited = [
                'id' => $_SESSION['id'],
                'message_number' => $message['message_number'],
                'name' => $_SESSION['name'],
                'username' => $_SESSION['username'],
                'date' => "2022-03-22 21:42:51",
                'edit' => true,
                'message' => $new_message
            ];

            $out_put_messages[] = $edited;
        } else {

            $out_put_messages[] = $message;
        }

        $i++;
    }

    $out_put_messages = json_encode($out_put_messages, JSON_PRETTY_PRINT);
    file_put_contents("../../data/users/groups/public_1/messages.json", $out_put_messages);
}
