CREATE DATABASE vue_students;

USE vue_students;

CREATE TABLE students(
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50) UNIQUE,
  web VARCHAR(100) NOT NULL
);
