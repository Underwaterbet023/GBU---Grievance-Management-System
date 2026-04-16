-- Create database and use it
CREATE DATABASE IF NOT EXISTS gbu_grievance;
USE gbu_grievance;

-- 1. Users table (Admins & Students combined with role)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(50) UNIQUE DEFAULT NULL, -- For students
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Main Admin', 'Department Admin', 'College Admin', 'University Admin') DEFAULT 'Student',
    department VARCHAR(100) DEFAULT NULL, -- E.g. 'Engineering', 'Biotechnology'
    hostel VARCHAR(100) DEFAULT NULL,     -- E.g. 'Birsa Munda'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Grievances table
CREATE TABLE IF NOT EXISTS grievances (
    id VARCHAR(20) PRIMARY KEY, -- Custom ID like GBU-2024-001
    user_id INT NOT NULL,
    category VARCHAR(100) NOT NULL, -- Academic, Hostel, Finance
    department VARCHAR(100) DEFAULT NULL,
    hostel VARCHAR(100) DEFAULT NULL,
    priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    file_path VARCHAR(255) DEFAULT NULL, -- Attached image/pdf
    status ENUM('Pending', 'In Progress', 'Resolved', 'Rejected') DEFAULT 'Pending',
    current_level ENUM('Department', 'College', 'University') DEFAULT 'Department',
    urgent_flag TINYINT(1) DEFAULT 0,
    ai_summary TEXT DEFAULT NULL, -- AI generated summary
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Responses/Remarks table for a grievance
CREATE TABLE IF NOT EXISTS responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grievance_id VARCHAR(20) NOT NULL,
    admin_id INT NOT NULL,
    remark TEXT NOT NULL,
    status_change ENUM('Pending', 'In Progress', 'Resolved', 'Rejected') DEFAULT 'In Progress',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grievance_id) REFERENCES grievances(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Escalation Logs
CREATE TABLE IF NOT EXISTS escalation_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grievance_id VARCHAR(20) NOT NULL,
    escalated_from ENUM('Department', 'College') NOT NULL,
    escalated_to ENUM('College', 'University') NOT NULL,
    escalated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grievance_id) REFERENCES grievances(id) ON DELETE CASCADE
);

-- Insert Dummy Users
-- Student: password is 'password123' -> $2y$10$wT/pItv7wPZ68sD/q56f6u4C/n5gqB9Y92ASt4n4fX9gS9F1r9yE6
INSERT INTO users (roll_number, name, email, password_hash, role, department, hostel) VALUES 
('21CSU001', 'John Doe', 'john@gbu.ac.in', '$2y$10$wT/pItv7wPZ68sD/q56f6u4C/n5gqB9Y92ASt4n4fX9gS9F1r9yE6', 'Student', 'Engineering', 'Birsa Munda'),
(NULL, 'HOD Engineering', 'hod.eng@gbu.ac.in', '$2y$10$wT/pItv7wPZ68sD/q56f6u4C/n5gqB9Y92ASt4n4fX9gS9F1r9yE6', 'Department Admin', 'Engineering', NULL),
(NULL, 'Dean Engineering', 'dean.eng@gbu.ac.in', '$2y$10$wT/pItv7wPZ68sD/q56f6u4C/n5gqB9Y92ASt4n4fX9gS9F1r9yE6', 'College Admin', 'Engineering', NULL),
(NULL, 'Registrar', 'registrar@gbu.ac.in', '$2y$10$wT/pItv7wPZ68sD/q56f6u4C/n5gqB9Y92ASt4n4fX9gS9F1r9yE6', 'University Admin', NULL, NULL),
(NULL, 'Main Admin', 'admin@gbu.ac.in', '$2y$10$wT/pItv7wPZ68sD/q56f6u4C/n5gqB9Y92ASt4n4fX9gS9F1r9yE6', 'Main Admin', NULL, NULL);

-- Insert Dummy Grievance
INSERT INTO grievances (id, user_id, category, department, priority, subject, description, status) VALUES 
('GBU-2024-001', 1, 'Academic', 'Engineering', 'High', 'Issue with Lab Equipment', 'The computers in the AI lab are not working properly. Some systems are not turning on.', 'Pending');
