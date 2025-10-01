DROP DATABASE IF EXISTS gradebook_database;
CREATE Database gradebook_database;
USE gradebook_database;

CREATE TABLE `courses` (
    `course_id` INT(11) NOT NULL AUTO_INCREMENT,
    `courseName` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`course_id`)
);

CREATE TABLE `users` (
    `user_id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `fullName` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `isTeacher` BIT NOT NULL, -- if false, user is student, if true user is teacher
    PRIMARY KEY (`user_id`)
);

CREATE TABLE `sections` (
    `section_id` INT(11) NOT NULL AUTO_INCREMENT,
    `course_id` INT(11) NOT NULL,
    `teacher_id` INT(11) NOT NULL,
    PRIMARY KEY (`section_id`),
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`),
    FOREIGN KEY (`teacher_id`) REFERENCES `users`(`user_id`)
);

CREATE TABLE `grades` (
    `grade_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `section_id` INT(11) NOT NULL,
    `assignment_name` VARCHAR(255) NOT NULL,
    `grade_value` DECIMAL(5,2) NOT NULL,
    PRIMARY KEY (`grade_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`),
    FOREIGN KEY (`section_id`) REFERENCES `sections`(`section_id`)
);

CREATE TABLE `enrollments` (
    `enrollment_id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `section_id` INT(11) NOT NULL,
    PRIMARY KEY (`enrollment_id`),
    FOREIGN KEY (`student_id`) REFERENCES `users`(`user_id`),
    FOREIGN KEY (`section_id`) REFERENCES `sections`(`section_id`)
);

-- below is AI generated junk data for testing/demo purposes


-- -----------------------------
-- Insert Teachers
-- -----------------------------
INSERT INTO users (username, fullName, email, password, isTeacher) VALUES
('teacher1', 'Alice Johnson', 'alice.johnson@example.com', 'password123', 1),
('teacher2', 'Bob Smith', 'bob.smith@example.com', 'password123', 1),
('teacher3', 'Carol Davis', 'carol.davis@example.com', 'password123', 1);

-- Teacher login info example:
-- Username: teacher1
-- Password: password123

-- -----------------------------
-- Insert Courses
-- -----------------------------
INSERT INTO courses (courseName) VALUES
('Math 101'),
('English 101'),
('History 101'),
('Science 101'),
('Art 101'),
('Music 101');

-- -----------------------------
-- Insert Sections (2 sections per course)
-- -----------------------------
INSERT INTO sections (course_id, teacher_id) VALUES
(1, 1), (1, 2),
(2, 1), (2, 3),
(3, 2), (3, 3),
(4, 1), (4, 2),
(5, 2), (5, 3),
(6, 1), (6, 3);

-- -----------------------------
-- Insert Students (25)
-- -----------------------------
INSERT INTO users (username, fullName, email, password, isTeacher) VALUES
('student1', 'Student One', 'student1@example.com', 'pass1', 0),
('student2', 'Student Two', 'student2@example.com', 'pass2', 0),
('student3', 'Student Three', 'student3@example.com', 'pass3', 0),
('student4', 'Student Four', 'student4@example.com', 'pass4', 0),
('student5', 'Student Five', 'student5@example.com', 'pass5', 0),
('student6', 'Student Six', 'student6@example.com', 'pass6', 0),
('student7', 'Student Seven', 'student7@example.com', 'pass7', 0),
('student8', 'Student Eight', 'student8@example.com', 'pass8', 0),
('student9', 'Student Nine', 'student9@example.com', 'pass9', 0),
('student10', 'Student Ten', 'student10@example.com', 'pass10', 0),
('student11', 'Student Eleven', 'student11@example.com', 'pass11', 0),
('student12', 'Student Twelve', 'student12@example.com', 'pass12', 0),
('student13', 'Student Thirteen', 'student13@example.com', 'pass13', 0),
('student14', 'Student Fourteen', 'student14@example.com', 'pass14', 0),
('student15', 'Student Fifteen', 'student15@example.com', 'pass15', 0),
('student16', 'Student Sixteen', 'student16@example.com', 'pass16', 0),
('student17', 'Student Seventeen', 'student17@example.com', 'pass17', 0),
('student18', 'Student Eighteen', 'student18@example.com', 'pass18', 0),
('student19', 'Student Nineteen', 'student19@example.com', 'pass19', 0),
('student20', 'Student Twenty', 'student20@example.com', 'pass20', 0),
('student21', 'Student Twenty-One', 'student21@example.com', 'pass21', 0),
('student22', 'Student Twenty-Two', 'student22@example.com', 'pass22', 0),
('student23', 'Student Twenty-Three', 'student23@example.com', 'pass23', 0),
('student24', 'Student Twenty-Four', 'student24@example.com', 'pass24', 0),
('student25', 'Student Twenty-Five', 'student25@example.com', 'pass25', 0);


-- Section 1 (Math 101, Teacher 1) - students 4,5,6,7,8
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(4, 1, 'Homework 1', 85), (4, 1, 'Quiz 1', 90), (4, 1, 'Exam 1', 88),
(5, 1, 'Homework 1', 82), (5, 1, 'Quiz 1', 87), (5, 1, 'Exam 1', 91),
(6, 1, 'Homework 1', 75), (6, 1, 'Quiz 1', 80), (6, 1, 'Exam 1', 78),
(7, 1, 'Homework 1', 92), (7, 1, 'Quiz 1', 88), (7, 1, 'Exam 1', 94),
(8, 1, 'Homework 1', 88), (8, 1, 'Quiz 1', 84), (8, 1, 'Exam 1', 90);

-- Section 2 (Math 101, Teacher 2) - students 9,10,11,12
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(9, 2, 'Homework 1', 77), (9, 2, 'Quiz 1', 81), (9, 2, 'Exam 1', 79),
(10, 2, 'Homework 1', 85), (10, 2, 'Quiz 1', 89), (10, 2, 'Exam 1', 90),
(11, 2, 'Homework 1', 80), (11, 2, 'Quiz 1', 83), (11, 2, 'Exam 1', 85),
(12, 2, 'Homework 1', 74), (12, 2, 'Quiz 1', 76), (12, 2, 'Exam 1', 78);

-- Section 3 (English 101, Teacher 1) - students 13,14,15,16
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(13, 3, 'Essay 1', 88), (13, 3, 'Quiz 1', 85), (13, 3, 'Midterm', 90),
(14, 3, 'Essay 1', 82), (14, 3, 'Quiz 1', 79), (14, 3, 'Midterm', 84),
(15, 3, 'Essay 1', 91), (15, 3, 'Quiz 1', 87), (15, 3, 'Midterm', 93),
(16, 3, 'Essay 1', 76), (16, 3, 'Quiz 1', 80), (16, 3, 'Midterm', 78);

-- Section 4 (English 101, Teacher 3) - students 17,18,19
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(17, 4, 'Essay 1', 83), (17, 4, 'Quiz 1', 79), (17, 4, 'Midterm', 81),
(18, 4, 'Essay 1', 85), (18, 4, 'Quiz 1', 82), (18, 4, 'Midterm', 88),
(19, 4, 'Essay 1', 77), (19, 4, 'Quiz 1', 74), (19, 4, 'Midterm', 79);

-- Section 5 (History 101, Teacher 2) - students 20,21,22
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(20, 5, 'Assignment 1', 91), (20, 5, 'Quiz 1', 88), (20, 5, 'Exam 1', 93),
(21, 5, 'Assignment 1', 84), (21, 5, 'Quiz 1', 80), (21, 5, 'Exam 1', 86),
(22, 5, 'Assignment 1', 78), (22, 5, 'Quiz 1', 75), (22, 5, 'Exam 1', 81);

-- Section 6 (History 101, Teacher 3) - students 23,24,25
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(23, 6, 'Assignment 1', 89), (23, 6, 'Quiz 1', 85), (23, 6, 'Exam 1', 90),
(24, 6, 'Assignment 1', 83), (24, 6, 'Quiz 1', 80), (24, 6, 'Exam 1', 84),
(25, 6, 'Assignment 1', 75), (25, 6, 'Quiz 1', 72), (25, 6, 'Exam 1', 78);

-- Section 7 (Science 101, Teacher 1) - students 4,6,8,10
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(4, 7, 'Lab 1', 92), (4, 7, 'Quiz 1', 88), (4, 7, 'Midterm', 94),
(6, 7, 'Lab 1', 80), (6, 7, 'Quiz 1', 76), (6, 7, 'Midterm', 82),
(8, 7, 'Lab 1', 85), (8, 7, 'Quiz 1', 83), (8, 7, 'Midterm', 87),
(10, 7, 'Lab 1', 78), (10, 7, 'Quiz 1', 74), (10, 7, 'Midterm', 80);

-- Section 8 (Science 101, Teacher 2) - students 12,14,16
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(12, 8, 'Lab 1', 79), (12, 8, 'Quiz 1', 75), (12, 8, 'Midterm', 81),
(14, 8, 'Lab 1', 84), (14, 8, 'Quiz 1', 80), (14, 8, 'Midterm', 86),
(16, 8, 'Lab 1', 90), (16, 8, 'Quiz 1', 87), (16, 8, 'Midterm', 92);

-- Section 9 (Art 101, Teacher 2) - students 18,20,22,24
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(18, 9, 'Project 1', 95), (18, 9, 'Quiz 1', 92), (18, 9, 'Final', 97),
(20, 9, 'Project 1', 89), (20, 9, 'Quiz 1', 85), (20, 9, 'Final', 91),
(22, 9, 'Project 1', 83), (22, 9, 'Quiz 1', 80), (22, 9, 'Final', 85),
(24, 9, 'Project 1', 77), (24, 9, 'Quiz 1', 73), (24, 9, 'Final', 79);

-- Section 10 (Art 101, Teacher 3) - students 26,27,28
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(26, 10, 'Project 1', 91), (26, 10, 'Quiz 1', 87), (26, 10, 'Final', 94),
(27, 10, 'Project 1', 84), (27, 10, 'Quiz 1', 80), (27, 10, 'Final', 86),
(28, 10, 'Project 1', 79), (28, 10, 'Quiz 1', 75), (28, 10, 'Final', 81);

-- Section 11 (Music 101, Teacher 1) - students 5,7,9,11
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(5, 11, 'Performance 1', 92), (5, 11, 'Quiz 1', 88), (5, 11, 'Final', 95),
(7, 11, 'Performance 1', 87), (7, 11, 'Quiz 1', 83), (7, 11, 'Final', 90),
(9, 11, 'Performance 1', 81), (9, 11, 'Quiz 1', 78), (9, 11, 'Final', 84),
(11, 11, 'Performance 1', 76), (11, 11, 'Quiz 1', 72), (11, 11, 'Final', 80);

-- Section 12 (Music 101, Teacher 3) - students 13,15,17,19
INSERT INTO grades (user_id, section_id, assignment_name, grade_value) VALUES
(13, 12, 'Performance 1', 93), (13, 12, 'Quiz 1', 89), (13, 12, 'Final', 96),
(15, 12, 'Performance 1', 88), (15, 12, 'Quiz 1', 84), (15, 12, 'Final', 91),
(17, 12, 'Performance 1', 82), (17, 12, 'Quiz 1', 78), (17, 12, 'Final', 85),
(19, 12, 'Performance 1', 77), (19, 12, 'Quiz 1', 74), (19, 12, 'Final', 80);


INSERT INTO enrollments (student_id, section_id) VALUES
-- Section 1 (Math 101, Teacher 1)
(4, 1), (5, 1), (6, 1), (7, 1), (8, 1),
-- Section 2 (Math 101, Teacher 2)
(9, 2), (10, 2), (11, 2), (12, 2),
-- Section 3 (English 101, Teacher 1)
(13, 3), (14, 3), (15, 3), (16, 3),
-- Section 4 (English 101, Teacher 3)
(17, 4), (18, 4), (19, 4),
-- Section 5 (History 101, Teacher 2)
(20, 5), (21, 5), (22, 5),
-- Section 6 (History 101, Teacher 3)
(23, 6), (24, 6), (25, 6),
-- Section 7 (Science 101, Teacher 1)
(4, 7), (6, 7), (8, 7), (10, 7),
-- Section 8 (Science 101, Teacher 2)
(12, 8), (14, 8), (16, 8),
-- Section 9 (Art 101, Teacher 2)
(18, 9), (20, 9), (22, 9), (24, 9),
-- Section 10 (Art 101, Teacher 3)
(26, 10), (27, 10), (28, 10),
-- Section 11 (Music 101, Teacher 1)
(5, 11), (7, 11), (9, 11), (11, 11),
-- Section 12 (Music 101, Teacher 3)
(13, 12), (15, 12), (17, 12), (19, 12);


