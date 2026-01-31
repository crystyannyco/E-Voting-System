-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 07:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-votingsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `AdminID` int(11) NOT NULL,
  `AdminName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Sex` tinyint(1) DEFAULT NULL,
  `Birthdate` date DEFAULT NULL,
  `Password` varchar(100) NOT NULL,
  `Profile` blob NOT NULL,
  `Role` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`AdminID`, `AdminName`, `Email`, `PhoneNumber`, `Username`, `Sex`, `Birthdate`, `Password`, `Profile`, `Role`, `CreatedAt`) VALUES
(1, 'ADMIN', 'admin@admin.com', '09123456789', 'admin1234', 0, '2025-05-17', 'Admin1234', 0x313734373435383839375f33306434346435323964323238383331376663332e6a7067, 1, '2025-05-16 21:14:28');

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `CandidateID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Position` int(11) NOT NULL,
  `Partylist` int(11) NOT NULL,
  `Election` int(11) NOT NULL,
  `Profile` blob DEFAULT NULL,
  `Platform` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `candidate`
--

INSERT INTO `candidate` (`CandidateID`, `FirstName`, `MiddleName`, `LastName`, `Position`, `Partylist`, `Election`, `Profile`, `Platform`) VALUES
(1, 'John', 'Andrew', 'Smith', 1, 1, 1, 0x313734373830383235365f36356232616461383063376463346135326339662e6a7067, 'Improve campus facilities and promote student welfare'),
(2, 'Maria', 'Elena', 'Garcia', 2, 1, 1, 0x313734373830383836375f65653464333733326336386638333831336263662e77656270, 'Enhance academic support systems and expand library resources'),
(3, 'David', 'Lee', 'Johnson', 3, 1, 1, 0x313734373830393235325f35306436343836353531313261666339386539342e706e67, 'Transparent administration and regular communication with students'),
(4, 'Sarah', 'Jane', 'Williams', 4, 2, 1, 0x313734373830383839355f61656462643934386332356639626132393735362e6a7067, 'Responsible budget management and financial transparency'),
(5, 'Michael', 'Thomas', 'Brown', 5, 2, 1, 0x313734373830383930355f66663464326563343536613863633761333236362e6a7067, 'Ensure proper allocation of student funds'),
(6, 'Jennifer', 'Rose', 'Davis', 6, 2, 1, 0x313734373830383931365f38353637653730343836316531346334646136302e6a7067, 'Strengthen community relations and student engagement'),
(7, 'Robert', 'Carlos', 'Martinez', 7, 3, 1, 0x313734373830383933325f32323835656335623563393163376364643734312e6a7067, 'Advocate for student rights and academic freedom'),
(8, 'Lisa', 'Marie', 'Rodriguez', 1, 3, 2, 0x313734373830383935325f30643035376666363037343432326166373638332e6a7067, 'Promote technology integration in computer studies'),
(9, 'James', 'Edward', 'Wilson', 2, 3, 2, 0x313734373830383936345f61663563316535656166656632656332346564642e706e67, 'Create more programming competitions and tech events'),
(10, 'Patricia', 'Lynn', 'Anderson', 3, 4, 2, 0x313734373830383937375f32386563393261663130646432383062386430642e6a7067, 'Improve documentation and communication in IT department'),
(11, 'Christopher', 'John', 'Taylor', 4, 4, 3, 0x313734373830383938365f39623032656536663764356634653333303739392e77656270, 'Financial literacy seminars for business students'),
(12, 'Michelle', 'Ann', 'Thomas', 5, 4, 3, NULL, 'Establish connections with industry partners'),
(13, 'Daniel', 'Miguel', 'Hernandez', 6, 5, 3, 0x313734373830393033315f35383832393331383632343536626433613237392e6a7067, 'Organize networking events with business professionals'),
(14, 'Elizabeth', 'Grace', 'Moore', 7, 5, 4, 0x313734373830393032315f36366463393564646333316437346532633963392e6a7067, 'Support research initiatives and scientific conferences'),
(15, 'Matthew', 'Joseph', 'Martin', 1, 5, 5, 0x313734373830393236315f35326636636635306633353564663339363438372e706e67, 'Promote liberal arts through cultural events and exhibitions');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepartmentID`, `DepartmentName`) VALUES
(1, 'College of Engineering'),
(2, 'College of Computer Studies'),
(3, 'College of Business'),
(4, 'College of Science'),
(5, 'College of Liberal Arts');

-- --------------------------------------------------------

--
-- Table structure for table `election`
--

CREATE TABLE `election` (
  `ElectionID` int(11) NOT NULL,
  `ElectionName` varchar(100) NOT NULL,
  `Start` datetime NOT NULL,
  `End` datetime NOT NULL,
  `Department` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `election`
--

INSERT INTO `election` (`ElectionID`, `ElectionName`, `Start`, `End`, `Department`) VALUES
(1, 'Student Council Election 2025', '2025-05-02 08:00:00', '2025-06-02 17:00:00', 0),
(2, 'Computer Studies Council Election', '2025-06-03 08:00:00', '2025-06-04 17:00:00', 1),
(3, 'Business Council Election', '2025-06-05 08:00:00', '2025-06-06 17:00:00', 3),
(4, 'Science Council Election', '2025-06-07 08:00:00', '2025-06-08 17:00:00', 4),
(5, 'Liberal Arts Council Election', '2025-06-09 08:00:00', '2025-06-10 17:00:00', 5);

-- --------------------------------------------------------

--
-- Table structure for table `partylist`
--

CREATE TABLE `partylist` (
  `PartylistID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `partylist`
--

INSERT INTO `partylist` (`PartylistID`, `Name`) VALUES
(1, 'United Students Party'),
(2, 'Progressive Alliance'),
(3, 'Student Reform Movement'),
(4, 'Campus Democrats'),
(5, 'Independent Candidates Coalition');

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `PositionID` int(11) NOT NULL,
  `PositionName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`PositionID`, `PositionName`) VALUES
(1, 'President'),
(2, 'Vice President'),
(3, 'Secretary'),
(4, 'Treasurer'),
(5, 'Auditor'),
(6, 'Public Relations Officer'),
(7, 'Senator');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `StudentID` int(9) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) NOT NULL,
  `Birthdate` date NOT NULL,
  `Gender` tinyint(1) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `Email` varchar(50) NOT NULL,
  `Department` int(11) NOT NULL,
  `Course` varchar(11) NOT NULL,
  `Year` int(11) NOT NULL,
  `Section` int(11) NOT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `Profile` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentID`, `FirstName`, `LastName`, `MiddleName`, `Birthdate`, `Gender`, `PhoneNumber`, `Email`, `Department`, `Course`, `Year`, `Section`, `Password`, `Profile`) VALUES
(1, 'John', 'Smith', 'Andrew', '2002-05-15', 1, '09123456789', 'john.smith@university.edu', 1, '1', 3, 1, 'fbbb1ce6cb9cc8141384eeca3eb92ddb', 0x64656661756c742e6a7067),
(2, 'Maria', 'Garcia', 'Elena', '2001-09-22', 0, '09234567890', 'maria.garcia@university.edu', 2, '1', 4, 2, '2282a5025041a1ac75fedcf515c84f4a', 0x64656661756c742e6a7067),
(3, 'David', 'Johnson', 'Lee', '2003-02-10', 1, '09345678901', 'david.johnson@university.edu', 3, 'BSBA', 2, 1, '6765e72c455c2ad22a8d46aeb6321684', 0x64656661756c742e6a7067),
(4, 'Sarah', 'Williams', 'Jane', '2002-11-30', 0, '09456789012', 'sarah.williams@university.edu', 4, 'BSMATH', 3, 2, 'c4a02341a81b76d0518aa78f897158ba', 0x64656661756c742e6a7067),
(5, 'Michael', 'Brown', 'Thomas', '2001-07-18', 1, '09567890123', 'michael.brown@university.edu', 5, 'BSPSYCH', 4, 1, '58d0d0c19ac9db6ab48b5a44ffe428ef', 0x64656661756c742e6a7067),
(6, 'Jennifer', 'Davis', 'Rose', '2003-04-05', 0, '09678901234', 'jennifer.davis@university.edu', 1, 'BSME', 2, 2, '952b49573da0ee0bf90e15c3cb47cdc5', 0x64656661756c742e6a7067),
(7, 'Robert', 'Martinez', 'Carlos', '2002-08-14', 1, '09789012345', 'robert.martinez@university.edu', 2, 'BSIT', 3, 1, 'd743e8366c719d36331683d52e6e1734', 0x64656661756c742e6a7067),
(8, 'Lisa', 'Rodriguez', 'Marie', '2001-12-03', 0, '09890123456', 'lisa.rodriguez@university.edu', 3, 'BSHRM', 4, 2, '5e734ebe50ccf6e7d49f1cfa3f875fd9', 0x64656661756c742e6a7067),
(9, 'James', 'Wilson', 'Edward', '2003-01-25', 1, '09901234567', 'james.wilson@university.edu', 4, 'BSBIO', 2, 1, '0703102a139553b5535b52188d4e6729', 0x64656661756c742e6a7067),
(10, 'Patricia', 'Anderson', 'Lynn', '2002-06-17', 0, '09012345678', 'patricia.anderson@university.edu', 5, 'BSPOLSCI', 3, 2, '14cdb0f8a1980b7d2416ead4cd2f6dd4', 0x64656661756c742e6a7067),
(11, 'Christopher', 'Taylor', 'John', '2001-10-09', 1, '09123450987', 'christopher.taylor@university.edu', 1, 'BSCE', 4, 1, 'ca2e80f30c2dabe6c8a918ba2efeb637', 0x64656661756c742e6a7067),
(12, 'Michelle', 'Thomas', 'Ann', '2003-03-27', 0, '09234509876', 'michelle.thomas@university.edu', 2, 'BSCS', 2, 2, 'a17909b94f0074049bb8b2d53728061d', 0x64656661756c742e6a7067),
(13, 'Daniel', 'Hernandez', 'Miguel', '2002-09-08', 1, '09345098765', 'daniel.hernandez@university.edu', 3, 'BSBA', 3, 1, '2a39293c0c995e2e00131930f8c499e5', 0x64656661756c742e6a7067),
(14, 'Elizabeth', 'Moore', 'Grace', '2001-05-21', 0, '09450987654', 'elizabeth.moore@university.edu', 4, 'BSMATH', 4, 2, '3f27b7f570b733e8da0830a2c6fbaf1a', 0x64656661756c742e6a7067),
(15, 'Matthew', 'Martin', 'Joseph', '2003-07-13', 1, '09509876543', 'matthew.martin@university.edu', 5, 'BSPOLSCI', 2, 1, '59eb949389eb7d6fe9588ab5d5d1a851', 0x64656661756c742e6a7067),
(16, 'Laura', 'Jackson', 'Kate', '2002-01-29', 0, '09598765432', 'laura.jackson@university.edu', 1, 'BSME', 3, 2, '21c9b9f57c5e6165645be5661ccface5', 0x64656661756c742e6a7067),
(17, 'Anthony', 'Thompson', 'Paul', '2001-11-16', 1, '09687654321', 'anthony.thompson@university.edu', 2, 'BSIT', 4, 1, 'ef1f0f464ce2ad279ed0bc48ac766d9c', 0x64656661756c742e6a7067),
(18, 'Kimberly', 'White', 'Nicole', '2003-06-04', 0, '09876543210', 'kimberly.white@university.edu', 3, 'BSHRM', 2, 2, 'd74d1b0f139d7615e2bb56b638519e34', 0x64656661756c742e6a7067),
(19, 'Kevin', 'Lopez', 'Ryan', '2002-04-18', 1, '09765432109', 'kevin.lopez@university.edu', 4, 'BSBIO', 3, 1, '16011dc19c14b1af3663813c65d9a481', 0x64656661756c742e6a7067),
(20, 'Jessica', 'Lee', 'Faith', '2001-08-07', 0, '09654321098', 'jessica.lee@university.edu', 5, 'BSPSYCH', 4, 2, 'af02711e153a39e36b69ac1febf4d4a9', 0x64656661756c742e6a7067),
(21, 'Brian', 'Gonzalez', 'Scott', '2003-02-23', 1, '09543210987', 'brian.gonzalez@university.edu', 1, 'BSCE', 2, 1, 'c8422ec5ab38e61ff60c93b09afbb270', 0x64656661756c742e6a7067),
(22, 'Amanda', 'Harris', 'Joy', '2002-10-15', 0, '09432109876', 'amanda.harris@university.edu', 2, 'BSCS', 3, 2, 'ce1d630a3fec6185d48483f83fb2c8ae', 0x64656661756c742e6a7067),
(23, 'Jason', 'Clark', 'Alex', '2001-07-30', 1, '09321098765', 'jason.clark@university.edu', 3, 'BSBA', 4, 1, '18771179bb46356d6d65fdca17f2a6d0', 0x64656661756c742e6a7067),
(24, 'Stephanie', 'Lewis', 'Hope', '2003-05-12', 0, '09210987654', 'stephanie.lewis@university.edu', 4, 'BSMATH', 2, 2, '4a431476723b32b3e3d5e6679ff438ea', 0x64656661756c742e6a7067),
(25, 'Ryan', 'Walker', 'Jack', '2002-03-02', 1, '09109876543', 'ryan.walker@university.edu', 5, 'BSPOLSCI', 3, 1, '645b68123abef26ab2bf20b60352cb1e', 0x64656661756c742e6a7067),
(26, 'Emily', 'Young', 'Claire', '2001-12-19', 0, '09098765432', 'emily.young@university.edu', 1, 'BSME', 4, 2, '516e805178391326180499bf0466e514', 0x64656661756c742e6a7067),
(27, 'Joshua', 'Allen', 'Grant', '2003-09-06', 1, '09876543219', 'joshua.allen@university.edu', 2, 'BSIT', 2, 1, 'dafccc746f494dc4c624acd6f038d6f0', 0x64656661756c742e6a7067),
(28, 'Nicole', 'King', 'Dawn', '2002-06-24', 0, '09765432198', 'nicole.king@university.edu', 3, 'BSHRM', 3, 2, '4b738537e84e5b451acf35ebbf7f7593', 0x64656661756c742e6a7067),
(29, 'Brandon', 'Wright', 'Luke', '2001-04-11', 1, '09654321987', 'brandon.wright@university.edu', 4, 'BSBIO', 4, 1, '168fdc38d32de8b0933370b2514e340a', 0x64656661756c742e6a7067),
(30, 'Brittany', 'Scott', 'Faye', '2003-01-28', 0, '09543219876', 'brittany.scott@university.edu', 5, 'BSPSYCH', 2, 2, '9ead8d6ec419080589f8929ccd8b1b4f', 0x64656661756c742e6a7067),
(31, 'Tyler', 'Green', 'Evan', '2002-11-05', 1, '09432198765', 'tyler.green@university.edu', 1, 'BSCE', 3, 1, '31d938716a91bd3332bd32cba152465e', 0x64656661756c742e6a7067),
(32, 'Rachel', 'Adams', 'Beth', '2001-08-26', 0, '09321987654', 'rachel.adams@university.edu', 2, 'BSCS', 4, 2, '2cca5876beeae45a3454d273f13896f0', 0x64656661756c742e6a7067),
(33, 'Justin', 'Baker', 'Noah', '2003-04-15', 1, '09219876543', 'justin.baker@university.edu', 3, 'BSBA', 2, 1, '94e670fd0b41b9f4de08e5ce700a6c81', 0x64656661756c742e6a7067),
(34, 'Samantha', 'Nelson', 'Lily', '2002-02-07', 0, '09198765432', 'samantha.nelson@university.edu', 4, 'BSMATH', 3, 2, '23c786ad026127a1bdf55219073afb83', 0x64656661756c742e6a7067),
(35, 'Kyle', 'Hill', 'Blake', '2001-10-31', 1, '09018765432', 'kyle.hill@university.edu', 5, 'BSPOLSCI', 4, 1, '064c8443037402268e2efb8ee7e59780', 0x64656661756c742e6a7067),
(36, 'Lauren', 'Rivera', 'May', '2003-07-22', 0, '09123456780', 'lauren.rivera@university.edu', 1, 'BSME', 2, 2, 'c50bc23c6143d7e4f00bdf0086ae8df7', 0x64656661756c742e6a7067),
(37, 'Gregory', 'Campbell', 'Dean', '2002-05-09', 1, '09234567809', 'gregory.campbell@university.edu', 2, 'BSIT', 3, 1, '714c45aaa70ac98bfdd50ab998b50f1c', 0x64656661756c742e6a7067),
(38, 'Megan', 'Mitchell', 'June', '2001-01-14', 0, '09345678090', 'megan.mitchell@university.edu', 3, 'BSHRM', 4, 2, '475324e98ba07929f82f67a42dea5e29', 0x64656661756c742e6a7067),
(39, 'Jose', 'Carter', 'Rico', '2003-11-03', 1, '09456780901', 'jose.carter@university.edu', 4, 'BSBIO', 2, 1, '19fd17e533111ab66d008c6d02c379d8', 0x64656661756c742e6a7067),
(40, 'Tiffany', 'Roberts', 'Gail', '2002-08-17', 0, '09567809012', 'tiffany.roberts@university.edu', 5, 'BSPSYCH', 3, 2, 'ba9a906a052bb57258adb1e9a52407a1', 0x64656661756c742e6a7067),
(41, 'Eric', 'Phillips', 'Ian', '2001-05-29', 1, '09678090123', 'eric.phillips@university.edu', 1, 'BSCE', 4, 1, 'c18958ff85c46f5e4c91fa8a39c750e0', 0x64656661756c742e6a7067),
(42, 'Amber', 'Evans', 'Joy', '2003-02-12', 0, '09780901234', 'amber.evans@university.edu', 2, 'BSCS', 2, 2, 'de3b5bc910dfca5d7b1dc433da2d0a95', 0x64656661756c742e6a7067),
(43, 'Andrew', 'Turner', 'Ray', '2002-12-04', 1, '09890123456', 'andrew.turner@university.edu', 3, 'BSBA', 3, 1, '1804365f4ccf377a4ce7d5a1a9f21ef1', 0x64656661756c742e6a7067),
(44, 'Danielle', 'Torres', 'Kim', '2001-09-16', 0, '09901234560', 'danielle.torres@university.edu', 4, 'BSMATH', 4, 2, 'a73a7b0463c856910876950e759de2d2', 0x64656661756c742e6a7067),
(45, 'Steven', 'Parker', 'Jay', '2003-06-01', 1, '09012345609', 'steven.parker@university.edu', 5, 'BSPOLSCI', 2, 1, '18b9cb674d20a9a739bcf94e3a080415', 0x64656661756c742e6a7067),
(46, 'Heather', 'Collins', 'Sue', '2002-03-22', 0, '09123456098', 'heather.collins@university.edu', 1, 'BSME', 3, 2, '49b1bf598763ffe7cb29d183390821f8', 0x64656661756c742e6a7067),
(47, 'Jeffrey', 'Edwards', 'Roy', '2001-12-10', 1, '09234560987', 'jeffrey.edwards@university.edu', 2, 'BSIT', 4, 1, '6ce7d72c891fed6931df9ce2e1e8cce9', 0x64656661756c742e6a7067),
(48, 'Amy', 'Stewart', 'Jean', '2003-08-30', 0, '09345609876', 'amy.stewart@university.edu', 3, 'BSHRM', 2, 2, '6c4e13d8f4507baf36451ecbedf3fb81', 0x64656661756c742e6a7067),
(49, 'Benjamin', 'Flores', 'Kurt', '2002-04-21', 1, '09456098765', 'benjamin.flores@university.edu', 4, 'BSBIO', 3, 1, 'dcd8f9e252620b53e79218a01f48ffd8', 0x64656661756c742e6a7067),
(50, 'Melissa', 'Morris', 'Vera', '2001-11-12', 0, '09560987654', 'melissa.morris@university.edu', 5, 'BSPSYCH', 4, 2, 'd3f4dfa7629d3c7bc18f7c41b74b84f2', 0x64656661756c742e6a7067),
(51, 'John ', 'Doe', '', '2025-05-01', 0, '09123456789', 'johndoe@gmail.com', 1, '1', 2, 4, '$2y$10$cSv9SX5e1RvT2OzyKKbQOugIjzMb5zGig528CJad5VXp5FaLb8.k2', ''),
(52, 'Christian Nico', 'Luzano', 'Brizuela', '2025-05-01', 1, '09123456789', 'christiannico@gmail.com', 1, '1', 3, 2, '$2y$10$NrOSFdv3cwA2l/0BHGvILeBHQSrv8r/tY.TeKr6jOIDoiH48DlW3O', '');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `VoteID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `ElectionID` int(11) NOT NULL,
  `PositionID` int(11) NOT NULL,
  `CandidateID` int(11) NOT NULL,
  `IsAbstain` tinyint(1) NOT NULL DEFAULT 0,
  `TimeVoted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`VoteID`, `StudentID`, `ElectionID`, `PositionID`, `CandidateID`, `IsAbstain`, `TimeVoted`) VALUES
(1, 1, 1, 1, 1, 0, '2025-06-01 09:15:23'),
(2, 1, 1, 2, 2, 0, '2025-06-01 09:15:23'),
(3, 1, 1, 3, 3, 0, '2025-06-01 09:15:23'),
(4, 2, 2, 1, 8, 0, '2025-06-03 10:30:45'),
(5, 2, 2, 2, 9, 0, '2025-06-03 10:30:45'),
(6, 2, 2, 3, 10, 0, '2025-06-03 10:30:45'),
(7, 3, 3, 4, 11, 0, '2025-06-05 11:45:12'),
(8, 3, 3, 5, 12, 0, '2025-06-05 11:45:12'),
(9, 3, 3, 6, 13, 0, '2025-06-05 11:45:12'),
(10, 4, 4, 7, 14, 0, '2025-06-07 14:20:33'),
(11, 5, 5, 1, 15, 0, '2025-06-09 08:10:56'),
(12, 6, 1, 1, 1, 0, '2025-06-01 15:30:22'),
(13, 6, 1, 2, 2, 0, '2025-06-01 15:30:22'),
(14, 6, 1, 3, 0, 1, '2025-06-01 15:30:22'),
(15, 7, 2, 1, 8, 0, '2025-06-03 09:45:18'),
(16, 7, 2, 2, 9, 0, '2025-06-03 09:45:18'),
(17, 8, 3, 4, 11, 0, '2025-06-05 13:25:47'),
(18, 8, 3, 5, 12, 0, '2025-06-05 13:25:47'),
(19, 9, 4, 7, 14, 0, '2025-06-07 16:50:19'),
(20, 10, 5, 1, 15, 0, '2025-06-09 10:05:38'),
(21, 11, 1, 1, 1, 0, '2025-06-01 11:12:54'),
(22, 12, 2, 1, 8, 0, '2025-06-03 14:33:27'),
(23, 13, 3, 4, 11, 0, '2025-06-05 09:18:42'),
(24, 14, 4, 7, 14, 0, '2025-06-07 13:27:39'),
(25, 15, 5, 1, 15, 0, '2025-06-09 15:44:12'),
(26, 16, 1, 1, 0, 1, '2025-06-01 10:22:31'),
(27, 17, 2, 1, 8, 0, '2025-06-03 11:39:46'),
(28, 18, 3, 4, 11, 0, '2025-06-05 14:55:22'),
(29, 19, 4, 7, 14, 0, '2025-06-07 09:07:11'),
(30, 20, 5, 1, 15, 0, '2025-06-09 12:15:33'),
(31, 52, 1, 1, 1, 0, '2025-05-22 05:40:21'),
(32, 52, 1, 2, 0, 1, '2025-05-22 05:40:21'),
(33, 52, 1, 3, 3, 0, '2025-05-22 05:40:21'),
(34, 52, 1, 4, 0, 1, '2025-05-22 05:40:21'),
(35, 52, 1, 5, 5, 0, '2025-05-22 05:40:21'),
(36, 52, 1, 6, 6, 0, '2025-05-22 05:40:21'),
(37, 52, 1, 7, 7, 0, '2025-05-22 05:40:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`CandidateID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `election`
--
ALTER TABLE `election`
  ADD PRIMARY KEY (`ElectionID`);

--
-- Indexes for table `partylist`
--
ALTER TABLE `partylist`
  ADD PRIMARY KEY (`PartylistID`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`PositionID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`StudentID`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`VoteID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `ElectionID` (`CandidateID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `CandidateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `election`
--
ALTER TABLE `election`
  MODIFY `ElectionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `partylist`
--
ALTER TABLE `partylist`
  MODIFY `PartylistID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `PositionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `StudentID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `VoteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
