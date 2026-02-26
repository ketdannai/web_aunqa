<?php
// ไฟล์: opencourse/opencourse.php
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

// SQL ดึงข้อมูลวิชาเปิดสอน JOIN ตารางที่เกี่ยวข้อง
$opencourses = [];
$sql = "SELECT o.*, c.course_name, c.course_code, 
        CONCAT(u.use_title, u.use_fname, ' ', u.use_lname) AS teacher_name, 
        s.section_name, t.term_year 
        FROM opencourse o
        LEFT JOIN course c ON o.course_id = c.course_id
        LEFT JOIN users u ON o.use_id = u.use_id
        LEFT JOIN section s ON o.section_id = s.section_id
        LEFT JOIN term t ON o.term_id = t.term_id
        ORDER BY t.term_year DESC, c.course_code ASC";

if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) { $opencourses[] = $row; }
    $result->free();
}

// ข้อมูลสำหรับ Dropdown
$courses_res = $link->query("SELECT course_id, course_name, course_code FROM course ORDER BY course_code ASC");
$sections_res = $link->query("SELECT section_id, section_name FROM section ORDER BY section_name ASC");
$terms_res = $link->query("SELECT term_id, term_year FROM term ORDER BY term_year DESC");
$users_res = $link->query("SELECT use_id, use_title, use_fname, use_lname FROM users ORDER BY use_fname ASC");

$success_message = $_SESSION["oc_success"] ?? null;
unset($_SESSION["oc_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายวิชาเปิดสอน | AUN-QA System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    
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

        /* Table */
        .table-custom thead th { 
            background-color: #f8f9fa; color: var(--primary-navy); 
            border: 1px solid #dee2e6; text-align: center; padding: 12px;
        }
        .table-custom td { border: 1px solid #dee2e6; vertical-align: middle; padding: 12px; }

        /* Print Logic */
        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .modal, .form-check-input, .ts-wrapper { display: none !important; }
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
            <a class="nav-link" href="../course/course.php">รายวิชา</a>
            <a class="nav-link active" href="opencourse.php">รายวิชาเปิด</a>
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
            <h2 class="fw-bold">รายงานข้อมูลรายวิชาที่เปิดสอน</h2>
            <p>ระบบบริหารจัดการคุณภาพ AUN-QA (พิมพ์เมื่อวันที่: <?php echo date('d/m/Y'); ?>)</p>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>รายวิชาเปิดสอน</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-primary shadow-sm" onclick='openOCModal("add")'>
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มวิชาเปิดใหม่
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
                <table class="table table-bordered table-custom align-middle" id="ocTable">
                    <thead>
                        <tr>
                            <th class="no-print" style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>รหัสวิชา - รายวิชา</th>
                            <th style="width: 10%;">กลุ่ม</th>
                            <th style="width: 15%;">ปีการศึกษา/เทอม</th>
                            <th>ผู้สอน</th>
                            <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($opencourses as $oc): 
                            $is_owner = ($oc['use_id'] == $logged_in_user_id);
                        ?>
                        <tr class="data-row">
                            <td class="text-center no-print">
                                <input type="checkbox" class="row-checkbox form-check-input">
                            </td>
                            <td>
                                <strong class="text-dark"><?php echo htmlspecialchars($oc['course_code']); ?></strong> 
                                <span class="ms-1"><?php echo htmlspecialchars($oc['course_name']); ?></span>
                            </td>
                            <td class="text-center"><?php echo htmlspecialchars($oc['section_name']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($oc['term_year']); ?></td>
                            <td><?php echo htmlspecialchars($oc['teacher_name']); ?></td>
                            <td class="text-center no-print">
                                <?php if ($is_admin || $is_owner): ?>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-light btn-sm border" onclick='openOCModal("edit", <?php echo json_encode($oc); ?>)'>
                                            <i class="bi bi-pencil-fill text-warning"></i>
                                        </button>
                                        <a href="process_opencourse.php?delete=<?php echo $oc['opencourse_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ลบรายการนี้?')">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <i class="bi bi-lock-fill text-muted"></i>
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

<div class="modal fade" id="ocModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="process_opencourse.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" id="modalHeaderColor">
                <h5 class="modal-title fw-bold" id="ocModalTitle">จัดการวิชาเปิดสอน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" id="ocAction">
                <input type="hidden" name="opencourse_id" id="ocId">

                <div class="mb-3">
                    <label class="form-label fw-bold">อาจารย์ผู้สอน</label>
                    <?php if ($is_admin): ?>
                        <select name="use_id" id="ocUserId" class="form-select">
                            <option value="">-- เลือกอาจารย์ --</option>
                            <?php $users_res->data_seek(0); while ($u = $users_res->fetch_assoc()): ?>
                                <option value="<?php echo $u['use_id']; ?>"><?php echo $u['use_title'].$u['use_fname']." ".$u['use_lname']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control bg-light" value="<?php echo $full_name; ?>" readonly>
                        <input type="hidden" name="use_id" value="<?php echo $logged_in_user_id; ?>">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">เลือกรายวิชา</label>
                    <select name="course_id" id="ocCourse" class="form-select" required>
                        <option value="">-- พิมพ์เพื่อค้นหา --</option>
                        <?php $courses_res->data_seek(0); while ($c = $courses_res->fetch_assoc()): ?>
                            <option value="<?php echo $c['course_id']; ?>"><?php echo $c['course_code']." ".$c['course_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">กลุ่มเรียน</label>
                        <select name="section_id[]" id="ocSection" class="form-select" multiple required>
                            <?php $sections_res->data_seek(0); while ($s = $sections_res->fetch_assoc()): ?>
                                <option value="<?php echo $s['section_id']; ?>"><?php echo $s['section_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ปีการศึกษา/เทอม</label>
                        <select name="term_id" id="ocTermId" class="form-select" required>
                            <option value="">-- เลือกเทอม --</option>
                            <?php $terms_res->data_seek(0); while ($t = $terms_res->fetch_assoc()): ?>
                                <option value="<?php echo $t['term_id']; ?>"><?php echo $t['term_year']; ?></option>
                            <?php endwhile; ?>
                        </select>
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
    // Tom Select Configuration
    const courseSelect = new TomSelect("#ocCourse", { create: false, sortField: { field: "text", direction: "asc"} });
    const sectionSelect = new TomSelect("#ocSection", { plugins: ['remove_button'], persist: false, create: false });

    const ocModal = new bootstrap.Modal(document.getElementById('ocModal'));

    function openOCModal(mode, data = null) {
        const actionInput = document.getElementById('ocAction');
        const titleText = document.getElementById('ocModalTitle');
        const header = document.getElementById('modalHeaderColor');
        const btnSubmit = document.getElementById('btnSubmit');

        courseSelect.clear();
        sectionSelect.clear();
        actionInput.value = mode;

        if (mode === 'add') {
            titleText.innerText = 'เพิ่มรายวิชาเปิดใหม่';
            header.className = 'modal-header bg-primary text-white';
            btnSubmit.className = 'btn btn-primary px-5 fw-bold';
            document.getElementById('ocId').value = '';
            document.getElementById('ocTermId').value = '';
            if (document.getElementById('ocUserId')) document.getElementById('ocUserId').value = '';
        } else {
            titleText.innerText = 'แก้ไขวิชาเปิดสอน';
            header.className = 'modal-header bg-warning';
            btnSubmit.className = 'btn btn-warning px-5 fw-bold';
            document.getElementById('ocId').value = data.opencourse_id;
            courseSelect.setValue(data.course_id);
            sectionSelect.setValue([data.section_id]);
            document.getElementById('ocTermId').value = data.term_id;
            if (document.getElementById('ocUserId')) document.getElementById('ocUserId').value = data.use_id;
        }
        ocModal.show();
    }

    // Print Selection Logic
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
        if (!hasSelection) { alert("กรุณาเลือกวิชาที่ต้องการพิมพ์"); return; }
        window.print();
        setTimeout(() => { rows.forEach(row => row.classList.remove('d-none-print')); }, 500);
    }
</script>
</body>
</html>