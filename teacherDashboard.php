<?php
    session_start();
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
    <div class="navigation"> <!-- default nav bar -->
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="home.php">Home</a>
    </div>

    <h2>Welcome back <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

</body>
