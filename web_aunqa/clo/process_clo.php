<?php
// ไฟล์: clo/process_clo.php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"])) {
    exit("Unauthorized access");
}

$current_user = $_SESSION["use_id"];
$user_role = $_SESSION["use_role"] ?? 'user';

// --- 1. การลบข้อมูล --- (เหมือนเดิม)
if (isset($_GET['delete_course']) && isset($_GET['delete_code'])) {
    $cid = intval($_GET['delete_course']);
    $code = mysqli_real_escape_string($link, $_GET['delete_code']);
    
    $check_stmt = $link->prepare("SELECT use_id FROM clo WHERE course_id = ? AND clo_code = ? LIMIT 1");
    $check_stmt->bind_param("is", $cid, $code);
    $check_stmt->execute();
    $res = $check_stmt->get_result();
    $data = $res->fetch_assoc();

    if ($data && ($user_role === 'admin' || $data['use_id'] == $current_user)) {
        $del_stmt = $link->prepare("DELETE FROM clo WHERE course_id = ? AND clo_code = ?");
        $del_stmt->bind_param("is", $cid, $code);
        if ($del_stmt->execute()) {
            $_SESSION["clo_success"] = "ลบข้อมูล CLO เรียบร้อยแล้ว";
        }
        $del_stmt->close();
    }
    $check_stmt->close();
    header("location: clo.php");
    exit;
}

// --- 2. การเพิ่มและแก้ไขข้อมูล ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? 'add';
    $use_id = intval($_POST['use_id']);
    $course_id = intval($_POST['course_id']);
    
    // แก้ไขบรรทัดที่ 45: 
    // รับค่าจาก clo_description ในฟอร์ม มาเก็บไว้ในตัวแปรเพื่อรอ insert ลง clo_code ใน DB
    // ใช้ ?? '' เพื่อกัน Error Undefined array key
    $clo_code_value = mysqli_real_escape_string($link, $_POST['clo_description'] ?? $_POST['clo_code'] ?? ''); 
    $plo_ids = $_POST['plo_id'] ?? []; 

    if ($action == 'edit') {
        $old_cid = intval($_POST['old_course_id']);
        $old_code = mysqli_real_escape_string($link, $_POST['old_clo_code']);
        
        $check_stmt = $link->prepare("SELECT use_id FROM clo WHERE course_id = ? AND clo_code = ? LIMIT 1");
        $check_stmt->bind_param("is", $old_cid, $old_code);
        $check_stmt->execute();
        $res = $check_stmt->get_result();
        $data = $res->fetch_assoc();

        if ($data && ($user_role === 'admin' || $data['use_id'] == $current_user)) {
            $del_old = $link->prepare("DELETE FROM clo WHERE course_id = ? AND clo_code = ?");
            $del_old->bind_param("is", $old_cid, $old_code);
            $del_old->execute();
            $del_old->close();
        } else {
            header("location: clo.php");
            exit;
        }
        $check_stmt->close();
    }

    // แก้ไขบรรทัดที่ 74: 
    // ตัด clo_description ออกจาก SQL เพราะใน DB ไม่มีคอลัมน์นี้
    if (is_array($plo_ids) && !empty($plo_ids)) {
        $sql = "INSERT INTO clo (plo_id, course_id, use_id, clo_code) VALUES (?, ?, ?, ?)";
        $stmt = $link->prepare($sql);
        
        foreach ($plo_ids as $p_id) {
            $plo_id = intval($p_id);
            // bind_param เหลือแค่ 4 ตัวแปร (i i i s)
            $stmt->bind_param("iiis", $plo_id, $course_id, $use_id, $clo_code_value);
            $stmt->execute();
        }
        $stmt->close();
        $_SESSION["clo_success"] = "บันทึกข้อมูลสำเร็จเรียบร้อยแล้ว";
    } else {
        $_SESSION["clo_error"] = "กรุณาเลือกอย่างน้อยหนึ่ง PLO";
    }

    header("location: clo.php");
    exit;
}
?>