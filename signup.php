<?php

if (isset($_COOKIE['user_token'])) {

    header("location: app/router/router.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="front/style/t_style.css">

    <title>sign-up</title>

</head>

<body class="bg-gradient-to-br from-emerald-500 to-indigo-600 relative">

    <header class="mx-auto w-full bg-white rounded-b-2xl py-5 fixed">

        <nav class="flex justify-around items-center">

            <div class="flex items-center">

                <h3 class="text-2xl font-medium text-blue-500">LOGO</h3>

            </div>

            <!-- <div class="space-x-8 lg:flex text-xl">

                <a href="">About Us</a>
                <a href="">Our Team</a>
                <a href="">Contact Us</a>

            </div> -->

            <div class="flex items-center space-x-2">

                <a href="login.php" class="px-4 py-2 bg-gradient-to-tr from-blue-600 to-indigo-600 text-indigo-100 rounded-md">
                    Log in
                </a>

                <a href="" class="px-4 py-2 text-gray-200 bg-gray-400 rounded-md">
                    Sign up
                </a>

            </div>

        </nav>

    </header>

    <main class="h-screen flex justify-center items-center w-full">

        <form action="app/form/signup_process.php" method="POST">

            <div class="bg-white px-10 py-8 rounded-xl w-screen shadow-md max-w-2xl">

                <div class="space-y-4">

                    <h1 class="text-center text-2xl font-semibold text-gray-600">Create your account</h1>

                    <div>

                        <label for="email" class="block mb-1 text-gray-600 font-semibold">Email</label>
                        <input name="email" type="email" id="email" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" require>

                    </div>

                    <div class="flex gap-2">

                        <div class="w-full">

                            <label for="name" class="block mb-1 text-gray-600 font-semibold">Name</label>
                            <input name="full_name" type="text" id="name" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" require>

                        </div>

                        <div class="w-full">

                            <label for="username" class="block mb-1 text-gray-600 font-semibold">Username</label>
                            <input name="username" type="text" id="username" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" require>

                        </div>

                    </div>

                    <div class="flex gap-2">

                        <div class="w-full">

                            <label for="pass" class="block mb-1 text-gray-600 font-semibold">Password</label>
                            <input name="password" type="password" id="pass" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" require>

                        </div>

                        <div class="w-full">

                            <label for="passv" class="block mb-1 text-gray-600 font-semibold">Password verification</label>
                            <input name="pass_verify" type="password" id="passv" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" require>

                        </div>

                    </div>

                    <div class="flex items-start mb-6">

                        <div class="flex items-center h-5">

                            <input name="policy" id="terms" type="checkbox" class="w-4 h-4 bg-gray-50 rounded border border-gray-300 focus:ring-3 focus:ring-blue-300" required>

                        </div>

                        <div class="ml-3 text-sm">

                            <label for="terms" class="font-medium text-gray-900">I agree with the <a href="#" class="text-blue-800 hover:underline">terms and conditions</a></label>

                        </div>

                    </div>

                </div>

                <button name="signup" value="signup" class="mt-4 w-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-indigo-100 py-2 rounded-md text-lg tracking-wide" name="register">Sign Up</button>

            </div>

        </form>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>