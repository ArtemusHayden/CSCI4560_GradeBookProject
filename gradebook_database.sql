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
`course_id` INT(11) NOT NULL,
`section_id` INT(11) NOT NULL,
`grade_value` DECIMAL(5,2) NOT NULL,
PRIMARY KEY (`grade_id`),
FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`),
FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`),
FOREIGN KEY (`section_id`) REFERENCES `sections`(`section_id`)
);

-- Below is AI generated mock data

INSERT INTO `courses` (`courseName`) VALUES
('Mathematics I'),       -- course_id = 1
('English Literature'),  -- 2
('Biology'),             -- 3
('Chemistry'),           -- 4
('World History'),       -- 5
('Computer Science'),    -- 6
('Art Appreciation'),    -- 7
('Physics');             -- 8

-- ---------- TEACHERS ---------- --teachpass1
INSERT INTO `users` (`username`, `fullName`, `email`, `password`, `isTeacher`) VALUES
('teacher1', 'Teacher One', 'teacher1@example.com', '$2y$10$PTXnlQB.ZHP9COG.1auHxO0UBFR4DYNwqZX29Z68nKpAEHQaZjXUy', 1),
('teacher2', 'Teacher Two', 'teacher2@example.com', '$2y$10$rYXHIqQ5WRYPKncMUK1ZiOVkD10ra/VvhXK7u9Esl6KWT1dlr2leO', 1),
('teacher3', 'Teacher Three', 'teacher3@example.com', '$2y$10$yFe8e58aJ6000X6wyLhAKuh0skwBV28PuWpvq7sW.puyj8pHdou2i', 1);


-- ---------- SECTIONS ----------
-- Mapping: section_id increments as inserted (we create 18 sections)
-- course_id mapping: 1=Math,2=English,3=Biology,4=Chemistry,5=History,6=CS,7=Art,8=Physics

INSERT INTO `sections` (`course_id`, `teacher_id`) VALUES
(1, 1),  -- section_id = 1  Math (teacher Alice)
(1, 2),  -- section_id = 2  Math (teacher Bob)

(2, 2),  -- section_id = 3  English (Bob)
(2, 3),  -- section_id = 4  English (Carol)
(2, 1),  -- section_id = 5  English (Alice)

(3, 3),  -- section_id = 6  Biology (Carol)
(3, 1),  -- section_id = 7  Biology (Alice)

(4, 1),  -- section_id = 8  Chemistry (Alice)
(4, 2),  -- section_id = 9  Chemistry (Bob)

(5, 2),  -- section_id = 10 History (Bob)
(5, 3),  -- section_id = 11 History (Carol)
(5, 1),  -- section_id = 12 History (Alice)

(6, 3),  -- section_id = 13 Computer Science (Carol)
(6, 2),  -- section_id = 14 Computer Science (Bob)

(7, 1),  -- section_id = 15 Art (Alice)
(7, 3),  -- section_id = 16 Art (Carol)

(8, 2),  -- section_id = 17 Physics (Bob)
(8, 1);  -- section_id = 18 Physics (Alice)

-- ---------- STUDENTS ---------- --studentpass1
INSERT INTO `users` (`username`, `fullName`, `email`, `password`, `isTeacher`) VALUES
('student1', 'Student One', 'student1@example.com', '$2y$10$MMaUHkgOfJIX3zmDKmYb8.Pn3.MmoH6KYm85mEcpRsQhtlUxMs1XW', 0),
('student2', 'Student Two', 'student2@example.com', '$2y$10$YbvunKvgTJUv6zAOjPrN4uVSSAEIA/gjTKTdBXTJ/JC0e.4vlvf5O', 0),
('student3', 'Student Three', 'student3@example.com', '$2y$10$Ce0PIkBPXU7jInKPNof.aOKZH6Ji7oEKVpqG6fpiX87J26fQ1Ujyi', 0),
('student4', 'Student Four', 'student4@example.com', '$2y$10$.LPKa9sHMSDSAx.pKVwXkuutz5IiqTWdC0GE9OX4MYNCSGxm2IGIa', 0),
('student5', 'Student Five', 'student5@example.com', '$2y$10$NDrfRyjZkskSVFNWrkcn5OpBe9S26UDzXXELF8SqUJPgQs9icqZk2', 0),
('student6', 'Student Six', 'student6@example.com', '$2y$10$s5qDWt2lC2G0XScIttafTeaMDjsGA4kroBrTl2MYrAgA8rI.ARW2u', 0),
('student7', 'Student Seven', 'student7@example.com', '$2y$10$gReT12PsMcTl/JGtNbjm/.Rcn7sQD9TF7AzKbtDZZWErFCl2RYGk6', 0),
('student8', 'Student Eight', 'student8@example.com', '$2y$10$g24XvF.26MbNa1Ua/KWMje9OEeyZiVBKI5FoLV4DD7n.NCX8jLB/a', 0),
('student9', 'Student Nine', 'student9@example.com', '$2y$10$FsYxS6kwLiTWvU9ninDQIevRCrr1QXJZd6FhpYPNDUluwy9qsi20a', 0),
('student10', 'Student Ten', 'student10@example.com', '$2y$10$LRC8FaMXwfEjJNAC2Y4Nauu80gVvWp7VlxNOtFQs.uE4NHIOjkAC.', 0),
('student11', 'Student Eleven', 'student11@example.com', '$2y$10$1PFuJITNYCroIGhJ1FIJK.nYChK4hPAKgZiySCa58aEhh.eeFb3P6', 0),
('student12', 'Student Twelve', 'student12@example.com', '$2y$10$wI0XaENBU1XfaL7LIVFwku5rygFVzOoQ85XgnQCeeGf2tebmE4xA2', 0),
('student13', 'Student Thirteen', 'student13@example.com', '$2y$10$uZkXCCynALhieqN6BUWWseHEnCSKPTGsUerbDl1ldeXWu/uaIrzeO', 0),
('student14', 'Student Fourteen', 'student14@example.com', '$2y$10$xLBEfBz.58m9H1fWCq0.bOcktn87hR4ADiq9f6GT9AP4jKbDCWJbu', 0),
('student15', 'Student Fifteen', 'student15@example.com', '$2y$10$Q09lP1A7YfNu3iRgmT7CW.yGLc2CLkBsyQ4PbQZzf4TjhDJvyaZim', 0),
('student16', 'Student Sixteen', 'student16@example.com', '$2y$10$cRZIvANNukrhyeYPoHouAuqipJR5WwdekXL3Pybid0F.J5VV3YGmW', 0),
('student17', 'Student Seventeen', 'student17@example.com', '$2y$10$EaB2qQIgI0lr/OF1kn2VyuYCRY6nahIApQOm6wJ6.CVAlVJc4IBxm', 0),
('student18', 'Student Eighteen', 'student18@example.com', '$2y$10$NFPxMruzHx.U4uzbJG9B.usi4bbY0Fwr371kWyQxBofOH9FjJksiC', 0),
('student19', 'Student Nineteen', 'student19@example.com', '$2y$10$yVmxxlnLYNUJHXYzizXJaeBbGqB7MB6PmAANFUC2r8U4Z2vkmBule', 0),
('student20', 'Student Twenty', 'student20@example.com', '$2y$10$PO6MGyg7PJ5OKBf3zZZESO6GQTFqxq.UNNPKb8ZQNpKJrDtkq3Gyi', 0);

-- ---------- GRADES / ENROLLMENTS ----------

INSERT INTO `grades` (`user_id`, `course_id`, `section_id`, `grade_value`) VALUES
-- student user_id = 4  (k=0) --> sections 1,7,13
(4, 1, 1, 95.00),
(4, 3, 7, 88.50),
(4, 6, 13, 76.25),

-- user_id = 5 (k=1) --> sections 2,8,14
(5, 1, 2, 84.00),
(5, 4, 8, 91.75),
(5, 6, 14, 69.50),

-- user_id = 6 (k=2) --> sections 3,9,15
(6, 2, 3, 100.00),
(6, 4, 9, 82.30),
(6, 7, 15, 74.60),

-- user_id = 7 (k=3) --> sections 4,10,16
(7, 2, 4, 89.40),
(7, 5, 10, 95.00),
(7, 7, 16, 88.50),

-- user_id = 8 (k=4) --> sections 5,11,17
(8, 2, 5, 76.25),
(8, 5, 11, 84.00),
(8, 8, 17, 91.75),

-- user_id = 9 (k=5) --> sections 6,12,18
(9, 3, 6, 69.50),
(9, 5, 12, 100.00),
(9, 8, 18, 82.30),

-- user_id = 10 (k=6) --> sections 7,13,1
(10, 3, 7, 74.60),
(10, 6, 13, 89.40),
(10, 1, 1, 95.00),

-- user_id = 11 (k=7) --> sections 8,14,2
(11, 4, 8, 88.50),
(11, 6, 14, 76.25),
(11, 1, 2, 84.00),

-- user_id = 12 (k=8) --> sections 9,15,3
(12, 4, 9, 91.75),
(12, 7, 15, 69.50),
(12, 2, 3, 100.00),

-- user_id = 13 (k=9) --> sections 10,16,4
(13, 5, 10, 82.30),
(13, 7, 16, 74.60),
(13, 2, 4, 89.40),

-- user_id = 14 (k=10) --> sections 11,17,5
(14, 5, 11, 95.00),
(14, 8, 17, 88.50),
(14, 2, 5, 76.25),

-- user_id = 15 (k=11) --> sections 12,18,6
(15, 5, 12, 84.00),
(15, 8, 18, 91.75),
(15, 3, 6, 69.50),

-- user_id = 16 (k=12) --> sections 13,1,7
(16, 6, 13, 100.00),
(16, 1, 1, 82.30),
(16, 3, 7, 74.60),

-- user_id = 17 (k=13) --> sections 14,2,8
(17, 6, 14, 89.40),
(17, 1, 2, 95.00),
(17, 4, 8, 88.50),

-- user_id = 18 (k=14) --> sections 15,3,9
(18, 7, 15, 76.25),
(18, 2, 3, 84.00),
(18, 4, 9, 91.75),

-- user_id = 19 (k=15) --> sections 16,4,10
(19, 7, 16, 69.50),
(19, 2, 4, 100.00),
(19, 5, 10, 82.30),

-- user_id = 20 (k=16) --> sections 17,5,11
(20, 8, 17, 74.60),
(20, 2, 5, 89.40),
(20, 5, 11, 95.00),

-- user_id = 21 (k=17) --> sections 18,6,12
(21, 8, 18, 88.50),
(21, 3, 6, 76.25),
(21, 5, 12, 84.00),

-- user_id = 22 (k=18) --> sections 1,7,13
(22, 1, 1, 91.75),
(22, 3, 7, 69.50),
(22, 6, 13, 100.00),

-- user_id = 23 (k=19) --> sections 2,8,14
(23, 1, 2, 82.30),
(23, 4, 8, 74.60),
(23, 6, 14, 89.40);
