<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (!isset($_POST['password']) || trim($_POST['password']) === '') {
        echo "Password is required.";
        exit;
    } else {
        $phone = trim($_POST['password']);
    }
    
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user, $email, $phone, $pass);

    if ($stmt->execute()) {
        echo "Registration Successful! <a href='login.php'>Login Here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="src/output.css">

</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-200 via-blue-800 to-red-500">
  <div class="container mx-auto flex flex-col md:flex-row items-center justify-center space-y-6 md:space-y-0 md:space-x-0 px-4">
    
    <!-- Image Section with white background -->
    <div class="w-full md:w-1/2 flex justify-center  rounded-xl">
      <img src="src/saloon.png" alt="Register Image" class="rounded-lg shadow-md">
    </div>

    <!-- Registration Form Section -->
    <div class="w-full md:w-1/2 bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Register Here!</h2>
      <form action="#" method="POST" class="space-y-4">
        <div>
          <label for="username" class="block text-gray-700">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter your username" 
                 class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" />
        </div>
        <div>
          <label for="email" class="block text-gray-700">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" 
                 class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" />
        </div>
        <div>
          <label for="password" class="block text-gray-700">Phone Number</label>
          <input type="password" id="password" name="password" placeholder="Enter your Number" 
                 class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" />
        </div>
        <div>
          <label for="confirm_password" class="block text-gray-700">Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Enter your password" 
                 class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" />
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white rounded py-2 hover:bg-green-600 transition">
          Register
        </button>
      </form>
    </div>
  </div>
</body>
</html> 
