<?php
    session_start();
    include "connect.php";

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    //get the current teacher's user_id
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND isTeacher = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $teacher_id = $teacher['user_id'];

    //get all sections and courses the teacher teaches
    $sql = "SELECT s.section_id, c.courseName FROM sections s JOIN courses c 
            ON s.course_id = c.course_id WHERE s.teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $sectionsResult = $stmt->get_result();  


    //get students from enrollments table for given section
    $selected_section = null;
    $studentsResult = null;
    if (isset($_POST['view_students'])) {
        $selected_section = intval($_POST['section_id']);
        $sql = "SELECT u.user_id, u.username, u.fullName FROM enrollments e JOIN users u 
                ON e.student_id = u.user_id WHERE e.section_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $selected_section);
        $stmt->execute();
        $studentsResult = $stmt->get_result();
    }

    //get assignments and average grades for given section
    $assignmentsResult = null;
    if (isset($_POST['view_assignments'])) {
        $selected_section = intval($_POST['section_id']);
        $sql = "SELECT assignment_name, ROUND(AVG(grade_value),2) AS avg_grade
                FROM grades WHERE section_id = ? GROUP BY assignment_name";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $selected_section);
        $stmt->execute();
        $assignmentsResult = $stmt->get_result();
    }

    //get assignments and grades for given student/section
    $studentGradesResult = null;
    if (isset($_POST['view_student_grades'])) {
        $selected_section = intval($_POST['section_id']);
        $student_id = intval($_POST['student_id']);

        $sql = "SELECT assignment_name, grade_value FROM grades 
                WHERE section_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $selected_section, $student_id);
        $stmt->execute();
        $studentGradesResult = $stmt->get_result();
    }
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

    <h2>Welcome back <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <h3>Your Classes & Sections:</h3>
    <?php if (isset($sectionsResult) && $sectionsResult && $sectionsResult->num_rows > 0): ?>
        <ul>
            <?php while ($row = $sectionsResult->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($row['courseName']); ?> - Section <?php echo $row['section_id']; ?>
                    <form action="teacherDashboard.php" method="post" style="display:inline;">
                        <input type="hidden" name="section_id" value="<?php echo $row['section_id']; ?>">
                        <button type="submit" name="view_students">View Students</button>
                        <button type="submit" name="view_assignments">View Assignments</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>You are not assigned to any sections yet.</p>
    <?php endif; ?>

    <!--show students-->
    <?php if ($studentsResult !== null): ?>
        <h3>Students in Section <?php echo $selected_section; ?>:</h3>
        <?php if ($studentsResult->num_rows > 0): ?>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $studentsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['fullName']); ?></td>
                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                            <td>
                                <form action="teacherDashboard.php" method="post" style="display:inline;">
                                    <input type="hidden" name="section_id" value="<?php echo $selected_section; ?>">
                                    <input type="hidden" name="student_id" value="<?php echo $student['user_id']; ?>">
                                    <button type="submit" name="view_student_grades">View Grades</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No students enrolled in this section.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!--show assignments-->
    <?php if ($assignmentsResult !== null): ?>
        <h3>Assignments for Section <?php echo $selected_section; ?>:</h3>
        <?php if ($assignmentsResult->num_rows > 0): ?>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Assignment</th>
                        <th>Average Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $assignmentsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['assignment_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['avg_grade']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No assignments/grades found for this section.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!--show individual grades-->
    <?php if ($studentGradesResult !== null): ?>
        <h3>Grades for Student in Section <?php echo $selected_section; ?>:</h3>
        <?php if ($studentGradesResult->num_rows > 0): ?>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Assignment</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $total = 0;
                        $count = 0;
                        while ($grade = $studentGradesResult->fetch_assoc()):
                            $total += $grade['grade_value'];
                            $count++;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['assignment_name']); ?></td>
                            <td><?php echo htmlspecialchars($grade['grade_value']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td><strong>Average Grade</strong></td>
                        <td>
                            <strong>
                                <?php 
                                    echo $count > 0 ? round($total / $count, 2) : 'N/A'; 
                                ?>
                            </strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>No grades found for this student in this section.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>


<!-- todo: method for teacher to add assignments, placing students in classes, 
adding classes/sections, dropping/adding students would be administrative jobs, will implement if possible -->
