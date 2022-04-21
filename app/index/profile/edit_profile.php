<?php

require_once "../../functions/functions.php";
require_once "../../functions/login-signup-fn.php";
require_once "../../functions/index_fn.php";

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

    if (!validation($about, 'about') or $phone_number == "") {

        if (empty($about)) {

            $about = "";
        } else {

            header("location: ../../router/router.php");
        }
    }

    if (!validation($nickname, 'nickname') or $phone_number == "") {

        if (empty($nickname)) {

            $nickname = "";
        } else {

            header("location: ../../router/router.php");
        }
    }

    if (!validation($phone_number, 'phone') or $phone_number == "") {

        if (empty($phone_number)) {

            $phone_number = "";
        } else {

            header("location: ../../router/router.php");
        }
    }

    if (!validation($name, 'name') or !validation($username, 'username') or !validation($email, 'email')) {

        header("location: ../../router/router.php");
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
            }
        }

        $users_json = file_get_contents("../../../data/users/users.json");
        $users_json = json_decode($users_json, true);

        foreach ($users_json as $user) {


            if ($user['id'] === $_SESSION['id']) {

                $user['user_name'] = $username;
                $user['name'] = $name;
                $user['phone_number'] = $phone_number;
                $user['nickname'] = $nickname;
                $user['email'] = $email;
                $user['about'] = $about;

                if ($profile_image_uploaded) {

                    $user['profile_pic'][] = "$image_new_name.$image_name[1]";
                }
            }

            $users[] = $user;
        }

        $users = json_encode($users, JSON_PRETTY_PRINT);
        $users = file_put_contents("../../../data/users/users.json", $users);

        header("location: ../../router/router.php");
    }
}
