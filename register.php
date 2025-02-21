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
    $user = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="src/output.css">
</head>
<body>
    <h2>Register</h2>
    <form action="" method="post">
        <input type="text" name="username" placeholder="Enter Your Username" required><br>
        <input type="email" name="email" placeholder="Enter Your Email" required><br>
        <input type="text" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}"><br>
        <input type="password" name="password" placeholder="Enter Your Password" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
