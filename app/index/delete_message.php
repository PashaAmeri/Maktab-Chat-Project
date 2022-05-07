<?php

require_once "../functions/functions.php";

ini_set('session.save_path', '../../data/sessions');
session_start();

if (!empty($_POST)) {

    $message_number = in_check($_POST['id']);

    //connecting to db

    $db_user = 'root';
    $db_password = '1234';

    $db_host = 'localhost';
    $db_name = 'chat_project';

    $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

    $pdo = new PDO($db_dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //deleting message from db
    $stm = $pdo->prepare("DELETE FROM `messages` WHERE ID = :message_id AND from_id = :user");
    $stm->execute(['message_id' => $message_number, 'user' => $_SESSION['id']]);
}
