<?php

require_once "../functions/functions.php";

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

if (!empty($_POST)) {

    $to_ajax = [];

    $message_number = in_check($_POST['id']);

    $messages_json = file_get_contents("../../data/users/groups/public_1/messages.json");
    $messages_json = json_decode($messages_json, true);

    foreach ($messages_json as $message) {

        if ($message['message_number'] > $message_number) {

            $date_to_ajax = date_parse_from_format("Y-m-d H:i:s", $message['date']);

            if ($date_to_ajax['hour'] >= 12) {

                $hour = $date_to_ajax['hour'] - 12;
                $time = "PM";
            } else {

                $hour = $date_to_ajax['hour'];
                $time = "AM";
            }

            $date_to_ajax = $hour . ":" . $date_to_ajax['minute'] . " " . $time;

            $message_to_ajax = [
                "id" => $message['id'],
                "message_number" => $message['message_number'],
                "name" => $message['name'],
                "username" => $message['username'],
                "date" => $date_to_ajax,
                "message" => $message['message']
            ];

            $to_ajax[] = $message_to_ajax;
        }
    }

    $to_ajax = json_encode($to_ajax);

    echo $to_ajax;
}
