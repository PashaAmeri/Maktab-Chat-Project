<?php

require_once "../functions/functions.php";

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

if (!empty($_POST)) {

    $date = date("Y-m-d H:i:s");

    $message = in_check($_POST['message']);

    if (strlen($message) <= 100) {

        $messages_json = file_get_contents("../../data/users/groups/public_1/messages.json");
        $messages_json = json_decode($messages_json, true);

        $i = sizeof($messages_json);
        $message_id = $messages_json[$i - 1];

        $system_message_join = [
            'id' => $_SESSION['id'],
            'message_number' => ($message_id['message_number'] + 1),
            'name' => $_SESSION['name'],
            'username' => $_SESSION['username'],
            "date" => $date,
            'message' => $message,
        ];

        $messages_json[] = $system_message_join;

        $messages_json = json_encode($messages_json, JSON_PRETTY_PRINT);
        file_put_contents("../../data/users/groups/public_1/messages.json", $messages_json);

        echo "1";
    } else {

        echo "0";
    }
}
