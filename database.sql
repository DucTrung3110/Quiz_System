-- Quiz Test System Database Schema
-- Created for PHP OOP MVC Assignment

-- Create database
CREATE DATABASE IF NOT EXISTS quiz_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quiz_system;

-- Create quizzes table
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    time_limit INT NULL COMMENT 'Time limit in minutes',
    status ENUM('active', 'inactive', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create questions table
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(500) NOT NULL,
    option_b VARCHAR(500) NOT NULL,
    option_c VARCHAR(500) NOT NULL,
    option_d VARCHAR(500) NOT NULL,
    correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_order (quiz_id, order_num)
);

-- Create results table
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    answers JSON NOT NULL COMMENT 'Store detailed answers in JSON format',
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_email (email),
    INDEX idx_completed_at (completed_at)
);

-- Insert sample data for demonstration

-- Sample Quiz 1: General Knowledge
INSERT INTO quizzes (title, description, time_limit) VALUES 
('General Knowledge Quiz', 'Test your general knowledge with this basic quiz covering various topics.', 10);

SET @quiz1_id = LAST_INSERT_ID();

INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
(@quiz1_id, 'What is the capital of France?', 'London', 'Berlin', 'Paris', 'Madrid', 'C', 1),
(@quiz1_id, 'Which planet is known as the Red Planet?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'B', 2),
(@quiz1_id, 'Who painted the Mona Lisa?', 'Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo', 'C', 3),
(@quiz1_id, 'What is the largest ocean on Earth?', 'Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean', 'Pacific Ocean', 'D', 4),
(@quiz1_id, 'Which element has the chemical symbol "O"?', 'Gold', 'Oxygen', 'Silver', 'Iron', 'B', 5);

-- Sample Quiz 2: Programming Basics
INSERT INTO quizzes (title, description, time_limit) VALUES 
('Programming Basics', 'Test your knowledge of basic programming concepts and terminology.', 15);

SET @quiz2_id = LAST_INSERT_ID();

INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
(@quiz2_id, 'What does HTML stand for?', 'High Tech Modern Language', 'HyperText Markup Language', 'Home Tool Markup Language', 'Hyperlink and Text Markup Language', 'B', 1),
(@quiz2_id, 'Which of the following is NOT a programming language?', 'Python', 'Java', 'HTML', 'C++', 'C', 2),
(@quiz2_id, 'What is the correct way to create a comment in PHP?', '# This is a comment', '// This is a comment', '/* This is a comment */', 'All of the above', 'D', 3),
(@quiz2_id, 'Which symbol is used to terminate a statement in PHP?', 'Semicolon (;)', 'Colon (:)', 'Period (.)', 'Comma (,)', 'A', 4);

-- Sample Quiz 3: Mathematics
INSERT INTO quizzes (title, description) VALUES 
('Basic Mathematics', 'Test your basic mathematical skills with these simple problems.');

SET @quiz3_id = LAST_INSERT_ID();

INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
(@quiz3_id, 'What is 2 + 2?', '3', '4', '5', '6', 'B', 1),
(@quiz3_id, 'What is 10 × 5?', '15', '50', '55', '45', 'B', 2),
(@quiz3_id, 'What is the square root of 16?', '2', '3', '4', '8', 'C', 3),
(@quiz3_id, 'What is 100 ÷ 4?', '20', '25', '30', '35', 'B', 4);

-- Create indexes for better performance
CREATE INDEX idx_quizzes_status ON quizzes(status);
CREATE INDEX idx_questions_quiz_order ON questions(quiz_id, order_num);
CREATE INDEX idx_results_quiz_score ON results(quiz_id, score);

-- Show table structure
DESCRIBE quizzes;
DESCRIBE questions;
DESCRIBE results;

-- Show sample data count
SELECT 'Quizzes' as table_name, COUNT(*) as record_count FROM quizzes
UNION ALL
SELECT 'Questions' as table_name, COUNT(*) as record_count FROM questions
UNION ALL
SELECT 'Results' as table_name, COUNT(*) as record_count FROM results;
