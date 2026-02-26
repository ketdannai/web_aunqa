<?php
// ไฟล์: article/article.php
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

// ดึงข้อมูลบทความทั้งหมด
$articles = [];
$sql = "SELECT * FROM article ORDER BY art_id DESC";
if ($result = $link->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
    $result->free();
}

$success_message = $_SESSION["art_success"] ?? null;
unset($_SESSION["art_success"]);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บทความวิชาการ | AUN-QA System</title>
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

        /* จัดการสไตล์ตารางและสีตัวอักษร */
        .table-custom thead th { 
            background-color: #f8f9fa; color: var(--primary-navy); 
            border: 1px solid #dee2e6; text-align: center; padding: 12px; font-family: 'Kanit';
        }
        .table-custom td { 
            border: 1px solid #dee2e6; 
            vertical-align: middle; 
            padding: 12px; 
            color: #000000 !important; /* บังคับให้เป็นสีดำเข้ม */
        }
        
        /* สีดำสำหรับทุกลิงก์และข้อความในตาราง */
        .table-custom td a, 
        .table-custom td span, 
        .table-custom .author-text,
        .table-custom .fw-bold {
            color: #000000 !important;
        }

        .author-text { font-size: 0.85rem; line-height: 1.4; }
        .evidence-link { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }

        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .modal, .form-check-input { display: none !important; }
            body { background: white; }
            .content { padding: 0; }
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
                <a class="nav-link active" href="article.php">บทความ</a>
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
                <h2 class="fw-bold">รายงานบทความวิชาการ (Academic Articles Report)</h2>
                <p>พิมพ์โดย: <?php echo $full_name; ?> | วันที่: <?php echo date('d/m/Y'); ?></p>
                <hr>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h1>บทความวิชาการ</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary shadow-sm px-4" onclick="openArtModal('add')">
                        <i class="bi bi-plus-circle me-1"></i> เพิ่มบทความ
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-printer me-1"></i> ออกรายงาน
                        </button>
                        <ul class="dropdown-menu shadow border-0">
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-2"></i>พิมพ์ทั้งหมด</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printSelectedArticles()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success border-0 shadow-sm no-print alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-custom align-middle" id="articleTable">
                        <thead>
                            <tr>
                                <th class="no-print" style="width: 45px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th style="width: 30%;">ชื่อบทความ</th>
                                <th style="width: 15%;">ประเภท</th>
                                <th style="width: 15%;">ผู้เขียน/ผู้ทำ</th> <th style="width: 20%;">แหล่งเผยแพร่</th>
                                <th style="width: 10%;">หลักฐาน</th>
                                <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($articles) > 0): ?>
                                <?php foreach ($articles as $art): 
                                    $is_owner = ($art['use_id'] == $logged_in_user_id);
                                    $author_names = [];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if (!empty($art["art_fname$i"])) {
                                            $author_names[] = htmlspecialchars(($art["art_title$i"] ?? '') . ($art["art_fname$i"] ?? '') . " " . ($art["art_lname$i"] ?? ''));
                                        }
                                    }
                                ?>
                                <tr class="article-row">
                                    <td class="text-center no-print"><input type="checkbox" class="row-checkbox form-check-input"></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($art['art_name']); ?></td>
                                    <td class="text-center small"><?php echo htmlspecialchars($art['art_type']); ?></td>
                                    <td class="author-text"><?php echo implode("<br>", $author_names); ?></td>
                                    <td class="small"><?php echo htmlspecialchars($art['art_meet']); ?></td>
                                    <td class="text-center">
                                        <?php if($art['art_evidence']): ?>
                                            <span class="evidence-link small" title="<?php echo htmlspecialchars($art['art_evidence']); ?>">
                                                <?php echo htmlspecialchars($art['art_evidence']); ?>
                                            </span>
                                        <?php else: ?>-<?php endif; ?>
                                    </td>
                                    <td class="text-center no-print">
                                        <?php if ($is_admin || $is_owner): ?>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-light btn-sm border" onclick='openArtModal("edit", <?php echo json_encode($art); ?>)'>
                                                    <i class="bi bi-pencil-fill text-warning"></i>
                                                </button>
                                                <a href="process_article.php?delete=<?php echo $art['art_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ยืนยันการลบข้อมูลนี้?')">
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
                                <tr><td colspan="7" class="text-center py-4 text-muted">ไม่พบข้อมูลบทความในระบบ</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="artModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form action="process_article.php" method="POST" class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="artModalTitle">กรอกข้อมูลบทความวิชาการ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="artAction" value="add">
                    <input type="hidden" name="art_id" id="artId">
                    <input type="hidden" name="use_id" value="<?php echo $logged_in_user_id; ?>">

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">ชื่อบทความ</label>
                            <input type="text" name="art_name" id="artName" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ประเภทบทความ</label>
                            <input type="text" name="art_type" id="artType" class="form-control" placeholder="เช่น วารสารระดับนานาชาติ" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">แหล่งเผยแพร่ / สถานที่ประชุม</label>
                            <input type="text" name="art_meet" id="artMeet" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">หลักฐาน (URL หรือ ข้อความ)</label>
                            <input type="text" name="art_evidence" id="artEvidence" class="form-control">
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3">
                        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-people-fill me-2"></i>รายชื่อผู้ร่วมทำบทความ (สูงสุด 5 ท่าน)</h6>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="row g-2 mb-2 align-items-end">
                                <div class="col-auto pb-1"><span class="badge bg-secondary">ท่านที่ <?php echo $i; ?></span></div>
                                <div class="col-md-2">
                                    <input type="text" name="art_title<?php echo $i; ?>" id="art_title<?php echo $i; ?>" class="form-control form-control-sm" placeholder="คำนำหน้า">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="art_fname<?php echo $i; ?>" id="art_fname<?php echo $i; ?>" class="form-control form-control-sm" placeholder="ชื่อจริง">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="art_lname<?php echo $i; ?>" id="art_lname<?php echo $i; ?>" class="form-control form-control-sm" placeholder="นามสกุล">
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const artModal = new bootstrap.Modal(document.getElementById('artModal'));

        function openArtModal(mode, data = null) {
            document.getElementById('artAction').value = mode;
            if (mode === 'add') {
                document.getElementById('artModalTitle').innerText = 'เพิ่มบทความวิชาการใหม่';
                document.getElementById('artId').value = '';
                document.getElementById('artName').value = '';
                document.getElementById('artType').value = '';
                document.getElementById('artMeet').value = '';
                document.getElementById('artEvidence').value = '';
                for (let i = 1; i <= 5; i++) {
                    document.getElementById('art_title' + i).value = '';
                    document.getElementById('art_fname' + i).value = '';
                    document.getElementById('art_lname' + i).value = '';
                }
            } else {
                document.getElementById('artModalTitle').innerText = 'แก้ไขข้อมูลบทความ';
                document.getElementById('artId').value = data.art_id;
                document.getElementById('artName').value = data.art_name;
                document.getElementById('artType').value = data.art_type;
                document.getElementById('artMeet').value = data.art_meet;
                document.getElementById('artEvidence').value = data.art_evidence;
                for (let i = 1; i <= 5; i++) {
                    document.getElementById('art_title' + i).value = data['art_title' + i] || '';
                    document.getElementById('art_fname' + i).value = data['art_fname' + i] || '';
                    document.getElementById('art_lname' + i).value = data['art_lname' + i] || '';
                }
            }
            artModal.show();
        }

        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
                cb.closest('tr').style.backgroundColor = this.checked ? '#f8f9ff' : '';
            });
        });

        function printSelectedArticles() {
            const rows = document.querySelectorAll('.article-row');
            let selected = false;
            rows.forEach(row => {
                if (!row.querySelector('.row-checkbox').checked) row.classList.add('d-none-print');
                else { selected = true; row.classList.remove('d-none-print'); }
            });

            if (!selected) { alert("กรุณาเลือกบทความที่ต้องการพิมพ์"); return; }
            window.print();
            setTimeout(() => { rows.forEach(row => row.classList.remove('d-none-print')); }, 500);
        }
    </script>
</body>
</html>