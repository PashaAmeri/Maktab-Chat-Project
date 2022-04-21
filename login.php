<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="front/style/t_style.css">

  <title>Log-In</title>

</head>

<body class="bg-gradient-to-br from-emerald-500 to-indigo-600">

  <header class="mx-auto w-full bg-white rounded-b-2xl py-5 fixed">

    <nav class="flex justify-around items-center">

      <div class="flex items-center">

        <h3 class="text-2xl font-medium text-blue-500">LOGO</h3>

      </div>

      <!-- left header section -->

      <!-- <div class="space-x-8 lg:flex text-xl">

        <a href="">About Us</a>
        <a href="">Our Team</a>
        <a href="">Contact Us</a>

      </div> -->

      <!-- right header section -->

      <div class="flex items-center space-x-2">

        <a href="login.php" class="px-4 py-2 text-gray-200 bg-gray-400 rounded-md">Log in</a>
        <a href="signup.php" class="px-4 py-2 bg-gradient-to-tr from-blue-600 to-indigo-600 text-indigo-100 rounded-md">Sign up</a>

      </div>

    </nav>

  </header>

  <div class="h-screen flex justify-center items-center w-full">

    <form action="app/form/login_process.php" method="POST" novalidate>

      <div class="bg-white px-10 py-8 rounded-xl w-screen shadow-md max-w-sm">

        <div class="space-y-4">

          <h1 class="text-center text-2xl font-semibold text-gray-600">Log-in to your account</h1>

          <div>

            <label for="username" class="block mb-1 text-gray-600 font-semibold">Username</label>
            <input name="username" id="username" type="text" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" required>

          </div>

          <div>

            <label for="password" class="block mb-1 text-gray-600 font-semibold">Password</label>
            <input name="password" id="password" type="password" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" required>

          </div>

          <div class="flex items-start mb-6">

            <div class="flex items-center h-5">

              <input name="remember" id="remember" aria-describedby="terms" type="checkbox" class="w-4 h-4 bg-gray-50 rounded border border-gray-300 focus:ring-3 focus:ring-blue-300">

            </div>

            <div class="ml-3 text-sm">

              <label for="remember" class="font-medium text-gray-900">Remember me</label>

            </div>

          </div>

        </div>

        <button type="submit" value="login" name="login" class="mt-4 w-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-indigo-100 py-2 rounded-md text-lg tracking-wide">Sign Up</button>

      </div>

    </form>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>