<?php
// ไฟล์: plo/plo.php
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

// รายการตัวเลือกสำหรับ Bloom's Taxonomy
$bloom_options = ["R" => "Remember", "U" => "Understand", "Ap" => "Apply", "An" => "Analyze", "Ev" => "Evaluate", "C" => "Create"];

// 3. ดึงข้อมูล PLO ทั้งหมด
$plo_list = [];
$sql = "SELECT * FROM plo ORDER BY plo_id ASC";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $plo_list[] = $row;
    }
    $result->free();
}

$success_message = $_SESSION["plo_success"] ?? null;
unset($_SESSION["plo_success"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLO | AUN-QA System</title>
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
        .table-custom td { border: 1px solid #dee2e6; vertical-align: top; padding: 12px; }
        
        .bloom-badge { 
            display: inline-block; padding: 2px 8px; border-radius: 4px; 
            font-size: 0.85rem; font-weight: 600; margin: 1px;
            background-color: #e7f1ff; color: #007bff; border: 1px solid #b3d7ff;
        }

        .category-card { 
            cursor: pointer; border: 1px solid #dee2e6; border-radius: 8px; 
            padding: 15px; transition: 0.3s; background-color: #fff; height: 100%;
        }
        .category-card:hover { border-color: var(--accent-blue); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-check-input:checked + .category-label .category-card { 
            border-color: var(--accent-blue); background-color: #f0f7ff; outline: 2px solid var(--accent-blue);
        }

        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .form-check-input, .dropdown-toggle { display: none !important; }
            .content { padding: 0 !important; }
            .table-custom th { background-color: #eee !important; color: black !important; }
            .table-custom td, .table-custom th { border: 1px solid #000 !important; font-size: 11px; }
            .print-only { display: block !important; text-align: center; margin-bottom: 20px; }
            tr.d-none-print { display: none !important; }
        }
        .print-only { display: none; }
    </style>
</head>
<body>

<div class="main-header no-print">
    <div class="d-flex justify-content-between align-items-center">
        <p class="mb-0">ยินดีต้อนรับ: <?php echo $full_name; ?></p>
        <a href="../login/logout.php" class="btn btn-sm btn-light fw-bold shadow-sm">logout</a>
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
            <a class="nav-link active" href="plo.php">PLO</a>
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
            <h2 class="fw-bold">รายงาน Program Learning Outcomes (PLO)</h2>
            <p>พิมพ์โดย: <?php echo $full_name; ?> | วันที่: <?php echo date('d/m/Y'); ?></p>
            <hr>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1>จัดการ PLO</h1>
            <div class="d-flex gap-2">
                <?php if ($is_admin): ?>
                <button class="btn btn-primary shadow-sm px-4" onclick="openPloModal('add')">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่ม PLO ใหม่
                </button>
                <?php endif; ?>
                
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle shadow-sm fw-bold px-4" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-printer me-1"></i> ออกรายงาน
                    </button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-text me-2"></i>พิมพ์ทั้งหมด</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printSelectedPlo()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
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
                <table class="table table-bordered table-custom align-middle" id="ploTable">
                    <thead>
                        <tr>
                            <th class="no-print" style="width: 40px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <th style="width: 30%;">รายละเอียด PLO</th>
                            <th style="width: 25%;">ทักษะเฉพาะทาง</th>
                            <th style="width: 25%;">ทักษะทั่วไป</th>
                            <th style="width: 10%;">Bloom's</th>
                            <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                   <tbody>
    <?php if (!empty($plo_list)): ?>
        <?php foreach ($plo_list as $plo): ?>
            <tr class="plo-row">
                <td class="text-center no-print">
                    <input type="checkbox" class="row-checkbox form-check-input">
                </td>
                
                <td class="fw-bold" style="color: #000;">
                    <?php echo nl2br(htmlspecialchars($plo['plo_code'])); ?>
                </td>
                
                <td class="small text-muted"><?php echo nl2br(htmlspecialchars($plo['plo_knowledge'] ?: '-')); ?></td>
                <td class="small text-muted"><?php echo nl2br(htmlspecialchars($plo['plo_skill'] ?: '-')); ?></td>
                
                <td class="text-center" style="color: #000;">
                    <?php 
                        if($plo['plo_bty']){
                            // แสดงผลเป็นข้อความปกติ คั่นด้วยเครื่องหมายคอมม่า
                            echo htmlspecialchars($plo['plo_bty']);
                        } else { 
                            echo '-'; 
                        }
                    ?>
                </td>
                
                <td class="text-center no-print">
                    <?php if ($is_admin): ?>
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-light btn-sm border" onclick='openPloModal("edit", <?php echo json_encode($plo); ?>)'>
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </button>
                            <a href="process_plo.php?delete=<?php echo $plo['plo_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ยืนยันการลบ PLO นี้?')">
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
        <tr><td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูลในระบบ</td></tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="ploModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form action="process_plo.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" id="modalHeader">
                <h5 class="modal-title fw-bold" id="ploModalTitle">บันทึกข้อมูล PLO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="action" id="ploAction">
                <input type="hidden" name="plo_id" id="ploId">

                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-bold">รายละเอียด PLO (Program Learning Outcome)</label>
                        <textarea name="plo_code" id="ploCode" class="form-control" rows="3" required placeholder="เช่น สามารถออกแบบและพัฒนาระบบเครือข่ายคอมพิวเตอร์..."></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Bloom's Taxonomy (ระดับการเรียนรู้)</label>
                        <div class="bg-light p-3 rounded-3 border">
                            <div class="row row-cols-2 row-cols-md-3 g-2">
                                <?php foreach($bloom_options as $key => $val): ?>
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input bloom-check" type="checkbox" name="blooms[]" value="<?php echo $key; ?>" id="bl_<?php echo $key; ?>">
                                        <label class="form-check-label small" for="bl_<?php echo $key; ?>">
                                            <strong><?php echo $key; ?></strong> (<?php echo $val; ?>)
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold mb-3">ประเภทและทักษะที่เกี่ยวข้อง (เลือกประเภทหลัก)</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="form-check-input d-none" name="plo_type" id="type_spec" value="specific" required>
                                <label class="category-label w-100" for="type_spec">
                                    <div class="category-card">
                                        <div class="fw-bold text-primary mb-2"><i class="bi bi-mortarboard-fill me-1"></i> ความรู้และทักษะเฉพาะทาง</div>
                                        <textarea name="text_spec" id="textSpec" class="form-control form-control-sm" rows="3" placeholder="ระบุทักษะทางวิชาชีพ..."></textarea>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="form-check-input d-none" name="plo_type" id="type_gen" value="general">
                                <label class="category-label w-100" for="type_gen">
                                    <div class="category-card">
                                        <div class="fw-bold text-secondary mb-2"><i class="bi bi-people-fill me-1"></i> ความรู้และทักษะทั่วไป (Soft Skills)</div>
                                        <textarea name="text_gen" id="textGen" class="form-control form-control-sm" rows="3" placeholder="ระบุการสื่อสาร ภาวะผู้นำ..."></textarea>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="submit" name="save_plo" class="btn btn-primary px-5 fw-bold shadow-sm" id="btnSubmit">บันทึกข้อมูล PLO</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ploModal = new bootstrap.Modal(document.getElementById('ploModal'));

    function openPloModal(mode, data = null) {
        const actionInput = document.getElementById('ploAction');
        const titleText = document.getElementById('ploModalTitle');
        const header = document.getElementById('modalHeader');
        const btnSubmit = document.getElementById('btnSubmit');

        // Reset Form
        document.querySelectorAll('.bloom-check').forEach(el => el.checked = false);
        
        actionInput.value = mode;
        if (mode === 'add') {
            titleText.innerText = 'เพิ่ม PLO ใหม่';
            header.className = 'modal-header bg-primary text-white';
            btnSubmit.className = 'btn btn-primary px-5 fw-bold shadow-sm';
            
            document.getElementById('ploId').value = '';
            document.getElementById('ploCode').value = '';
            document.getElementById('textSpec').value = '';
            document.getElementById('textGen').value = '';
            document.getElementById('type_spec').checked = true;
        } else {
            titleText.innerText = 'แก้ไขข้อมูล PLO';
            header.className = 'modal-header bg-warning text-dark';
            btnSubmit.className = 'btn btn-warning px-5 fw-bold shadow-sm';

            document.getElementById('ploId').value = data.plo_id;
            document.getElementById('ploCode').value = data.plo_code;
            document.getElementById('textSpec').value = data.plo_knowledge || '';
            document.getElementById('textGen').value = data.plo_skill || '';
            
            if(data.plo_bty) {
                const blooms = data.plo_bty.split(',');
                blooms.forEach(b => {
                    const check = document.getElementById('bl_' + b.trim());
                    if(check) check.checked = true;
                });
            }

            if(data.plo_knowledge && data.plo_knowledge !== "") {
                document.getElementById('type_spec').checked = true;
            } else {
                document.getElementById('type_gen').checked = true;
            }
        }
        ploModal.show();
    }

    // --- Print & Selection Logic ---
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = this.checked;
            cb.closest('tr').style.backgroundColor = this.checked ? '#f8f9ff' : '';
        });
    });

    function printSelectedPlo() {
        const rows = document.querySelectorAll('.plo-row');
        let hasSelection = false;
        
        rows.forEach(row => {
            const isChecked = row.querySelector('.row-checkbox').checked;
            if (!isChecked) {
                row.classList.add('d-none-print');
            } else {
                hasSelection = true;
                row.classList.remove('d-none-print');
            }
        });

        if (!hasSelection) {
            alert("กรุณาเลือก PLO ที่ต้องการพิมพ์");
            return;
        }

        window.print();
        
        // คืนสถานะการแสดงผล
        setTimeout(() => {
            rows.forEach(row => row.classList.remove('d-none-print'));
        }, 500);
    }
</script>
</body>
</html>