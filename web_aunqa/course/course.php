<?php
// ไฟล์: course/course.php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/login.php");
    exit;
}

$logged_in_user_id = $_SESSION["use_id"];
$user_role = $_SESSION["use_role"] ?? 'user';
$is_admin = ($user_role == 'admin');
$full_name = htmlspecialchars(($_SESSION["use_title"] ?? '') . ($_SESSION["use_fname"] ?? '') . " " . ($_SESSION["use_lname"] ?? ''));

// SQL เรียงตามหมวดจากน้อยไปมาก (1, 2, 3...) และตามด้วยรหัสวิชา
$courses = [];
$sql = "SELECT c.*, cat.category_name, sub.categorycourse_name
        FROM course c
        LEFT JOIN category cat ON c.category_id = cat.category_id
        LEFT JOIN categorycourse sub ON c.categorycourse_id = sub.categorycourse_id
        ORDER BY cat.category_id ASC, c.course_code ASC";

if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) { $courses[] = $row; }
    $result->free();
}

$categories = $link->query("SELECT * FROM category ORDER BY category_id ASC");
$sub_categories = $link->query("SELECT * FROM categorycourse ORDER BY categorycourse_name ASC");

$success_message = $_SESSION["course_success"] ?? null;
unset($_SESSION["course_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรายวิชา | AUN-QA System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --bg-dark: #222222;
            --accent-blue: #007bff;
            --sidebar-link-bg: #343a40;
            --sidebar-active: #cce0ff;
            --primary-navy: #003566;
        }
        body { font-family: 'Sarabun', sans-serif; background-color: #f4f6f9; margin: 0; }
        
        /* Header */
        .main-header { background-color: var(--accent-blue); color: white; padding: 15px 20px; z-index: 1000; position: relative; }

        /* Sidebar */
        .sidebar { width: 250px; background-color: var(--bg-dark); min-height: 100vh; flex-shrink: 0; }
        .sidebar .nav-link { 
            color: #f8f9fa; padding: 12px 15px; margin-bottom: 1px; 
            font-size: 1rem; background-color: var(--sidebar-link-bg); 
            text-decoration: none; display: block; font-weight: 300; font-family: 'Kanit';
        }
        .sidebar .nav-link:hover { background-color: #495057; }
        .sidebar .nav-link.active { background-color: var(--sidebar-active); color: #212529; font-weight: 600; }

        /* Content */
        .content { flex-grow: 1; padding: 40px; background-color: white; }
        h1 { font-family: 'Kanit'; font-weight: 600; color: var(--primary-navy); }

        /* Table Style */
        .table-custom thead th { 
            background-color: #f8f9fa; color: var(--primary-navy); 
            border: 1px solid #dee2e6; text-align: center; padding: 12px;
        }
        .table-custom td { border: 1px solid #dee2e6; vertical-align: middle; padding: 12px; }

        /* Print Logic */
        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .modal, .form-check-input { display: none !important; }
            body { background: white; }
            .content { padding: 0; }
            .table-custom { border: 1px solid black !important; width: 100%; }
            .table-custom th, .table-custom td { border: 1px solid black !important; color: black !important; }
            tr.d-none-print { display: none !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
        }
        .print-header { display: none; }
        .selected-row { background-color: #fff9db !important; }
    </style>
</head>
<body>

<div class="main-header no-print">
    <div class="d-flex justify-content-between align-items-center">
        <p class="mb-0">ยินดีต้อนรับ: <?php echo $full_name; ?></p>
        <a href="../login/logout.php" class="btn btn-sm btn-light fw-bold">logout</a>
    </div>
</div>

<div class="d-flex">
    <nav class="sidebar no-print">
        <div class="nav flex-column">
            <a class="nav-link" href="../dashboard.php">หน้าแรก</a>
            <a class="nav-link" href="../profile/profile.php">ข้อมูลส่วนตัว</a>
            <a class="nav-link" href="../teacher/teacher.php">อาจารย์</a>
            <a class="nav-link active" href="course.php">รายวิชา</a>
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
        <div class="print-header">
            <h2 class="fw-bold">รายงานข้อมูลรายวิชาในหลักสูตร</h2>
            <p>ระบบบริหารจัดการคุณภาพ AUN-QA (พิมพ์เมื่อวันที่: <?php echo date('d/m/Y'); ?>)</p>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>จัดการรายวิชา</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-primary shadow-sm" onclick='openCourseModal("add")'>
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มรายวิชา
                </button>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-printer me-1"></i> ออกรายงาน
                    </button>
                    <ul class="dropdown-menu shadow">
                        <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-2"></i>พิมพ์ทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printSelectedOnly()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success no-print border-0 shadow-sm"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-custom align-middle" id="courseTable">
                    <thead>
                        <tr>
                            <th class="no-print" style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th style="width: 120px;">รหัสวิชา</th>
                            <th>ชื่อวิชา</th>
                            <th>หมวดวิชา</th>
                            <th>หมวดย่อย</th>
                            <th style="width: 100px;">หน่วยกิต</th>
                            <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): 
                            $is_owner = (isset($c['use_id']) && $c['use_id'] == $logged_in_user_id);
                        ?>
                        <tr class="data-row">
                            <td class="text-center no-print">
                                <input type="checkbox" class="row-checkbox form-check-input">
                            </td>
                            <td class="text-center fw-bold text-dark"><?php echo htmlspecialchars($c['course_code']); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($c['course_name']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($c['category_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($c['categorycourse_name'] ?? '-'); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($c['course_credit']); ?></td>
                            <td class="text-center no-print">
                                <?php if ($is_admin || $is_owner): ?>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-light btn-sm border" onclick='openCourseModal("edit", <?php echo json_encode($c); ?>)'>
                                            <i class="bi bi-pencil-fill text-warning"></i>
                                        </button>
                                        <a href="process_course.php?delete=<?php echo $c['course_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ลบรายวิชานี้?')">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="courseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="process_course.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" id="modalHeaderColor">
                <h5 class="modal-title fw-bold" id="courseModalTitle">ข้อมูลรายวิชา</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" id="courseAction">
                <input type="hidden" name="course_id" id="courseId">
                <input type="hidden" name="use_id" value="<?php echo $logged_in_user_id; ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">รหัสวิชา</label>
                        <input type="text" name="course_code" id="courseCode" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">ชื่อวิชา</label>
                        <input type="text" name="course_name" id="courseName" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">หมวดวิชา</label>
                        <select name="category_id" id="courseCat" class="form-select" required>
                            <option value="">-- เลือกหมวด --</option>
                            <?php $categories->data_seek(0); while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">หมวดย่อย</label>
                        <select name="categorycourse_id" id="courseSubCat" class="form-select" required>
                            <option value="">-- เลือกหมวดย่อย --</option>
                            <?php $sub_categories->data_seek(0); while ($sub = $sub_categories->fetch_assoc()): ?>
                                <option value="<?php echo $sub['categorycourse_id']; ?>"><?php echo $sub['categorycourse_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">หน่วยกิต</label>
                        <input type="text" name="course_credit" id="courseCredit" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="submit" class="btn btn-primary px-5 fw-bold" id="btnSubmit">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const bModal = new bootstrap.Modal(document.getElementById('courseModal'));

function openCourseModal(mode, data = null) {
    const actionInput = document.getElementById('courseAction');
    const titleText = document.getElementById('courseModalTitle');
    const header = document.getElementById('modalHeaderColor');
    const btnSubmit = document.getElementById('btnSubmit');

    actionInput.value = mode;
    if (mode === 'add') {
        titleText.innerText = 'เพิ่มรายวิชาใหม่';
        header.className = 'modal-header bg-primary text-white';
        btnSubmit.className = 'btn btn-primary px-5 fw-bold';
        btnSubmit.innerText = 'บันทึกข้อมูล';
        document.getElementById('courseId').value = '';
        document.getElementById('courseCode').value = '';
        document.getElementById('courseName').value = '';
        document.getElementById('courseCat').value = '';
        document.getElementById('courseSubCat').value = '';
        document.getElementById('courseCredit').value = '';
    } else {
        titleText.innerText = 'แก้ไขข้อมูลรายวิชา';
        header.className = 'modal-header bg-warning';
        btnSubmit.className = 'btn btn-warning px-5 fw-bold';
        btnSubmit.innerText = 'บันทึกการแก้ไข';
        document.getElementById('courseId').value = data.course_id;
        document.getElementById('courseCode').value = data.course_code;
        document.getElementById('courseName').value = data.course_name;
        document.getElementById('courseCat').value = data.category_id;
        document.getElementById('courseSubCat').value = data.categorycourse_id;
        document.getElementById('courseCredit').value = data.course_credit;
    }
    bModal.show();
}

// Logic พิมพ์เฉพาะที่เลือก
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('selected-row', this.checked);
    });
});

document.querySelectorAll('.row-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('selected-row', this.checked);
    });
});

function printSelectedOnly() {
    const rows = document.querySelectorAll('.data-row');
    let hasSelection = false;
    rows.forEach(row => {
        if (!row.querySelector('.row-checkbox').checked) row.classList.add('d-none-print');
        else { hasSelection = true; row.classList.remove('d-none-print'); }
    });
    if (!hasSelection) { alert("กรุณาเลือกรายวิชาอย่างน้อย 1 รายการ"); return; }
    window.print();
    setTimeout(() => { rows.forEach(row => row.classList.remove('d-none-print')); }, 500);
}
</script>
</body>
</html>