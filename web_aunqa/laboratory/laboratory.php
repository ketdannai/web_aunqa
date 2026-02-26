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

// 2.1 ดึงรายชื่อ User สำหรับ Admin (เพื่อมอบหมายผู้รับผิดชอบ)
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

        .main-header {
            background-color: var(--accent-blue);
            color: white;
            padding: 15px 20px;
            z-index: 1000;
            position: relative;
        }

        .sidebar {
            width: 250px;
            background-color: var(--bg-dark);
            min-height: 100vh;
            flex-shrink: 0;
        }

        .sidebar .nav-link {
            color: #f8f9fa;
            padding: 12px 15px;
            margin-bottom: 1px;
            font-size: 1rem;
            background-color: var(--sidebar-link-bg);
            text-decoration: none;
            display: block;
            font-weight: 300;
            font-family: 'Kanit';
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }

        .sidebar .nav-link.active {
            background-color: var(--sidebar-active);
            color: #212529;
            font-weight: 600;
        }

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

        .table-custom thead th {
            background-color: #f8f9fa;
            color: var(--primary-navy);
            border: 1px solid #dee2e6;
            text-align: center;
            padding: 12px;
            font-family: 'Kanit';
        }

        .table-custom td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 12px;
        }

        .img-preview-sm {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid #ddd;
            transition: 0.2s;
        }

        .img-preview-sm:hover {
            transform: scale(1.1);
            border-color: var(--accent-blue);
        }

        .pic-edit-wrapper {
            position: relative;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-del-pic {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 0px 5px;
            font-size: 10px;
            border-radius: 50%;
            border: 1px solid white;
        }

        @media print {

            .sidebar,
            .main-header,
            .no-print,
            .btn,
            .alert,
            .modal,
            .form-check-input {
                display: none !important;
            }

            body {
                background: white;
            }

            .content {
                padding: 0;
            }

            .table-custom {
                border: 1px solid black !important;
                width: 100%;
            }

            .table-custom th,
            .table-custom td {
                border: 1px solid black !important;
                color: black !important;
                font-size: 12px;
            }

            tr.d-none-print {
                display: none !important;
            }

            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
            }
        }

        .print-header {
            display: none;
        }
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
                <h2 class="fw-bold">รายงานข้อมูลห้องปฏิบัติการ (Laboratory Report)</h2>
                <p>พิมพ์โดย: <?php echo $full_name; ?> | วันที่: <?php echo date('d/m/Y'); ?></p>
                <hr>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h1>ห้องปฏิบัติการ</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary shadow-sm px-4" onclick="openLabModal('add')">
                        <i class="bi bi-plus-circle me-1"></i> เพิ่มห้องปฏิบัติการ
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-printer me-1"></i> ออกรายงาน
                        </button>
                        <ul class="dropdown-menu shadow border-0">
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-2"></i>พิมพ์ทั้งหมด</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printSelectedLabs()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
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
                    <table class="table table-bordered table-custom align-middle" id="labTable">
                        <thead>
                            <tr>
                                <th class="no-print" style="width: 45px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th style="width: 25%;">ชื่อห้อง / ผู้รับผิดชอบ</th>
                                <th style="width: 20%;">ครุภัณฑ์สำคัญ</th>
                                <th style="width: 10%; text-align: center;">จำนวน</th>
                                <th style="width: 20%; text-align: center;">รูปภาพ</th>
                                <th style="width: 10%; text-align: center;">สถานะ</th>
                                <th class="text-center no-print" style="width: 100px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($labs) > 0): ?>
                                <?php foreach ($labs as $lab):
                                    $is_owner = ($lab['use_id'] == $logged_in_user_id);
                                    $owner_name = htmlspecialchars($lab['use_title'] . $lab['use_fname'] . " " . $lab['use_lname']);
                                ?>
                                    <tr class="lab-row">
                                        <td class="text-center no-print"><input type="checkbox" class="row-checkbox form-check-input"></td>
                                        <td>
                                            <div class="fw-bold" style="color: #000; font-size: 1.1rem;">
                                                <?php echo htmlspecialchars($lab['lab_name']); ?>
                                            </div>
                                            <div class="small text-muted">ผู้รับผิดชอบ: <?php echo $owner_name; ?></div>
                                        </td>
                                        <td class="small"><?php echo nl2br(htmlspecialchars($lab['lab_durable'] ?? '-')); ?></td>
                                        <td class="text-center fw-bold"><?php echo htmlspecialchars($lab['lab_num']); ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                                <?php
                                                if (!empty($lab['lab_pic'])) {
                                                    $pics = explode(',', $lab['lab_pic']);
                                                    foreach ($pics as $p) {
                                                        $path = "../uploads_piclab/" . trim($p);
                                                        echo '<img src="' . $path . '" class="img-preview-sm" onclick="viewFullImage(\'' . $path . '\')">';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="text-center" style="color: #000; font-weight: 500;">
                                            <?php echo htmlspecialchars($lab['lab_status']); ?>
                                        </td>
                                        <td class="text-center no-print">
                                            <?php if ($is_admin || $is_owner): ?>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-light btn-sm border" onclick='openLabModal("edit", <?php echo json_encode($lab); ?>)'>
                                                        <i class="bi bi-pencil-fill text-warning"></i>
                                                    </button>
                                                    <a href="process_lab.php?delete=<?php echo $lab['lab_id']; ?>" class="btn btn-light btn-sm border" onclick="return confirm('ยืนยันการลบข้อมูลนี้?')">
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
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">ไม่พบข้อมูลห้องปฏิบัติการในระบบ</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="labModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_lab.php" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white" id="modalHeader">
                    <h5 class="modal-title fw-bold" id="labModalTitle">กรอกข้อมูลห้องปฏิบัติการ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="labAction" value="add">
                    <input type="hidden" name="lab_id" id="labId">

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">ชื่อห้องปฏิบัติการ</label>
                            <input type="text" name="lab_name" id="labName" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">จำนวนเครื่อง/ชุด</label>
                            <input type="number" name="lab_num" id="labNum" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ผู้รับผิดชอบ</label>
                        <?php if ($is_admin): ?>
                            <select name="use_id" id="labUserId" class="form-select" required>
                                <option value="">-- เลือกผู้รับผิดชอบ --</option>
                                <?php foreach ($user_options as $opt): ?>
                                    <option value="<?php echo $opt['use_id']; ?>">
                                        <?php echo $opt['use_title'] . $opt['use_fname'] . " " . $opt['use_lname']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control bg-light" value="<?php echo $full_name; ?>" readonly>
                            <input type="hidden" name="use_id" value="<?php echo $logged_in_user_id; ?>">
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">รายการครุภัณฑ์ (ระบุครุภัณฑ์ที่สำคัญ)</label>
                        <textarea name="lab_durable" id="labDurable" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">สถานะความพร้อม</label>
                        <select name="lab_status" id="labStatus" class="form-select">
                            <option value="พร้อมใช้งาน">พร้อมใช้งาน</option>
                            <option value="ไม่พร้อมใช้งาน">ไม่พร้อมใช้งาน</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">รูปภาพประกอบ (เลือกได้หลายรูป)</label>
                        <input type="file" name="lab_pics[]" id="labPics" class="form-control" accept="image/*" multiple>
                        <div id="existingPics" class="mt-3 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" id="btnSubmit">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0 text-end">
                <button type="button" class="btn-close btn-close-white mb-2" data-bs-dismiss="modal"></button>
                <img src="" id="fullResImage" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const labModal = new bootstrap.Modal(document.getElementById('labModal'));
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));

        function viewFullImage(src) {
            document.getElementById('fullResImage').src = src;
            imageModal.show();
        }

        function openLabModal(mode, data = null) {
            const actionInput = document.getElementById('labAction');
            const picContainer = document.getElementById('existingPics');
            const header = document.getElementById('modalHeader');
            const btn = document.getElementById('btnSubmit');

            picContainer.innerHTML = '';
            actionInput.value = mode;

            if (mode === 'add') {
                document.getElementById('labModalTitle').innerText = 'เพิ่มห้องปฏิบัติการใหม่';
                header.className = 'modal-header bg-primary text-white';
                btn.className = 'btn btn-primary px-5 fw-bold shadow-sm';
                document.getElementById('labId').value = '';
                document.getElementById('labName').value = '';
                document.getElementById('labNum').value = '';
                document.getElementById('labDurable').value = '';
                document.getElementById('labStatus').value = 'พร้อมใช้งาน';
                if (document.getElementById('labUserId')) document.getElementById('labUserId').value = "<?php echo $logged_in_user_id; ?>";
            } else {
                document.getElementById('labModalTitle').innerText = 'แก้ไขข้อมูลห้องปฏิบัติการ';
                header.className = 'modal-header bg-warning text-dark';
                btn.className = 'btn btn-warning px-5 fw-bold shadow-sm';
                document.getElementById('labId').value = data.lab_id;
                document.getElementById('labName').value = data.lab_name;
                document.getElementById('labNum').value = data.lab_num;
                document.getElementById('labDurable').value = data.lab_durable;
                document.getElementById('labStatus').value = data.lab_status;
                if (document.getElementById('labUserId')) document.getElementById('labUserId').value = data.use_id;

                if (data.lab_pic) {
                    data.lab_pic.split(',').forEach(p => {
                        const fileName = p.trim();
                        if (fileName) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'pic-edit-wrapper';
                            wrapper.innerHTML = `
                                <img src="../uploads_piclab/${fileName}" class="rounded border" style="width:60px; height:60px; object-fit:cover;">
                                <button type="button" class="btn btn-danger btn-del-pic" onclick="deleteSinglePic(${data.lab_id}, '${fileName}', this.parentElement)">
                                    <i class="bi bi-x"></i>
                                </button>
                            `;
                            picContainer.appendChild(wrapper);
                        }
                    });
                }
            }
            labModal.show();
        }

        function deleteSinglePic(labId, fileName, element) {
            if (confirm('ลบรูปภาพนี้ถาวร?')) {
                fetch(`process_lab.php?delete_single_pic=1&lab_id=${labId}&file=${fileName}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) element.remove();
                    });
            }
        }

        // Selection & Print Logic
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
                cb.closest('tr').style.backgroundColor = this.checked ? '#f8f9ff' : '';
            });
        });

        function printSelectedLabs() {
            const rows = document.querySelectorAll('.lab-row');
            let selected = false;
            rows.forEach(row => {
                if (!row.querySelector('.row-checkbox').checked) row.classList.add('d-none-print');
                else {
                    selected = true;
                    row.classList.remove('d-none-print');
                }
            });

            if (!selected) {
                alert("กรุณาเลือกรายการที่ต้องการพิมพ์");
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