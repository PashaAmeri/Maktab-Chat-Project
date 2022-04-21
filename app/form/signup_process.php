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
    } else {

        if ($password === $passwordCon) {

            $user_validate = true;
        } else {

            session_destroy();

            header("location: ../../signup.php?error=true&type=password_not_match");
        }
    }

    if ($user_validate) {

        session_start();

        $date = date("Y-m-d H:i:s");

        //creating uniqe token
        $token = hashed_str();
        $password = password_hash($password, PASSWORD_DEFAULT);

        $user = [
            "token" => [
                'usertoken' => $token[0],
                'validator' => $token[1]
            ],
            "id" => $id = rand_str('id'),

            'user_name' => $username,
            'password' => $password,

            'name' => $name,
            'email' => $email,

            'nickname' => "",
            'phone_number' => "",
            'profile_pic' => [],
            'about' => "",

            'last_login' => "$date",

            'register_date' => $date,
            'policy' => true
        ];

        var_dump($user);

        $users_json = file_get_contents('../../data/users/users.json');
        $users_json = json_decode($users_json, true);

        for ($i = 0; $i < sizeof($users_json); $i++) {

            if ($users_json[$i]['user_name'] === $username) {

                $condition = false;
                break;
            } else {

                $condition = true;
            }
        }

        if ($condition) {

            $users_json[] = $user;
            $users_json = json_encode($users_json, JSON_PRETTY_PRINT);

            setcookie("user_token", "$token[0]:$token[1]", time() + (((60 * 60) * 24) * 30) * 6, "/");

            file_put_contents("../../data/users/users.json", $users_json);
            mkdir("../../data/users/users_files/" . $id . "/", 0777);
            mkdir("../../data/users/users_files/" . $id . "/chats", 0777);
            mkdir("../../data/users/users_files/" . $id . "/profile_pics", 0777);
            mkdir("../../data/users/users_files/" . $id . "/files", 0777);

            //public chat

            $public_chat_member = [
                'id' => $id,
                'user_name' => $username,
                'name' => $name,
                'role' => "member",
                'nickname' => "",
                'join_date' => $date
            ];

            $members_json = file_get_contents("../../data/users/groups/public_1/members.json");
            $members_json = json_decode($members_json);

            $members_json[] = $public_chat_member;

            $members_json = json_encode($members_json, JSON_PRETTY_PRINT);
            file_put_contents("../../data/users/groups/public_1/members.json", $members_json);

            //adding public chat to profile
            $f_public = fopen("../../data/users/users_files/$id/chats/public_1.json", "w");
            fwrite($f_public, '[{"name":"Public","type":"group"}]');

            fclose($f_public);

            //send a message to others new user joined

            $messages_json = file_get_contents("../../data/users/groups/public_1/messages.json");
            $messages_json = json_decode($messages_json, true);

            $i = sizeof($messages_json);
            $message_id = $messages_json[$i - 1];

            $system_message_join = [
                'id' => "system",
                'message_number' => ($message_id['message_number'] + 1),
                "date" => $date,
                'message' => "$name joined the chat!",
            ];

            $messages_json[] = $system_message_join;

            $messages_json = json_encode($messages_json, JSON_PRETTY_PRINT);
            file_put_contents("../../data/users/groups/public_1/messages.json", $messages_json);

            // user created and moving to index page
            header("location: ../router/router.php");
        } else {

            session_destroy();

            // user exist => going back to sign up page
            header("location: ../../signup.php?error=true&type=user_exist");
        }
    }
}
