<?php
// ... (ส่วน PHP Logic ด้านบนคงเดิมทั้งหมด) ...
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

// Logic ตรวจสอบและดึงข้อมูลคงเดิม
$has_teacher_profile = false;
$check_sql = "SELECT COUNT(*) FROM teachers WHERE use_id = ?";
if ($check_stmt = $link->prepare($check_sql)) {
    $check_stmt->bind_param("i", $logged_in_user_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();
    if ($count > 0) $has_teacher_profile = true;
}

$user_options = [];
if ($is_admin) {
    $sql_users = "SELECT use_id, use_title, use_fname, use_lname FROM users 
                  WHERE use_id NOT IN (SELECT use_id FROM teachers)
                  ORDER BY use_fname ASC";
    if ($res_users = $link->query($sql_users)) {
        while ($u_row = $res_users->fetch_assoc()) { $user_options[] = $u_row; }
    }
}

$can_add_teacher = $is_admin || !$has_teacher_profile;

$teachers = [];
$sql = "SELECT t.*, u.use_title, u.use_fname, u.use_lname 
        FROM teachers t 
        INNER JOIN users u ON t.use_id = u.use_id 
        ORDER BY t.teac_id ASC";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) { $teachers[] = $row; }
}

$success_message = $_SESSION["teacher_success"] ?? null;
unset($_SESSION["teacher_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลอาจารย์ | AUN-QA System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --bg-dark: #222222;
            --accent-blue: #007bff;
            --sidebar-link-bg: #343a40;
            --sidebar-active: #cce0ff;
        }
        body { font-family: 'Sarabun', sans-serif; background-color: #f4f6f9; margin: 0; }
        
        .main-header { background-color: var(--accent-blue); color: white; padding: 15px 20px; z-index: 1000; position: relative; }

        .sidebar { width: 250px; background-color: var(--bg-dark); min-height: 100vh; flex-shrink: 0; }
        .sidebar .nav-link { 
            color: #f8f9fa; padding: 12px 15px; margin-bottom: 1px; 
            font-size: 1rem; background-color: var(--sidebar-link-bg); 
            text-decoration: none; display: block; font-weight: 300; font-family: 'Kanit';
        }
        .sidebar .nav-link.active { background-color: var(--sidebar-active); color: #212529; font-weight: 600; }

        .content { flex-grow: 1; padding: 40px; background-color: white; }
        h1 { font-family: 'Kanit'; font-weight: 600; color: #003566; }

        /* การตั้งค่าตาราง */
        .table-teacher thead th { 
            background-color: #f8f9fa; color: #003566; 
            border: 1px solid #dee2e6; text-align: center; padding: 12px;
        }
        .table-teacher td { border: 1px solid #dee2e6; vertical-align: middle; padding: 12px; }

        /* CSS สำหรับการพิมพ์รายงาน */
        @media print {
            /* ซ่อนส่วนที่ไม่ต้องการในรายงาน */
            .sidebar, .main-header, .no-print, .btn, .alert, .modal, .form-check-input { display: none !important; }
            
            body { background: white !important; }
            .content { padding: 0 !important; width: 100%; }
            .table-teacher { border: 1px solid #000 !important; width: 100% !important; }
            .table-teacher th, .table-teacher td { border: 1px solid #000 !important; color: black !important; }
            
            /* ซ่อนแถวที่ไม่ได้ถูกเลือก */
            tr.d-none-print { display: none !important; }
            
            .print-header { display: block !important; text-align: center; margin-bottom: 30px; }
        }
        .print-header { display: none; }
        .selected-row { background-color: #fff9db !important; } /* ไฮไลท์แถวที่เลือกบนหน้าเว็บ */
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
            <a class="nav-link active" href="teacher.php">อาจารย์</a>
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
        <div class="print-header">
            <h2 style="font-weight: bold;">รายงานข้อมูลอาจารย์ประจำหลักสูตร</h2>
            <p>ระบบบริหารจัดการคุณภาพ AUN-QA (ข้อมูล ณ วันที่ <?php echo date('d/m/Y'); ?>)</p>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>ข้อมูลอาจารย์</h1>
            <div class="d-flex gap-2">
                <?php if ($can_add_teacher): ?>
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                        <i class="bi bi-plus-circle me-1"></i> เพิ่มข้อมูล
                    </button>
                <?php endif; ?>
                
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

        <div class="card border-0 shadow-sm p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-teacher align-middle" id="teacherTable">
                    <thead>
                        <tr>
                            <th class="no-print" style="width: 40px; text-align: center;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>ตำแหน่งวิชาการ</th>
                            <th>วุฒิการศึกษา</th>
                            <th>สาขา</th>
                            <th>สถานะ</th>
                            <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $t): 
                            $is_owner = ($t['use_id'] == $logged_in_user_id);
                        ?>
                        <tr class="teacher-row">
                            <td class="text-center no-print">
                                <input type="checkbox" class="row-checkbox form-check-input">
                            </td>
                            <td class="fw-bold"><?php echo htmlspecialchars($t['use_title'].$t['use_fname']." ".$t['use_lname']); ?></td>
                            <td><?php echo htmlspecialchars($t['teac_position']); ?></td>
                            <td><?php echo htmlspecialchars($t['teac_qualification']); ?></td>
                            <td><?php echo htmlspecialchars($t['teac_branch']); ?></td>
                            <td><?php echo htmlspecialchars($t['teac_status']); ?></td>
                            <td class="text-center no-print">
                                <?php if ($is_admin || $is_owner): ?>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-light btn-sm border" onclick='openEditTeacherModal(<?php echo json_encode($t); ?>)'>
                                            <i class="bi bi-pencil-fill text-warning"></i>
                                        </button>
                                        <a href="delete_teacher.php?id=<?php echo $t['teac_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ลบข้อมูล?')">
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

<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="insert_teacher.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">กรอกข้อมูลอาจารย์</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่ออาจารย์</label>
                    <?php if ($is_admin): ?>
                        <select name="user_id" class="form-select border-primary" required>
                            <option value="">-- กรุณาเลือกรายชื่อ --</option>
                            <?php foreach($user_options as $opt): ?>
                                <option value="<?php echo $opt['use_id']; ?>"><?php echo $opt['use_title'].$opt['use_fname']." ".$opt['use_lname']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control bg-light" value="<?php echo $full_name; ?>" readonly>
                        <input type="hidden" name="user_id" value="<?php echo $logged_in_user_id; ?>">
                    <?php endif; ?>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">ตำแหน่งวิชาการ</label><input type="text" name="teac_position" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">วุฒิการศึกษา</label><input type="text" name="teac_qualification" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">สาขา</label><input type="text" name="teac_branch" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">สถานะ</label><input type="text" name="teac_status" class="form-control" required></div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="submit" class="btn btn-primary px-5 fw-bold">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="update_teacher.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">แก้ไขข้อมูลอาจารย์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="teac_id" id="edit_teac_id">
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อ-นามสกุล</label>
                    <input type="text" id="edit_full_name" class="form-control bg-light" readonly>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">ตำแหน่งวิชาการ</label><input type="text" name="teac_position" id="edit_teac_position" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">วุฒิการศึกษา</label><input type="text" name="teac_qualification" id="edit_teac_qualification" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">สาขา</label><input type="text" name="teac_branch" id="edit_teac_branch" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">สถานะ</label><input type="text" name="teac_status" id="edit_teac_status" class="form-control" required></div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="submit" class="btn btn-warning px-5 fw-bold">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ฟังก์ชันเปิดแก้ไข
function openEditTeacherModal(data) {
    document.getElementById('edit_teac_id').value = data.teac_id;
    document.getElementById('edit_full_name').value = data.use_title + data.use_fname + ' ' + data.use_lname;
    document.getElementById('edit_teac_position').value = data.teac_position;
    document.getElementById('edit_teac_qualification').value = data.teac_qualification;
    document.getElementById('edit_teac_branch').value = data.teac_branch;
    document.getElementById('edit_teac_status').value = data.teac_status;
    new bootstrap.Modal(document.getElementById('editTeacherModal')).show();
}

// ระบบเลือกทั้งหมด
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        toggleRowHighlight(cb);
    });
});

// ฟังก์ชันไฮไลท์แถวเมื่อเลือก
document.querySelectorAll('.row-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        toggleRowHighlight(this);
    });
});

function toggleRowHighlight(cb) {
    if(cb.checked) cb.closest('tr').classList.add('selected-row');
    else cb.closest('tr').classList.remove('selected-row');
}

// ฟังก์ชันหัวใจสำคัญ: พิมพ์เฉพาะที่เลือก
function printSelectedOnly() {
    const rows = document.querySelectorAll('.teacher-row');
    let hasSelection = false;

    rows.forEach(row => {
        const checkbox = row.querySelector('.row-checkbox');
        if (!checkbox.checked) {
            row.classList.add('d-none-print'); // เพิ่ม Class เพื่อซ่อนใน @media print
        } else {
            hasSelection = true;
            row.classList.remove('d-none-print');
        }
    });

    if (!hasSelection) {
        alert("กรุณาเลือกข้อมูลอย่างน้อย 1 รายการเพื่อพิมพ์รายงาน");
        return;
    }

    window.print();

    // หลังจากสั่งพิมพ์ (หรือยกเลิก) ให้เอา Class ซ่อนออก เพื่อให้หน้าเว็บกลับมาปกติ
    setTimeout(() => {
        rows.forEach(row => row.classList.remove('d-none-print'));
    }, 500);
}
</script>
</body>
</html>