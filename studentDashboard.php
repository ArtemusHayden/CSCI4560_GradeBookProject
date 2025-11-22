<?php
    session_start();
    include "connect.php";
    require_once('fpdf/fpdf.php');

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $student_id = $_SESSION['user_id'];
    $selected_section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : null;

    //all enrolled classes
    $query = "SELECT c.courseName, s.section_id, u.fullName AS teacher_name
            FROM enrollments e
            JOIN sections s ON e.section_id = s.section_id
            JOIN courses c ON s.course_id = c.course_id
            JOIN users u ON s.teacher_id = u.user_id
            WHERE e.student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    //grades if a section is selected
    $grades_result = null;
    $section_info = null;

    if ($selected_section_id) {
        //section info
        $section_query = "SELECT c.courseName, s.section_id, u.fullName AS teacher_name
                        FROM sections s
                        JOIN courses c ON s.course_id = c.course_id
                        JOIN users u ON s.teacher_id = u.user_id
                        WHERE s.section_id = ?";
        $section_stmt = $conn->prepare($section_query);
        $section_stmt->bind_param("i", $selected_section_id);
        $section_stmt->execute();
        $section_info = $section_stmt->get_result()->fetch_assoc();

        //grades for this student and section
        $grades_query = "SELECT assignment_name, grade_value
                        FROM grades
                        WHERE user_id = ? AND section_id = ?";
        $grades_stmt = $conn->prepare($grades_query);
        $grades_stmt->bind_param("ii", $student_id, $selected_section_id);
        $grades_stmt->execute();
        $grades_result = $grades_stmt->get_result();
    }

    //PDF download 
    if (isset($_POST['download_pdf'])) {
        //student info
        $user_query = $conn->prepare("SELECT username, fullName FROM users WHERE user_id = ?");
        $user_query->bind_param("i", $student_id);
        $user_query->execute();
        $user = $user_query->get_result()->fetch_assoc();

        //all enrolled classes and grades
        $query = "
            SELECT c.courseName, s.section_id, u.fullName AS teacher_name, g.assignment_name, g.grade_value
            FROM enrollments e JOIN sections s ON e.section_id = s.section_id JOIN courses c 
            ON s.course_id = c.course_id JOIN users u ON s.teacher_id = u.user_id
            LEFT JOIN grades g ON g.section_id = s.section_id AND g.user_id = e.student_id
            WHERE e.student_id = ? ORDER BY c.courseName, g.assignment_name";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        //organize data by course
        $grades_by_course = [];
        while ($row = $result->fetch_assoc()) {
            $course = $row['courseName'];
            if (!isset($grades_by_course[$course])) {
                $grades_by_course[$course] = [
                    'teacher' => $row['teacher_name'],
                    'section' => $row['section_id'],
                    'grades' => []
                ];
            }
            if ($row['assignment_name']) {
                $grades_by_course[$course]['grades'][] = [
                    'assignment' => $row['assignment_name'],
                    'grade' => $row['grade_value']
                ];
            }
        }

        //generate PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(0, 10, 'Student Progress Report', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(0, 8, 'Student: ' . $user['fullName'] . " (" . $user['username'] . ")", 0, 1);
        $pdf->Cell(0, 8, 'Date: ' . date("F j, Y"), 0, 1);
        $pdf->Ln(8);

        foreach ($grades_by_course as $course => $data) {
            $pdf->SetFont('Times', 'B', 13);
            $pdf->Cell(0, 8, $course . " (Section " . $data['section'] . ")", 0, 1);
            $pdf->SetFont('Times', '', 12);
            $pdf->Cell(0, 6, "Teacher: " . $data['teacher'], 0, 1);
            $pdf->Ln(3);

            if (!empty($data['grades'])) {
                $pdf->SetFont('Times', 'B', 12);
                $pdf->Cell(100, 8, 'Assignment', 1);
                $pdf->Cell(40, 8, 'Grade', 1);
                $pdf->Ln();

                $pdf->SetFont('Times', '', 12);
                $total = 0;
                $count = 0;

                foreach ($data['grades'] as $grade) {
                    $pdf->Cell(100, 8, $grade['assignment'], 1);
                    $pdf->Cell(40, 8, $grade['grade'], 1);
                    $pdf->Ln();
                    $total += $grade['grade'];
                    $count++;
                }

                $average = $count > 0 ? number_format($total / $count, 2) : 'N/A';
                $pdf->Cell(100, 8, 'Average Grade:', 1);
                $pdf->Cell(40, 8, $average, 1);
                $pdf->Ln(12);
            } else {
                $pdf->Cell(0, 8, 'No grades available for this class.', 0, 1);
                $pdf->Ln(5);
            }
        }
        $pdf->Output('I', 'Progress_Report.pdf');
        exit;
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

    <div class="classes-and-sections"> 
        <h2>Welcome back <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

        <h3>Your Enrolled Classes</h3>

        <?php if ($result->num_rows > 0): ?> <!-- show student enrolled courses in table -->
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Section ID</th>
                        <th>Teacher</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['courseName']); ?></td>
                            <td><?php echo htmlspecialchars($row['section_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                            <td>
                                <a href="?section_id=<?php echo urlencode($row['section_id']); ?>" class="view_grades">
                                    View Grades
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <form method="POST" target="_blank"> <!-- get progress report -->
                <button type="submit" name="download_pdf" class="view-btn">Print Progress Report (All Classes)</button>
            </form>
        </div>

    <?php else: ?>
        <p>You are not currently enrolled in any sections.</p>
    <?php endif; ?>


    <div class="student-results">
        <?php if ($selected_section_id && $section_info): ?> <!-- get grades for student -->
            <hr>
            <h3>Grades for <?php echo htmlspecialchars($section_info['courseName']); ?> (Section <?php echo htmlspecialchars($section_info['section_id']); ?>)</h3>
            <p><strong>Teacher:</strong> <?php echo htmlspecialchars($section_info['teacher_name']); ?></p>

            <?php if ($grades_result && $grades_result->num_rows > 0): ?>
                <table border="1" cellpadding="8" cellspacing="0">
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
                        while ($grade = $grades_result->fetch_assoc()): 
                            $total += $grade['grade_value'];
                            $count++;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['assignment_name']); ?></td>
                                <td><?php echo htmlspecialchars($grade['grade_value']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <p><strong>Average Grade:</strong> <!-- compute average -->
                    <?php echo $count > 0 ? number_format($total / $count, 2) : 'N/A'; ?>
                </p>
            <?php else: ?>
                <p>No grades available for this section yet.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
