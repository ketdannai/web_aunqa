<?php
// ไฟล์: development/development.php
session_start();
require_once "../config.php";

// 1. ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/login.php");
    exit;
}

// 2. ข้อมูลผู้ใช้ปัจจุบัน
$logged_in_user_id = $_SESSION["use_id"];
$user_role = $_SESSION["use_role"] ?? 'user';
$is_admin = ($user_role == 'admin');
$full_name = htmlspecialchars(($_SESSION["use_title"] ?? '') . ($_SESSION["use_fname"] ?? '') . " " . ($_SESSION["use_lname"] ?? ''));

// 3. ดึงข้อมูลพัฒนานักศึกษา พร้อม JOIN ข้อมูลที่เกี่ยวข้อง
$dev_list = [];
$sql = "SELECT d.*, u.use_title, u.use_fname, u.use_lname,
        s1.section_name as s1_name, s2.section_name as s2_name,
        s3.section_name as s3_name, s4.section_name as s4_name,
        s5.section_name as s5_name
        FROM development d
        LEFT JOIN users u ON d.use_id = u.use_id
        LEFT JOIN section s1 ON d.section_id1 = s1.section_id
        LEFT JOIN section s2 ON d.section_id2 = s2.section_id
        LEFT JOIN section s3 ON d.section_id3 = s3.section_id
        LEFT JOIN section s4 ON d.section_id4 = s4.section_id
        LEFT JOIN section s5 ON d.section_id5 = s5.section_id
        ORDER BY d.dev_id DESC";

if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $dev_list[] = $row;
    }
    $result->free();
}

// 4. ดึงรายชื่อกลุ่มเรียน (สร้าง Array เก็บไว้ก่อนเพื่อใช้ซ้ำใน Loop)
$sections_options = [];
$sections_res = $link->query("SELECT section_id, section_name FROM section ORDER BY section_name ASC");
while($s_row = $sections_res->fetch_assoc()){
    $sections_options[] = $s_row;
}

// 5. ดึงรายชื่อ User สำหรับ Admin
$user_options = [];
if ($is_admin) {
    $sql_users = "SELECT use_id, use_title, use_fname, use_lname FROM users ORDER BY use_fname ASC";
    if ($res_users = $link->query($sql_users)) {
        while ($u_row = $res_users->fetch_assoc()) {
            $user_options[] = $u_row;
        }
    }
}

$success_message = $_SESSION["dev_success"] ?? null;
unset($_SESSION["dev_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พัฒนานักศึกษา | AUN-QA System</title>
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
            --success-green: #28a745;
        }

        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; margin: 0; }
        .main-header { background-color: var(--accent-blue); color: white; padding: 15px 20px; position: relative; z-index: 1000; }

        .sidebar { width: 250px; background-color: var(--bg-dark); min-height: 100vh; flex-shrink: 0; }
        .sidebar .nav-link { 
            color: #f8f9fa; padding: 12px 15px; margin-bottom: 1px; 
            font-size: 1rem; background-color: var(--sidebar-link-bg); 
            text-decoration: none; display: block; font-family: 'Kanit'; font-weight: 300;
        }
        .sidebar .nav-link:hover { background-color: #495057; }
        .sidebar .nav-link.active { background-color: var(--sidebar-active); color: #212529; font-weight: 600; }

        .content { flex-grow: 1; padding: 40px; background-color: white; }
        h1 { font-family: 'Kanit'; font-weight: 600; color: var(--primary-navy); }

        .search-container .input-group-text { background-color: #fff; border-right: none; }
        .search-container .form-control { border-left: none; }
        .search-container .form-control:focus { box-shadow: none; border-color: #dee2e6; }

        .table-custom thead th { 
            background-color: #f8f9fa; color: var(--primary-navy); 
            border: 1px solid #dee2e6; text-align: center; padding: 12px; font-family: 'Kanit';
        }
        .table-custom td { border: 1px solid #dee2e6; vertical-align: middle; padding: 12px; }
        
        .img-preview { 
            width: 50px; height: 50px; object-fit: cover; 
            border-radius: 4px; border: 1px solid #ddd;
            cursor: pointer;
        }
        .sec-badge { background-color: #e9ecef; color: #495057; border-radius: 4px; padding: 2px 8px; font-size: 0.8rem; margin-bottom: 3px; display: inline-block; border: 1px solid #ced4da; }

        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .form-check-input, .dropdown-toggle, .search-container { display: none !important; }
            body { background: white; }
            .content { padding: 0 !important; }
            .table-custom th { background-color: #eee !important; color: black !important; }
            .table-custom td, .table-custom th { border: 1px solid #000 !important; font-size: 11px; }
            tr.d-none-print { display: none !important; }
            .print-only { display: block !important; text-align: center; margin-bottom: 20px; }
        }
        .print-only { display: none; }
    </style>
</head>
<body>

<div class="main-header no-print">
    <div class="d-flex justify-content-between align-items-center">
        <p class="mb-0">ยินดีต้อนรับ: <?php echo $full_name; ?></p>
        <a href="../login/logout.php" class="btn btn-sm btn-light fw-bold text-primary">logout</a>
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
            <a class="nav-link active" href="development.php">พัฒนานักศึกษา</a>
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
        <div class="print-only">
            <h2 class="fw-bold">รายงานข้อมูลโครงการพัฒนานักศึกษา</h2>
            <p>พิมพ์โดย: <?php echo $full_name; ?> | วันที่: <?php echo date('d/m/Y'); ?></p>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>พัฒนานักศึกษา</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-primary shadow-sm px-4" onclick="openDevModal('add')">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มโครงการใหม่
                </button>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle shadow-sm fw-bold" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-printer me-1"></i> ออกรายงาน
                    </button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-2"></i>พิมพ์ทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printSelectedDev()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row mb-3 no-print">
            <div class="col-md-4 ms-auto">
                <div class="input-group shadow-sm search-container">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="ค้นหาชื่อโครงการ, อาจารย์, หรือกลุ่มเรียน...">
                </div>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show no-print">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-custom align-middle" id="devTable">
                    <thead>
                        <tr>
                            <th class="no-print" style="width: 45px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <th style="width: 25%;">ชื่องาน / ผู้รับผิดชอบ</th>
                            <th style="width: 25%;">กลุ่มเรียน (จำนวนนักศึกษา)</th>
                            <th style="width: 20%;">วันเวลา / สถานที่</th>
                            <th class="text-center no-print" style="width: 10%;">รูปภาพ</th>
                            <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dev_list)): ?>
                            <?php foreach ($dev_list as $dev): 
                                $is_owner = ($dev['use_id'] == $logged_in_user_id);
                                $owner_name = htmlspecialchars(($dev['use_title']??'').$dev['use_fname']." ".$dev['use_lname']);
                                
                                $sec_badges = [];
                                for ($i = 1; $i <= 5; $i++) {
                                    if (!empty($dev["s{$i}_name"])) {
                                        $sec_badges[] = '<span class="sec-badge">' . htmlspecialchars($dev["s{$i}_name"]) . " (" . ($dev["count_id$i"] ?? 0) . " คน)</span>";
                                    }
                                }
                            ?>
                                <tr class="dev-row">
                                    <td class="text-center no-print"><input type="checkbox" class="row-checkbox form-check-input"></td>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($dev['dev_name']); ?></div>
                                        <div class="small text-muted mt-1">โดย: <?php echo $owner_name; ?></div>
                                    </td>
                                    <td><?php echo implode(" ", $sec_badges); ?></td>
                                    <td>
                                        <div class="small fw-bold"><i class="bi bi-calendar3 me-1"></i> <?php echo htmlspecialchars($dev['dev_date']); ?></div>
                                        <div class="small text-muted mt-1"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> <?php echo htmlspecialchars($dev['dev_at']); ?></div>
                                    </td>
                                    <td class="text-center no-print">
                                        <?php if(!empty($dev['dev_pic'])): 
                                            $pics = explode(',', $dev['dev_pic']);
                                        ?>
                                            <img src="../uploads_picdev/<?php echo trim($pics[0]); ?>" class="img-preview" onclick="window.open(this.src)">
                                            <?php if(count($pics) > 1): ?>
                                                <div style="font-size: 10px;" class="text-muted mt-1">+<?php echo count($pics)-1; ?> รูป</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center no-print">
                                        <?php if ($is_admin || $is_owner): ?>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-light btn-sm border" onclick='openDevModal("edit", <?php echo json_encode($dev); ?>)'>
                                                    <i class="bi bi-pencil-fill text-warning"></i>
                                                </button>
                                                <a href="process_development.php?delete=<?php echo $dev['dev_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ลบข้อมูลโครงการนี้?')">
                                                    <i class="bi bi-trash-fill text-danger"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <i class="bi bi-lock-fill text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">ยังไม่มีข้อมูลพัฒนานักศึกษา</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="devModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="process_development.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="devModalTitle">จัดการข้อมูลพัฒนานักศึกษา</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="devAction" value="add">
                    <input type="hidden" name="dev_id" id="devId">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">ชื่อโครงการ/กิจกรรม</label>
                            <input type="text" name="dev_name" id="devName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">วัน/เวลา</label>
                            <input type="text" name="dev_date" id="devDate" class="form-control" placeholder="เช่น 25 ก.พ. 67 เวลา 09:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">สถานที่</label>
                            <input type="text" name="dev_at" id="devAt" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">วัตถุประสงค์</label>
                            <textarea name="dev_obj" id="devObj" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="col-12 border-top pt-2 mt-3">
                            <label class="form-label fw-bold text-primary"><i class="bi bi-people-fill me-1"></i> กลุ่มเรียนและจำนวนนักศึกษา (สูงสุด 5 กลุ่ม)</label>
                        </div>
                        
                        <?php for($i=1; $i<=5; $i++): ?>
                        <div class="col-md-8">
                            <select name="section_id<?php echo $i; ?>" id="devSection<?php echo $i; ?>" class="form-select">
                                <option value="">-- เลือกกลุ่มเรียนที่ <?php echo $i; ?> --</option>
                                <?php foreach($sections_options as $sec): ?>
                                    <option value="<?php echo $sec['section_id']; ?>"><?php echo $sec['section_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="count_id<?php echo $i; ?>" id="devCount<?php echo $i; ?>" class="form-control" placeholder="จำนวนนักศึกษา">
                        </div>
                        <?php endfor; ?>

                        <div class="col-md-12 mt-3 border-top pt-2">
                            <label class="form-label fw-bold">ผู้รับผิดชอบโครงการ</label>
                            <select name="use_id" id="devUserId" class="form-select" required>
                                <?php if($is_admin): ?>
                                    <?php foreach($user_options as $u): ?>
                                        <option value="<?php echo $u['use_id']; ?>"><?php echo $u['use_title'].$u['use_fname']." ".$u['use_lname']; ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="<?php echo $logged_in_user_id; ?>"><?php echo $full_name; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">อัปโหลดรูปภาพกิจกรรม</label>
                            <input type="file" name="dev_pics[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">สามารถเลือกได้หลายรูปพร้อมกัน</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4">บันทึกข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const devModal = new bootstrap.Modal(document.getElementById('devModal'));
    
    // ระบบค้นหา Real-time
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#devTable tbody tr.dev-row');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
        });
    });

    function openDevModal(mode, data = null) {
        document.getElementById('devAction').value = mode;
        const title = document.getElementById('devModalTitle');
        
        if (mode === 'add') {
            title.innerText = 'เพิ่มข้อมูลพัฒนานักศึกษาใหม่';
            document.getElementById('devId').value = '';
            document.getElementById('devName').value = '';
            document.getElementById('devDate').value = '';
            document.getElementById('devAt').value = '';
            document.getElementById('devObj').value = '';
            if(document.getElementById('devUserId')) document.getElementById('devUserId').value = "<?php echo $logged_in_user_id; ?>";
            for(let i=1; i<=5; i++) {
                document.getElementById('devSection'+i).value = '';
                document.getElementById('devCount'+i).value = '';
            }
        } else {
            title.innerText = 'แก้ไขข้อมูลพัฒนานักศึกษา';
            document.getElementById('devId').value = data.dev_id;
            document.getElementById('devName').value = data.dev_name;
            document.getElementById('devDate').value = data.dev_date;
            document.getElementById('devAt').value = data.dev_at;
            document.getElementById('devObj').value = data.dev_obj;
            if(document.getElementById('devUserId')) document.getElementById('devUserId').value = data.use_id;
            for(let i=1; i<=5; i++) {
                document.getElementById('devSection'+i).value = data['section_id'+i] || '';
                document.getElementById('devCount'+i).value = data['count_id'+i] || '';
            }
        }
        devModal.show();
    }

    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function printSelectedDev() {
        const rows = document.querySelectorAll('.dev-row');
        let selected = false;
        rows.forEach(row => {
            if (!row.querySelector('.row-checkbox').checked) {
                row.classList.add('d-none-print');
            } else {
                selected = true;
                row.classList.remove('d-none-print');
            }
        });
        if (!selected) { alert("กรุณาเลือกโครงการที่ต้องการพิมพ์"); return; }
        window.print();
        setTimeout(() => { rows.forEach(row => row.classList.remove('d-none-print')); }, 500);
    }
</script>
</body>
</html>