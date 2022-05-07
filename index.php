<?php

require_once "app/functions/index_fn.php";

ini_set('session.save_path', 'data/sessions');
session_start();

if (isset($_SESSION['status']) and $_SESSION['status'] === true) {

    $admin = false;
    $block = false;

    //connecting to db

    $db_user = 'root';
    $db_password = '1234';

    $db_host = 'localhost';
    $db_name = 'chat_project';

    $db_dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name;

    $pdo = new PDO($db_dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //get user data
    $stm = $pdo->prepare("SELECT * FROM `users` WHERE ID = ?");
    $stm->execute([$_SESSION['id']]);

    $user_db = $stm->fetch();

    //get public chat data
    $stm = $pdo->prepare("SELECT * FROM `chats` WHERE ID = ?");
    $stm->execute(['1']);

    $public_info = $stm->fetch();

    //get public members
    $stm = $pdo->prepare("SELECT * FROM `chats_members` WHERE chat_id = ?");
    $stm->execute(['1']);

    $public_members = $stm->fetchAll();

    //get messages from public
    $stm = $pdo->prepare("SELECT messages.ID , messages.from_id , messages.to_id , messages.message , messages.date , messages.edit , users.name , users.username , users.nickname FROM `messages` INNER JOIN `users` ON messages.to_id = :chat_id AND users.ID = messages.from_id");
    $stm->execute(['chat_id' => '1']);

    $public_messages = $stm->fetchAll();

    $stm = $pdo->prepare("SELECT * FROM `chats` INNER JOIN `chats_members` ON chats_members.user_id = ?");
    $stm->execute([$_SESSION['id']]);

    $user_chats = $stm->fetchAll();

    // var_dump($user_db);
    // var_dump($public_info);
    // var_dump($public_members);
    // var_dump($public_messages);
    // var_dump($user_chats);

    //check if he user is admin or blocked

    foreach ($public_members as $member) {

        if ($_SESSION['id'] === $member['user_id']) {

            if ($member['role'] === "admin") {

                $admin = true;
            } elseif ($member['role'] === "block") {

                $block = true;
            }

            break;
        }
    }

    $user_dir = "data/users/users_files/$_SESSION[id]";
    // $chats_dir = scandir("$user_dir/chats");

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="front/style/t_style.css">
        <link rel="stylesheet" href="front/style/index/style.css">

        <title>home</title>

    </head>

    <body>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        <div id="chat_place" class="flex h-screen antialiased text-gray-800">

            <div class="flex flex-row h-full w-full overflow-x-hidden">

                <div class="flex flex-col py-2 pl-6 pr-2 w-64 bg-white flex-shrink-0">

                    <!-- <div class="flex flex-row items-center justify-center h-12 w-full">
                    <div class="flex items-center justify-center rounded-2xl bg-emerald-500 h-10 w-10">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <div class="ml-2 font-bold text-2xl">Chat Daan</div>
                </div> -->
                    <div class="relative text-indigo-600 -ml-3 mt-3 -mb-5">

                        <input type="search" name="serch" placeholder="Search" class="bg-gray-100 h-10 px-5 pr-10 rounded-full text-sm focus:outline-none">

                        <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" width="512px" height="512px">
                                <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                            </svg>
                        </button>

                    </div>

                    <div>

                        <div class="flex flex-col mt-8 mb-2">

                            <div class="flex flex-row items-center justify-between text-xs">

                                <img onclick="siedbar_show()" id="menue" class="h-5 w-5 cursor-pointer opacity-50" src="resources/pics/hamburgur.svg">
                                <span class="font-bold">Conversations</span>
                                <span class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full">4</span>

                            </div>

                            <div class="flex flex-col space-y-1 mt-4 -mx-2 h-96 overflow-y-auto">

                                <?php

                                $con_counter = 0;

                                foreach ($user_chats as $chat) {

                                    $con_counter++;
                                ?>
                                    <button id="conversation_<?= $con_counter ?>" class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2">

                                        <span class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full">

                                            <?= substr($chat['name'], 0, 1) ?>
                                        </span>

                                        <span class="ml-2 text-sm font-semibold"><?= $chat['name'] ?></span>

                                    </button>
                                <?php

                                }

                                ?>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="flex flex-col flex-auto h-full">
                    <div id="chat_header" class="relative flex flex-col flex-auto flex-shrink-0 rounded-l-2xl bg-gradient-to-tr from-emerald-300 to-indigo-300 h-full pb-4">

                        <div class="w-full h-20 bg-gradient-to-tr from-indigo-200 to-emerald-200 rounded-tl-xl flex items-center px-8 gap-5">

                            <div>

                                <span class="flex items-center justify-center h-12 w-12 rounded-full bg-red-200 flex-shrink-0 text-xl"><?= substr($public_info['name'], 0, 1) ?></span>

                            </div>

                            <div>

                                <span class="ml-2 text-xl font-semibold text-gray-800"><?= $public_info["name"] ?></span>

                            </div>

                            <?php

                            if ($public_info['type'] !== "group") {

                            ?>

                                <div>

                                    <span class="ml-2 text-sm text-gray-600">last seen recently</span>

                                </div>

                            <?php

                            }

                            ?>

                        </div>

                        <div class="flex flex-col h-full overflow-x-auto mb-4 ml-4 justify-end">

                            <div class="flex flex-col h-full">

                                <div id="mesages_box" class="grid grid-cols-12 mt-2">
                                    <?php

                                    foreach ($public_messages as $message) {

                                        if ($message['from_id'] === "0") {

                                    ?>
                                            <div id="<?= $message['ID'] ?>" class="col-start-1 col-end-13 p-1 justify-self-center rounded-full">

                                                <div class="flex flex-row items-center">

                                                    <div class="relative ml-3 text-xs bg-emerald-200 py-1 px-2 shadow rounded-xl">

                                                        <span><?= $message['message'] ?></span>

                                                    </div>

                                                </div>

                                            </div>

                                        <?php

                                        } elseif ($message['from_id'] !== $_SESSION['id']) {

                                        ?>

                                            <div id="<?= $message['ID'] ?>" class="col-start-1 col-end-8 p-3 rounded-lg">

                                                <div class="flex flex-row items-center">

                                                    <span class="flex self-start items-center justify-center h-10 w-10 rounded-full bg-red-200 flex-shrink-0"><?= substr($message['name'], 0, 1) ?></span>

                                                    <div class="relative ml-3 text-md bg-white pb-2 px-4 shadow rounded-xl">

                                                        <div class="flex">

                                                            <span class="text-xs text-gray-500 pt-1"><?= $message['name'] ?></span>

                                                        </div>

                                                        <span><?= $message['message'] ?></span>

                                                        <div>

                                                            <div id="edit" class="flex self-end justify-between w-full mt-2 gap-1">

                                                                <span class="pr-4 text-xs text-gray-500"><?= message_date($message['date']) ?></span>
                                                                <span id="edit_lable" class="text-xs text-gray-500 <?php if ($message['edit'] === '1') {
                                                                                                                        echo "visible pr-1";
                                                                                                                    } else {
                                                                                                                        echo "invisible -mx-5";
                                                                                                                    } ?>">Edited</span>

                                                                <?php
                                                                if ($admin === true) {
                                                                ?>
                                                                    <img name="<?= $message['ID'] ?>" onclick="message_popover_admin()" id="<?= $message['username'] ?>" class="h-5 cursor-pointer" src="resources/pics/kebab.png">

                                                                <?php
                                                                }
                                                                ?>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        <?php

                                        } else {

                                        ?>
                                            <div id="<?= $message['ID'] ?>" class="col-start-6 col-end-13 p-3 rounded-lg">

                                                <div class="flex items-center justify-start flex-row-reverse">

                                                    <?php
                                                    if (!empty($_SESSION['profile_pic'])) {
                                                    ?>
                                                        <img src="<?php echo "$user_dir/profile_pics/" . $_SESSION['profile_pic'][sizeof($_SESSION['profile_pic']) - 1] ?>" class="flex self-start justify-center h-10 w-10 rounded-full flex-shrink-0">
                                                    <?php
                                                    } else {
                                                    ?>

                                                        <span class="flex self-start items-center justify-center h-10 w-10 rounded-full bg-red-200 flex-shrink-0"><?= substr($message['name'], 0, 1) ?></span>
                                                    <?php
                                                    }
                                                    ?>
                                                    <div class="relative flex flex-col mr-3 text-md bg-indigo-100 py-2 shadow rounded-xl">

                                                        <span id="message" class="px-4"><?= $message['message'] ?></span>

                                                        <div id="edit" class="flex self-end justify-between w-full mt-2 gap-1">

                                                            <img name="<?= $message['ID'] ?>" onclick="message_popover()" id="popover" class="h-5 cursor-pointer" src="resources/pics/kebab.png">
                                                            <span id="edit_lable" class="text-xs text-gray-500 <?php if ($message['edit'] === '1') {
                                                                                                                    echo "visible pr-1";
                                                                                                                } else {
                                                                                                                    echo "invisible -mx-5";
                                                                                                                } ?>">Edited</span>
                                                            <span class="pr-4 text-xs text-gray-500"><?= message_date($message['date']) ?></span>

                                                        </div>


                                                    </div>

                                                </div>

                                            </div>
                                    <?php

                                        }
                                    }

                                    ?>

                                </div>

                            </div>

                        </div>

                        <div class="w-full px-4">

                            <form autocomplete="off" id="message_form" class="flex flex-row items-center h-14 rounded-xl bg-white w-full px-4">
                                <?php
                                if (!$block) {
                                ?>
                                    <div>

                                        <div class="file-input">

                                            <input type="file" name="file_input" id="file-input" class="file-input__input">

                                            <label class="file-input__label text-gray-400 hover:text-gray-600" for="file-input">

                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="3 2 19 19" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>

                                            </label>

                                        </div>

                                    </div>

                                    <div class="flex-grow ml-4">

                                        <div class="relative w-full">

                                            <input placeholder="Write a message..." name="message" id="message_in" type="text" class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pr-11 pl-4 h-10">

                                            <button class="absolute flex items-center justify-center h-full w-12 right-0 top-0 text-gray-400 hover:text-gray-600">

                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>

                                            </button>

                                        </div>

                                    </div>

                                    <div class="ml-4">

                                        <button value="send" name="send" type="submit" id="message_send" class="flex items-center justify-center bg-emerald-600 hover:bg-emerald-800 rounded-xl text-white px-4 py-1 flex-shrink-0">
                                            <span id="message_edit_text">Send</span>

                                            <span class="ml-2">

                                                <svg class="w-4 h-4 transform rotate-45 -mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>

                                            </span>

                                        </button>

                                    </div>

                                <?php
                                } else {
                                ?>

                                    <div class="w-full flex justify-center items-center">

                                        <span class="text-gray-500 text-xl tracking-wider"><i>Unable to send message in this chat due to blocking!</i></span>

                                    </div>

                                <?php
                                }
                                ?>

                            </form>

                        </div>

                        <div id="popoverelement" class="absolute top-1/2 left-1/2 px-1 bg-white rounded-md p-1">

                            <ul>

                                <li id="delete" class="p-2 text-center text-red-700 hover:bg-gray-100 rounded-md">Delete</li>
                                <hr>
                                <li id="edit_message" class="p-2 text-center hover:bg-gray-100 rounded-md">edit</li>

                            </ul>

                        </div>

                        <div id="popoverelement_admin" class="absolute top-1/2 left-1/2 px-1 bg-white rounded-md p-1">

                            <ul>

                                <li id="delete_users_message" class="p-2 text-center text-red-700 hover:bg-gray-100 rounded-md">Delete</li>
                                <hr>
                                <li id="block_user" class="p-2 text-center hover:bg-gray-100 rounded-md">Block</li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <aside class="w-80 absolute top-0 h-screen" id="sidebar">

            <div class="overflow-y-auto py-4 px-3 bg-gray-50 rounded h-full">

                <div class="w-full flex justify-end">

                    <button onclick="siedbar_hide()" type="button" class="ml-auto -mx-1.5 -my-1.5 p-1 inline-flex h-6 w-6" data-collapse-toggle="dropdown-cta" aria-label="Close">

                        <svg class="w-8 h-8 text-gray-500 hover:text-black" fill="currentColor" viewBox="2 8 15 15" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>

                    </button>

                </div>

                <div class="flex flex-col items-center bg-gradient-to-br to-emerald-300 from-indigo-200 border border-gray-200 mt-4 w-full py-6 px-4 rounded-lg">

                    <div class="h-20 w-20 rounded-full border overflow-hidden">

                        <?php
                        if (!empty($_SESSION['profile_pic'])) {
                        ?>
                            <img onclick="profile_slide_show()" src="<?php echo "$user_dir/profile_pics/" . $_SESSION['profile_pic'][sizeof($_SESSION['profile_pic']) - 1] ?>" class="cursor-pointer flex self-start justify-center h-28 w-28 rounded-full flex-shrink-0">
                        <?php
                        } else {
                        ?>
                            <span class="flex self-start text-5xl items-center justify-center h-20 w-20 rounded-full bg-red-200 flex-shrink-0"><?= substr($_SESSION['name'], 0, 1) ?></span>
                        <?php
                        }
                        ?>
                    </div>

                    <div class="text-sm font-semibold mt-2"><?php if (array_key_exists('nickname', $_SESSION) === true) {

                                                                echo $_SESSION['nickname'];
                                                            } else {

                                                                echo $_SESSION['name'];
                                                            } ?></div>

                    <div class="text-xs text-gray-700"><?= $_SESSION['username'] ?></div>

                </div>

                <ul class="space-y-2 mt-3">

                    <li>

                        <button id="saved_messages" class="flex w-full items-center p-2 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-200">

                            <img class="w-6 opacity-50" src="resources/pics/bookmark.svg">
                            <span class="ml-3">Saved messages</span>

                        </button>

                    </li>

                    <li>

                        <button onclick="profile_modal_show()" id="profile_btn" class="flex items-center w-full p-2 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-200">

                            <img class="w-6 opacity-50" src="resources/pics/profile.svg">
                            <span class="ml-3">profile</span>

                        </button>

                    </li>

                    <li>
                        <form action="app/router/log_out.php" method="post">

                            <button type="submit" name="logout" class="flex items-center w-full p-2 text-base font-normal text-gray-900 rounded-lg hover:bg-red-100">

                                <img class="w-6 opacity-50" src="resources/pics/logout.svg">
                                <span class="ml-3 text-red-600">Log out</span>

                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </aside>

        <section id="edit_profile_modal" class="absolute top-0 grid w-screen place-items-center h-screen">

            <div class="lg:h-4/5 lg:w-2/3 w-full h-full bg-gray-50 rounded-xl shadow-md px-6 py-4 overflow-auto">

                <header class="w-full h-10 flex">

                    <div class="w-4/5">

                        <h4 class="text-xl">Edit your Profile</h4>

                    </div>

                    <div class="w-full flex justify-end">

                        <button onclick="profile_modal_hide()" type="button" class="ml-auto -mx-1.5 -my-1.5 p-1 inline-flex h-6 w-6" data-collapse-toggle="dropdown-cta" aria-label="Close">

                            <svg class="w-8 h-8 text-gray-500 hover:text-black" fill="currentColor" viewBox="2 8 15 15" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>

                        </button>

                    </div>

                </header>

                <div class="flex-grow border-t border-gray-400"></div>

                <section class="py-3">

                    <form action="app/index/profile/edit_profile.php" method="POST" enctype="multipart/form-data">

                        <div class="flex justify-center">

                            <?php
                            if (!empty($_SESSION['profile_pic'])) {
                            ?>
                                <img onclick="profile_slide_show()" src="<?php echo "$user_dir/profile_pics/" . $_SESSION['profile_pic'][sizeof($_SESSION['profile_pic']) - 1] ?>" class="cursor-pointer flex self-start justify-center h-28 w-28 rounded-full flex-shrink-0">
                            <?php
                            } else {
                            ?>
                                <span class="flex self-start text-5xl items-center justify-center h-28 w-28 rounded-full bg-red-200 flex-shrink-0"><?= substr($_SESSION['name'], 0, 1) ?></span>
                            <?php
                            }
                            ?>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8 mt-5 mx-7">

                            <div class="grid grid-cols-1">

                                <label class="text-lg text-gray-500 text-light font-semibold">Username</label>
                                <input name="username" class="py-2 px-3 rounded-lg border-2 border-blue-300 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent text-lg" type="text" value="<?= $_SESSION['username'] ?>" placeholder="<?= $_SESSION['username'] ?>" required>

                            </div>

                            <div class="grid grid-cols-1">

                                <label class="text-lg text-gray-500 text-light font-semibold">Name</label>
                                <input name="name" class="py-2 px-3 rounded-lg border-2 border-blue-300 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent text-lg" type="text" value="<?= $_SESSION['name'] ?>" placeholder="<?= $_SESSION['name'] ?>" required>

                            </div>

                        </div>

                        <div class="grid grid-cols-1 mt-5 mx-7">

                            <label class="text-lg text-gray-500 text-light font-semibold">Email</label>
                            <input name="email" class="text-lg py-2 px-3 rounded-lg border-2 border-blue-300 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" type="email" value="<?= $_SESSION['email'] ?>" placeholder="<?= $_SESSION['email'] ?>" required>

                        </div>

                        <div class="grid grid-cols-1 mt-5 mx-7">

                            <label class="text-lg text-gray-500 text-light font-semibold">Bio</label>
                            <textarea name="about" class="py-2 px-3 rounded-lg border-2 border-blue-300 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" type="text" placeholder="Tell us about yourself..."><?php if (array_key_exists("about", $_SESSION)) {
                                                                                                                                                                                                                                                    echo $_SESSION['about'];
                                                                                                                                                                                                                                                } ?></textarea>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8 mt-5 mx-7">

                            <div class="grid grid-cols-1">

                                <label class="text-lg text-gray-500 text-light font-semibold">Phone Number</label>
                                <input name="phone_number" class="py-2 px-3 rounded-lg border-2 border-blue-300 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent text-lg" type="text" value="<?php if (array_key_exists("phone_number", $_SESSION)) {
                                                                                                                                                                                                                                    echo $_SESSION['phone_number'];
                                                                                                                                                                                                                                } ?>" placeholder="<?php if (array_key_exists("phone_number", $_SESSION)) {
                                                                                                                                                                                                                                                        echo $_SESSION['phone_number'];
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                        echo "09123456789";
                                                                                                                                                                                                                                                    } ?>">

                            </div>

                            <div class="grid grid-cols-1">

                                <label class="text-lg text-gray-500 text-light font-semibold">Nickname</label>
                                <input name="nickname" class="py-2 px-3 rounded-lg border-2 border-blue-300 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent text-lg" type="text" value="<?php if (array_key_exists("nickname", $_SESSION)) {
                                                                                                                                                                                                                                echo $_SESSION['nickname'];
                                                                                                                                                                                                                            } ?>" placeholder="<?php if (array_key_exists("nickname", $_SESSION)) {
                                                                                                                                                                                                                                                    echo $_SESSION['nickname'];
                                                                                                                                                                                                                                                } ?>">

                            </div>

                        </div>

                        <div class="grid grid-cols-1 mt-5 mx-7 lg:mb-20">

                            <label class="uppercase md:text-sm text-xs text-gray-500 text-light font-semibold mb-1">Upload Photo</label>

                            <div class='flex items-center justify-center w-full'>

                                <label class='flex flex-col border-4 border-dashed w-full h-32 hover:bg-gray-100 hover:border-blue-300 group'>

                                    <div class='flex flex-col items-center justify-center pt-7'>

                                        <svg class="w-10 h-10 text-blue-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>

                                        <p class='lowercase text-sm text-gray-400 group-hover:text-blue-600 pt-1 tracking-wider'>Select a photo</p>

                                    </div>

                                    <input name="photo" type='file' class="hidden">

                                </label>

                            </div>

                        </div>

                        <div class='flex items-center justify-center md:gap-8 gap-4 pt-5 pb-5 bottom-20 right-0 left-0 lg:fixed'>

                            <button onclick="profile_back()" type="button" class='w-auto bg-gray-400 hover:bg-gray-700 rounded-lg shadow-xl font-medium text-white px-4 py-2'>Cancel</button>
                            <button name="submit" type="submit" class='w-auto bg-emerald-500 hover:bg-emerald-700 rounded-lg shadow-xl font-medium text-white px-4 py-2'>Save Changes</button>

                        </div>

                    </form>

                </section>

            </div>

        </section>

        <section id="profile_slide" class="w-screen h-screen absolute top-0 left-0 bg-gray-900 ">

            <button onclick="profile_slide_hide()" class="absolute right-5 top-5 w-9 h-9 cursor-pointer flex justify-center items-center">

                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 172 172" style=" fill:#000000;">
                    <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                        <path d="M0,172v-172h172v172z" fill="none"></path>
                        <g id="original-icon" fill="#cccccc">
                            <path d="M40.13333,22.93333c-1.46702,0 -2.93565,0.55882 -4.05365,1.67969l-11.46667,11.46667c-2.24173,2.24173 -2.24173,5.87129 0,8.10729l41.81302,41.81302l-41.81302,41.81302c-2.24173,2.24173 -2.24173,5.87129 0,8.10729l11.46667,11.46667c2.24173,2.24173 5.87129,2.24173 8.10729,0l41.81302,-41.81302l41.81302,41.81302c2.236,2.24173 5.87129,2.24173 8.10729,0l11.46667,-11.46667c2.24173,-2.24173 2.24173,-5.87129 0,-8.10729l-41.81302,-41.81302l41.81302,-41.81302c2.24173,-2.236 2.24173,-5.87129 0,-8.10729l-11.46667,-11.46667c-2.24173,-2.24173 -5.87129,-2.24173 -8.10729,0l-41.81302,41.81302l-41.81302,-41.81302c-1.12087,-1.12087 -2.58663,-1.67969 -4.05365,-1.67969z"></path>
                        </g>
                    </g>
                </svg>

            </button>

            <div class="max-w-md mx-auto my-40">

                <div id="default-carousel" class="relative" data-carousel="static">

                    <!-- Carousel wrapper -->

                    <div class="overflow-hidden relative h-96 rounded-lg">

                        <?php
                        foreach ($_SESSION['profile_pic'] as $pic) {
                        ?>

                            <div id="picbox" class="hidden duration-700 ease-in-out" data-carousel-item>

                                <span class="absolute top-1/2 left-1/2 text-2xl font-semibold text-white -translate-x-1/2 -translate-y-1/2 sm:text-3xl">First Slide</span>
                                <img src="<?php echo "$user_dir/profile_pics/" . $pic ?>" class="block absolute top-1/2 left-1/2 w-full -translate-x-1/2 -translate-y-1/2" alt="...">

                                <div id="options_img" class="w-full h-14 absolute bg-black top-0 opacity-60 flex justify-center gap-6 items-center">

                                    <button id="<?= array_search($pic, $_SESSION['profile_pic']) ?>" onclick="delete_profile_pic(this)" class="cursor-pointer">

                                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="32" height="32" viewBox="0 0 172 172" style=" fill:#000000;">
                                            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                                                <path d="M0,172v-172h172v172z" fill="none"></path>
                                                <g id="original-icon" fill="#ffffff">
                                                    <path d="M71.66667,14.33333l-7.16667,7.16667h-28.66667c-4.3,0 -7.16667,2.86667 -7.16667,7.16667c0,4.3 2.86667,7.16667 7.16667,7.16667h14.33333h71.66667h14.33333c4.3,0 7.16667,-2.86667 7.16667,-7.16667c0,-4.3 -2.86667,-7.16667 -7.16667,-7.16667h-28.66667l-7.16667,-7.16667zM35.83333,50.16667v93.16667c0,7.88333 6.45,14.33333 14.33333,14.33333h71.66667c7.88333,0 14.33333,-6.45 14.33333,-14.33333v-93.16667zM64.5,64.5c4.3,0 7.16667,2.86667 7.16667,7.16667v64.5c0,4.3 -2.86667,7.16667 -7.16667,7.16667c-4.3,0 -7.16667,-2.86667 -7.16667,-7.16667v-64.5c0,-4.3 2.86667,-7.16667 7.16667,-7.16667zM107.5,64.5c4.3,0 7.16667,2.86667 7.16667,7.16667v64.5c0,4.3 -2.86667,7.16667 -7.16667,7.16667c-4.3,0 -7.16667,-2.86667 -7.16667,-7.16667v-64.5c0,-4.3 2.86667,-7.16667 7.16667,-7.16667z"></path>
                                                </g>
                                            </g>
                                        </svg>

                                    </button>

                                    <button class="cursor-pointer">

                                        <img class="w-7 h-7" src="resources/pics/download.svg">

                                    </button>

                                </div>

                            </div>



                        <?php
                        }
                        ?>

                    </div>

                    <!-- Slider indicators -->

                    <div class="flex absolute bottom-5 left-1/2 z-30 space-x-3 -translate-x-1/2">

                        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3" data-carousel-slide-to="2"></button>

                    </div>

                    <button type="button" class="flex absolute top-0 left-0 z-30 justify-start items-center px-4 h-full w-1/4 cursor-pointer group focus:outline-none" data-carousel-prev>

                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">

                            <svg class="w-5 h-5 text-white sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>

                            <span class="hidden">Previous</span>

                        </span>

                    </button>

                    <button type="button" class="flex absolute top-0 right-0 z-30 justify-end items-center px-4 h-full w-1/4 cursor-pointer group focus:outline-none" data-carousel-next>

                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">

                            <svg class="w-5 h-5 text-white sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="hidden">Next</span>

                        </span>

                    </button>

                </div>

            </div>

        </section>


        <script>
            function send_message() {

                let message_js = $("#message_in").val();

                $.ajax({
                    method: "POST",
                    url: "app/index/send_message_process.php",
                    data: $('#message_form').serialize(),

                    success: function(result) {

                        if (result === "1") {

                            let date = new Date();
                            let hour = date.getHours();
                            let min = date.getMinutes();

                            let id = $("#mesages_box > div").last().attr('id');
                            id++;

                            if (hour >= 12) {

                                hour = hour - 12;
                                var time = "PM";
                            } else {

                                var time = "AM";
                            }

                            date = hour + ":" + min + " " + time;

                            $('#mesages_box').append(`<div id="${id}" class="col-start-6 col-end-13 p-3 rounded-lg"><div class="flex items-center justify-start flex-row-reverse"><?php if (!empty($_SESSION['profile_pic'])) { ?><img src="<?php echo "$user_dir/profile_pics/" . $_SESSION['profile_pic'][0] ?>" class="flex self-start justify-center h-10 w-10 rounded-full flex-shrink-0"><?php } else { ?><span class="flex self-start items-center justify-center h-10 w-10 rounded-full bg-red-200 flex-shrink-0"><?= substr($_SESSION['name'], 0, 1) ?></span><?php } ?><div class="relative flex flex-col mr-3 text-md bg-blue-100 py-2 shadow rounded-xl"><span class="px-4">${message_js}</span><div class="flex self-end justify-between w-full mt-2 gap-3"><img name="${id}" onclick="message_popover()" id="popover" class="h-5 cursor-pointer" src="resources/pics/kebab.png"><span class="pr-4 text-xs text-gray-500">${date}</span></div></div></div></div>`);

                            $("#message_in").val("");

                        } else {

                            console.log(result);
                        }

                    },
                    error: function(err) {
                        console.error(err);
                    },
                });
            };

            ////////////////////////////////////////////////////////////

            function get_message() {

                let id = $("#mesages_box > div").last().attr('id');

                $.ajax({
                    method: "POST",
                    url: "app/index/get_chats_process.php",
                    data: {
                        id: id
                    },

                    success: function(result) {

                        if (result !== "[]") {

                            let messages_json = JSON.parse(result);

                            for (message_json of messages_json) {

                                $('#mesages_box').append(`<div id="${message_json['ID']}" class="col-start-1 col-end-8 p-3 rounded-lg"><div class="flex flex-row items-center"><span class="flex self-start items-center justify-center h-10 w-10 rounded-full bg-red-200 flex-shrink-0">${message_json['name'].substr(0, 1)}</span><div class="relative ml-3 text-md bg-white pb-2 px-4 shadow rounded-xl"><div><span class="text-xs text-gray-500 pt-1">${message_json['name']}</span></div><span>${message_json['message']}</span><div><span class="text-xs text-gray-500">${message_json['date']}</span></div></div></div></div>`);

                            }
                            console.log(result);
                        }
                    },

                    error: function(err) {
                        console.error(err);
                    },
                });

                setTimeout("get_message()", 3000);
            };

            ////////////////////////////////////////////////////////////

            function message_popover() {

                var id_popover = event.srcElement.name;

                $('#popoverelement').toggle();

                $('#delete').on('click', function() {

                    $.ajax({
                        method: "POST",
                        url: "app/index/delete_message.php",
                        data: {
                            id: id_popover
                        },

                        success: function(result) {

                            $('#' + id_popover).remove();
                            $('#popoverelement').hide();

                            console.log(id_popover)
                            console.log(result)

                        },

                        error: function(err) {
                            console.error(err);
                        },
                    });
                });

                $('#edit_message').on('click', function() {

                    $('#message_form').prop('id', 'edit_form');
                    $('#message_send').prop('id', 'message_edit');
                    $('#message_edit_text').text('Edit')

                    $('#message_edit').removeClass('bg-emerald-600 hover:bg-emerald-800');
                    $('#message_edit').addClass('bg-blue-500 hover:bg-blue-800');

                    $('#popoverelement').hide();

                    let message_edit = $('#' + id_popover + " #message").text();

                    console.log(id_popover);
                    $('#message_in').val(message_edit);

                    $('#message_edit').on('click', function() {

                        var new_message = $('#message_in').val();

                        if (new_message !== "") {

                            $.ajax({
                                method: "POST",
                                url: "app/index/edit_message.php",
                                data: {
                                    id: id_popover,
                                    edit_to: new_message,
                                },

                                success: function(result) {

                                    $('#' + id_popover + " #message").text(new_message);

                                    $('#edit_form').prop('id', 'message_form');
                                    $('#message_edit').prop('id', 'message_send');
                                    $('#message_in').val("");
                                    $('#message_send').removeClass('bg-blue-500 hover:bg-blue-800');
                                    $('#message_send').addClass('bg-emerald-600 hover:bg-emerald-800');
                                    $('#edit_lable').removeClass('invisible');
                                    $('#edit_lable').addClass('visible');
                                    $('#message_edit_text').text('Send')

                                    new_message = null;
                                    id_popover = null;

                                    console.log(result)

                                },

                                error: function(err) {

                                    console.error(err);

                                    $('#message_form').prop('id', 'edit_form');
                                },
                            });
                        }
                    });

                });
            };

            ////////////////////////////////////////////////////////////

            function send_files() {

                let message_js = $("#message_in").val();

                $.ajax({
                    method: "POST",
                    url: "app/index/chat_process.php",
                    data: $('#message_form').serialize(),

                    success: function(result) {

                        if (result === "1") {

                            let date = new Date();
                            let hour = date.getHours();
                            let min = date.getMinutes();

                            let id = $("#mesages_box > div").last().attr('id');
                            id++;

                            if (hour >= 12) {

                                hour = hour - 12;
                                var time = "PM";
                            } else {

                                var time = "AM";
                            }

                            date = hour + ":" + min + " " + time;

                            $('#mesages_box').append(`<div id="${id}" class="col-start-6 col-end-13 p-3 rounded-lg"><div class="flex items-center justify-start flex-row-reverse"><span class="flex self-start items-center justify-center h-10 w-10 rounded-full bg-red-200 flex-shrink-0"><?= substr($_SESSION['name'], 0, 1) ?></span><div class="relative flex flex-col mr-3 text-md bg-indigo-100 py-2 shadow rounded-xl"><span class="px-4">${message_js}</span><div class="flex self-end justify-between w-full mt-2 gap-3"><img id="${id}" class="h-5 cursor-pointer" src="resources/pics/kebab.png"><span class="pr-4 text-xs text-gray-500">${date}</span></div></div></div></div>`);

                            $("#message_in").val("");
                        } else if (result === "0") {

                            //to do: changing bottun color to red
                        } else {

                            console.log(result);
                        }

                    },
                    error: function(err) {
                        console.error(err);
                    },
                });
            };


            <?php
            if ($admin === true) {
            ?>

                function message_popover_admin() {

                    var id_popover_admin = event.srcElement.name;

                    $('#popoverelement_admin').toggle();

                    $('#delete_users_message').on('click', function() {

                        $.ajax({
                            method: "POST",
                            url: "app/index/admin/delete_message_admin.php",
                            data: {
                                id: id_popover_admin
                            },

                            success: function(result) {

                                if (result === "done") {

                                    $('#' + id_popover_admin).remove();
                                    $('#popoverelement_admin').hide();
                                    id_popover_admin = null;
                                }
                            },

                            error: function(err) {
                                console.error(err);
                            },

                        });
                    });

                    var user_to_block = event.srcElement.id;

                    $('#block_user').on('click', function() {

                        $('#popoverelement_admin').hide();

                        $.ajax({
                            method: "POST",
                            url: "app/index/admin/block_users.php",
                            data: {
                                user: user_to_block,
                            },

                            success: function(result) {

                                user_to_block = null;
                            },

                            error: function(err) {

                                console.error(err);

                            },
                        });
                    });
                };

            <?php
            }
            ?>
        </script>
        <script src="front/js/home/php_message_ajax.js"></script>
        <script src="https://unpkg.com/flowbite@1.4.0/dist/flowbite.js"></script>









        <!-- <div>
        <div class="flex flex-row items-center justify-between text-xs mt-6">
            <span class="font-bold">Archivied</span>
            <span class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full">7</span>
        </div>
        <div class="flex flex-col space-y-1 mt-4 -mx-2">
            <button class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2">
                <div class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full">
                    H
                </div>
                <div class="ml-2 text-sm font-semibold">Henry Boyd</div>
            </button>
        </div>
    </div> -->

    </body>

    </html>

<?php

} else {

    session_destroy();

    header("location: app/router/router.php");
}

?>




















<!-- <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                       
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Deactivate account</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to deactivate your account? All of your data will be permanently removed. This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Deactivate</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div> -->









<!-- <div id="dropdown-cta" class="p-4 mt-6 bg-blue-50 rounded-lg" role="alert">

<p class="mb-3 text-sm text-blue-900">
    Preview the new Flowbite dashboard navigation! You can turn the new navigation off for a limited time in your profile.
</p>

<a class="text-sm text-blue-900 underline hover:text-blue-800" href="#">Turn new navigation off</a>

</div> -->