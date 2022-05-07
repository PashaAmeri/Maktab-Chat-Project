<?php

require_once "../../functions/functions.php";
require_once "../../functions/login-signup-fn.php";
require_once "../../functions/index_fn.php";

date_default_timezone_set("iran");

ini_set('session.save_path', '../../../data/sessions');
session_start();

if (isset($_POST['submit'])) {

    $dir = "../../../data/users/users_files/$_SESSION[id]";

    $name = in_check($_POST['name']);
    $username = strtolower(in_check($_POST['username']));
    $email = in_check($_POST['email']);
    $about = in_check($_POST['about']);
    $phone_number = in_check($_POST['phone_number']);
    $nickname = in_check($_POST['nickname']);

    if (!validation($about, 'about') or $about == "") {

        if (empty($about)) {

            $about = null;
        } else {

            header("location: ../../router/router.php");
            exit;
        }
    }

    if (!validation($nickname, 'nickname') or $nickname == "") {

        if (empty($nickname)) {

            $nickname = null;
        } else {

            header("location: ../../router/router.php");
            exit;
        }
    }

    if (!validation($phone_number, 'phone') or $phone_number == "") {

        if (empty($phone_number)) {

            $phone_number = null;
        } else {

            header("location: ../../router/router.php");
            exit;
        }
    }

    if (!validation($name, 'name') or !validation($username, 'username') or !validation($email, 'email')) {

        header("location: ../../router/router.php");
        exit;
    } else {

        $profile_image_uploaded = false;

        if (!empty($_FILES['photo']['name'])) {

            $tmp = $_FILES['photo']['tmp_name'];
            $image_name = explode(".", $_FILES['photo']['name']);

            if ($image_info = getimagesize($tmp)) {

                $to_size = ['width' => 800, 'height' => 800];

                $image_new_name = image_name($image_name[0]);

                move_uploaded_file($tmp, "$dir/files/$image_new_name.$image_name[1]");

                $image = file_get_contents("$dir/files/$image_new_name.$image_name[1]");
                $image_id = imagecreatefromstring($image);
                $new_image = imagescale($image_id, $to_size['width'], $to_size['height'], IMG_BICUBIC);
                imagejpeg($new_image, "$dir/profile_pics/$image_new_name.$image_name[1]");

                $profile_image_uploaded = true;
            } else {

                header("location: ../../router/router.php");
                exit;
            }
        }

        //connecting to db

        $db_user = 'root';
        $db_password = '1234';

        $db_host = 'localhost';
        $db_name = 'chat_project';

        $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

        $pdo = new PDO($db_dsn, $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        //get user data
        $stm = $pdo->prepare("UPDATE `users` SET username = :username , name = :name , phone_number = :phone , nickname = :nickname , email = :email , about = :about WHERE ID = :user_id");
        $stm->execute(['username' => $username, 'name' => $name, 'phone' => $phone_number, 'nickname' => $nickname, 'email' => $email, 'about' => $about, 'user_id' => $_SESSION['id']]);

        if ($profile_image_uploaded) {

            $date = date("Y-m-d H:i:s");

            $stm = $pdo->prepare("INSERT INTO profile_pics (`user_id` , `address` , `date`) VALUES (:user , :address , :date)");
            $stm->execute(['user' => $_SESSION['id'], 'address' => "$image_new_name.$image_name[1]", 'date' => $date]);
        }

        header("location: ../../router/router.php");
        exit;
    }
}
