<?php
// Database configuration using SQLite for simplicity
define('DB_PATH', __DIR__ . '/../quiz_system.db');

// Create database connection
try {
    $pdo = new PDO("sqlite:" . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quizzes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            time_limit INTEGER NULL,
            status VARCHAR(20) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            quiz_id INTEGER NOT NULL,
            question TEXT NOT NULL,
            option_a VARCHAR(500) NOT NULL,
            option_b VARCHAR(500) NOT NULL,
            option_c VARCHAR(500) NOT NULL,
            option_d VARCHAR(500) NOT NULL,
            correct_answer VARCHAR(1) NOT NULL,
            order_num INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS results (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            quiz_id INTEGER NOT NULL,
            student_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            score INTEGER NOT NULL,
            total_questions INTEGER NOT NULL,
            percentage DECIMAL(5,2) NOT NULL,
            answers TEXT NOT NULL,
            completed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            reset_token VARCHAR(255) NULL,
            reset_token_expires DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Check if sample data exists
    $count = $pdo->query("SELECT COUNT(*) as count FROM quizzes")->fetch();
    if ($count['count'] == 0) {
        // Insert sample quizzes
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('General Knowledge Quiz', 'Test your general knowledge with this basic quiz covering various topics.', 10)");
        $quiz1_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz1_id, 'What is the capital of France?', 'London', 'Berlin', 'Paris', 'Madrid', 'C', 1),
            ($quiz1_id, 'Which planet is known as the Red Planet?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'B', 2),
            ($quiz1_id, 'Who painted the Mona Lisa?', 'Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo', 'C', 3),
            ($quiz1_id, 'What is the largest ocean on Earth?', 'Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean', 'Pacific Ocean', 'D', 4),
            ($quiz1_id, 'Which element has the chemical symbol \"O\"?', 'Gold', 'Oxygen', 'Silver', 'Iron', 'B', 5)");
        
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('Programming Basics', 'Test your knowledge of basic programming concepts and terminology.', 15)");
        $quiz2_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz2_id, 'What does HTML stand for?', 'High Tech Modern Language', 'HyperText Markup Language', 'Home Tool Markup Language', 'Hyperlink and Text Markup Language', 'B', 1),
            ($quiz2_id, 'Which of the following is NOT a programming language?', 'Python', 'Java', 'HTML', 'C++', 'C', 2),
            ($quiz2_id, 'What is the correct way to create a comment in PHP?', '# This is a comment', '// This is a comment', '/* This is a comment */', 'All of the above', 'D', 3),
            ($quiz2_id, 'Which symbol is used to terminate a statement in PHP?', 'Semicolon (;)', 'Colon (:)', 'Period (.)', 'Comma (,)', 'A', 4)");
        
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('Basic Mathematics', 'Test your basic mathematical skills with these simple problems.', 12)");
        $quiz3_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz3_id, 'What is 2 + 2?', '3', '4', '5', '6', 'B', 1),
            ($quiz3_id, 'What is 10 × 5?', '15', '50', '55', '45', 'B', 2),
            ($quiz3_id, 'What is the square root of 16?', '2', '3', '4', '8', 'C', 3),
            ($quiz3_id, 'What is 100 ÷ 4?', '20', '25', '30', '35', 'B', 4),
            ($quiz3_id, 'What is 7 × 8?', '54', '56', '58', '60', 'B', 5),
            ($quiz3_id, 'What is 144 ÷ 12?', '11', '12', '13', '14', 'B', 6),
            ($quiz3_id, 'What is 5²?', '10', '15', '20', '25', 'D', 7),
            ($quiz3_id, 'What is 3 × 15?', '35', '40', '45', '50', 'C', 8),
            ($quiz3_id, 'What is 81 ÷ 9?', '8', '9', '10', '11', 'B', 9),
            ($quiz3_id, 'What is 6 + 7 × 2?', '20', '26', '19', '17', 'A', 10)");

        // Science Quiz
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('Basic Science', 'Test your knowledge of basic science concepts from physics, chemistry, and biology.', 15)");
        $quiz4_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz4_id, 'What is the chemical symbol for water?', 'H2O', 'CO2', 'NaCl', 'O2', 'A', 1),
            ($quiz4_id, 'How many bones are in an adult human body?', '105', '206', '306', '406', 'B', 2),
            ($quiz4_id, 'What is the speed of light in vacuum?', '300,000 km/s', '150,000 km/s', '450,000 km/s', '600,000 km/s', 'A', 3),
            ($quiz4_id, 'Which planet is closest to the Sun?', 'Venus', 'Earth', 'Mercury', 'Mars', 'C', 4),
            ($quiz4_id, 'What gas do plants absorb from the atmosphere?', 'Oxygen', 'Carbon Dioxide', 'Nitrogen', 'Hydrogen', 'B', 5),
            ($quiz4_id, 'What is the hardest natural substance?', 'Gold', 'Iron', 'Diamond', 'Platinum', 'C', 6),
            ($quiz4_id, 'How many chambers does a human heart have?', '2', '3', '4', '5', 'C', 7),
            ($quiz4_id, 'What is the chemical symbol for gold?', 'Go', 'Gd', 'Au', 'Ag', 'C', 8),
            ($quiz4_id, 'Which blood type is known as the universal donor?', 'A', 'B', 'AB', 'O', 'D', 9),
            ($quiz4_id, 'What is the smallest unit of matter?', 'Molecule', 'Atom', 'Cell', 'Particle', 'B', 10)");

        // History Quiz
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('World History', 'Test your knowledge of important historical events and figures.', 18)");
        $quiz5_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz5_id, 'In which year did World War II end?', '1944', '1945', '1946', '1947', 'B', 1),
            ($quiz5_id, 'Who was the first person to walk on the moon?', 'Buzz Aldrin', 'Neil Armstrong', 'John Glenn', 'Alan Shepard', 'B', 2),
            ($quiz5_id, 'Which ancient wonder of the world was located in Alexandria?', 'Hanging Gardens', 'Lighthouse', 'Colossus', 'Mausoleum', 'B', 3),
            ($quiz5_id, 'In which year did the Berlin Wall fall?', '1987', '1988', '1989', '1990', 'C', 4),
            ($quiz5_id, 'Who painted the ceiling of the Sistine Chapel?', 'Leonardo da Vinci', 'Raphael', 'Michelangelo', 'Donatello', 'C', 5),
            ($quiz5_id, 'Which empire was ruled by Julius Caesar?', 'Greek Empire', 'Roman Empire', 'Ottoman Empire', 'Byzantine Empire', 'B', 6),
            ($quiz5_id, 'In which year did the Titanic sink?', '1910', '1911', '1912', '1913', 'C', 7),
            ($quiz5_id, 'Who was the first President of the United States?', 'Thomas Jefferson', 'John Adams', 'Benjamin Franklin', 'George Washington', 'D', 8),
            ($quiz5_id, 'Which country gifted the Statue of Liberty to the United States?', 'England', 'France', 'Spain', 'Italy', 'B', 9),
            ($quiz5_id, 'In which city was President John F. Kennedy assassinated?', 'Dallas', 'Houston', 'Austin', 'San Antonio', 'A', 10)");

        // Geography Quiz  
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('World Geography', 'Test your knowledge of countries, capitals, and geographical features.', 15)");
        $quiz6_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz6_id, 'What is the capital of Australia?', 'Sydney', 'Melbourne', 'Canberra', 'Perth', 'C', 1),
            ($quiz6_id, 'Which is the longest river in the world?', 'Amazon', 'Nile', 'Mississippi', 'Yangtze', 'B', 2),
            ($quiz6_id, 'Which country has the most time zones?', 'Russia', 'United States', 'China', 'Canada', 'A', 3),
            ($quiz6_id, 'What is the smallest country in the world?', 'Monaco', 'Vatican City', 'San Marino', 'Liechtenstein', 'B', 4),
            ($quiz6_id, 'Which mountain range contains Mount Everest?', 'Andes', 'Alps', 'Himalayas', 'Rockies', 'C', 5),
            ($quiz6_id, 'What is the capital of Canada?', 'Toronto', 'Vancouver', 'Montreal', 'Ottawa', 'D', 6),
            ($quiz6_id, 'Which desert is the largest in the world?', 'Sahara', 'Gobi', 'Antarctica', 'Arabian', 'C', 7),
            ($quiz6_id, 'What is the deepest ocean trench?', 'Puerto Rico Trench', 'Java Trench', 'Mariana Trench', 'Peru-Chile Trench', 'C', 8),
            ($quiz6_id, 'Which African country is completely surrounded by South Africa?', 'Swaziland', 'Lesotho', 'Botswana', 'Namibia', 'B', 9),
            ($quiz6_id, 'What is the capital of Brazil?', 'Rio de Janeiro', 'São Paulo', 'Brasília', 'Salvador', 'C', 10)");

        // Technology Quiz
        $pdo->exec("INSERT INTO quizzes (title, description, time_limit) VALUES 
            ('Technology & Computers', 'Test your knowledge of modern technology and computer science.', 20)");
        $quiz7_id = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, order_num) VALUES
            ($quiz7_id, 'What does CPU stand for?', 'Computer Processing Unit', 'Central Processing Unit', 'Core Processing Unit', 'Central Program Unit', 'B', 1),
            ($quiz7_id, 'Who founded Microsoft?', 'Steve Jobs', 'Bill Gates', 'Mark Zuckerberg', 'Larry Page', 'B', 2),
            ($quiz7_id, 'What does WWW stand for?', 'World Wide Web', 'World Wide Wire', 'World Web Wide', 'Wide World Web', 'A', 3),
            ($quiz7_id, 'Which company developed the Android operating system?', 'Apple', 'Microsoft', 'Google', 'Samsung', 'C', 4),
            ($quiz7_id, 'What is the most popular programming language in 2023?', 'Java', 'Python', 'JavaScript', 'C++', 'B', 5),
            ($quiz7_id, 'What does AI stand for?', 'Automated Intelligence', 'Artificial Intelligence', 'Advanced Intelligence', 'Applied Intelligence', 'B', 6),
            ($quiz7_id, 'Which social media platform was founded by Mark Zuckerberg?', 'Twitter', 'Instagram', 'Facebook', 'LinkedIn', 'C', 7),
            ($quiz7_id, 'What does RAM stand for?', 'Random Access Memory', 'Rapid Access Memory', 'Read Access Memory', 'Remote Access Memory', 'A', 8),
            ($quiz7_id, 'Which company created the iPhone?', 'Samsung', 'Google', 'Apple', 'Microsoft', 'C', 9),
            ($quiz7_id, 'What is the binary equivalent of decimal 10?', '1010', '1100', '1001', '1110', 'A', 10)");
        
        // Create demo user account
        $hashedPassword = password_hash('demo123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password, full_name, created_at) VALUES 
            ('demo', 'demo@example.com', '$hashedPassword', 'Người dùng Demo', datetime('now'))");
    }
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
