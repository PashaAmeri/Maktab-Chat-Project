<?php

require_once "../functions/login-signup-fn.php";
require_once "../functions/functions.php";

date_default_timezone_set("iran");

ini_set('session.save_path', '../../data/sessions');
session_start();

$user_validate = false;

/*------------------------------------------ sign up page ------------------------------------------*/

if (isset($_POST['signup'])) {

    $name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordCon = $_POST['pass_verify'];
    $policy = $_POST['policy'];

    $name = ucwords(in_check($name));
    $email = in_check($email);
    $username = strtolower(in_check($username));
    $password = in_check($password);
    $passwordCon = in_check($passwordCon);
    $policy = in_check($policy);

    if (!validation($name, 'name') or !validation($username, 'username') or !validation($email, 'email') or !validation($password, 'password') or $policy !== 'on') {

        session_destroy();

        header("location: ../../signup.php?error=true");
        exit;
    } else {

        if ($password === $passwordCon) {

            $user_validate = true;
        } else {

            session_destroy();

            header("location: ../../signup.php?error=true&type=password_not_match");
            exit;
        }
    }

    if ($user_validate) {

        session_start();

        $date = date("Y-m-d H:i:s");

        //creating uniqe token
        $token = hashed_str();
        $password = password_hash($password, PASSWORD_DEFAULT);

        //db connection using pdo

        $db_user = 'root';
        $db_password = '1234';

        $db_host = 'localhost';
        $db_name = 'chat_project';

        $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

        $pdo = new PDO($db_dsn, $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $stm = $pdo->prepare('SELECT username FROM users WHERE username = ?');
        $stm->execute([$username]);

        if ($users_db = $stm->fetch()) {

            $condition = false;
        } else {

            $condition = true;
        }

        if ($condition) {

            var_dump($users_db);

            $stm = $pdo->prepare("INSERT INTO `users` (username , name , email , password , register_date , last_login) VALUES (:username , :name , :email , :password , :date1 , :date2)");
            $stm->execute(['username' => $username, 'name' => $name, 'email' => $email, 'password' => $password, 'date1' => $date, 'date2' => $date]);

            $stm = $pdo->prepare("SELECT ID FROM `users` WHERE username = ?");
            $stm->execute([$username]);

            $id = $stm->fetch();
            var_dump($id);

            mkdir("../../data/users/users_files/" . $id['ID'] . "/", 0777);
            mkdir("../../data/users/users_files/" . $id['ID'] . "/profile_pics", 0777);
            mkdir("../../data/users/users_files/" . $id['ID'] . "/files", 0777);

            //to set token cookie for staying logqed in

            setcookie("user_token", "$token[0]:$token[1]", time() + (((60 * 60) * 24) * 30) * 6, "/");

            $stm = $pdo->prepare("INSERT INTO `token` (`user_id` , `token` , `validator`) VALUES (:user , :token , :validator)");
            $stm->execute(['user' => $id['ID'], 'token' => $token[0], 'validator' => $token[1]]);

            //public chat

            $stm = $pdo->prepare("INSERT INTO `chats_members` (`chat_id` , `user_id` , `role` , `join_date`) VALUES (:chat , :user , :role , :date)");
            $stm->execute(['chat' => 1, 'user' => $id['ID'], 'role' => 'member', 'date' => $date]);

            //send a message to others new user joined

            $stm = $pdo->prepare("INSERT INTO `messages` (`from_id` , `to_id` , `message` , `date`) VALUES (:from , :to , :message , :date)");
            $stm->execute(['from' => 0, 'to' => '1', 'message' => "$name joined the chat!", 'date' => $date]);

            // user created and moving to index page
            header("location: ../router/router.php");
            exit;
        } else {

            session_destroy();

            // user exist => going back to sign up page
            header("location: ../../signup.php?error=true&type=user_exist");
            exit;
        }
    }
}
