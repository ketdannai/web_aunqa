<?php
// ไฟล์: profile/profile.php
session_start();
require_once "../config.php";

// 1. ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/login.php");
    exit;
}

// 2. ดึงข้อมูลจาก Session มาแสดงผล
$title = htmlspecialchars($_SESSION["use_title"] ?? '');
$fname = htmlspecialchars($_SESSION["use_fname"] ?? '');
$lname = htmlspecialchars($_SESSION["use_lname"] ?? '');
$user_role = htmlspecialchars($_SESSION["use_role"] ?? 'user');
$full_name = htmlspecialchars(($_SESSION["use_title"] ?? '') . ($_SESSION["use_fname"] ?? '') . " " . ($_SESSION["use_lname"] ?? ''));
$is_admin = ($user_role == 'admin');

// 3. ดึงข้อความแจ้งเตือน
$success_message = $_SESSION["profile_success"] ?? null;
$error_message = $_SESSION["profile_error"] ?? null;
unset($_SESSION["profile_success"], $_SESSION["profile_error"]);

// ฟังก์ชันสำหรับเมนู Active
function is_active($target_file) {
    return (basename($_SERVER['PHP_SELF']) == $target_file) ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลส่วนตัว | AUN-QA System</title>
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

        body { 
            font-family: 'Sarabun', sans-serif; 
            background-color: #f4f6f9; 
            margin: 0; 
        }

        /* Main Header matching Article.php */
        .main-header { 
            background-color: var(--accent-blue); 
            color: white; 
            padding: 15px 20px; 
            z-index: 1000; 
            position: relative; 
        }

        /* Sidebar matching Article.php */
        .sidebar { 
            width: 250px; 
            background-color: var(--bg-dark); 
            min-height: 100vh; 
            flex-shrink: 0; 
        }
        .sidebar .nav-link { 
            color: #f8f9fa; padding: 12px 15px; margin-bottom: 1px; 
            font-size: 1rem; background-color: var(--sidebar-link-bg); 
            text-decoration: none; display: block; font-weight: 300; font-family: 'Kanit';
        }
        .sidebar .nav-link:hover { background-color: #495057; }
        .sidebar .nav-link.active { background-color: var(--sidebar-active); color: #212529; font-weight: 600; }

        /* Content Area Area */
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
        
        /* Card Style for Profile Form */
        .card-profile {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .form-label { font-weight: 600; font-family: 'Kanit'; color: #444; }
        .btn-update { 
            font-family: 'Kanit';
            font-weight: 500;
            padding: 10px 40px;
        }
    </style>
</head>
<body>

    <div class="main-header">
        <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0">ยินดีต้อนรับ: <?php echo $full_name; ?></p>
            <a href="../login/logout.php" class="btn btn-sm btn-light fw-bold">logout</a>
        </div>
    </div>

    <div class="d-flex">
        <nav class="sidebar">
            <div class="nav flex-column">
                <a class="nav-link" href="../dashboard.php">หน้าแรก</a>
                <a class="nav-link active" href="profile.php">ข้อมูลส่วนตัว</a>
                <a class="nav-link" href="../teacher/teacher.php">อาจารย์</a>
                <a class="nav-link" href="../course/course.php">รายวิชา</a>
                <a class="nav-link" href="../opencourse/opencourse.php">รายวิชาเปิด</a>
                <a class="nav-link" href="../section/section.php">กลุ่มเรียน</a>
                <a class="nav-link" href="../article/article.php">บทความ</a>
                <a class="nav-link" href="../research/research.php">วิจัย</a>
                <a class="nav-link" href="../development/development.php">พัฒนานักศึกษา</a>
                <a class="nav-link" href="../plo/plo.php">PLO</a> 
                <a class="nav-link" href="../clo/clo.php">CLO</a>
                <a class="nav-link" href="../services/services.php">งานบริการวิชาการ</a>
                <a class="nav-link" href="../laboratory/laboratory.php">ห้องปฏิบัติการ</a>
                <?php if ($is_admin): ?>
                    <a class="nav-link" href="../manage_users.php"><i class="bi bi-people-fill me-2"></i> จัดการผู้ใช้งาน</a>
                <?php endif; ?>
            </div>
        </nav>

        <main class="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>ข้อมูลส่วนตัว</h1>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card card-profile p-4">
                <div class="card-body">
                    <form action="update_profile.php" method="POST">
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label class="form-label">คำนำหน้า</label>
                                <input type="text" name="use_title" class="form-control" value="<?php echo $title; ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">ชื่อจริง</label>
                                <input type="text" name="use_fname" class="form-control" value="<?php echo $fname; ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">นามสกุล</label>
                                <input type="text" name="use_lname" class="form-control" value="<?php echo $lname; ?>" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 p-3 bg-light rounded-3 border">
                            <div>
                                <span class="fw-bold text-muted me-2 font-family-kanit">ระดับสิทธิ์:</span>
                                <span class="badge bg-primary px-3 py-2" style="font-size: 0.85rem;"><?php echo strtoupper($user_role); ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-update shadow-sm">
                                <i class="bi bi-save me-2"></i> บันทึกข้อมูลส่วนตัว
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>