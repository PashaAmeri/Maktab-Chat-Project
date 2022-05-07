<?php

require_once "../functions/functions.php";

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

if (!empty($_POST)) {

    $date = date("Y-m-d H:i:s");

    $message = in_check($_POST['message']);

    if (strlen($message) <= 100) {

        //connecting to db

        $db_user = 'root';
        $db_password = '1234';

        $db_host = 'localhost';
        $db_name = 'chat_project';

        $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

        $pdo = new PDO($db_dsn, $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        //put messages in db
        $stm = $pdo->prepare("INSERT INTO `messages` (`from_id` , `to_id` , `message` , `date`) VALUES (:from , :to , :message , :date)");
        $stm->execute(['from' => $_SESSION['id'], 'to' => '1', 'message' => $message, 'date' => $date]);

        $user_db = $stm->fetch();

        echo "1";
    } else {

        echo "0";
    }
}
