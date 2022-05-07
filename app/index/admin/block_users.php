<?php

require_once "../../functions/functions.php";

ini_set('session.save_path', '../../../data/sessions');
session_start();

if (!empty($_POST)) {

    $username = in_check($_POST['user']);

    //connectig to db

    $db_user = 'root';
    $db_password = '1234';

    $db_host = 'localhost';
    $db_name = 'chat_project';

    $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

    $pdo = new PDO($db_dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //check for user
    $stm = $pdo->prepare("SELECT role FROM chats_members WHERE user_id = :id");
    $stm->execute(['id' => $_SESSION['id']]);

    $is_admin = $stm->fetch();

    if ($is_admin['role'] === 'admin') {

        //deleting message from db
        $stm = $pdo->prepare("UPDATE `chats_members` SET `role` = 'block' WHERE `chats_members`.`user_id` = ?;");
        $stm->execute([$username]);
    }

    echo 'done';
}
