-- MySQL dump
-- Database: helping_hand
-- Created: 2025-04-03

DROP DATABASE IF EXISTS `helping_hand`;

-- Create the database
CREATE DATABASE `helping_hand` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Use the database
USE `helping_hand`;

-- --------------------------------------------------------
-- Table structure for table `TicketComments`
-- --------------------------------------------------------

CREATE TABLE `TicketComments` (
  `TicketID` int NOT NULL,
  `UserID` int NOT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `Time` timestamp NOT NULL,
  PRIMARY KEY (`TicketID`,`UserID`,`Time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- --------------------------------------------------------
-- Table structure for table `TicketUsers`
-- --------------------------------------------------------

CREATE TABLE `TicketUsers` (
  `TicketID` int NOT NULL,
  `UserID` int NOT NULL,
  PRIMARY KEY (`TicketID`,`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- --------------------------------------------------------
-- Table structure for table `Tickets`
-- --------------------------------------------------------

CREATE TABLE `Tickets` (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    created_by INT NOT NULL,    
    assigned_to INT DEFAULT NULL,
    subject VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('Low','Medium','High') NOT NULL DEFAULT 'High',
    Status ENUM('Open','Closed') NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FAQ TINYINT(1) NOT NULL DEFAULT 0  -- ✅ Added for FAQ functionality
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add resolved_at column for metrics tracking
ALTER TABLE `Tickets`
ADD `resolved_at` TIMESTAMP NULL DEFAULT NULL;


-- --------------------------------------------------------
-- Table structure for table `Users`
-- --------------------------------------------------------

CREATE TABLE `Users` (
  `ID` int NOT NULL,
  `FirstName` varchar(30) DEFAULT NULL,
  `LastName` varchar(30) DEFAULT NULL,
  `AccessLevel` enum('Client','Support','Manage') DEFAULT NULL,
  `EmailAddress` varchar(50) DEFAULT NULL,
  `Password` char(64) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE Users MODIFY ID INTEGER NOT NULL AUTO_INCREMENT;
  
-- Dumping data for table `Users`

INSERT INTO `Users` VALUES 
(1,'Admin','Admin','Manage','admin@example.com','8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918');

-- End of dump
