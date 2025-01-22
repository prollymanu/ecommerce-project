<?php 
session_start();
include('server/connection.php');

// if user has already register, take them to the account page
if (isset($_SESSION['logged_in'])) {
  header('location: account.php');
  exit;
}

function validatePassword($password) {
    if (strlen($password) < 6) {
        return "Password must be at least 6 characters long.";
    }
    if (!preg_match('/\d/', $password)) {
        return "Password must contain at least one number.";
    }
    if (!preg_match('/[^a-zA-Z\d\s]/', $password)) {
        return "Password must contain at least one special character.";
    }
    return true;
}

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validate name and email
    if (empty($name)) {
        header('location: register.php?error=Name is required');
        exit();
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('location: register.php?error=Invalid email address');
        exit();
    }

    // Validate password
    $validationResult = validatePassword($password);
    if ($validationResult !== true) {
        header('location: register.php?error=' . $validationResult);
        exit();
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        header('location: register.php?error=Passwords do not match');
        exit();
    }

    // Check if user already exists
    $stmt1 = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
    $stmt1->bind_param('s', $email);
    $stmt1->execute();
    $stmt1->store_result();

    if ($stmt1->num_rows > 0) {
        header('location: register.php?error=User with this email already exists');
        exit();
    } else {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users(user_name, user_email, user_password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            // getting user id
            $user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['logged_in'] = true;
            header('location: account.php?register=You have successfully registered');
            exit();
        } else {
            header('location: register.php?error=Could not create account at this moment');
            exit();
        }
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed top-0 left-0 w-full z-10">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="#" class="text-2xl font-bold text-black">Shaki</a>
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

    <!-- Registration Section -->
    <section class="mt-20 w-full max-w-md bg-white p-8 rounded-lg shadow-md mx-auto">
        <div class="text-center">
            <h2 class="text-2xl font-bold">Register</h2>
            <hr class="my-4 border-gray-300">
        </div>
        <form id="register-form" method="POST" action="register.php" class="space-y-6">
            <!-- Error Message -->
            <?php if (isset($_GET['error'])): ?>
                <p class="text-red-500 text-center"><?php echo $_GET['error']; ?></p>
            <?php endif; ?>

            <!-- Name -->
            <div>
                <label for="register-name" class="block text-gray-700 font-medium mb-1">Name</label>
                <input type="text" id="register-name" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Name" required>
            </div>

            <!-- Email -->
            <div>
                <label for="register-email" class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" id="register-email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Email" required>
            </div>

            <!-- Password -->
            <div>
                <label for="register-password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" id="register-password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Password" required>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="register-confirm-password" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                <input type="password" id="register-confirm-password" name="confirmPassword" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Confirm Password" required>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" id="register-btn" name="register" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300">
                    Register
                </button>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <a id="login-url" href="login.php" class="text-blue-500 hover:underline">Already have an account? Login</a>
            </div>
        </form>
    </section>

    <!-- JavaScript for Navbar Toggle -->
    <script>
        const toggleButton = document.getElementById('toggleButton');
        const navLinks = document.getElementById('navLinks');
        const mobileNavLinks = document.getElementById('mobileNavLinks');

        toggleButton.addEventListener('click', () => {
            mobileNavLinks.classList.toggle('hidden');
        });
    </script>




      <?php include('layouts/footer.php'); ?>