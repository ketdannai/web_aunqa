-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2026 at 06:12 AM
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
-- Database: `web_aunqa`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `art_id` int(20) NOT NULL,
  `use_id` int(20) NOT NULL,
  `art_name` varchar(255) DEFAULT NULL,
  `art_title1` varchar(255) DEFAULT NULL,
  `art_fname1` varchar(255) DEFAULT NULL,
  `art_lname1` varchar(255) DEFAULT NULL,
  `art_title2` varchar(255) DEFAULT NULL,
  `art_fname2` varchar(255) DEFAULT NULL,
  `art_lname2` varchar(255) DEFAULT NULL,
  `art_title3` varchar(255) DEFAULT NULL,
  `art_fname3` varchar(255) DEFAULT NULL,
  `art_lname3` varchar(25) DEFAULT NULL,
  `art_title4` varchar(255) DEFAULT NULL,
  `art_fname4` varchar(255) DEFAULT NULL,
  `art_lname4` varchar(255) NOT NULL,
  `art_title5` varchar(255) DEFAULT NULL,
  `art_fname5` varchar(255) DEFAULT NULL,
  `art_lname5` varchar(255) DEFAULT NULL,
  `art_meet` varchar(255) DEFAULT NULL,
  `art_evidence` varchar(255) DEFAULT NULL,
  `art_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`art_id`, `use_id`, `art_name`, `art_title1`, `art_fname1`, `art_lname1`, `art_title2`, `art_fname2`, `art_lname2`, `art_title3`, `art_fname3`, `art_lname3`, `art_title4`, `art_fname4`, `art_lname4`, `art_title5`, `art_fname5`, `art_lname5`, `art_meet`, `art_evidence`, `art_type`) VALUES
(3, 14, 'เปลียบเทียบศักยาภาพการขยายพันธุ์จากหน่อยและเมล็ดของปาล์มสาคู', '', 'สกุลรัตน์', 'หาญศึก', '', 'โพยมพร', 'รักษาชล', 'ผศ.', 'กลอยใจ', 'ครุฑจ้อน', '', '', '', '', '', '', 'งานประชุมวิชาการเกษตร ครั้งที่25 ประจำปี2567 ณ คณะเกษตรศาสตร์ มหาวิทยาลัยขอนแก่น', '', 'การประชุมวิชาการระดับชาติ'),
(4, 14, 'Development of Real-Time Water Monitoring and Alert System for Sustainable Aquaculture in the Tapi River', 'ดร.', 'ชริยา', 'นนทกาญจน์', '', '', '', '', '', '', '', '', '', '', '', '', 'the 11th International Conference on Engineering, Applied Sciences, and Technology (ICEAST), May 6-9 2025, Phuket, Thailand', '', 'บทความวิจัยหรือบทความวิชาการฉบับสมบูรณ์ที่ตีพิมพ์ และผลงานที่ได้รับการจดอนุสิทธิบัตรปีปฎิทิน 2567'),
(5, 14, 'แอปพลิเคชั่นสื่อประชาสัมพันธ์กลุมวิสาหกิจชุมชน อำเภอถ้ำพรรณรา จังหวัดนครศรีธรรมราช', 'ผ.ศ.', 'นิธิพร', 'วรรณโสภณ', '', '', '', '', '', '', '', '', '', '', '', '', 'การประชุมวิชาการระดับนานาชาติด้านวิทยาศาสตร์และเทคโนโลยีเครือข่ายสถาบันอุดมศึกษาภาคใต้ ครั้งที่9 ', '', 'บทความวิจัยฉบับสมบูรณ์ ที่ได้รับการตีพิมพ์สืบเนื่องจากการประชุมวิชาการ');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(20) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, '1.หมวดวิชาศึกษาทั่วไป'),
(2, '2.หมวดวิชาเฉพาะ');

-- --------------------------------------------------------

--
-- Table structure for table `categorycourse`
--

CREATE TABLE `categorycourse` (
  `categorycourse_id` int(20) NOT NULL,
  `categorycourse_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categorycourse`
--

INSERT INTO `categorycourse` (`categorycourse_id`, `categorycourse_name`) VALUES
(1, '1.1กลุ่มวิชาคุณภาพชีวิตดี มีสุข'),
(2, '1.2กลุ่มวิชาพลเมืองดี วิถีประชาธิปไตย'),
(3, '1.3กลุ่มวิชาภาษาและการสื่อสาร'),
(4, '1.4กลุ่มวิชาวิทยาศาสตร์และเทคโนโลย'),
(5, '2.1กลุ่มวิชาพื้นฐานวิชาชีพ'),
(6, '2.2กลุ่มวิชาชีพบังคับ'),
(7, '2.3กลุ่มวิชาชีพเลือก');

-- --------------------------------------------------------

--
-- Table structure for table `clo`
--

CREATE TABLE `clo` (
  `clo_id` int(20) NOT NULL,
  `plo_id` int(20) NOT NULL,
  `course_id` int(20) NOT NULL,
  `use_id` int(20) NOT NULL,
  `clo_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(20) NOT NULL,
  `use_id` int(20) NOT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `course_code` varchar(255) DEFAULT NULL,
  `course_credit` varchar(255) DEFAULT NULL,
  `category_id` int(20) NOT NULL,
  `categorycourse_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `use_id`, `course_name`, `course_code`, `course_credit`, `category_id`, `categorycourse_id`) VALUES
(16, 11, 'ศาสตร์พระราชา ', '0001800165 ', '2 : 1', 1, 1),
(17, 11, 'ฟุตบอล', '0001100165', '0 : 1', 1, 1),
(18, 11, 'ว่ายน้ำ ', '0001100265', '0 : 1 ', 1, 1),
(19, 11, 'กีฬาลีลาศ', '0001100365 ', '0 : 1 ', 1, 1),
(20, 11, 'จักรยานเพื่อนันทนาการ', '0001100465 ', '0 : 1 ', 1, 1),
(21, 11, 'บาสเกตบอล ', '0001100565', '0 : 1', 1, 1),
(22, 11, 'ตะกร้อ', '0001100665 ', '0 : 1', 1, 1),
(23, 11, 'แบดมินตัน ', '0001100765 ', '0 : 1', 1, 1),
(24, 11, 'วอลเลย์บอล ', '0001100865 ', '0 : 1', 1, 1),
(25, 11, 'ฟุตซอล', '0001100965', '0 : 1 ', 1, 1),
(26, 11, 'เทนนิส', '0001101065', '0 : 1', 1, 1),
(27, 11, 'กอล์ฟ', '0001101165 ', '0 : 1', 1, 1),
(28, 11, 'สารัตถะแห่งความงาม', '0001200165', '3 : 0', 1, 1),
(29, 11, 'ดนตรีเพื่อชีวิต ', '0001200265', '3 : 0 ', 1, 1),
(30, 11, 'วิตกับเศรษฐกิจพอเพียง ', '0001300165 ', '3 : 0', 1, 1),
(31, 11, 'เศรษฐศาสตร์ในชีวิตประจำวัน ', '0001300265', '3 : 0 ', 1, 1),
(32, 11, 'อรรถรสในงานศิลปะ', '0001800265', '2 : 1 ', 1, 1),
(33, 11, 'การส่งเสริมสุขภาพและการออกกำลังกาย', '0001800365', '2 : 1', 1, 1),
(34, 11, 'ผู้นำนันทนาการ', '0001800465', '2 : 1', 1, 1),
(35, 11, 'สมาธิเพื่อการพัฒนาชีวิต ', '0001800565 ', '2 : 1 ', 1, 1),
(36, 11, 'พลเมืองกับจิตสำนึกต่อสังคม ', '0002300165 ', '3 : 0', 1, 2),
(37, 11, 'จริยธรรมสำหรับมนุษย', '0002200165 ', '3 : 0', 1, 2),
(38, 11, 'มนุษยสัมพันธ์และการพัฒนาบุคลิกภาพ', '0002200265 ', '3 : 0', 1, 2),
(39, 11, 'มนุษยสัมพันธ์เพื่อการดำรงชีวิต', '0002200365', '3 : 0 ', 1, 2),
(40, 11, 'วัฒนวิถีแห่งการดำรงชีวิต', '0002200465', '3 : 0', 1, 2),
(41, 11, 'จิตวิทยาเชิงบวก', '0002200565', '3 : 0', 1, 2),
(42, 11, 'จิตวิทยาในการทำงาน', '0002200665', '3 : 0', 1, 2),
(43, 11, 'ภาวะผู้นำและการทำงานเป็นทีม', '0002200765 ', '3 : 0', 1, 2),
(44, 11, 'สังคมกับการปกครอง', '0002300265 ', '3 : 0', 1, 2),
(45, 11, 'อารยธรรมไทยในบริบทโลกาภิวัตน', '0002300365', '3 : 0', 1, 2),
(46, 11, ' ไทยศึกษา', '0002300465', ' 3 : 0', 1, 2),
(47, 11, ' ความรู้ทั่วไปเกี่ยวกับกฎหมาย ', '0002300565', '3 : 0', 1, 2),
(48, 11, 'เอเชียตะวันออกเฉียงใต้ศึกษา', '0002300665', ' 3 : 0', 1, 2),
(49, 11, 'ชุมชนศึกษา', '0002300765', '3 : 0', 1, 2),
(50, 11, 'วัฒนธรรมและขนบประเพณีภาคใต้', '0002300865', ' 3 : 0', 1, 2),
(51, 11, 'สนทนาภาษาอังกฤษ', '0003500165', ' 2 : 1', 1, 3),
(52, 11, 'การอ่านและการเขียนภาษาอังกฤษ', '0003500265', ' 2 : 1', 1, 3),
(53, 11, ' มนุษย์กับวรรณกรรม', '0003400165', '3 : 0', 1, 3),
(54, 11, 'ภาษาไทยเพื่อการสื่อสาร', '0003400265', ' 2 : 1', 1, 3),
(55, 11, 'ทักษะการอ่านภาษาไทย', '0003400365', ' 2 : 1', 1, 3),
(56, 11, 'ทักษะการเขียนภาษาไทย', '0003400465', ' 2 : 1', 1, 3),
(57, 11, 'ศิลปะการพูด', '0003400565', ' 2 : 1', 1, 3),
(58, 11, 'การอ่านและการเขียนเชิงวิชาการ', '0003400665', ' 2 : 1', 1, 3),
(59, 11, 'การอ่านเพื่อการเรียนรู้ตลอดชีวิต ', '0003400765', ' 2 : 1', 1, 3),
(60, 11, 'ภาษาอังกฤษเพื่อการสื่อสาร', '0003500365', ' 2 : 1', 1, 3),
(61, 11, 'ภาษาอังกฤษเพื่อการทำงาน', '0003500465', ' 2 : 1', 1, 3),
(62, 11, 'ภาษาอังกฤษเพื่อการนำเสนอ', '0003500565', ' 2 : 1', 1, 3),
(63, 11, 'ภาษาอังกฤษเพื่อการเรียนรู้ตลอดชีวิต', '0003500665', ' 2 : 1', 1, 3),
(64, 11, 'ภาษาจีนเพื่อการสื่อสาร', '0003500765', ' 2 : 1', 1, 3),
(65, 11, 'ภาษามลายูเพื่อการสื่อสาร', '0003500865', ' 2 : 1', 1, 3),
(66, 11, 'ภาษาญี่ปุ่นเพื่อการสื่อสาร', '0003500965', ' 2 : 1', 1, 3),
(67, 11, ' เทคโนโลยีและนวัตกรรม ', '0004800165', ' 2 : 1', 1, 4),
(68, 11, ' คณิตศาสตร์ในชีวิตประจำวัน', '0004600165', ' 2 : 1', 1, 4),
(69, 11, 'ความรู้เชิงตัวเลข', '0004600265', ' 2 : 1', 1, 4),
(70, 11, 'คณิตศาสตร์สำหรับธุรกิจ', '0004600365', ' 2 : 1', 1, 4),
(71, 11, ' ความงามของคณิตศาสตร', '0004600465', ' 2 : 1', 1, 4),
(72, 11, 'ระบบสารสนเทศเพื่อการตัดสินใจ', '0004600565', ' 2 : 1', 1, 4),
(73, 11, 'มนุษย์กับผลิตภัณฑ์เคม', '0004700165', ' 3 : 0', 1, 4),
(74, 11, 'สิ่งแวดล้อมและการจัดการทรัพยากร', '0004700265', ' 3 : 0', 1, 4),
(75, 11, 'ยาและสารเสพติด', '0004700365', ' 3 : 0', 1, 4),
(76, 11, ' เทคโนโลยีสีเขียว', '0004700465', ' 3 : 0', 1, 4),
(77, 11, 'ปรากฏการณ์สำคัญทางวิทยาศาสตร', '0004700565', ' 2 : 1', 1, 4),
(78, 11, 'วิทยาศาสตร์และเทคโนโลยีเพื่อคุณภาพชีวิต ', '0004700665', ' 2 : 1', 1, 4),
(79, 11, 'การจัดการนวัตกรรมสำหรับผู้ประกอบการ', '0004800265', ' 2 : 1', 1, 4),
(80, 11, 'การพัฒนาทักษะการคิดนอกกรอบ', '0004800365', ' 2 : 1', 1, 4),
(81, 11, 'สถิติพื้นฐาน', '0221300165', ' 3 : 0', 2, 5),
(82, 11, 'คณิตศาสตร์ดิสครีตสำหรับเทคโนโลยีสารสนเทศ', '0232110165', '3 : 0', 2, 5),
(83, 11, ' พื้นฐานเทคโนโลยีสารสนเทศ', '0232110265', ' 2 : 1', 2, 5),
(84, 11, 'การสื่อสารข้อมูลและเครือข่าย', '0232210165', ' 3 : 0', 2, 6),
(85, 11, ' เทคโนโลยีแพลตฟอร์ม', '0232220265', ' 2 : 1', 2, 6),
(86, 11, ' การเขียนโปรแกรมคอมพิวเตอร', '0232310165', ' 2 : 1', 2, 6),
(87, 11, 'โครงสร้างข้อมูลและขั้นตอนวิธ', '0232310265', ' 2 : 1', 2, 6),
(88, 11, 'ระบบฐานข้อมูล', '0232320365', ' 2 : 1', 2, 6),
(89, 11, 'การออกแบบปฏิสัมพันธ์ตามประสบการณ์ผู้ใช้ ', '0232320465', ' 2 : 1', 2, 6),
(90, 11, 'วิศวกรรมซอฟต์แวร', '0232330565', ' 2 : 1', 2, 6),
(91, 11, 'การพัฒนาและปฏิบัติการ', '0232330665', ' 2 : 1', 2, 6),
(92, 11, 'โครงงานทางเทคโนโลยีสารสนเทศ', '0232441165', ' 3 : 0', 2, 6),
(93, 11, 'การฝึกงานทางเทคโนโลยีสารสนเทศ', '0232441265', ' 3 : 0', 2, 6),
(94, 11, ' สหกิจศึกษา', '0232441365', '0 : 6', 2, 6),
(95, 11, 'การสร้างดิจิทัลคอนเทนต', '0232410165', ' 2 : 1', 2, 6),
(96, 11, ' การเขียนโปรแกรมเชิงวัตถ', '0232420265', ' 2 : 1', 2, 6),
(97, 11, ' การออกแบบและพัฒนาเว็บ', '0232420365', ' 2 : 1', 2, 6),
(98, 11, 'การออกแบบและติดตั้งระบบเครือข่าย', '0232420465', ' 2 : 1', 2, 6),
(99, 11, 'ปัญญาประดิษฐ', '0232420565', ' 3 : 0', 2, 6),
(100, 11, 'วิทยาการข้อมูล', '0232430665', ' 3 : 0', 2, 6),
(101, 11, ' การพัฒนาโมบายแอปพลิเคชัน', '0232430765', ' 2 : 1', 2, 6),
(102, 11, 'การพัฒนาคลาวด์แอปพลิเคชัน', '0232430865', ' 2 : 1', 2, 6),
(103, 11, ' ประสบการณ์การทำงานทางวิชาชีพ', '0232430965', '0 : 2', 2, 6),
(104, 11, 'การเตรียมความพร้อมการฝึกงานและสหกิจศึกษา', '0232441065', '0 : 1', 2, 6),
(105, 11, 'ความมั่นคงปลอดภัยทางไซเบอร', '0232530165', ' 2 : 1', 2, 6),
(106, 11, 'กฎหมายและจริยธรรมทางเทคโนโลยีสารสนเทศ', '0232530265', '3 : 0', 2, 6),
(107, 11, ' สัมมนาทางเทคโนโลยีสารสนเทศ', '0232530365', '0 : 1', 2, 6),
(108, 11, ' การเป็นผู้ประกอบการทางเทคโนโลยีสารสนเทศ', '0232540465', ' 3 : 0', 2, 6),
(109, 11, 'การพัฒนาโมบายแอปพลิเคชันแบบผสม', '0232630165', ' 2 : 1', 2, 7),
(110, 11, ' การพัฒนาเว็บแบบฟูลแสต็ก', '0232630265', ' 2 : 1', 2, 7),
(111, 11, 'ซอฟต์แวร์โอเพ่นซอร์ส', '0232630365', ' 2 : 1', 2, 7),
(112, 11, 'เทคโนโลยีเว็บและเว็บเซอร์วิส', '0232630465', ' 2 : 1', 2, 7),
(113, 11, 'การพัฒนาซอฟต์แวร์บนเทคโนโลยีดอทเน็ต', '0232630565', ' 2 : 1', 2, 7),
(114, 11, 'การพัฒนาซอฟต์แวร์ด้วยภาษาไพทอน', '0232630665', ' 2 : 1', 2, 7),
(115, 11, 'การพัฒนาซอฟต์แวร์เพื่อธุรกิจดิจิทัล', '0232640765', ' 2 : 1', 2, 7),
(116, 11, ' ระบบสารสนเทศทางภูมิศาสตร', '0232640865', ' 2 : 1', 2, 7),
(117, 11, 'ระบบสนับสนุนการตัดสินใจ', '0232640965', ' 2 : 1', 2, 7),
(118, 11, ' หัวข้อพิเศษทางการพัฒนาซอฟต์แวร์ 1', '0232641065', ' 2 : 1', 2, 7),
(119, 11, 'หัวข้อพิเศษทางการพัฒนาซอฟต์แวร์ 2', '0232641165', ' 2 : 1', 2, 7),
(120, 11, 'การเรียนรู้ของเครื่อง', '0232730165', ' 2 : 1', 2, 7),
(121, 11, 'การทำเหมืองข้อมูล', '0232730265', ' 2 : 1', 2, 7),
(122, 11, 'การวิเคราะห์ข้อมูลขนาดใหญ', '0232730365', ' 2 : 1', 2, 7),
(123, 11, 'ธุรกิจอัจฉริยะ', '0232730465', ' 2 : 1', 2, 7),
(124, 11, 'เทคโนโลยีบล็อกเชน', '0232730565', ' 2 : 1', 2, 7),
(125, 11, 'ภาษาโปรแกรมเพื่องานวิทยาการข้อมูลและปัญญาประดิษฐ์ ', '0232730665', ' 2 : 1', 2, 7),
(126, 11, 'ระบบผู้เชี่ยวชาญ', '0232740765', ' 2 : 1', 2, 7),
(127, 11, 'การประมวลผลภาพ', '0232740865', ' 2 : 1', 2, 7),
(128, 11, 'การประมวลผลภาษาธรรมชาต', '0232740965', ' 2 : 1', 2, 7),
(129, 11, 'หัวข้อพิเศษทางวิทยาการข้อมูลและปัญญาประดิษฐ์ 1', '0232741065', ' 2 : 1', 2, 7),
(130, 11, 'หัวข้อพิเศษทางวิทยาการข้อมูลและปัญญาประดิษฐ์ 2', '0232741165', ' 2 : 1', 2, 7),
(131, 11, 'ความคิดสร้างสรรค์เพื่อการออกแบบสื่อดิจิทัล', '0232830165', ' 2 : 1', 2, 7),
(132, 11, 'การพัฒนาอินโฟกราฟิกและโมชันกราฟิก', '0232830265', ' 2 : 1', 2, 7),
(133, 11, 'คอมพิวเตอร์กราฟิกและแอนิเมชัน', '0232830365', ' 2 : 1', 2, 7),
(134, 11, 'คอมพิวเตอร์แอนิเมชัน 3 มิต', '0232830465', ' 2 : 1', 2, 7),
(135, 11, 'การผลิตเสียงเพื่อสื่อดิจิทัล', '0232830565', ' 2 : 1', 2, 7),
(136, 11, 'ปัญญาประดิษฐ์เพื่อดิจิทัลคอนเทนต', '0232840665', ' 2 : 1', 2, 7),
(137, 11, 'เทคโนโลยีเสมือนจริง ', '0232840765', ' 2 : 1', 2, 7),
(138, 11, 'การออกแบบและพัฒนาเกม', '0232840865', ' 2 : 1', 2, 7),
(139, 11, 'การถ่ายภาพเพื่องานออกแบบดิจิทัลคอนเทนต', '0232840965', ' 2 : 1', 2, 7),
(140, 11, 'นวัตกรรมการผลิตสื่อ', '0232841065', ' 2 : 1', 2, 7),
(141, 11, 'หัวข้อพิเศษทางเทคโนโลยีดิจิทัลคอนเทนต์ 1', '0232841165', ' 2 : 1', 2, 7),
(142, 11, ' หัวข้อพิเศษทางเทคโนโลยีดิจิทัลคอนเทนต์ 2', '0232841265', ' 2 : 1', 2, 7),
(143, 11, 'การรักษาความปลอดภัยเครือข่าย', '0232930165', ' 2 : 1', 2, 7),
(144, 11, 'เทคโนโลยีไคลเอนต์/เซิร์ฟเวอร', '0232930265', ' 2 : 1', 2, 7),
(145, 11, 'ระบบปฏิบัติการเครือข่าย', '0232930365', ' 2 : 1', 2, 7),
(146, 11, 'อินเทอร์เน็ตประสานสรรพสิ่ง', '0232930465', ' 2 : 1', 2, 7),
(147, 11, 'ระบบฝังตัว', '0232930565', ' 2 : 1', 2, 7),
(148, 11, 'ระบบดิจิตอล', '0232930665', ' 2 : 1', 2, 7),
(149, 11, 'การเขียนโปรแกรมบนเครือข่าย', '0232940765', ' 2 : 1', 2, 7),
(150, 11, 'การจัดการระบบเครือข่าย', '0232940865', ' 2 : 1', 2, 7),
(151, 11, 'การเข้ารหัสลับเพื่อความปลอดภัย', '0232940965', ' 2 : 1', 2, 7),
(152, 11, 'ความมั่นคงปลอดภัยของคลาวด', '0232941065', ' 2 : 1', 2, 7),
(153, 11, 'การพัฒนาแพลตฟอร์มอินเทอร์เน็ตประสานสรรพสิ่ง', '0232941165', ' 2 : 1', 2, 7),
(154, 11, 'หัวข้อพิเศษทางระบบเครือข่ายและความมั่นคงปลอดภัยทางไซเบอร์ 1', '0232941265', ' 2 : 1', 2, 7),
(155, 11, 'หัวข้อพิเศษทางระบบเครือข่ายและความมั่นคงปลอดภัยทางไซเบอร์ 2', '0232941365', ' 2 : 1', 2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `development`
--

CREATE TABLE `development` (
  `dev_id` int(20) NOT NULL,
  `use_id` int(20) NOT NULL,
  `section_id1` int(11) DEFAULT NULL,
  `count_id1` int(11) DEFAULT 0,
  `section_id2` int(11) DEFAULT NULL,
  `count_id2` int(11) DEFAULT 0,
  `section_id3` int(11) DEFAULT NULL,
  `count_id3` int(11) DEFAULT 0,
  `section_id4` int(11) DEFAULT NULL,
  `count_id4` int(11) DEFAULT 0,
  `section_id5` int(11) DEFAULT NULL,
  `count_id5` int(11) DEFAULT 0,
  `dev_name` varchar(255) DEFAULT NULL,
  `dev_date` varchar(255) DEFAULT NULL,
  `dev_at` varchar(255) DEFAULT NULL,
  `dev_obj` varchar(255) DEFAULT NULL,
  `dev_pic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laboratory`
--

CREATE TABLE `laboratory` (
  `lab_id` int(20) NOT NULL,
  `use_id` int(11) NOT NULL,
  `lab_name` varchar(266) DEFAULT NULL,
  `lab_num` varchar(256) DEFAULT NULL,
  `lab_durable` varchar(256) DEFAULT NULL,
  `lab_status` varchar(256) DEFAULT NULL,
  `lab_pic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `laboratory`
--

INSERT INTO `laboratory` (`lab_id`, `use_id`, `lab_name`, `lab_num`, `lab_durable`, `lab_status`, `lab_pic`) VALUES
(9, 16, ' ห้องปฏิบัติคอมพิวเตอร์มัลติมีเดีย ห้อง CS1', '25', 'เครื่องคอมพิวเตอร์ ', 'พร้อมใช้งาน', ''),
(10, 16, 'ห้องปฏิบัติการโปรแกรมห้อง CS2', '30', 'เครื่องคอมพิวเตอร', 'พร้อมใช้งาน', ''),
(11, 16, 'ห้องปฏิบัติการคอมพิวเตอร์ประมวลผลขั้นสูงห้อง CS3', '30', 'เครื่องคอมพิวเตอร์สมรรถนะสูง', 'พร้อมใช้งาน', ''),
(12, 16, 'ห้องปฏิบัติการคอมพิวเตอร์วิทยาการข้อมูลห้อง CS4', '30', 'เครื่องคอมพิวเตอร', 'พร้อมใช้งาน', ''),
(13, 16, 'ห้องปฏิบัติการบำรุงรักษาคอมพิวเตอรห้อง CS5', '30', 'เครื่องคอมพิวเตอร', 'พร้อมใช้งาน', ''),
(14, 16, ' ห้องปฏิบัติการสร้างสรรค์ดิจิทัลห้อง CS6', '30', 'ระบบเครือข่ายแบบสาย', 'พร้อมใช้งาน', '');

-- --------------------------------------------------------

--
-- Table structure for table `opencourse`
--

CREATE TABLE `opencourse` (
  `opencourse_id` int(20) NOT NULL,
  `course_id` int(20) NOT NULL,
  `use_id` int(20) NOT NULL,
  `section_id` int(20) NOT NULL,
  `term_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plo`
--

CREATE TABLE `plo` (
  `plo_id` int(11) NOT NULL,
  `plo_code` varchar(256) DEFAULT NULL,
  `plo_bty` varchar(256) DEFAULT NULL,
  `plo_knowledge` varchar(256) DEFAULT NULL,
  `plo_skill` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `plo`
--

INSERT INTO `plo` (`plo_id`, `plo_code`, `plo_bty`, `plo_knowledge`, `plo_skill`) VALUES
(18, 'PLO1 มีเจตคติที่ดี คุณธรรมจริยธรรม  ระเบียบวินัย ขยันหมั่นเพียร ใฝ่รู้ มีความ สำนึกต่อจรรยาบรรณวิชาชีพและมีความ รับผิดชอบต่อหน้าที่และสังคม ', 'R,U', '', 'ทักษะการสื่อสาร,ทักษะการใช้เทคโนโลยี (IT),การคิดวิเคราะห์ (Critical Thinking),การแก้ปัญหา (Problem Solving),การทำงานเป็นทีม,ทักษะภาษาอังกฤษ'),
(19, 'PLO2 สามารถสื่อสาร มีมนุษย์สัมพันธ์  ทำงานร่วมกับผู้อื่น มีทักษะในการทำงาน เป็นทีม สามารถบริหารจัดการการทำงาน ได้อย่างเหมาะสม และมีทัศนคติที่ดีในการ ทำงาน และเป็นผู้ที่มีความสามารถในการ เรียนรู้ตลอดชีวิต ', 'R,U,Ap', '', 'ทักษะการสื่อสาร,ทักษะการใช้เทคโนโลยี (IT),การคิดวิเคราะห์ (Critical Thinking),การแก้ปัญหา (Problem Solving),การทำงานเป็นทีม,ทักษะภาษาอังกฤษ'),
(20, 'PLO3  มีความรู้ความเข้าใจทางด้านทฤษฎีพื้นฐาน ทางด้านเทคโนโลยีสารสนเทศ รวมทั้งสามารถติดตาม ความก้าวหน้าทางด้านเทคโนโลยีสารสนเทศ และ เข้าใจผลกระทบของการเปลี่ยนแปลงของเทคโนโลยีที่ เกี่ยวข้อง ', 'R,U,Ap', 'ความรู้พื้นฐานวิชาชีพ,ความรู้เฉพาะทาง,ความรู้ด้านกฎหมายที่เกี่ยวข้อง,การบูรณาการความรู้,ความรู้ในสถานการณ์,การประยุกต์ใช้ทฤษฎี', ''),
(21, 'PLO4 ออกแบบและพัฒนาซอฟต์แวร์ โดยคำนึง ความปลอดภัยของข้อมูล ประยุกต์ใช้          วิทยาการข้อมูลและเทคโนโลยีปัญญาประดิษฐ์ในการ พัฒนาซอฟต์แวร์ เพื่อแก้ปัญหาในงานต่าง ๆ ได้ ', 'U,Ap,An,Ev,C', 'ความรู้พื้นฐานวิชาชีพ,ความรู้เฉพาะทาง,ความรู้ด้านกฎหมายที่เกี่ยวข้อง,การบูรณาการความรู้,ความรู้ในสถานการณ์,การประยุกต์ใช้ทฤษฎี', ''),
(22, 'PLO5 ออกแบบระบบเครือข่ายคอมพิวเตอร์และ ระบบความปลอดภัย จัดการบำรุงรักษาฮาร์ดแวร์  และวางแผนจัดการการใช้งานเทคโนโลยีสารสนเทศ ', 'U,Ap,An,Ev,C', 'ความรู้พื้นฐานวิชาชีพ,ความรู้เฉพาะทาง,ความรู้ด้านกฎหมายที่เกี่ยวข้อง,การบูรณาการความรู้,ความรู้ในสถานการณ์,การประยุกต์ใช้ทฤษฎี', ''),
(25, 'PLO6 คิด วิเคราะห์ ริเริ่มสร้างสรรค์ และบูรณาการ ศาสตร์ทางด้านเทคโนโลยีสารสนเทศกับศาสตร์ด้าน ต่าง ๆ เพื่อสร้างสิ่งประดิษฐ์และนวัตกรรมเพื่อนำไปใช้ ประโยชน์ในท้องถิ่น ', 'U,Ap,An,Ev,C', 'ความรู้พื้นฐานวิชาชีพ,ความรู้เฉพาะทาง,ความรู้ด้านกฎหมายที่เกี่ยวข้อง,การบูรณาการความรู้,ความรู้ในสถานการณ์,การประยุกต์ใช้ทฤษฎี', '');

-- --------------------------------------------------------

--
-- Table structure for table `research`
--

CREATE TABLE `research` (
  `res_id` int(20) NOT NULL,
  `use_id` int(20) NOT NULL,
  `res_name` varchar(255) DEFAULT NULL,
  `res_title1` varchar(255) DEFAULT NULL,
  `res_fname1` varchar(255) DEFAULT NULL,
  `res_lname1` varchar(255) DEFAULT NULL,
  `res_title2` varchar(255) DEFAULT NULL,
  `res_fname2` varchar(255) DEFAULT NULL,
  `res_lname2` varchar(255) DEFAULT NULL,
  `res_title3` varchar(255) DEFAULT NULL,
  `res_fname3` varchar(255) DEFAULT NULL,
  `res_lname3` varchar(255) DEFAULT NULL,
  `res_title4` varchar(255) DEFAULT NULL,
  `res_fname4` varchar(255) DEFAULT NULL,
  `res_lname4` varchar(255) DEFAULT NULL,
  `res_title5` varchar(255) DEFAULT NULL,
  `res_fname5` varchar(255) DEFAULT NULL,
  `res_lname5` varchar(255) DEFAULT NULL,
  `res_date` varchar(255) DEFAULT NULL,
  `res_publish` varchar(255) DEFAULT NULL,
  `res_capital` varchar(255) DEFAULT NULL,
  `res_budget` varchar(255) DEFAULT NULL,
  `res_type` varchar(255) DEFAULT NULL,
  `res_meet` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `research`
--

INSERT INTO `research` (`res_id`, `use_id`, `res_name`, `res_title1`, `res_fname1`, `res_lname1`, `res_title2`, `res_fname2`, `res_lname2`, `res_title3`, `res_fname3`, `res_lname3`, `res_title4`, `res_fname4`, `res_lname4`, `res_title5`, `res_fname5`, `res_lname5`, `res_date`, `res_publish`, `res_capital`, `res_budget`, `res_type`, `res_meet`) VALUES
(2, 14, 'การวิจัยและพัฒนานวัตกรรมผลิตภัณฑ์สาคูจังหวัดพัทลุงสู่เชิงพานิชย์', 'ผศ.', 'กลอยใจ', 'ครุฑจ้อน', '', '', '', '', '', '', '', '', '', '', '', '', '30/9/2567', '-', '-', '1,613,000', 'ผลงานวิจัยของอาจารผู้รับผิดชอบหลักสูตร นับรวมผลงาน5ปีย้อนหลัง', '-'),
(3, 14, 'การพัฒนาสื่อมัลติมีเดียเรื่องกายบริหารมณีเวชสำหรับการจัดการความรู้', 'ผศ.', 'นิธิพร', 'วรรณโสภณ', '', '', '', '', '', '', '', '', '', '', '', '', '30/12/2564', '-', 'งบประมาณเงินรายได้', '70,000', 'ผลงานวิจัยของอาจารผู้รับผิดชอบหลักสูตร นับรวมผลงาน5ปีย้อนหลัง', '-');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(20) NOT NULL,
  `course_id` int(11) NOT NULL,
  `use_id` int(20) NOT NULL,
  `section_name` varchar(255) DEFAULT NULL,
  `section_num` varchar(255) DEFAULT NULL,
  `section_year` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `serv_id` int(20) NOT NULL,
  `use_id` int(200) NOT NULL,
  `serv_name` varchar(255) DEFAULT NULL,
  `serv_pic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`serv_id`, `use_id`, `serv_name`, `serv_pic`) VALUES
(7, 14, 'โครงการ\"ค่ายคอมพิวเตอร์ ระดับประถมศึกษา ปีที่3-4 โรงเรียนเทศบาลวัดชัยชุมพล\" วันที่ 19-20 มิ.ย.67', ''),
(8, 14, 'โครงการ อบรมค่ายคอมพิวเตอร์ โรงเรียนทุ่งสง วันที่6-7 มกราคม 2567', ''),
(9, 14, 'กรรมการผู้ทรงคุณวุฒิการแข่งขันวิชาการด้านคอมพิวเตอร์ ระดับเทศบาล วันที่ 19 ก.พ. 2567', '');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teac_id` int(20) NOT NULL,
  `use_id` int(20) NOT NULL,
  `teac_position` varchar(255) DEFAULT NULL,
  `teac_status` varchar(255) DEFAULT NULL,
  `teac_qualification` varchar(255) DEFAULT NULL,
  `teac_gradute` varchar(255) DEFAULT NULL,
  `teac_branch` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teac_id`, `use_id`, `teac_position`, `teac_status`, `teac_qualification`, `teac_gradute`, `teac_branch`) VALUES
(4, 14, 'ผศ.ดร.', 'อาจารย์ผู้สอน,อาจารย์ประจำหลักสูตร', 'วท.ม.', NULL, 'การจัดการเทคโนโลยีสารสนเทศ'),
(5, 15, 'ผศ.ดร.', 'อาจารย์ผู้สอน,อาจารย์ประจำหลักสูตร', 'วท.ม.', NULL, 'การจัดการเทคโนโลยีสารสนเทศ'),
(6, 16, 'อาจารย์', 'อาจารย์ผู้สอน,อาจารย์ประจำหลักสูตร', 'Ph.D.  วท.ม. บธ.บ.', NULL, 'Computer Sciences เทคโนโลยีสารสนเทศ คอมพิวเตอร์ธุรกิจ');

-- --------------------------------------------------------

--
-- Table structure for table `term`
--

CREATE TABLE `term` (
  `term_id` int(20) NOT NULL,
  `term_year` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `term`
--

INSERT INTO `term` (`term_id`, `term_year`) VALUES
(1, '1/68'),
(2, '2/68'),
(3, '3/68'),
(4, '1/69'),
(5, '2/69'),
(6, '3/69'),
(7, '1/70'),
(8, '2/70'),
(9, '3/70'),
(10, '1/71'),
(11, '2/71'),
(12, '3/71'),
(13, '1/72'),
(14, '2/72'),
(15, '3/72\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `use_id` int(20) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `use_title` varchar(20) DEFAULT NULL,
  `use_fname` varchar(50) DEFAULT NULL,
  `use_lname` varchar(50) DEFAULT NULL,
  `salt_use` varchar(256) NOT NULL,
  `use_role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`use_id`, `username`, `password`, `use_title`, `use_fname`, `use_lname`, `salt_use`, `use_role`) VALUES
(10, 'ketdanai', '$2y$10$XaDEwFfzyHEKsfhdBOj7yepA5svSFMnXcKdzvKJ70Dhbf/j39D9pO', 'นาย', 'เกตน์ดนัย', 'คำปล้อง', '', 'user'),
(11, 'admin', '$2y$10$moSyuu5.D6lyWywd/B01W.EVbAOFSoUyuTMISXUTNTnXiIOf8aAzW', 'นาย', 'แอดมิน', 'แอดมิน', '', 'admin'),
(12, 'seel', '$2y$10$uu./B.0ywWriN6a6O44p1.cfbM3XG0TCanung/y25VWS7eMLAmNvW', 'นาย', 'ศีล', 'ศีล', '', 'user'),
(13, 'sal', '$2y$10$UK92.ym84dE3Nc4U2M0Om.EU0zrA/ISzfqBAUZCnuN/3eWniCm2dO', 'นาย', 'ศาล', 'รักกมล', '', 'user'),
(14, 'kloyjai', '$2y$10$Eq61d1P1djVdVDJWWHzXxuh1IXufzS6hZSXVIkIMrrJY0fANShr7a', 'ผศ.', 'กลอยใจ', 'ครุฑจ้อน', '', 'user'),
(15, 'nitiporn', '$2y$10$A3du.Wer24Rwxzv36HH9ZeEkKZSV8q7KjFV3ubxSg4NaZ9eOI7XP.', 'ผ.ศ.', 'นิติพร', 'วรรณโสภณ', '', 'user'),
(16, 'chariya', '$2y$10$q.hBnj0rHKMUPPpHn6z80e/fG.tpCA2194BB5hqozsB1Euihh2RIm', 'ดร.', 'ชริยา', 'นนทกาญจน์', '', 'user'),
(17, 'romchat', '$2y$10$8vzSFNcBK5N/8SiCBEb6n.NGW0u0yk3R0Zumo6ZrZZatOpsSkGyZO', 'นาย', 'ั่ร่มฉัตร', 'boonamphon', '', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`art_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `categorycourse`
--
ALTER TABLE `categorycourse`
  ADD PRIMARY KEY (`categorycourse_id`);

--
-- Indexes for table `clo`
--
ALTER TABLE `clo`
  ADD PRIMARY KEY (`clo_id`),
  ADD KEY `plo_id` (`plo_id`,`course_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `use_id` (`use_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `categorycourse_id` (`categorycourse_id`);

--
-- Indexes for table `development`
--
ALTER TABLE `development`
  ADD PRIMARY KEY (`dev_id`),
  ADD KEY `use_id` (`use_id`,`section_id1`);

--
-- Indexes for table `laboratory`
--
ALTER TABLE `laboratory`
  ADD PRIMARY KEY (`lab_id`);

--
-- Indexes for table `opencourse`
--
ALTER TABLE `opencourse`
  ADD PRIMARY KEY (`opencourse_id`),
  ADD KEY `course_id` (`course_id`,`use_id`,`section_id`),
  ADD KEY `term_id` (`term_id`);

--
-- Indexes for table `plo`
--
ALTER TABLE `plo`
  ADD PRIMARY KEY (`plo_id`);

--
-- Indexes for table `research`
--
ALTER TABLE `research`
  ADD PRIMARY KEY (`res_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `use_id` (`use_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`serv_id`),
  ADD KEY `use_id` (`use_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teac_id`),
  ADD KEY `use_id` (`use_id`);

--
-- Indexes for table `term`
--
ALTER TABLE `term`
  ADD PRIMARY KEY (`term_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`use_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `art_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categorycourse`
--
ALTER TABLE `categorycourse`
  MODIFY `categorycourse_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `clo`
--
ALTER TABLE `clo`
  MODIFY `clo_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `development`
--
ALTER TABLE `development`
  MODIFY `dev_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `laboratory`
--
ALTER TABLE `laboratory`
  MODIFY `lab_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `opencourse`
--
ALTER TABLE `opencourse`
  MODIFY `opencourse_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `plo`
--
ALTER TABLE `plo`
  MODIFY `plo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `research`
--
ALTER TABLE `research`
  MODIFY `res_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `serv_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teac_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `term`
--
ALTER TABLE `term`
  MODIFY `term_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `use_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
