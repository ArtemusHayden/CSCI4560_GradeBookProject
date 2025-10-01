<?php
    include "connect.php"; //call connection to database
    mysqli_report(MYSQLI_REPORT_OFF); //stops throwing exceptions

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]); //retrieve user inputs from form
        $fullName = trim($_POST["fullName"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $role = $_POST["role"];

        //$hashPassword = password_hash($password, PASSWORD_DEFAULT); ---> SHOULD USE THIS FOR ACTUAL APPLICATION

        //convert role to boolean value
        $isTeacher = ($role === "teacher") ? 1 : 0;

        //prepare insert statement
        $stmt = $conn->prepare("INSERT INTO users (username, fullName, email, password, isTeacher) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $fullName, $email, $password, $isTeacher); // ----> SHOULD ACTUALLY PASS IN $hashPassword

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Registration successful!</p>";
        } else {
            if ($conn->errno === 1062) { //error code for duplicate entries
                if (strpos($conn->error, 'username') !== false) { //error message for duplicate username
                    echo "That username is already taken!";
                } 
                if (strpos($conn->error, 'email') !== false) { //error message for duplicate email
                    echo "That email is already registered!";
                } 
            } 
        }
        $stmt->close();
    }
    $conn->close();
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
    <div class="navigation"> <!-- defaul nav bar -->
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="home.php">Home</a>
    </div>

    <form method="POST" action="register.php"> <!-- form for user inputs -->
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Full Name:</label><br>
        <input type="text" name="fullName" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Role:</label><br>
        <select name="role" required>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
        </select><br><br>

        <button type="submit">Register</button>
    </form>
</body>
