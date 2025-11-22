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

    //add assignments and grades for given student/section
    if (isset($_POST['submit_assignment'])) {
        $section_id = $_POST['selected_section'];
        $assignment_name = $_POST['assignment_name'];
        $grades = $_POST['grades'];

        foreach ($grades as $student_id => $grade_value) {
            $stmt = $conn->prepare("INSERT INTO grades (user_id, section_id, assignment_name, grade_value) 
                                    VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisd", $student_id, $section_id, $assignment_name, $grade_value);
            $stmt->execute();
        }

        //refresh student’s grades
        $sql = "SELECT assignment_name, grade_value 
                FROM grades WHERE section_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $section_id, $student_id);
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

    <div class="classes-and-sections"> <!-- display teacher's section and classes -->
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
    </div>

    <?php if ($studentsResult !== null): ?> <!--show students-->
        <div class="student-results">
            <h3>Students in Section <?php echo $selected_section; ?>:</h3>
            <?php if ($studentsResult->num_rows > 0): ?>
                <table>
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
        </div>
    <?php endif; ?>

    <?php if ($assignmentsResult !== null): ?> <!--show assignments-->
        <div class="all-assignments">
            <h3>Assignments for Section <?php echo $selected_section; ?>:</h3>
            <?php if ($assignmentsResult->num_rows > 0): ?>
                <table>
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
        </div>

    <div class="add-assignment"> <!--add new assignments-->
        <h4>Add New Assignment</h4>
        <form method="post" action="">
            <input type="hidden" name="selected_section" value="<?php echo htmlspecialchars($selected_section); ?>">

            <label for="assignment_name">Assignment Name:</label><br>
            <input type="text" id="assignment_name" name="assignment_name" required><br><br>

            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $studentsQuery = "SELECT u.user_id AS student_id, u.fullName AS student_name FROM users u
                                    JOIN enrollments e ON u.user_id = e.student_id 
                                    WHERE e.section_id = '$selected_section' AND u.isTeacher = 0";
                    $studentsResult = $conn->query($studentsQuery);

                    if ($studentsResult && $studentsResult->num_rows > 0):
                        while ($student = $studentsResult->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                            <td>
                                <input type="number" name="grades[<?php echo $student['student_id']; ?>]" min="0" max="100" step="0.1" required>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                        echo "<tr><td colspan='2'>No students found in this section.</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>

            <br>
            <button type="submit" name="submit_assignment" value="1">Save Assignment</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($studentGradesResult !== null): ?> <!--show individual grades-->
        <div class="individual-grades">
            <h3>Grades for Student in Section <?php echo $selected_section; ?>:</h3>
            <?php if ($studentGradesResult->num_rows > 0): ?>
                <table>
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
                                    <?php echo $count > 0 ? round($total / $count, 2) : 'N/A'; ?>
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No grades found for this student in this section.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
