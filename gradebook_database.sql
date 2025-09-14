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
