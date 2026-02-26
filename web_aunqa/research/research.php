<?php
// ไฟล์: research/research.php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/login.php");
    exit;
}

$logged_in_user_id = $_SESSION["use_id"];
$full_name = htmlspecialchars(($_SESSION["use_title"] ?? '') . ($_SESSION["use_fname"] ?? '') . " " . ($_SESSION["use_lname"] ?? ''));
$user_role = $_SESSION["use_role"] ?? 'user';
$is_admin = ($user_role == 'admin');

// ดึงข้อมูลวิจัยทั้งหมด
$research_list = [];
$sql = "SELECT * FROM research ORDER BY res_id DESC";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) { $research_list[] = $row; }
    $result->free();
}

$success_message = $_SESSION["res_success"] ?? null;
unset($_SESSION["res_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>งานวิจัย | AUN-QA System</title>
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

        .table-custom thead th { 
            background-color: #f8f9fa; color: var(--primary-navy); 
            border: 1px solid #dee2e6; text-align: center; padding: 12px; font-family: 'Kanit';
        }
        .table-custom td { border: 1px solid #dee2e6; vertical-align: middle; padding: 12px; color: #000; }
        
        .budget-text { color: #000; font-weight: 600; font-family: 'monospace'; }
        .author-list { font-size: 0.85rem; color: #000; line-height: 1.4; }

        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .modal, .form-check-input { display: none !important; }
            body { background: white; }
            .content { padding: 0 !important; }
            .table-custom { border: 1px solid black !important; width: 100%; }
            .table-custom th, .table-custom td { border: 1px solid black !important; color: black !important; font-size: 11px; }
            tr.d-none-print { display: none !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
        }
        .print-header { display: none; }
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
            <a class="nav-link" href="../section/section.php">กลุ่มเรียน</a>
            <a class="nav-link" href="../article/article.php">บทความ</a>
            <a class="nav-link active" href="research.php">วิจัย</a>
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
            <h2 class="fw-bold">รายงานข้อมูลงานวิจัยวิชาการ</h2>
            <p>พิมพ์โดย: <?php echo $full_name; ?> | วันที่: <?php echo date('d/m/Y'); ?></p>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>งานวิจัย</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-primary shadow-sm px-4" onclick="openResModal('add')">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มงานวิจัย
                </button>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle shadow-sm fw-bold" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-printer me-1"></i> ออกรายงาน
                    </button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-2"></i>พิมพ์ทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printSelectedResearch()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
                    </ul>
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
                <table class="table table-bordered table-custom align-middle" id="researchTable">
                    <thead>
                        <tr>
                            <th class="no-print" style="width: 45px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <th style="width: 25%;">ชื่องานวิจัย</th>
                            <th style="width: 20%;">คณะผู้ทำวิจัย</th>
                            <th style="width: 10%;">วันที่ตีพิมพ์</th>
                            <th style="width: 20%;">แหล่งเผยแพร่</th>
                            <th style="width: 15%;">แหล่งทุน / งบประมาณ</th>
                            <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($research_list) > 0): ?>
                            <?php foreach ($research_list as $res): 
                                $is_owner = ($res['use_id'] == $logged_in_user_id);
                                $authors = [];
                                for ($i = 1; $i <= 5; $i++) {
                                    if (!empty($res["res_fname$i"])) {
                                        $authors[] = htmlspecialchars(($res["res_title$i"] ?? '') . $res["res_fname$i"] . " " . $res["res_lname$i"]);
                                    }
                                }
                            ?>
                                <tr class="research-row">
                                    <td class="text-center no-print"><input type="checkbox" class="row-checkbox form-check-input"></td>
                                    <td class="fw-bold" style="color: #000;"><?php echo htmlspecialchars($res['res_name']); ?></td>
                                    <td class="author-list"><?php echo implode("<br>", $authors); ?></td>
                                    <td class="text-center small"><?php echo date('d/m/Y', strtotime($res['res_date'])); ?></td>
                                    <td class="small"><?php echo htmlspecialchars($res['res_meet'] ?: '-'); ?></td>
                                    <td>
                                        <div class="small text-muted">ทุน: <?php echo htmlspecialchars($res['res_capital'] ?: 'ไม่ระบุ'); ?></div>
                                        <div class="budget-text"><?php echo number_format((float)$res['res_budget'], 2); ?></div>
                                    </td>
                                    <td class="text-center no-print">
                                        <?php if ($is_admin || $is_owner): ?>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-light btn-sm border" onclick='openResModal("edit", <?php echo json_encode($res); ?>)'>
                                                    <i class="bi bi-pencil-fill text-warning"></i>
                                                </button>
                                                <a href="process_research.php?delete=<?php echo $res['res_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ลบข้อมูลวิจัยนี้?')">
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
                            <tr><td colspan="7" class="text-center py-4 text-muted">ยังไม่มีข้อมูลงานวิจัย</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="resModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form action="process_research.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="resModalTitle">กรอกข้อมูลงานวิจัย</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" id="resAction" value="add">
                <input type="hidden" name="res_id" id="resId">
                <input type="hidden" name="use_id" value="<?php echo $logged_in_user_id; ?>">

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-bold">ชื่องานวิจัย</label>
                        <input type="text" name="res_name" id="resName" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">ประเภท</label>
                        <input type="text" name="res_type" id="resType" class="form-control" placeholder="เช่น วิจัยในชั้นเรียน" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">วันที่เผยแพร่</label>
                        <input type="date" name="res_date" id="resDate" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">แหล่งเผยแพร่ / การประชุม</label>
                        <input type="text" name="res_meet" id="resMeet" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">แหล่งทุน</label>
                        <input type="text" name="res_capital" id="resCapital" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">งบประมาณ (บาท)</label>
                        <input type="number" step="0.01" name="res_budget" id="resBudget" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">แหล่งตีพิมพ์สำรอง</label>
                        <input type="text" name="res_publish" id="resPublish" class="form-control">
                    </div>
                </div>

                <div class="bg-light p-3 rounded-3 border">
                    <h6 class="fw-bold mb-3"><i class="bi bi-people-fill me-2"></i>รายชื่อผู้วิจัย (สูงสุด 5 ท่าน)</h6>
                    <?php for($i=1; $i<=5; $i++): ?>
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <input type="text" name="res_title<?php echo $i; ?>" id="res_title<?php echo $i; ?>" class="form-control form-control-sm" placeholder="คำนำหน้า">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="res_fname<?php echo $i; ?>" id="res_fname<?php echo $i; ?>" class="form-control form-control-sm" placeholder="ชื่อจริง">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="res_lname<?php echo $i; ?>" id="res_lname<?php echo $i; ?>" class="form-control form-control-sm" placeholder="นามสกุล">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">บันทึกข้อมูลวิจัย</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const resModal = new bootstrap.Modal(document.getElementById('resModal'));
    
    function openResModal(mode, data = null) {
        document.getElementById('resAction').value = mode;
        const title = document.getElementById('resModalTitle');
        
        if (mode === 'add') {
            title.innerText = 'เพิ่มข้อมูลงานวิจัยใหม่';
            document.getElementById('resId').value = '';
            document.getElementById('resName').value = '';
            document.getElementById('resType').value = '';
            document.getElementById('resDate').value = '';
            document.getElementById('resMeet').value = '';
            document.getElementById('resPublish').value = '';
            document.getElementById('resCapital').value = '';
            document.getElementById('resBudget').value = '';
            for(let i=1; i<=5; i++) {
                document.getElementById('res_title'+i).value = '';
                document.getElementById('res_fname'+i).value = '';
                document.getElementById('res_lname'+i).value = '';
            }
        } else {
            title.innerText = 'แก้ไขข้อมูลงานวิจัย';
            document.getElementById('resId').value = data.res_id;
            document.getElementById('resName').value = data.res_name;
            document.getElementById('resType').value = data.res_type;
            document.getElementById('resDate').value = data.res_date;
            document.getElementById('resMeet').value = data.res_meet;
            document.getElementById('resPublish').value = data.res_publish;
            document.getElementById('resCapital').value = data.res_capital;
            document.getElementById('resBudget').value = data.res_budget;
            for(let i=1; i<=5; i++) {
                document.getElementById('res_title'+i).value = data['res_title'+i] || '';
                document.getElementById('res_fname'+i).value = data['res_fname'+i] || '';
                document.getElementById('res_lname'+i).value = data['res_lname'+i] || '';
            }
        }
        resModal.show();
    }

    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = this.checked;
            cb.closest('tr').style.backgroundColor = this.checked ? '#f8f9ff' : '';
        });
    });

    function printSelectedResearch() {
        const rows = document.querySelectorAll('.research-row');
        let selected = false;
        
        rows.forEach(row => {
            const checkbox = row.querySelector('.row-checkbox');
            if (!checkbox.checked) {
                row.classList.add('d-none-print');
            } else {
                selected = true;
                row.classList.remove('d-none-print');
            }
        });

        if (!selected) {
            alert("กรุณาเลือกงานวิจัยที่ต้องการพิมพ์");
            return;
        }

        window.print();

        setTimeout(() => {
            rows.forEach(row => row.classList.remove('d-none-print'));
        }, 500);
    }
</script>
</body>
</html>