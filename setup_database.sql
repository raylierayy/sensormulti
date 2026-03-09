-- ============================================================
-- sensor_db: Student Driver Sensor Test System
-- Database: Microsoft SQL Server (DESKTOP-P17AQJI\SQLEXPRESS)
-- ============================================================

-- 1. Create the Database
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'sensor_db')
BEGIN
    CREATE DATABASE sensor_db;
END
GO

USE sensor_db;
GO

-- ============================================================
-- 2. Users Table (for login / authentication)
-- ============================================================
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'users')
BEGIN
    CREATE TABLE users (
        id       INT IDENTITY(1,1) PRIMARY KEY,
        username VARCHAR(50)  NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    );
END
GO

-- Seed default users
IF NOT EXISTS (SELECT * FROM users WHERE username = 'admin')
    INSERT INTO users (username, password) VALUES ('admin', 'admin123');
IF NOT EXISTS (SELECT * FROM users WHERE username = 'instructor')
    INSERT INTO users (username, password) VALUES ('instructor', 'pass123');
IF NOT EXISTS (SELECT * FROM users WHERE username = 'testuser')
    INSERT INTO users (username, password) VALUES ('testuser', 'test123');
GO

-- ============================================================
-- 3. DROP existing tables in reverse order of dependencies (Child first, then Parent)
-- ============================================================

IF OBJECT_ID('dbo.Sensors', 'U') IS NOT NULL
    DROP TABLE dbo.Sensors;
GO

IF OBJECT_ID('dbo.Students', 'U') IS NOT NULL
    DROP TABLE dbo.Students;
GO

-- ============================================================
-- 4. Students Table
-- Matches the provided image: ID, lastname, firstname, datetime_started, 
-- datetime_finished, instructor, pass_fail (or pass/fail), remarks, tests_count
-- ============================================================
CREATE TABLE Students (
    ID INT IDENTITY(1,1) PRIMARY KEY,
    lastname VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    datetime_started DATETIME DEFAULT GETDATE(),
    datetime_finished DATETIME NULL,
    instructor VARCHAR(255) NULL,
    pass_fail VARCHAR(50) NULL,      -- "Pass", "Fail", or NULL (Pending)
    remarks VARCHAR(MAX) NULL,
    tests_count INT DEFAULT 0
);
GO

-- ============================================================
-- 5. Sensors Table (Logs individual test readings)
-- Matches image exactly: ID, datetime_started, datetime_finished, assigned_side,
-- calibration_distance, allowed_distance_error, car_distance_from_line,
-- final_distance_error_raw, final_distance_error_percentage, final_computed_accuracy, studentID
-- ============================================================


CREATE TABLE Sensors (
    ID INT IDENTITY(1,1) PRIMARY KEY,
    datetime_started DATETIME DEFAULT GETDATE(),
    datetime_finished DATETIME NULL,
    assigned_side VARCHAR(50),        -- e.g., 'L', 'F', 'R'
    calibration_distance FLOAT,       -- in cm
    allowed_distance_error FLOAT,     -- in cm
    car_distance_from_line FLOAT,     -- raw sensor reading at stop (cm)
    final_distance_error_raw FLOAT,   -- abs(car - calib) in cm
    final_distance_error_percentage FLOAT, -- (error_raw / calib) * 100
    final_computed_accuracy FLOAT,    -- 100 - error_percentage
    studentID INT,                    -- foreign key

    CONSTRAINT FK_Sensors_Student
        FOREIGN KEY (studentID) REFERENCES Students(ID)
        ON DELETE CASCADE
);
GO

-- Drop old deprecated tables if they exist
IF OBJECT_ID('dbo.drivedata', 'U') IS NOT NULL DROP TABLE dbo.drivedata;
IF OBJECT_ID('dbo.drivers', 'U') IS NOT NULL DROP TABLE dbo.drivers;
GO
