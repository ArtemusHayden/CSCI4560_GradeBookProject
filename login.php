<?php
    session_start();
    include "connect.php"; //call connection to database

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
        $username = trim($_POST['username']); //retrieve user inputs from form
        $password = $_POST['password'];

        //fetch user
        $stmt = $conn->prepare("SELECT user_id, username, password, isTeacher FROM users WHERE username = ?"); //SQL query to search for user
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result(); //store found user

        if ($stmt->num_rows === 1) { //if user is found/username is valid
            $stmt->bind_result($user_id, $real_username, $real_password, $isTeacher); //assigns query results to usable variables
            $stmt->fetch();

            if ($password == $real_password) { // if(password_verify($password, $real_password)) ---> SHOULD USE THIS FOR ACTUAL APPLICATION
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $real_username;
                $_SESSION['isTeacher'] = $isTeacher; //store these values in session to be used later on
                if ($isTeacher) {
                    header("Location: teacherDashboard.php"); //redirect to teacher dashboard
                } else {
                    header("Location: studentDashboard.php"); //redirect to student dashboard
                }
                exit;
            } else {
                $message = "Invalid password!";
            }
        } else {
            $message = "Username not found!";
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
    <div class="navigation">
        <div class="nav-left">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="home.php">Home</a>
            <a href="about.php">About</a>
        </div>
        <div class="nav-right">
            <p>SimplyGrade</p>
        </div>
    </div>

    <div class="login-container">
        <h2>Welcome back!</h2>

        <form method="POST"> <!-- form for user inputs -->
            <label>Username:</label><br>
            <input type="text" name="username" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">Login</button>
        </form>

        <?php
            if (!empty($message)) {
                echo '<p class="error-message">' . $message . '</p>';
            }
        ?>
    </div>
</body>
