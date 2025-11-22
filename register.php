<?php
    include "connect.php"; //call connection to database
    mysqli_report(MYSQLI_REPORT_OFF); //stops throwing exceptions

    $orgResult = $conn->query("SELECT organization_id, organization_name FROM organizations ORDER BY organization_name ASC");
    $message = '';

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
            $message = "Registration successful!";
        } else {
            if ($conn->errno === 1062) { //error code for duplicate entries
                if (strpos($conn->error, 'username') !== false) { //error message for duplicate username
                     $message = "That username is already taken!";
                } 
                if (strpos($conn->error, 'email') !== false) { //error message for duplicate email
                     $message = "That email is already registered!";
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
    <div class="navigation">
        <div class="nav-left">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="home.php">Home</a>
            <a href="about.php">About</a>
            <a href="logout.php">Logout</a>
        </div>
        <div class="nav-right">
            <p>SimplyGrade</p>
        </div>
    </div>

    <div class="register-container">
        <h2>Create an Account</h2>

        <form method="POST" action="register.php"> <!-- form for registering -->
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Full Name:</label>
            <input type="text" name="fullName" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>

            <label>Organization:</label>
            <select name="organization_id" required>
                <option value="">Select Organization</option> <!-- list organizations -->
                <?php
                    if ($orgResult && $orgResult->num_rows > 0) {
                        while ($row = $orgResult->fetch_assoc()) {
                            echo '<option value="' . $row['organization_id'] . '">' . htmlspecialchars($row['organization_name']) . '</option>';
                        }
                    }
                ?>
            </select>

            <button type="submit">Register</button>
        </form>

        <?php
            if (!empty($message)) {
                echo '<p class="error-message">' . $message . '</p>';
            }
        ?>
    </div>
</body>
