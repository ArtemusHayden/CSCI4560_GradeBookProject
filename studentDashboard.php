<?php
    session_start();
    include "connect.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    //get student ID
    $student_id = $_SESSION['user_id'];

    //query to fetch all sections/courses student is enrolled in
    $query = "SELECT c.courseName, s.section_id, u.fullName AS teacher_name
            FROM enrollments e JOIN sections s ON e.section_id = s.section_id
            JOIN courses c ON s.course_id = c.course_id JOIN users u 
            ON s.teacher_id = u.user_id WHERE e.student_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
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

    <h2>Welcome back <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <h3>Your Enrolled Classes</h3>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Section ID</th>
                    <th>Teacher</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['courseName']); ?></td>
                        <td><?php echo htmlspecialchars($row['section_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You are not currently enrolled in any sections.</p>
    <?php endif; ?>

</body>
