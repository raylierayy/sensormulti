-- ============================================================
-- multi_sensor_db: Student Driver Multi-Sensor Test System
-- Database: Microsoft SQL Server (DESKTOP-P17AQJI\SQLEXPRESS)
-- ============================================================

-- 1. Create the Database
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'multi_sensor_db')
BEGIN
    CREATE DATABASE multi_sensor_db;
END
GO

USE multi_sensor_db;
GO

-- ============================================================
-- 2. DROP existing tables in reverse order of dependencies
-- ============================================================

IF OBJECT_ID('dbo.Sensors', 'U') IS NOT NULL DROP TABLE dbo.Sensors;
GO
IF OBJECT_ID('dbo.Sessions', 'U') IS NOT NULL DROP TABLE dbo.Sessions;
GO
IF OBJECT_ID('dbo.Students', 'U') IS NOT NULL DROP TABLE dbo.Students;
GO
IF OBJECT_ID('dbo.CalibrationPresets', 'U') IS NOT NULL DROP TABLE dbo.CalibrationPresets;
GO
IF OBJECT_ID('dbo.users', 'U') IS NOT NULL DROP TABLE dbo.users;
GO


-- ============================================================
-- 3. Users Table (for login / authentication)
-- ============================================================
CREATE TABLE users (
    id       INT IDENTITY(1,1) PRIMARY KEY,
    username VARCHAR(50)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
GO

-- Seed default users
INSERT INTO users (username, password) VALUES ('admin', 'admin123');
INSERT INTO users (username, password) VALUES ('instructor', 'pass123');
INSERT INTO users (username, password) VALUES ('testuser', 'test123');
GO

-- ============================================================
-- 4. Students Table
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
-- 5. Sessions Table (Groups multiple tests together)
-- ============================================================
CREATE TABLE Sessions (
    ID INT IDENTITY(1,1) PRIMARY KEY,
    studentID INT NOT NULL,
    datetime_started DATETIME DEFAULT GETDATE(),
    datetime_finished DATETIME NULL,
    avg_distance_error_raw FLOAT NULL,
    avg_distance_error_percentage FLOAT NULL,
    avg_computed_accuracy FLOAT NULL,
    session_status VARCHAR(50) DEFAULT 'Ongoing', -- 'Ongoing' or 'Completed'

    CONSTRAINT FK_Sessions_Student
        FOREIGN KEY (studentID) REFERENCES Students(ID)
        ON DELETE CASCADE
);
GO

-- ============================================================
-- 6. Sensors Table (Logs individual test readings inside a session)
-- ============================================================
CREATE TABLE Sensors (
    ID INT IDENTITY(1,1) PRIMARY KEY,
    sessionID INT NOT NULL,
    studentID INT NOT NULL,           -- Kept for legacy/redundancy if needed, or join through Sessions
    datetime_started DATETIME DEFAULT GETDATE(),
    datetime_finished DATETIME NULL,
    
    -- MAIN Sensor
    assigned_side_1 VARCHAR(50),
    calibration_distance_1 FLOAT,
    allowed_distance_error_1 FLOAT,
    car_distance_from_line_1 FLOAT,     
    final_distance_error_raw_1 FLOAT,   
    final_distance_error_percentage_1 FLOAT, 
    final_computed_accuracy_1 FLOAT,    

    -- SENSOR 2
    assigned_side_2 VARCHAR(50),
    calibration_distance_2 FLOAT,
    allowed_distance_error_2 FLOAT,
    car_distance_from_line_2 FLOAT,     
    final_distance_error_raw_2 FLOAT,   
    final_distance_error_percentage_2 FLOAT, 
    final_computed_accuracy_2 FLOAT,   

    -- SENSOR 3
    assigned_side_3 VARCHAR(50),
    calibration_distance_3 FLOAT,
    allowed_distance_error_3 FLOAT,
    car_distance_from_line_3 FLOAT,     
    final_distance_error_raw_3 FLOAT,   
    final_distance_error_percentage_3 FLOAT, 
    final_computed_accuracy_3 FLOAT,

    CONSTRAINT FK_Sensors_Session
        FOREIGN KEY (sessionID) REFERENCES Sessions(ID)
        ON DELETE CASCADE,
    CONSTRAINT FK_Sensors_Student_Direct
        FOREIGN KEY (studentID) REFERENCES Students(ID)
        -- No cascade here since Sessions handles it, or just rely on session cascade
);
GO

-- ============================================================
-- 7. CalibrationPresets Table (Saves instructor's baselines)
-- ============================================================
CREATE TABLE CalibrationPresets (
    ID INT IDENTITY(1,1) PRIMARY KEY,
    assigned_side VARCHAR(50),        
    calibration_distance FLOAT,       
    allowed_distance_error FLOAT,     
    created_at DATETIME DEFAULT GETDATE()
);
GO
