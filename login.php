<?php 
session_start();

include('server/connection.php');

// Redirect if already logged in
if (isset($_SESSION['logged_in'])) {
  header('location: account.php');
  exit;
}

if (isset($_POST['login_btn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare statement to fetch user details by email
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password FROM users WHERE user_email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $user_name, $user_email, $hashed_password);
        $stmt->fetch();

        // Verify password using password_verify
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['logged_in'] = true;

            header('location: account.php?message=Logged in successfully');
            exit();
        } else {
            // Incorrect password
            header('location: login.php?error=Wrong email or password');
            exit();
        }
    } else {
        // No user found with the provided email
        header('location: login.php?error=No account found with that email');
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed top-0 left-0 w-full z-10">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="#" class="text-2xl font-bold text-black">Shaki</a> <!-- Brand is now black -->
            <button class="navbar-toggler text-black lg:hidden focus:outline-none" id="toggleButton">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <ul class="hidden lg:flex space-x-6 items-center" id="navLinks">
                <li><a href="index.php" class="text-gray-700 hover:text-blue-500">Home</a></li>
                <li><a href="shop.php" class="text-gray-700 hover:text-blue-500">Shop</a></li>
                <li><a href="about.php" class="text-gray-700 hover:text-blue-500">About</a></li>
                <li><a href="contact.php" class="text-gray-700 hover:text-blue-500">Contact Us</a></li>
                <li class="flex space-x-4">
                    <a href="cart.php" class="text-gray-700 hover:text-blue-500"><i class="fa-solid fa-cart-shopping"></i></a>
                    <a href="account.php" class="text-gray-700 hover:text-blue-500"><i class="fa-solid fa-user"></i></a>
                </li>
            </ul>
        </div>
        <!-- Mobile Menu -->
        <ul class="lg:hidden bg-white w-full hidden flex-col space-y-4 p-4" id="mobileNavLinks">
            <li><a href="index.php" class="text-gray-700 hover:text-blue-500 block">Home</a></li>
            <li><a href="shop.php" class="text-gray-700 hover:text-blue-500 block">Shop</a></li>
            <li><a href="about.php" class="text-gray-700 hover:text-blue-500 block">About</a></li>
            <li><a href="contact.php" class="text-gray-700 hover:text-blue-500 block">Contact Us</a></li>
            <li class="flex space-x-4">
                <a href="cart.php" class="text-gray-700 hover:text-blue-500"><i class="fa-solid fa-cart-shopping"></i></a>
                <a href="account.php" class="text-gray-700 hover:text-blue-500"><i class="fa-solid fa-user"></i></a>
            </li>
        </ul>
    </nav>

    <!-- Login Section -->
    <section class="mt-20 w-full max-w-md bg-white p-8 rounded-lg shadow-md mx-auto">
        <div class="text-center">
            <h2 class="text-2xl font-bold">Login</h2>
            <hr class="my-4 border-gray-300">
        </div>
        <form id="login-form" method="POST" action="login.php" class="space-y-6">
            <!-- Error Message -->
            <?php if (isset($_GET['error'])): ?>
                <p class="text-red-500 text-center"><?php echo $_GET['error']; ?></p>
            <?php endif; ?>

            <!-- Email -->
            <div>
                <label for="login-email" class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" id="login-email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Enter your email" required>
            </div>

            <!-- Password -->
            <div>
                <label for="login-password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" id="login-password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Enter your password" required>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" id="login-btn" name="login_btn" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300">
                    Login
                </button>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <a id="register-url" href="register.php" class="text-blue-500 hover:underline">Don't have an account? Register</a>
            </div>
        </form>
    </section>

    <!-- JavaScript for Navbar Toggle -->
    <script>
        const toggleButton = document.getElementById('toggleButton');
        const mobileNavLinks = document.getElementById('mobileNavLinks');

        toggleButton.addEventListener('click', () => {
            mobileNavLinks.classList.toggle('hidden');
        });
    </script>


      <?php include('layouts/footer.php'); ?>