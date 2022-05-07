<?php

require_once "../functions/functions.php";

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

if (!empty($_POST)) {

    $to_ajax = [];

    $message_number = in_check($_POST['id']);

    //connecting to db

    $db_user = 'root';
    $db_password = '1234';

    $db_host = 'localhost';
    $db_name = 'chat_project';

    $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

    $pdo = new PDO($db_dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //get user data
    $stm = $pdo->prepare("SELECT messages.ID , messages.from_id , messages.to_id , messages.message , messages.date , messages.edit , users.name , users.username , users.nickname FROM `messages` INNER JOIN users WHERE messages.to_id = :chat_id AND messages.ID > :last_message AND users.ID = messages.from_id");
    $stm->execute(['chat_id' => '1', 'last_message' => $message_number]);

    $messages = $stm->fetchAll();

    //prepare for sending dato to user

    foreach ($messages as $message) {

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
            "id" => $message['from_id'],
            "message_number" => $message['ID'],
            "name" => $message['name'],
            "username" => $message['username'],
            "date" => $date_to_ajax,
            "message" => $message['message'],
            "edit" => $message['edit']
        ];

        $to_ajax[] = $message_to_ajax;
    }

    $to_ajax = json_encode($to_ajax);
    // echo $message_number;
    echo $to_ajax;
}
