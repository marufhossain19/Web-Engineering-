-- ============================================
-- Weby Platform - Complete Database Schema
-- ============================================
-- This SQL file contains all tables and columns
-- for a fresh installation of Weby platform
-- ============================================

CREATE DATABASE IF NOT EXISTS weby_db;
USE weby_db;

-- ============================================
-- Users Table
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT 'default.png',
    
    -- Profile Information (Added for profile edit feature)
    batch VARCHAR(10) DEFAULT NULL,
    section VARCHAR(10) DEFAULT NULL,
    student_id VARCHAR(50) DEFAULT NULL,
    
    -- Social Links (Added for profile edit feature)
    github_url VARCHAR(255) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    gmail VARCHAR(100) DEFAULT NULL,
    
    -- Privacy Settings (Added for profile privacy feature)
    show_student_id TINYINT(1) DEFAULT 1,
    show_github TINYINT(1) DEFAULT 1,
    show_linkedin TINYINT(1) DEFAULT 1,
    show_email TINYINT(1) DEFAULT 1,
    show_gmail TINYINT(1) DEFAULT 1,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Notes Table
-- ============================================
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    course_code VARCHAR(50) NOT NULL,
    teacher_name VARCHAR(100) DEFAULT NULL,
    semester VARCHAR(10) NOT NULL,
    year INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    
    -- Privacy Setting (Added for public/private notes)
    is_public TINYINT(1) DEFAULT 1,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_semester_year (semester, year),
    INDEX idx_course_code (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Questions Table
-- ============================================
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    course_code VARCHAR(50) NOT NULL,
    teacher_name VARCHAR(100) DEFAULT NULL,
    exam_type ENUM('mid', 'final', 'quiz') NOT NULL,
    semester VARCHAR(10) NOT NULL,
    year INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    
    -- Privacy Setting (Added for public/private questions)
    is_public TINYINT(1) DEFAULT 1,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_exam_type (exam_type),
    INDEX idx_semester_year (semester, year),
    INDEX idx_course_code (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Likes Table
-- ============================================
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource_id INT NOT NULL,
    resource_type ENUM('note', 'question') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (user_id, resource_id, resource_type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_resource (resource_id, resource_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Bookmarks Table
-- ============================================
CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource_id INT NOT NULL,
    resource_type ENUM('note', 'question') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (user_id, resource_id, resource_type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_resource (resource_id, resource_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Downloads Table (Added for download tracking)
-- ============================================
CREATE TABLE IF NOT EXISTS downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource_id INT NOT NULL,
    resource_type ENUM('note', 'question') NOT NULL,
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_download (user_id, resource_id, resource_type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_resource (resource_id, resource_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Sample Data (Optional - for testing)
-- ============================================

-- Sample User (Password: password123)
INSERT INTO users (name, email, password, department) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science');

-- Sample Notes
INSERT INTO notes (user_id, title, course_code, teacher_name, semester, year, file_path) VALUES
(1, 'Introduction to Programming', 'CSE101', 'Dr. Smith', '1.1', 2024, 'uploads/notes/sample.pdf'),
(1, 'Data Structures Basics', 'CSE201', 'Prof. Johnson', '2.1', 2024, 'uploads/notes/sample2.pdf');

-- Sample Questions
INSERT INTO questions (user_id, title, course_code, teacher_name, exam_type, semester, year, file_path) VALUES
(1, 'Programming Midterm 2024', 'CSE101', 'Dr. Smith', 'mid', '1.1', 2024, 'uploads/questions/sample.pdf'),
(1, 'Data Structures Final 2024', 'CSE201', 'Prof. Johnson', 'final', '2.1', 2024, 'uploads/questions/sample2.pdf');

-- ============================================
-- Database Schema Information
-- ============================================
-- Total Tables: 6
-- 1. users - User accounts with profile and privacy settings
-- 2. notes - Study notes with privacy controls
-- 3. questions - Previous exam questions with privacy controls
-- 4. likes - User likes/favorites for notes and questions
-- 5. bookmarks - User bookmarks for notes and questions
-- 6. downloads - Download tracking (unique per user)
--
-- Features Supported:
-- ✅ User authentication
-- ✅ Profile management with social links
-- ✅ Privacy controls for profile fields
-- ✅ Notes and questions upload
-- ✅ Public/private content
-- ✅ Like system
-- ✅ Bookmark system
-- ✅ Download tracking (unique per user)
-- ✅ Contribution badges (calculated from notes + questions count)
-- ============================================
