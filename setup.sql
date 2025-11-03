-- setup.sql - create database, students table and sample data
DROP DATABASE IF EXISTS `ajaxdemo`;
CREATE DATABASE IF NOT EXISTS `ajaxdemo` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ajaxdemo`;

CREATE TABLE IF NOT EXISTS `students` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `roll_no` VARCHAR(64) DEFAULT NULL,
  `subjects` VARCHAR(512) DEFAULT NULL,
  `branch` VARCHAR(128) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`name`),
  KEY (`roll_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `students` (`name`, `roll_no`, `subjects`, `branch`, `email`) VALUES
('Asha Sharma','R1001','Math,Physics','Computer Science','asha@example.com'),
('Rahul Kumar','R1002','Math,Physics','Computer Science','rahul@example.com'),
('Priya Mehta','R1003','Chemistry,Biology','Mechanical','priya@example.com'),
('David Kumar','R1004','Math,Computer Science','Computer Science','david@example.com'),
('Ashwin Mathur','R1005','Math,Computer Science','Computer Science','ashwinmathur@example.com');
