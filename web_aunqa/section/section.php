<?php
// ไฟล์: section/section.php
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

$user_options = [];
if ($is_admin) {
    $sql_users = "SELECT use_id, use_title, use_fname, use_lname FROM users ORDER BY use_fname ASC";
    if ($res_users = $link->query($sql_users)) {
        while ($u_row = $res_users->fetch_assoc()) { $user_options[] = $u_row; }
    }
}

$sql = "SELECT s.*, u.use_title, u.use_fname, u.use_lname 
        FROM section s 
        LEFT JOIN users u ON s.use_id = u.use_id 
        ORDER BY s.section_year DESC, s.section_name ASC";
$result = $link->query($sql);

$success_message = $_SESSION["sec_success"] ?? null;
unset($_SESSION["sec_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กลุ่มเรียน | AUN-QA System</title>
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
        
        .main-header { background-color: var(--accent-blue); color: white; padding: 15px 20px; z-index: 1000; position: relative; }

        .sidebar { width: 250px; background-color: var(--bg-dark); min-height: 100vh; flex-shrink: 0; }
        .sidebar .nav-link { 
            color: #f8f9fa; padding: 12px 15px; margin-bottom: 1px; 
            font-size: 1rem; background-color: var(--sidebar-link-bg); 
            text-decoration: none; display: block; font-weight: 300; font-family: 'Kanit';
        }
        .sidebar .nav-link:hover { background-color: #495057; }
        .sidebar .nav-link.active { background-color: var(--sidebar-active); color: #212529; font-weight: 600; }

        .content { flex-grow: 1; padding: 40px; background-color: white; }
        h1 { font-family: 'Kanit'; font-weight: 600; color: var(--primary-navy); }

        .table-custom thead th { 
            background-color: #f8f9fa; color: var(--primary-navy); 
            border: 1px solid #dee2e6; text-align: center; padding: 12px;
        }
        /* บังคับ Text ในตารางเป็นสีดำ */
        .table-custom td { 
            border: 1px solid #dee2e6; 
            vertical-align: middle; 
            padding: 12px; 
            color: #000000 !important; 
        }
        .table-custom td .fw-bold, .table-custom td small { color: #000000 !important; }

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
        .selected-row { background-color: #f0f7ff !important; }
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
            <a class="nav-link" href="../opencourse/opencourse.php">รายวิชาเปิด</a>
            <a class="nav-link active" href="section.php">กลุ่มเรียน</a>
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
            <h2 class="fw-bold">รายงานข้อมูลกลุ่มเรียน (Sections Report)</h2>
            <p>พิมพ์โดย: <?php echo $full_name; ?> | วันที่: <?php echo date('d/m/Y'); ?></p>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>กลุ่มเรียน</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-primary shadow-sm px-4" onclick='openSectionModal("add")'>
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มกลุ่มเรียน
                </button>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-printer me-1"></i> ออกรายงาน
                    </button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-2"></i>พิมพ์ทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printSelectedSections()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success border-0 shadow-sm no-print"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-custom align-middle" id="sectionTable">
                    <thead>
                        <tr>
                            <th class="no-print" style="width: 45px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>กลุ่มเรียน</th>
                            <th class="text-center" style="width: 15%;">จำนวนนักศึกษา</th>
                            <th class="text-center" style="width: 15%;">ปีการศึกษา</th>
                            <th>อาจารย์ที่ปรึกษา</th>
                            <th class="text-center no-print" style="width: 120px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $is_owner = ($row['use_id'] == $logged_in_user_id);
                            ?>
                            <tr class="data-row">
                                <td class="text-center no-print">
                                    <input type="checkbox" class="row-checkbox form-check-input">
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['section_name']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($row['section_num']); ?> คน</td>
                                <td class="text-center"><?php echo htmlspecialchars($row['section_year']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['use_title'] . $row['use_fname'] . " " . $row['use_lname']); ?>
                                    <?php if($is_owner): ?> 
                                        <small class="ms-1 no-print">(คุณ)</small> 
                                    <?php endif; ?>
                                </td>
                                <td class="text-center no-print">
                                    <?php if ($is_admin || $is_owner): ?>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-light btn-sm border" onclick='openSectionModal("edit", <?php echo json_encode($row); ?>)'>
                                                <i class="bi bi-pencil-fill text-warning"></i>
                                            </button>
                                            <a href="process_section.php?delete=<?php echo $row['section_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ยืนยันการลบข้อมูล?')">
                                                <i class="bi bi-trash-fill text-danger"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <i class="bi bi-lock-fill text-muted" title="สิทธิ์เฉพาะเจ้าของ"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูลกลุ่มเรียน</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="process_section.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" id="modalHeaderColor">
                <h5 class="modal-title fw-bold" id="sectionModalTitle">กรอกข้อมูลกลุ่มเรียน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" id="secAction" value="add">
                <input type="hidden" name="section_id" id="secId">

                <div class="mb-4">
                    <label class="form-label fw-bold">อาจารย์ที่ปรึกษา</label>
                    <?php if ($is_admin): ?>
                        <select name="use_id" id="secUserId" class="form-select border-primary" required>
                            <option value="">-- เลือกอาจารย์ที่ปรึกษา --</option>
                            <?php foreach($user_options as $opt): ?>
                                <option value="<?php echo $opt['use_id']; ?>">
                                    <?php echo $opt['use_title'].$opt['use_fname']." ".$opt['use_lname']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control bg-light" value="<?php echo $full_name; ?>" readonly>
                        <input type="hidden" name="use_id" id="secUserId" value="<?php echo $logged_in_user_id; ?>">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อกลุ่มเรียน</label>
                    <input type="text" name="section_name" id="secName" class="form-control" placeholder="เช่น IT65-1" required>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">จำนวนนักศึกษา (คน)</label>
                        <input type="number" name="section_num" id="secNum" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ปีการศึกษาที่เข้าเรียน</label>
                        <input type="text" name="section_year" id="secYear" class="form-control" placeholder="เช่น 2565" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" id="btnSubmit">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sectionModal = new bootstrap.Modal(document.getElementById('sectionModal'));

    function openSectionModal(mode, data = null) {
        const actionInput = document.getElementById('secAction');
        const titleText = document.getElementById('sectionModalTitle');
        const header = document.getElementById('modalHeaderColor');
        const btnSubmit = document.getElementById('btnSubmit');
        const userIdSelect = document.getElementById('secUserId');

        actionInput.value = mode;

        if (mode === 'add') {
            titleText.innerText = 'เพิ่มกลุ่มเรียนใหม่';
            header.className = 'modal-header bg-primary text-white';
            btnSubmit.className = 'btn btn-primary px-5 fw-bold shadow-sm';
            document.getElementById('secId').value = '';
            document.getElementById('secName').value = '';
            document.getElementById('secNum').value = '';
            document.getElementById('secYear').value = '';
            if(userIdSelect.tagName === 'SELECT') userIdSelect.value = '';
        } else {
            titleText.innerText = 'แก้ไขข้อมูลกลุ่มเรียน';
            header.className = 'modal-header bg-warning';
            btnSubmit.className = 'btn btn-warning px-5 fw-bold shadow-sm';
            document.getElementById('secId').value = data.section_id;
            document.getElementById('secName').value = data.section_name;
            document.getElementById('secNum').value = data.section_num;
            document.getElementById('secYear').value = data.section_year;
            if(userIdSelect.tagName === 'SELECT') userIdSelect.value = data.use_id;
        }
        sectionModal.show();
    }

    // ระบบเลือกพิมพ์
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

    function printSelectedSections() {
        const rows = document.querySelectorAll('.data-row');
        let hasSelection = false;
        rows.forEach(row => {
            if (!row.querySelector('.row-checkbox').checked) row.classList.add('d-none-print');
            else { hasSelection = true; row.classList.remove('d-none-print'); }
        });
        
        if (!hasSelection) { alert("กรุณาเลือกกลุ่มเรียนที่ต้องการพิมพ์"); return; }
        
        window.print();
        setTimeout(() => { rows.forEach(row => row.classList.remove('d-none-print')); }, 500);
    }
</script>
</body>
</html>