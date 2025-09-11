<?php
    $servername = "localhost";
    $username = "root";
    $password = "";

    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully";

    // $stmt = $conn->prepare("INSERT INTO users ("username", "full name", "email", "password", "FALSE"));
    // $stmt->bind_param("sss", $username, $hashed_password, $email);
    //test connection by inserting
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simply Grade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navigation">
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="home.php">Home</a>
    </div>

    

</body>