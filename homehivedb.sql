-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 04:37 PM
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
-- Database: `homehivedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountsdb`
--

CREATE TABLE `accountsdb` (
  `id` int(11) NOT NULL,
  `Lname` varchar(50) NOT NULL,
  `Fname` varchar(50) NOT NULL,
  `Mname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `Birthdate` date NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `streetAddress` varchar(255) NOT NULL,
  `postal` varchar(10) NOT NULL,
  `UploadedIDType` varchar(50) NOT NULL,
  `UploadIDPhoto` varchar(255) NOT NULL,
  `ProfilePic` varchar(255) DEFAULT NULL,
  `pRole` enum('Tenant') DEFAULT 'Tenant',
  `sRole` enum('None','Admin','PropertyOwner') DEFAULT 'None',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `userID` bigint(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accountsdb`
--

INSERT INTO `accountsdb` (`id`, `Lname`, `Fname`, `Mname`, `email`, `phone`, `Birthdate`, `city`, `state`, `streetAddress`, `postal`, `UploadedIDType`, `UploadIDPhoto`, `ProfilePic`, `pRole`, `sRole`, `created_at`, `userID`, `password`, `latitude`, `longitude`) VALUES
(16, 'Agpaoa', 'Juville', '', 'juvilleagpaoa@gmail.com', '09123456789', '2000-01-01', 'Quezon City', 'Metro Manila', 'Novaliches', '1117', 'Philippine National ID', 'uploads/pagibig.jpeg', NULL, 'Tenant', 'None', '2025-04-10 13:05:11', 1000405347, '$2y$10$B6XyrbTcg6ej3bPl2O87SeIyBvoALdx.FhGXl6X3Bz4a/Grm705/G', 0.00000000, 0.00000000),
(26, '', 'HomeHive', '', 'homehiveofficial2025@gmail.com', '09919157775', '2000-01-01', 'Quezon City', 'Metro Manila', 'Marianito Street, Roxas Circle', '1100', 'Philippine National ID', 'uploads/testid.jpeg', NULL, 'Tenant', 'Admin', '2025-05-15 05:36:27', 1000270702, '$2y$10$Xx66OJrN2Ke0ZBEGYJBRheBKbUlM/x1f/PHQS872r3AIY2ihEoZQC', 14.69234590, 121.03348550),
(27, 'Dela Cruz', 'Juan', '', 'andraezachary9@gmail.com', '09123456789', '2003-06-11', 'Quezon City', 'Metro Manila', 'Marianito Street, Roxas Circle', '1100', 'Philippine National ID', 'uploads/PSA_1.jpg', 'profile_1000845370.jpg', 'Tenant', 'PropertyOwner', '2025-05-15 10:48:10', 1000845370, '$2y$10$YIg6SNuJz0hKxwhzps74eOlrlgerNfECTI0zmsWDngeaKXE158BlO', 14.69236750, 121.03347320),
(28, 'Clara', 'Maria', 'Jobert', 'keizouboaz@gmail.com', '09123456789', '2000-06-11', 'Pasig', 'Metro Manila', '13e A. Mabini St', '1231', 'Philippine National ID', 'uploads/PSA_2.jpg', 'profile_1000720659.jpg', 'Tenant', 'PropertyOwner', '2025-05-15 10:50:55', 1000720659, '$2y$10$TFA9Jiya03JAhEK3vncjEuf9iclhB4Ctkn1S/zA0cKIf6UMYoraMa', 14.56083240, 121.07655930),
(29, 'Tulan', 'Julia', 'Mae', 'xenith0611@gmail.com', '09123456789', '2001-05-01', 'Manila', 'Manila', '547-A Caballeros Street', '1006', 'Philippine National ID', 'uploads/PSA_3.jpg', 'profile_1000323661.jpg', 'Tenant', 'None', '2025-05-15 10:55:12', 1000323661, '$2y$10$NAAkE.eUf5AoYx2nzqnfF.SURnN2F9.FADQrZMYozilejWb/D7vz2', 14.59980770, 120.97065180),
(30, 'Hudlo', 'Zedrick', 'Leo', 'hahakdog012@gmail.com', '09123456789', '1984-09-21', 'Manila', 'Manila', 'Rm.720 Downtown Center Building, 516 Quintin,Paredes St,Binondo', '1006', 'Philippine National ID', 'uploads/PSA_4.jpg', 'profile_1000554533.jpg', 'Tenant', 'PropertyOwner', '2025-05-15 10:57:25', 1000554533, '$2y$10$oNYCr9CNQZj0TTjLSu8KS.ch4lNC58uwCntdJhhj.z3tq7wWPF7ui', 14.59044920, 120.98036210),
(31, 'Soriano', 'Michael', 'Angelo', 'genox0611@gmail.com', '09123456789', '1994-12-25', 'Makati CIty', 'Manila', '154 H.V Dela Costa Corner Valero St., Salcedo Village, Bel Air, Makati City', '1006', 'Philippine National ID', 'uploads/PSA_5.jpg', 'profile_1000762105.jpg', 'Tenant', 'PropertyOwner', '2025-05-15 11:00:57', 1000762105, '$2y$10$pdHYJIG727UFERXYraU1KueOLRx9mX1RsNOESfaZj/y/y0RSIfguK', 14.55679490, 121.02112260),
(38, 'Soriano', 'Kenny', '', 'kennysoriano2003@gmail.com', '09215855690', '2003-03-13', 'Quezon City', 'Metro Manila', 'Senator Street', '1509', 'Philippine National ID', 'uploads/ids/images_5.jpeg', 'profile_1000522371.gif', 'Tenant', 'None', '2025-05-19 08:21:57', 1000522371, '$2y$10$kzcJPrb.2Q3g9z1IaEBTVOHoc4ELa6wRg/a2dL5TqoKAe0rBVjhEm', 14.70942450, 121.03703230);

-- --------------------------------------------------------

--
-- Table structure for table `apartmentimages`
--

CREATE TABLE `apartmentimages` (
  `id` int(11) NOT NULL,
  `apartment_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apartmentimages`
--

INSERT INTO `apartmentimages` (`id`, `apartment_id`, `image_path`) VALUES
(1, 64, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sdr-watermarked-1.jpg'),
(2, 64, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sdr-watermarked-2.jpg'),
(3, 64, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sdr-watermarked-4.jpg'),
(4, 64, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sdr-watermarked-5.jpg'),
(5, 64, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sdr-watermarked-7.jpg'),
(6, 64, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sdr-watermarked-8.jpg'),
(7, 64, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sdr-watermarked-13.jpg'),
(8, 65, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/feature_image.jpg'),
(9, 65, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_2028.jpg'),
(10, 65, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_2037.jpg'),
(11, 65, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_2078.jpg'),
(12, 65, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_2089.jpg'),
(13, 65, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_2117.jpg'),
(14, 65, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_7833.jpg'),
(15, 66, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/jazz-1241-watermarked-1.jpg'),
(16, 66, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/jazz-1241-watermarked-3.jpg'),
(17, 66, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/jazz-1241-watermarked-5.jpg'),
(18, 66, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/jazz-1241-watermarked-7.jpg'),
(19, 66, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/jazz-1241-watermarked-12.jpg'),
(20, 67, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sapphire-4k-watermarked-3.jpg'),
(21, 67, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sapphire-4k-watermarked-4.jpg'),
(22, 67, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sapphire-4k-watermarked-6.jpg'),
(23, 67, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sapphire-4k-watermarked-8.jpg'),
(24, 67, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sapphire-4k-watermarked-11.jpg'),
(25, 67, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/sapphire-4k-watermarked-14.jpg'),
(26, 68, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_1781.jpg'),
(27, 68, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_1806.jpg'),
(28, 68, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_1811.jpg'),
(29, 68, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_1840.jpg'),
(30, 68, '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/properties/IMG_1851.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `chatlogs`
--

CREATE TABLE `chatlogs` (
  `id` int(11) NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `receiver_id` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `sender_read` tinyint(1) DEFAULT 0,
  `receiver_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatlogs`
--

INSERT INTO `chatlogs` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `is_read`, `sender_read`, `receiver_read`) VALUES
(1, 1000323661, 1000845370, 'hi', '2025-05-21 08:26:07', 0, 0, 0),
(2, 1000845370, 1000323661, 'hello', '2025-05-21 08:26:10', 0, 0, 0),
(3, 1000323661, 1000845370, 'waaaa', '2025-05-21 08:26:15', 0, 0, 0),
(4, 1000845370, 1000323661, 'watchu want?', '2025-05-21 08:27:59', 0, 0, 0),
(5, 1000323661, 1000845370, 'yo whtup bruv', '2025-05-21 08:28:26', 0, 0, 0),
(6, 1000323661, 1000845370, 'hahahaah', '2025-05-21 08:29:16', 0, 0, 0),
(7, 1000845370, 1000323661, 'rent ka?', '2025-05-21 08:29:50', 0, 0, 0),
(8, 1000323661, 1000845370, 'oum', '2025-05-21 08:29:59', 0, 0, 0),
(9, 1000845370, 1000323661, 'oki', '2025-05-21 08:30:02', 0, 0, 0),
(10, 1000323661, 1000845370, 'aaaaaa', '2025-05-21 08:30:06', 0, 0, 0),
(11, 1000845370, 1000323661, 'Please fill out this form for your application: <a href=\"http://172.20.10.10/HomeHiveOfficial/userdashboard/rentalapplicationform.php?sender_id=1000845370&receiver_id=1000323661&property_id=65\" target=\"_blank\" style=\"color:rgb(255, 117, 3); text-decoration: underline; font-weight: bold;\" onclick=\"return confirmLinkClick(event)\">Application Form</a>', '2025-05-21 08:30:12', 0, 0, 0),
(12, 1000845370, 1000323661, 'fill up kana huh', '2025-05-21 08:30:23', 0, 0, 0),
(13, 1000323661, 1000845370, 'magkano ba', '2025-05-21 08:30:30', 0, 0, 0),
(14, 1000845370, 1000323661, 'di kaba marunong mag basa', '2025-05-21 08:30:36', 0, 0, 0),
(15, 1000845370, 1000323661, 'nasa post na ah', '2025-05-21 08:30:39', 0, 0, 0),
(16, 1000323661, 1000845370, 'pass na', '2025-05-21 08:30:50', 0, 0, 0),
(17, 1000845370, 1000323661, 'ge wag na', '2025-05-21 08:30:53', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(11) NOT NULL,
  `lessor_name` varchar(255) DEFAULT NULL,
  `lessee_name` varchar(255) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `lease_duration` varchar(100) DEFAULT NULL,
  `lease_start_date` date DEFAULT NULL,
  `lease_end_date` date DEFAULT NULL,
  `rent_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `advance_rent` decimal(10,2) DEFAULT NULL,
  `security_deposit` decimal(10,2) DEFAULT NULL,
  `lessor_signature` varchar(255) DEFAULT NULL,
  `lessee_signature` varchar(255) DEFAULT NULL,
  `witness1_signature` varchar(255) DEFAULT NULL,
  `witness2_signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `floors` int(11) DEFAULT NULL,
  `parking` enum('Available','None') NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `kitchen` enum('Available','None') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Rejected','Approved') DEFAULT 'Pending',
  `property_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `owner_id`, `name`, `location`, `price`, `type`, `description`, `floors`, `parking`, `bedrooms`, `bathrooms`, `kitchen`, `created_at`, `status`, `property_id`, `latitude`, `longitude`) VALUES
(64, 1000720659, 'Fully Furnished 1 Bedroom Unit in Signa Designer Residences', '1227, Metro Manila, Makati, Signa Designer Residences, Valero St', 40000.00, 'Condo', 'Signa Designer Residences is one of the luxuria projects of Robinsons Land Corporation. It has the best location in the prime business district in Metro Manila.\r\n\r\nMajor financial headquarters, embassies, law firms, and Makati stock exchanges are just a few steps away from Signa Designer Residences’ driveway.\r\n\r\nThe unit is located on the 6th floor. It is fully furnished with comfortable and durable furniture and fixture. It has a balcony for added space and open area.\r\n\r\nNearby establishments within less than 1 kilometer:\r\nYuchengco building, PBCom Tower, Makati spots club, Philam Life building, Ayala Triangle Garden, Pag-ibig (HDMF) Makati office.', 1, 'None', 1, 1, 'Available', '2025-05-15 11:31:04', 'Approved', 2000786242, 14.55922320, 121.01912230),
(65, 1000845370, 'Fully Furnished 1 bedroom for rent in Aseana Paranaque City Manila', 'Aseana, Bradco Avenue,Metro Manila', 50000.00, 'Condo', 'To highlight a few basic features Fully 1 bedroom for rent in Aseana Paranaque City Manila:\r\nIntercom\r\nHot shower system\r\nInduction cooktop\r\nFlat screen TV\r\nBuilt-in wardrobe\r\nQueensize bed\r\nSome upgrades in the unit\r\n\r\nShelving in the living room and in the bedroom\r\n7cuft fridge\r\nCeiling fan\r\nKitchen and dining ware\r\nRug, decors and painting', 1, 'None', 1, 1, 'Available', '2025-05-15 12:02:21', 'Approved', 2000622197, 14.52895280, 120.99177160),
(66, 1000554533, 'Fully Furnished 1 Bedroom in Jazz Residences Metro Manila', 'Jazz Residences, Makati City', 30000.00, 'Condo', 'Fully furnished, very comfortable bed and optimized furniture.\r\nBalcony view is relaxing and mesmerizing especially during at night.\r\nSplit-type airconditioning unit\r\nWater heater system installed in shower\r\nConvertible coffee table to dining table\r\nComplete dining and cooking wares included\r\nWashing machine\r\nFlat screen TV and comfortable size fridge\r\nEasily accessible to public transport\r\nConvenience shops located at the ground level\r\nJazz mall with various restaurants, supermarket, BDO bank and other convenience', 1, 'None', 1, 1, 'Available', '2025-05-15 12:15:48', 'Approved', 2000719074, 14.56383330, 121.02178200),
(67, 1000554533, '1 bedroom with parking in Sapphire Bloc condominium in Ortigas, Mandaluyong', 'Starbucks, Sapphire Road, San Antonio, Pasig First District, Pasig, Eastern Manila District, Metro Manila, 1605, Philippines', 34000.00, 'Condo', 'A proud project of Robinsons Land Corporation! Fully furnished 1 bedroom with parking in Sapphire Bloc, get this condo for rent in Ortigas, Mandaluyong\r\nThis property is literally at the back of Robinsons Galleria which means you have access to BPO offices, restaurants, banks, schools and other business establishments. It also a few minutes walk towards the MRT Ortigas station and just a few steps from shuttle services, point-to-point bus services, city buses, and other commute transportation.\r\n\r\nSapphire Bloc is proud of its proximity to major business hubs within Ortigas central business. Resident in Sapphire Bloc can just walk anywhere in Ortigas CBD thereby avoiding traffic congestion.\r\n\r\nLiving in this modern condo for rent will gain you access to one of the best condominium amenities you can imagine. They have 2 swimming pools and a lap pool. They also have an equipped fitness center, and a sprawling landscaped garden.\r\n\r\nThis rent 1 bedroom with parking in Sapphire Bloc condominium in Ortigas, Mandaluyong is fully furnished and ready for move-in anytime. This unit comes with a very nice kitchen equipped with cabinets, dining and cooking ware, perfect for your daily needs. It also has a nice wooden 2-seater dining table. It also has a 2-seater sofa along with a beautiful wooden TV console. The unit is professionally designed.\r\n \r\nThis 1 bedroom unit is located on the 4th floor North wing of Sapphire Bloc Residences.\r\n \r\nNearby establishments are Robinsons Galleria, Security and Exchange Commission, Robinsons Cyber Beta Building, Pacific Hub Corporation, Marco Polo Hotel and Union Bank Plaza\r\n\r\n \r\nFeatures of rent 1 bedroom with parking in Sapphire Bloc condominium in Ortigas, Mandaluyong:\r\n\r\n1 bedroom\r\n1 toilet\r\nHot water system\r\nWindow type aircon unit\r\nToilet with lavatory\r\nFully functional kitchen\r\ncooking and dining wares\r\nWardrobe\r\nQueen size bed', 1, 'Available', 1, 1, 'Available', '2025-05-15 12:26:41', 'Approved', 2000692650, 14.58739161, 121.06286272),
(68, 1000762105, 'Rent studio condominium unit in McKinley Hill Taguig Metro Manila', 'Florence Way, McKinley Hill, Pinagsama, Taguig District 2, Taguig, Southern Manila District, Metro Manila, 1634, Philippines', 21000.00, 'Studio', 'Rent studio condominium unit in McKinley Hill Taguig Metro Manila has a laid-back ambiance, to soothe your busy day.\r\n\r\nThe unit is located on the 11th floor of Tower 3 Viceroy Residences. Viceroy Residences is just a few steps from the beautiful and romantic Venice Grand Mall.\r\n\r\nThis unit is just perfect for a single occupant or a couple. Whether you have a work-from-home setup or working in one of the business hubs in the area, the unit offers a cozy and straight-forward lifestyle.\r\n\r\nPlaces of interest:\r\n\r\nVenice Grand Mall\r\nPhilippine Army Gym\r\nPhilippine Army General Hospital\r\nChinese International School\r\nKorean International School Manila\r\nCognizant Philippines\r\nbanks and other business establishments.\r\nFeatures:\r\n\r\nFully equip and fully functional kitchen\r\nQueen size bed with pull-out single bed\r\n2-door personal fridge\r\nHot water system in the shower\r\nBuilt-in wardrobe', 1, 'None', 0, 1, 'Available', '2025-05-15 13:03:02', 'Approved', 2000476418, 14.53293163, 121.04964192);

-- --------------------------------------------------------

--
-- Table structure for table `propertyrejections`
--

CREATE TABLE `propertyrejections` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `rejection_reasons` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rentalapplications`
--

CREATE TABLE `rentalapplications` (
  `id` int(11) NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `receiver_id` bigint(20) NOT NULL,
  `property_id` int(11) NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `employer` varchar(255) NOT NULL,
  `income` varchar(255) NOT NULL,
  `employment_type` varchar(50) NOT NULL,
  `certificate_file` varchar(255) DEFAULT NULL,
  `rented_before` varchar(3) NOT NULL,
  `last_rental_stay` varchar(255) DEFAULT NULL,
  `reason_leaving` varchar(255) DEFAULT NULL,
  `previous_landlord` varchar(255) DEFAULT NULL,
  `household_members` int(11) NOT NULL,
  `pets` varchar(3) NOT NULL,
  `smoke` varchar(3) NOT NULL,
  `guests` varchar(3) NOT NULL,
  `move_in_date` date NOT NULL,
  `stay_length` varchar(50) NOT NULL,
  `parking` varchar(3) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `confirm_info` tinyint(1) NOT NULL,
  `authorize_verification` tinyint(1) NOT NULL,
  `consent_processing` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','awaiting payment','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rentalmessages`
--

CREATE TABLE `rentalmessages` (
  `id` int(11) NOT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `receiver_id` bigint(20) NOT NULL,
  `property_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentalmessages`
--

INSERT INTO `rentalmessages` (`id`, `sender_id`, `receiver_id`, `property_id`, `message`, `sent_at`, `is_read`) VALUES
(1, 1000323661, 1000845370, 65, 'I’m ready to apply.', '2025-05-21 00:25:30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `verificationdocuments`
--

CREATE TABLE `verificationdocuments` (
  `id` int(11) NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `certificate_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verificationdocuments`
--

INSERT INTO `verificationdocuments` (`id`, `owner_id`, `document_type`, `document_path`, `certificate_path`, `created_at`) VALUES
(1, 1000720659, 'Philippine National ID', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/ids/PSA_2.jpg', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/certificates/ClaraCert.png', '2025-05-15 11:31:04'),
(2, 1000845370, 'Philippine National ID', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/ids/PSA_1.jpg', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/certificates/DelaCruzCert-1.png', '2025-05-15 12:02:21'),
(3, 1000554533, 'Philippine National ID', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/ids/PSA_4.jpg', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/certificates/HudloTitleCert-1.png', '2025-05-15 12:15:48'),
(4, 1000554533, 'Philippine National ID', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/ids/PSA_4.jpg', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/certificates/HudloTitleCert-1.png', '2025-05-15 12:26:41'),
(5, 1000762105, 'Philippine National ID', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/ids/PSA_5.jpg', '/opt/lampp/htdocs/HomeHiveOfficial/userdashboard/uploads/certificates/SorianoCert.png', '2025-05-15 13:03:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accountsdb`
--
ALTER TABLE `accountsdb`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `userID` (`userID`),
  ADD UNIQUE KEY `userID_2` (`userID`);

--
-- Indexes for table `apartmentimages`
--
ALTER TABLE `apartmentimages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apartment_id` (`apartment_id`);

--
-- Indexes for table `chatlogs`
--
ALTER TABLE `chatlogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `propertyrejections`
--
ALTER TABLE `propertyrejections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `rentalapplications`
--
ALTER TABLE `rentalapplications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender` (`sender_id`),
  ADD KEY `receiver` (`receiver_id`),
  ADD KEY `property` (`property_id`);

--
-- Indexes for table `rentalmessages`
--
ALTER TABLE `rentalmessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sender` (`sender_id`),
  ADD KEY `fk_receiver` (`receiver_id`),
  ADD KEY `fk_property` (`property_id`);

--
-- Indexes for table `verificationdocuments`
--
ALTER TABLE `verificationdocuments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountsdb`
--
ALTER TABLE `accountsdb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `apartmentimages`
--
ALTER TABLE `apartmentimages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `chatlogs`
--
ALTER TABLE `chatlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `propertyrejections`
--
ALTER TABLE `propertyrejections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rentalapplications`
--
ALTER TABLE `rentalapplications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rentalmessages`
--
ALTER TABLE `rentalmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `verificationdocuments`
--
ALTER TABLE `verificationdocuments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apartmentimages`
--
ALTER TABLE `apartmentimages`
  ADD CONSTRAINT `ApartmentImages_ibfk_1` FOREIGN KEY (`apartment_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chatlogs`
--
ALTER TABLE `chatlogs`
  ADD CONSTRAINT `ChatLogs_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `accountsdb` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ChatLogs_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `accountsdb` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `Properties_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `accountsdb` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `propertyrejections`
--
ALTER TABLE `propertyrejections`
  ADD CONSTRAINT `PropertyRejections_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `rentalapplications`
--
ALTER TABLE `rentalapplications`
  ADD CONSTRAINT `property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `receiver` FOREIGN KEY (`receiver_id`) REFERENCES `accountsdb` (`userID`),
  ADD CONSTRAINT `sender` FOREIGN KEY (`sender_id`) REFERENCES `accountsdb` (`userID`);

--
-- Constraints for table `rentalmessages`
--
ALTER TABLE `rentalmessages`
  ADD CONSTRAINT `fk_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `accountsdb` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`sender_id`) REFERENCES `accountsdb` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `verificationdocuments`
--
ALTER TABLE `verificationdocuments`
  ADD CONSTRAINT `VerificationDocuments_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `accountsdb` (`userID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
