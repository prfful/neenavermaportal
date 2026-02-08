<?php
session_start();
//include_once('header.php');

// If already logged in
if (isset($_SESSION['mla_loggedin']) && $_SESSION['mla_loggedin'] === true) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO -->
  <title>Login | MLA Dhar, BJP Madhya Pradesh</title>
  <meta name="description" content="Login to the official MLA Dhar portal. Access BJP updates, gallery, and constituency services.">
  <meta name="keywords" content="MLA Dhar login, BJP Dhar login, Madhya Pradesh BJP portal">
  <meta name="author" content="BJP Dhar">

  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <!-- Hero Heading -->
  <section class="bg-orange-600 text-white text-center py-8">
    <h1 class="text-3xl font-bold">MLA Dhar Login</h1>
    <p class="mt-2 text-lg">Bharatiya Janata Party - Madhya Pradesh</p>
  </section>

  <!-- Login Box -->
  <div class="flex items-center justify-center min-h-[70vh]">
    <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold text-center text-orange-600 mb-6">Sign In</h2>

      <!-- Form -->
      <form method="POST" action="login_check.php" class="space-y-4">
        
        <!-- Username -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
          <input type="text" id="username" name="username" required
                 class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2">
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" id="password" name="password" required
                 class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2">
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center">
            <input type="checkbox" name="remember" class="mr-2"> Remember Me
          </label>
          <a href="forgot-password.php" class="text-orange-600 hover:underline">Forgot Password?</a>
        </div>

        <!-- Submit -->
        <button type="submit" 
                class="w-full bg-orange-600 text-white font-bold py-2 rounded-lg hover:bg-orange-700 transition">
          Login
        </button>
      </form>
    </div>
  </div>

</body>
</html>
