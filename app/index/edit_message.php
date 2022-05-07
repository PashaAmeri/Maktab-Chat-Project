<?php

require_once "../functions/functions.php";

ini_set('session.save_path', '../../data/sessions');
session_start();

var_dump($_POST['id']);

if (isset($_POST['edit_to'])) {

    $message_number = in_check($_POST['id']);
    $new_message = in_check($_POST['edit_to']);

    //connecting to db

    $db_user = 'root';
    $db_password = '1234';

    $db_host = 'localhost';
    $db_name = 'chat_project';

    $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

    $pdo = new PDO($db_dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //get user data
    $stm = $pdo->prepare("UPDATE `messages` SET message = :new_message , edit = :edit WHERE ID = :id AND from_id = :user");
    $stm->execute(['new_message' => $new_message, 'edit' => 1, 'id' => $message_number, 'user' => $_SESSION['id']]);
}
