<?php
// ไฟล์: clo/clo.php
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

// ดึงข้อมูล CLO โดยรวม PLO ที่เกี่ยวข้องเข้าด้วยกัน
$sql = "SELECT c.course_code, c.course_name, cl.clo_code, cl.course_id, cl.use_id,
        GROUP_CONCAT(p.plo_code ORDER BY p.plo_id SEPARATOR ', ') AS all_plo_codes,
        GROUP_CONCAT(p.plo_id ORDER BY p.plo_id) AS all_plo_ids
        FROM clo cl
        LEFT JOIN course c ON cl.course_id = c.course_id
        LEFT JOIN plo p ON cl.plo_id = p.plo_id
        GROUP BY cl.course_id, cl.clo_code
        ORDER BY cl.course_id DESC, cl.clo_code ASC";

$result = $link->query($sql);
$courses_res = $link->query("SELECT course_id, course_code, course_name FROM course ORDER BY course_code ASC");
$plos_res = $link->query("SELECT plo_id, plo_code FROM plo ORDER BY plo_id ASC");

$success_message = $_SESSION["clo_success"] ?? null;
unset($_SESSION["clo_success"]);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการ CLO | AUN-QA System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
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
        }

        h1 {
            font-family: 'Kanit';
            font-weight: 600;
            color: var(--primary-navy);
        }

        /* Search Box Style */
        .search-container .input-group-text { background-color: #fff; border-right: none; }
        .search-container .form-control { border-left: none; }
        .search-container .form-control:focus { box-shadow: none; border-color: #dee2e6; }

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
            vertical-align: top;
            padding: 12px;
        }

        .course-code-badge {
            background-color: #e7f1ff;
            color: #0056b3;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #b3d7ff;
        }

        .plo-text {
            color: #666;
            font-size: 0.9rem;
        }

        @media print {
            .sidebar, .main-header, .no-print, .btn, .alert, .modal, .dropdown, .ts-wrapper, .search-container {
                display: none !important;
            }

            body { background: white; }
            .content { padding: 0; }
            .table-custom { border: 1px solid black !important; width: 100%; table-layout: fixed; }
            .table-custom th, .table-custom td {
                border: 1px solid black !important;
                color: black !important;
                font-size: 12px;
                word-wrap: break-word;
            }
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
                <a class="nav-link" href="../research/research.php">วิจัย</a>
                <a class="nav-link" href="../development/development.php">พัฒนานักศึกษา</a>
                <a class="nav-link" href="../plo/plo.php">PLO</a>
                <a class="nav-link active" href="clo.php">CLO</a>
                <a class="nav-link" href="../services/services.php">งานบริการวิชาการ</a>
                <a class="nav-link" href="../laboratory/laboratory.php">ห้องปฏิบัติการ</a>
                <?php if ($is_admin): ?>
                    <a class="nav-link" href="../manage_users.php"><i class="bi bi-people-fill me-2"></i> จัดการผู้ใช้งาน</a>
                <?php endif; ?>
            </div>
        </nav>

        <main class="content">
            <div class="print-header">
                <h2 class="fw-bold">รายงานผลลัพธ์การเรียนรู้ระดับรายวิชา (Course Learning Outcomes)</h2>
                <p>พิมพ์โดย: <?php echo $full_name; ?> | วันที่: <?php echo date('d/m/Y'); ?></p>
                <hr>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h1>จัดการ CLO</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary shadow-sm px-4" onclick="openCloModal('add')">
                        <i class="bi bi-plus-circle me-1"></i> เพิ่ม CLO
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-printer me-1"></i> ออกรายงาน
                        </button>
                        <ul class="dropdown-menu shadow border-0">
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-2"></i>พิมพ์ทั้งหมด</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printSelectedClo()"><i class="bi bi-check-square me-2"></i>พิมพ์เฉพาะที่เลือก</a></li>
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
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="ค้นหารหัสวิชา, ชื่อวิชา, CLO หรือ PLO...">
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
                    <table class="table table-bordered table-custom align-middle" id="cloTable">
                        <thead>
                            <tr>
                                <th class="no-print" style="width: 45px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th style="width: 10%;">รหัสวิชา</th>
                                <th style="width: 15%;">ชื่อรายวิชา</th>
                                <th style="width: 30%;">CLO (Course Learning Outcome)</th>
                                <th style="width: 30%;">PLO ที่เชื่อมโยง</th>
                                <th class="text-center no-print" style="width: 15%;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($item = $result->fetch_assoc()):
                                    $is_owner = ($item['use_id'] == $logged_in_user_id);
                                ?>
                                    <tr class="clo-row">
                                        <td class="text-center no-print">
                                            <input type="checkbox" class="row-checkbox form-check-input">
                                        </td>

                                        <td class="text-center">
                                            <span style="color: #000; font-weight: 500;">
                                                <?php echo htmlspecialchars($item['course_code']); ?>
                                            </span>
                                        </td>

                                        <td class="fw-bold"><?php echo htmlspecialchars($item['course_name']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($item['clo_code'])); ?></td>
                                        <td class="plo-text"><?php echo !empty($item['all_plo_codes']) ? htmlspecialchars($item['all_plo_codes']) : '-'; ?></td>

                                        <td class="text-center no-print">
                                            <?php if ($is_admin || $is_owner): ?>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-light btn-sm border" onclick='openCloModal("edit", <?php echo json_encode($item); ?>)'>
                                                        <i class="bi bi-pencil-fill text-warning"></i>
                                                    </button>
                                                    <a href="process_clo.php?delete_course=<?php echo $item['course_id']; ?>&delete_code=<?php echo urlencode($item['clo_code']); ?>"
                                                        class="btn btn-light btn-sm border" onclick="return confirm('ยืนยันการลบข้อมูลนี้?')">
                                                        <i class="bi bi-trash-fill text-danger"></i>
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <i class="bi bi-lock-fill text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูล CLO ในระบบ</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="cloModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="process_clo.php" method="POST" class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="cloModalTitle">บันทึกข้อมูล CLO</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" id="cloAction" value="add">
                    <input type="hidden" name="old_course_id" id="oldCourseId">
                    <input type="hidden" name="old_clo_code" id="oldCloCode">
                    <input type="hidden" name="use_id" value="<?php echo $logged_in_user_id; ?>">

                    <div class="mb-4">
                        <label class="form-label fw-bold">เลือกรายวิชา</label>
                        <select name="course_id" id="cloCourseId" class="form-select" required>
                            <option value="">-- ค้นหารายวิชา --</option>
                            <?php $courses_res->data_seek(0);
                            while ($c = $courses_res->fetch_assoc()): ?>
                                <option value="<?php echo $c['course_id']; ?>"><?php echo htmlspecialchars($c['course_code'] . " " . $c['course_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">CLO Code / รายละเอียด</label>
                        <textarea name="clo_code" id="cloCode" class="form-control" rows="5" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">PLO ที่เชื่อมโยง (กด Ctrl ค้างเพื่อเลือกหลายตัว)</label>
                        <select name="plo_id[]" id="cloPloId" class="form-select" multiple style="height: 150px;" required>
                            <?php $plos_res->data_seek(0);
                            while ($p = $plos_res->fetch_assoc()): ?>
                                <option value="<?php echo $p['plo_id']; ?>"><?php echo htmlspecialchars($p['plo_code']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        // --- ระบบค้นหา Real-time ---
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const value = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#cloTable tbody tr.clo-row');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(value) ? "" : "none";
            });
        });

        const courseSelector = new TomSelect("#cloCourseId", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
        const cloModal = new bootstrap.Modal(document.getElementById('cloModal'));

        function openCloModal(mode, data = null) {
            document.getElementById('cloAction').value = mode;
            const ploSelect = document.getElementById('cloPloId');
            ploSelect.selectedIndex = -1;
            courseSelector.clear();

            if (mode === 'add') {
                document.getElementById('cloModalTitle').innerText = 'เพิ่ม CLO ใหม่';
                document.getElementById('cloCode').value = '';
            } else {
                document.getElementById('cloModalTitle').innerText = 'แก้ไขข้อมูล CLO';
                document.getElementById('oldCourseId').value = data.course_id;
                document.getElementById('oldCloCode').value = data.clo_code;
                document.getElementById('cloCode').value = data.clo_code;
                courseSelector.setValue(data.course_id);

                if (data.all_plo_ids) {
                    const ids = data.all_plo_ids.split(',');
                    Array.from(ploSelect.options).forEach(opt => opt.selected = ids.includes(opt.value));
                }
            }
            cloModal.show();
        }

        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
                cb.closest('tr').style.backgroundColor = this.checked ? '#f8f9ff' : '';
            });
        });

        function printSelectedClo() {
            const rows = document.querySelectorAll('.clo-row');
            let selected = false;
            rows.forEach(row => {
                if (!row.querySelector('.row-checkbox').checked) row.classList.add('d-none-print');
                else {
                    selected = true;
                    row.classList.remove('d-none-print');
                }
            });

            if (!selected) {
                alert("กรุณาเลือกข้อมูลที่ต้องการพิมพ์");
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