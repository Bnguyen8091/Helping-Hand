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

-- Dumping data for table `TicketComments`

INSERT INTO `TicketComments` VALUES 
(1,2,'The thing does not work.','2025-03-26 17:02:31'),
(2,1,'Did you reboot it?','2025-03-26 17:04:09'),
(2,1,'Glad that worked.  Closing Ticket.','2025-03-26 17:04:52'),
(2,1,'test','2025-04-03 16:02:14'),
(2,4,'I can\'t figure out how to make this work.','2025-03-26 17:03:41'),
(2,4,'Thanks, That worked!!','2025-03-26 17:04:28'),
(2,4,'hello','2025-04-02 16:31:06');

-- --------------------------------------------------------
-- Table structure for table `TicketUsers`
-- --------------------------------------------------------

CREATE TABLE `TicketUsers` (
  `TicketID` int NOT NULL,
  `UserID` int NOT NULL,
  PRIMARY KEY (`TicketID`,`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `TicketUsers`

INSERT INTO `TicketUsers` VALUES 
(1,2),(2,4),(2,1);

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

-- Dumping data for table `Tickets`

INSERT INTO Tickets (created_by, subject, description, priority)
VALUES (2, 'Printer not working', 'My office printer makes grinding noises and wont print', 'High');

INSERT INTO helping_hand.tickets (created_by, assigned_to, subject, description, priority)
VALUES (4, 1, 'Email config issue', 'Client is having trouble setting up email filters.', 'High');

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
(1,'Jordan','Smith','Support','jsmith@example.com','4789c4be19736e6dd5f834a2ff34dd8bf8b398ccfb6a3fc5b06dc4a997916c85'),
(2,'Hunter','Jones','Client','hjones@example.com','fae36668fea6fa614cf300dbb58367261ef0385ee9e201eb725b0c9bbadc51b2'),
(3,'Ashley','Brown','Manage','abrown@example.com','8ee4f8483a5afec004633a476fa1ec620b4fbca477634a92a47f8388c1af4bdf'),
(4,'Sam','Hill','Client','shill@example.com','4a2d42a0b261f9aaec967e6dd94b241565d7a486b64a1e69abfa1fccff66569a');

-- End of dump
