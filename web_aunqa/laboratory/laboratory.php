<?php
// ไฟล์: laboratory/laboratory.php
session_start();
require_once "../config.php";

// 1. ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/login.php");
    exit;
}

// 2. ดึงข้อมูลจาก Session
$logged_in_user_id = $_SESSION["use_id"];
$full_name = htmlspecialchars(($_SESSION["use_title"] ?? '') . ($_SESSION["use_fname"] ?? '') . " " . ($_SESSION["use_lname"] ?? ''));
$user_role = $_SESSION["use_role"] ?? 'user';
$is_admin = ($user_role == 'admin');

// 2.1 ดึงรายชื่อ User สำหรับ Admin
$user_options = [];
if ($is_admin) {
    $sql_users = "SELECT use_id, use_title, use_fname, use_lname FROM users ORDER BY use_fname ASC";
    if ($res_users = $link->query($sql_users)) {
        while ($u_row = $res_users->fetch_assoc()) {
            $user_options[] = $u_row;
        }
    }
}

// 3. ดึงข้อมูลห้องปฏิบัติการ
$labs = [];
$sql = "SELECT l.*, u.use_title, u.use_fname, u.use_lname 
        FROM laboratory l
        LEFT JOIN users u ON l.use_id = u.use_id
        ORDER BY l.lab_id DESC";

if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $labs[] = $row;
    }
    $result->free();
}

$success_message = $_SESSION["lab_success"] ?? null;
unset($_SESSION["lab_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ห้องปฏิบัติการ | AUN-QA System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { --bg-dark: #222222; --accent-blue: #007bff; --sidebar-link-bg: #343a40; --sidebar-active: #cce0ff; --primary-navy: #003566; }
        body { font-family: 'Sarabun', sans-serif; background-color: #f4f6f9; margin: 0; }
        .main-header { background-color: var(--accent-blue); color: white; padding: 15px 20px; z-index: 1000; position: relative; }
        .sidebar { width: 250px; background-color: var(--bg-dark); min-height: 100vh; flex-shrink: 0; }
        .sidebar .nav-link { color: #f8f9fa; padding: 12px 15px; margin-bottom: 1px; font-size: 1rem; background-color: var(--sidebar-link-bg); text-decoration: none; display: block; font-weight: 300; font-family: 'Kanit'; }
        .sidebar .nav-link:hover { background-color: #495057; }
        .sidebar .nav-link.active { background-color: var(--sidebar-active); color: #212529; font-weight: 600; }
        .content { flex-grow: 1; padding: 40px; background-color: white; min-height: 100vh; }
        h1 { font-family: 'Kanit'; font-weight: 600; color: var(--primary-navy); }

        /* Search Box Styles */
        .search-container .input-group-text { background-color: #fff; border-right: none; }
        .search-container .form-control { border-left: none; }
        .search-container .form-control:focus { box-shadow: none; border-color: #dee2e6; }

        .table-custom thead th { background-color: #f8f9fa; color: var(--primary-navy); border: 1px solid #dee2e6; text-align: center; padding: 12px; font-family: 'Kanit'; }
        .table-custom td { border: 1px solid #dee2e6; vertical-align: middle; padding: 12px; }
        .img-preview-sm { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #ddd; transition: 0.2s; }
        .pic-edit-wrapper { position: relative; display: inline-block; margin: 5px; }
        .btn-del-pic { position: absolute; top: -5px; right: -5px; padding: 0px 6px; font-size: 12px; border-radius: 50%; background: #dc3545; color: white; border: 1px solid white; cursor: pointer; }

        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .modal, .form-check-input, .search-container { display: none !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
            .content { padding: 0; width: 100%; }
        }
        .print-header { display: none; }
    </style>
</head>
<body>

    <div class="main-header no-print">
        <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0">ยินดีต้อนรับ: <?php echo $full_name; ?></p>
            <a href="../login/logout.php" class="btn btn-sm btn-light fw-bold text-primary">Logout</a>
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
                <a class="nav-link" href="../section/section.php">กลุ่มเรียน</a>
                <a class="nav-link" href="../article/article.php">บทความ</a>
                <a class="nav-link" href="../research/research.php">วิจัย</a>
                <a class="nav-link" href="../development/development.php">พัฒนานักศึกษา</a>
                <a class="nav-link" href="../plo/plo.php">PLO</a>
                <a class="nav-link" href="../clo/clo.php">CLO</a>
                <a class="nav-link" href="../services/services.php">งานบริการวิชาการ</a>
                <a class="nav-link active" href="laboratory.php">ห้องปฏิบัติการ</a>
                <?php if ($is_admin): ?>
                    <a class="nav-link" href="../manage_users.php"><i class="bi bi-people-fill me-2"></i> จัดการผู้ใช้งาน</a>
                <?php endif; ?>
            </div>
        </nav>

        <main class="content">
            <div class="print-header">
                <h2>รายงานข้อมูลห้องปฏิบัติการ</h2>
                <hr>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h1>ห้องปฏิบัติการ</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary shadow-sm" onclick="openLabModal('add')">
                        <i class="bi bi-plus-circle me-1"></i> เพิ่มห้องปฏิบัติการ
                    </button>
                    <button class="btn btn-success shadow-sm" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> ออกรายงาน
                    </button>
                </div>
            </div>

            <div class="row mb-3 no-print">
                <div class="col-md-4 ms-auto">
                    <div class="input-group shadow-sm search-container">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="ค้นหาชื่อห้อง, ครุภัณฑ์, หรือผู้รับผิดชอบ...">
                    </div>
                </div>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show no-print">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm p-3">
                <div class="table-responsive">
                    <table class="table table-custom align-middle" id="labTable">
                        <thead>
                            <tr>
                                <th class="no-print" style="width: 45px;"><input type="checkbox" id="selectAll"></th>
                                <th>ชื่อห้อง / ผู้รับผิดชอบ</th>
                                <th>ครุภัณฑ์สำคัญ</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-center">รูปภาพ</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center no-print">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($labs as $lab): 
                                $is_owner = ($lab['use_id'] == $logged_in_user_id);
                            ?>
                            <tr class="lab-row">
                                <td class="text-center no-print"><input type="checkbox" class="row-checkbox"></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($lab['lab_name']); ?></strong><br>
                                    <small class="text-muted">โดย: <?php echo htmlspecialchars($lab['use_fname']." ".$lab['use_lname']); ?></small>
                                </td>
                                <td class="small"><?php echo nl2br(htmlspecialchars($lab['lab_durable'])); ?></td>
                                <td class="text-center fw-bold"><?php echo $lab['lab_num']; ?></td>
                                <td class="text-center">
                                    <?php if (!empty($lab['lab_pic'])): 
                                        $pics = explode(',', $lab['lab_pic']); ?>
                                        <img src="../uploads_piclab/<?php echo trim($pics[0]); ?>" class="img-preview-sm" onclick="window.open(this.src)">
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo ($lab['lab_status'] == 'พร้อมใช้งาน') ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $lab['lab_status']; ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <?php if ($is_admin || $is_owner): ?>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-warning" onclick='openLabModal("edit", <?php echo json_encode($lab); ?>)'><i class="bi bi-pencil"></i></button>
                                            <a href="process_lab.php?delete=<?php echo $lab['lab_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบข้อมูล?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    <?php else: ?><i class="bi bi-lock-fill text-muted"></i><?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="labModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="process_lab.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" id="modalTitle">ข้อมูลห้องปฏิบัติการ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="lab_id" id="lab_id">
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label">ชื่อห้องปฏิบัติการ</label><input type="text" name="lab_name" id="lab_name" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label">จำนวนครุภัณฑ์</label><input type="number" name="lab_num" id="lab_num" class="form-control" required min="0"></div>
                            <div class="col-12"><label class="form-label">รายละเอียดครุภัณฑ์สำคัญ</label><textarea name="lab_durable" id="lab_durable" class="form-control" rows="3"></textarea></div>
                            <div class="col-md-6"><label class="form-label text-dark">ผู้รับผิดชอบ</label>
                                <select name="use_id" id="use_id" class="form-select">
                                    <?php if($is_admin): foreach($user_options as $u): ?>
                                        <option value="<?php echo $u['use_id']; ?>"><?php echo $u['use_fname']." ".$u['use_lname']; ?></option>
                                    <?php endforeach; else: ?>
                                        <option value="<?php echo $logged_in_user_id; ?>"><?php echo $full_name; ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label text-dark">สถานะ</label>
                                <select name="lab_status" id="lab_status" class="form-select">
                                    <option value="พร้อมใช้งาน">พร้อมใช้งาน</option>
                                    <option value="ไม่พร้อมใช้งาน/ซ่อมแซม">ไม่พร้อมใช้งาน/ซ่อมแซม</option>
                                </select>
                            </div>
                            <div class="col-12"><label class="form-label text-dark">อัปโหลดรูปภาพใหม่</label><input type="file" name="lab_pics[]" class="form-control" multiple accept="image/*">
                                <div id="editPicsPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary px-4">บันทึกข้อมูล</button></div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const labModalObj = new bootstrap.Modal(document.getElementById('labModal'));

        // ระบบค้นหา Real-time
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const value = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#labTable tbody tr.lab-row');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
            });
        });

        function openLabModal(mode, data = null) {
            document.getElementById('formAction').value = mode;
            document.getElementById('editPicsPreview').innerHTML = '';
            if (mode === 'add') {
                document.getElementById('modalTitle').innerText = 'เพิ่มข้อมูลห้องปฏิบัติการ';
                document.getElementById('lab_id').value = '';
                document.getElementById('lab_name').value = '';
                document.getElementById('lab_num').value = '';
                document.getElementById('lab_durable').value = '';
                document.getElementById('lab_status').value = 'พร้อมใช้งาน';
            } else {
                document.getElementById('modalTitle').innerText = 'แก้ไขข้อมูลห้องปฏิบัติการ';
                document.getElementById('lab_id').value = data.lab_id;
                document.getElementById('lab_name').value = data.lab_name;
                document.getElementById('lab_num').value = data.lab_num;
                document.getElementById('lab_durable').value = data.lab_durable;
                document.getElementById('lab_status').value = data.lab_status;
                document.getElementById('use_id').value = data.use_id;
                if (data.lab_pic) {
                    data.lab_pic.split(',').forEach(p => {
                        if(p.trim() != ""){
                            const div = document.createElement('div');
                            div.className = 'pic-edit-wrapper';
                            div.innerHTML = `<img src="../uploads_piclab/${p.trim()}" class="img-preview-sm">
                                             <button type="button" class="btn-del-pic" onclick="deletePic(${data.lab_id},'${p.trim()}',this)">×</button>`;
                            document.getElementById('editPicsPreview').appendChild(div);
                        }
                    });
                }
            }
            labModalObj.show();
        }

        function deletePic(labId, fileName, btn) {
            if (confirm('ลบรูปภาพนี้?')) {
                fetch(`process_lab.php?delete_single_pic=1&lab_id=${labId}&file=${fileName}`)
                .then(res => res.json()).then(data => { if(data.success) btn.parentElement.remove(); });
            }
        }

        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>