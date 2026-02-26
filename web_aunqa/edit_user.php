<?php
// ไฟล์: E:\xampp\htdocs\web_aunqa\edit_user.php

session_start();
 
// ตรวจสอบสิทธิ์ Admin 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["use_role"] !== 'admin'){
    header("location: dashboard.php");
    exit;
}

require_once "config.php"; 

$edit_id = $title = $fname = $lname = $role = $username = "";
$title_err = $fname_err = $lname_err = $role_err = $username_err = "";

// 1. ดึงข้อมูลผู้ใช้ที่จะแก้ไขเมื่อเข้าหน้าด้วย GET
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $edit_id = trim($_GET["id"]);
    
    $sql = "SELECT username, use_title, use_fname, use_lname, use_role FROM users WHERE use_id = ?";
    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param("i", $edit_id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($username, $title, $fname, $lname, $role);
                $stmt->fetch();
            } else {
                header("location: manage_users.php");
                exit();
            }
        }
        $stmt->close();
    }
} 
// 2. ประมวลผลการอัปเดตเมื่อมีการส่ง POST
else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $edit_id = trim($_POST["id"]);
    $title = trim($_POST["title"] ?? '');
    $fname = trim($_POST["fname"] ?? '');
    $lname = trim($_POST["lname"] ?? '');
    $role = trim($_POST["role"] ?? '');
    $new_password = trim($_POST["new_password"] ?? '');

    // Validation
    if (empty($title)) { $title_err = "กรุณาเลือกคำนำหน้า"; }
    if (empty($fname)) { $fname_err = "กรุณากรอกชื่อจริง"; }
    if (empty($lname)) { $lname_err = "กรุณากรอกนามสกุล"; }
    if (!in_array($role, ['user', 'admin'])) { $role_err = "สิทธิ์ไม่ถูกต้อง"; }

    if (empty($title_err) && empty($fname_err) && empty($lname_err) && empty($role_err)) {
        
        // อัปเดตข้อมูลทั่วไป
        $sql = "UPDATE users SET use_title = ?, use_fname = ?, use_lname = ?, use_role = ? WHERE use_id = ?";
        
        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param("ssssi", $title, $fname, $lname, $role, $edit_id);
            
            if ($stmt->execute()) {
                // ตรวจสอบว่ามีการกรอกรหัสผ่านใหม่มาหรือไม่
                if (!empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql_pw = "UPDATE users SET password = ? WHERE use_id = ?";
                    if ($stmt_pw = $link->prepare($sql_pw)) {
                        $stmt_pw->bind_param("si", $hashed_password, $edit_id);
                        $stmt_pw->execute();
                        $stmt_pw->close();
                    }
                }

                $_SESSION["manage_users_success"] = "อัปเดตข้อมูลผู้ใช้ ID: " . $edit_id . " สำเร็จ";
                header("location: manage_users.php");
                exit;
            } else {
                $_SESSION["manage_users_error"] = "เกิดข้อผิดพลาดในการอัปเดต: " . $stmt->error;
            }
            $stmt->close();
        }
    }
} else {
    header("location: manage_users.php");
    exit;
}

if (isset($link) && $link !== false) {
    $link->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขผู้ใช้ ID: <?php echo htmlspecialchars($edit_id); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f7f6; }
        .card { border: none; border-radius: 15px; }
        .card-header { border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0"><i class="bi bi-person-gear me-2"></i>แก้ไขข้อมูลผู้ใช้ ID: <?php echo htmlspecialchars($edit_id); ?></h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($_SESSION["manage_users_error"])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION["manage_users_error"]; unset($_SESSION["manage_users_error"]); ?></div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_id); ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($username); ?>" disabled>
                        <small class="text-muted">ไม่สามารถแก้ไข Username ได้</small>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="title" class="form-label fw-bold">คำนำหน้า</label>
                            <select id="title" name="title" class="form-select <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">เลือก</option>
                                <?php 
                                    $titles = ['นาย', 'นาง', 'นางสาว', 'ผศ.', 'ดร.', 'อ.' , 'ผศ.ดร.'];
                                    foreach ($titles as $t) {
                                        $selected = ($title == $t) ? 'selected' : '';
                                        echo "<option value='{$t}' {$selected}>{$t}</option>";
                                    }
                                ?>
                            </select>
                            <div class="invalid-feedback"><?php echo $title_err; ?></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="fname" class="form-label fw-bold">ชื่อจริง</label>
                            <input type="text" name="fname" id="fname" class="form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($fname); ?>">
                            <div class="invalid-feedback"><?php echo $fname_err; ?></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="lname" class="form-label fw-bold">นามสกุล</label>
                            <input type="text" name="lname" id="lname" class="form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($lname); ?>">
                            <div class="invalid-feedback"><?php echo $lname_err; ?></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold">สิทธิ์การใช้งาน (Role)</label>
                        <select id="role" name="role" class="form-select <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>">
                            <option value="user" <?php echo ($role == 'user') ? 'selected' : ''; ?>>user</option>
                            <option value="admin" <?php echo ($role == 'admin') ? 'selected' : ''; ?>>admin</option>
                        </select>
                        <div class="invalid-feedback"><?php echo $role_err; ?></div>
                    </div>

                    <div class="mb-4 p-3 bg-light border rounded">
                        <label for="new_password" class="form-label fw-bold text-primary"><i class="bi bi-key-fill me-1"></i>ตั้งรหัสผ่านใหม่ (Reset Password)</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="กรอกรหัสผ่านใหม่ที่นี่">
                        <div class="form-text text-danger">* หากไม่ต้องการเปลี่ยน ให้ปล่อยว่างไว้</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="manage_users.php" class="btn btn-outline-secondary px-4"><i class="bi bi-x-circle me-1"></i>ยกเลิก</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i>บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>