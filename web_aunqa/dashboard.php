<?php
// เริ่มต้น Session
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบอยู่หรือไม่
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login/login.php");
    exit;
}

// สร้างชื่อเต็มสำหรับแสดงผล
$full_name = htmlspecialchars(($_SESSION["use_title"] ?? '') . ($_SESSION["use_fname"] ?? '') . " " . ($_SESSION["use_lname"] ?? ''));
$user_role = $_SESSION["use_role"] ?? 'user';
$is_admin = ($user_role == 'admin');

// ฟังก์ชันสำหรับแสดง Active Class
function is_active($target_file)
{
    return (basename($_SERVER['PHP_SELF']) == $target_file) ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก | AUN-QA System Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-dark: #222222;
            --accent-blue: #007bff;
            --sidebar-link-bg: #343a40;
            --sidebar-active: #cce0ff;
            --primary-navy: #003566;
        }

        /* ใช้ Sarabun เป็นหลักเหมือนหน้า Article */
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }

        /* Header แบบเดียวกับ Article */
        .main-header {
            background-color: var(--accent-blue);
            color: white;
            padding: 15px 20px;
            z-index: 1000;
            position: relative;
        }

        /* Sidebar แบบเดียวกับ Article */
        .sidebar {
            width: 250px;
            background-color: var(--bg-dark);
            min-height: 100vh;
            flex-shrink: 0;
        }

        .sidebar .nav-link {
            color: #f8f9fa;
            padding: 12px 15px;
            margin-bottom: 1px;
            font-size: 1rem;
            background-color: var(--sidebar-link-bg);
            text-decoration: none;
            display: block;
            font-weight: 300;
            font-family: 'Kanit';
            /* หัวข้อเมนูใช้ Kanit */
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
            color: white;
        }

        .sidebar .nav-link.active {
            background-color: var(--sidebar-active);
            color: #212529;
            font-weight: 600;
        }

        /* Content Area */
        .content {
            flex-grow: 1;
            padding: 40px;
            background-color: white;
            min-height: 100vh;
        }

        h1 {
            font-family: 'Kanit';
            font-weight: 600;
            color: var(--primary-navy);
        }

        h5 {
            font-family: 'Kanit';
            color: #6c757d;
            margin-bottom: 30px;
        }

        .info-box {
            background-color: #e9f5ff;
            border-left: 5px solid var(--accent-blue);
            padding: 25px;
            border-radius: 8px;
            line-height: 1.8;
            font-size: 1.05rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: justify;
        }
    </style>
</head>

<body>

    <div class="main-header">
        <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0">ยินดีต้อนรับ: <?php echo $full_name; ?></p>
            <a href="login/logout.php" class="btn btn-sm btn-light fw-bold">logout</a>
        </div>
    </div>

    <div class="d-flex">
        <nav class="sidebar">
            <div class="nav flex-column">
                <a class="nav-link <?php echo is_active('dashboard.php'); ?>" href="dashboard.php">หน้าแรก</a>
                <a class="nav-link" href="profile/profile.php">ข้อมูลส่วนตัว</a>
                <a class="nav-link" href="teacher/teacher.php">อาจารย์</a>
                <a class="nav-link" href="course/course.php">รายวิชา</a>
                <a class="nav-link" href="opencourse/opencourse.php">รายวิชาเปิด</a>
                <a class="nav-link" href="section/section.php">กลุ่มเรียน</a>
                <a class="nav-link" href="article/article.php">บทความ</a>
                <a class="nav-link" href="research/research.php">วิจัย</a>
                <a class="nav-link" href="development/development.php">พัฒนานักศึกษา</a>
                <a class="nav-link" href="plo/plo.php">PLO</a>
                <a class="nav-link" href="clo/clo.php">CLO</a>
                <a class="nav-link" href="services/services.php">งานบริการวิชาการ</a>
                <a class="nav-link" href="laboratory/laboratory.php">ห้องปฏิบัติการ</a>

                <?php if ($is_admin): ?>
                    <a class="nav-link <?php echo is_active('manage_users.php'); ?>" href="manage_users.php">
                        <i class="bi bi-people-fill me-2"></i> จัดการผู้ใช้งาน
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <main class="content">
            <h1>ยินดีต้อนรับเข้าสู่เว็บไซต์</h1>
            <h5>ระบบสารสนเทศ เพื่อการจัดการข้อมูลการประกันคุณภาพหลักสูตร AUN</h5>

            <div class="info-box">
                <p class="mb-0">
                    เว็บไซต์ระบบสารสนเทศเพื่อการจัดการข้อมูลการประกันคุณภาพหลักสูตรตามเกณฑ์ AUN-QA (ASEAN University Network – Quality Assurance) จัดทำขึ้นเพื่อเป็นกลไกสำคัญในการสนับสนุน พัฒนา และยกระดับกระบวนการดำเนินงานด้านการประกันคุณภาพการศึกษาในระดับหลักสูตรให้มีความเป็นระบบ มาตรฐาน และสอดคล้องกับแนวปฏิบัติในระดับสากล โดยมุ่งเน้นให้การบริหารจัดการข้อมูลเป็นไปอย่างมีประสิทธิภาพ โปร่งใส ตรวจสอบได้ และตอบสนองต่อการเปลี่ยนแปลงของบริบททางการศึกษาในยุคดิจิทัล ระบบดังกล่าวได้รับการออกแบบให้เป็นศูนย์กลางในการรวบรวม จัดเก็บ และบริหารจัดการข้อมูลที่เกี่ยวข้องกับทุกองค์ประกอบตามเกณฑ์ AUN-QA ไม่ว่าจะเป็นข้อมูลด้านผลลัพธ์การเรียนรู้ (Learning Outcomes) โครงสร้างหลักสูตร การจัดการเรียนการสอน การประเมินผล ทรัพยากรสนับสนุน ตลอดจนข้อมูลการปรับปรุงและพัฒนาอย่างต่อเนื่อง (Continuous Improvement) โดยข้อมูลทั้งหมดจะถูกจัดเก็บอย่างเป็นระบบ สามารถสืบค้น เรียกดู วิเคราะห์ และสร้างรายงานประกอบการประเมินได้อย่างสะดวกและรวดเร็ว นอกจากนี้ ระบบยังช่วยลดภาระงานด้านเอกสาร เสริมสร้างความคล่องตัวในการจัดเตรียมรายงานการประเมินตนเอง (Self-Assessment Report: SAR) และสนับสนุนการทำงานร่วมกันของคณาจารย์ บุคลากร และผู้บริหาร ผ่านเครื่องมือที่เอื้อต่อการติดตามความก้าวหน้า การกำหนดตัวชี้วัด และการประเมินผลในแต่ละรอบการดำเนินงาน ทั้งยังช่วยให้สามารถตรวจสอบย้อนกลับข้อมูลได้อย่างชัดเจน สร้างความเชื่อมั่นต่อกระบวนการประกันคุณภาพทั้งภายในและภายนอกสถาบัน ด้วยการประยุกต์ใช้เทคโนโลยีสารสนเทศอย่างมีประสิทธิภาพ เว็บไซต์ระบบสารสนเทศนี้จึงมิได้เป็นเพียงแค่คลังข้อมูล หากแต่เป็นเครื่องมือเชิงกลยุทธ์ที่สนับสนุนการตัดสินใจของผู้บริหาร ส่งเสริมวัฒนธรรมคุณภาพภายในองค์กร และเป็นรากฐานสำคัญในการขับเคลื่อนการพัฒนาหลักสูตรให้มีคุณภาพอย่างต่อเนื่อง มั่นคง และยั่งยืน อันสอดรับกับมาตรฐานการศึกษาระดับอาเซียนและระดับสากลอย่างแท้จริง
                </p>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>