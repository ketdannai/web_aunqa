<?php
// ไฟล์: opencourse/process_opencourse.php
session_start();
require_once "../config.php";

// ตรวจสอบการ Login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    exit("Unauthorized access");
}

$logged_user_id = $_SESSION["use_id"];
$user_role = $_SESSION["use_role"] ?? 'user';

// --- 1. จัดการการลบข้อมูล ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // ดึงข้อมูลมาตรวจสอบเจ้าของก่อนลบ
    $res = $link->query("SELECT use_id FROM opencourse WHERE opencourse_id = $id");
    $data = $res->fetch_assoc();

    if ($data) {
        // เงื่อนไข: เป็น Admin ลบได้หมด หรือ เป็นเจ้าของข้อมูลถึงลบได้
        if ($user_role === 'admin' || $data['use_id'] == $logged_user_id) {
            $stmt = $link->prepare("DELETE FROM opencourse WHERE opencourse_id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION["oc_success"] = "ลบข้อมูลเรียบร้อยแล้ว";
            }
            $stmt->close();
        }
    }
    header("location: opencourse.php");
    exit;
}

// --- 2. จัดการการเพิ่มและแก้ไขข้อมูล ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $oc_id = isset($_POST['opencourse_id']) ? intval($_POST['opencourse_id']) : null;
    $course_id = intval($_POST['course_id']);
    $term_id = intval($_POST['term_id']);
    
    // รับค่า use_id (จาก select หรือ hidden input)
    $target_use_id = intval($_POST['use_id']);

    if ($action == 'add') {
        // ตรวจสอบว่ามีการส่งกลุ่มเรียนมาเป็น Array หรือไม่
        if (isset($_POST['section_id']) && is_array($_POST['section_id'])) {
            $sections = $_POST['section_id'];
            $count = 0;

            // เตรียม Statement สำหรับการวนลูป INSERT
            $sql = "INSERT INTO opencourse (course_id, use_id, section_id, term_id) VALUES (?, ?, ?, ?)";
            if ($stmt = $link->prepare($sql)) {
                foreach ($sections as $sec_id) {
                    $section_id = intval($sec_id);
                    $stmt->bind_param("iiii", $course_id, $target_use_id, $section_id, $term_id);
                    if ($stmt->execute()) {
                        $count++;
                    }
                }
                $stmt->close();
                $_SESSION["oc_success"] = "เพิ่มรายวิชาสำเร็จรวม $count กลุ่มเรียน";
            }
        } else {
            $_SESSION["oc_error"] = "กรุณาเลือกอย่างน้อยหนึ่งกลุ่มเรียน";
        }
    } 
    else if ($action == 'edit') {
        // สำหรับการแก้ไข ปกติจะแก้ไขทีละ 1 แถว (ใช้ค่าแรกจาก Array)
        $section_id = is_array($_POST['section_id']) ? intval($_POST['section_id'][0]) : intval($_POST['section_id']);

        // ตรวจสอบสิทธิ์ก่อนแก้ไข
        $check_res = $link->query("SELECT use_id FROM opencourse WHERE opencourse_id = $oc_id");
        $check_data = $check_res->fetch_assoc();

        if ($check_data) {
            if ($user_role === 'admin' || $check_data['use_id'] == $logged_user_id) {
                if ($user_role === 'admin') {
                    // Admin แก้ไขได้ทุกอย่างรวมถึงเปลี่ยนเจ้าของ (ผู้สอน)
                    $sql = "UPDATE opencourse SET course_id=?, section_id=?, term_id=?, use_id=? WHERE opencourse_id=?";
                    $stmt = $link->prepare($sql);
                    $stmt->bind_param("iiiii", $course_id, $section_id, $term_id, $target_use_id, $oc_id);
                } else {
                    // User แก้ไขได้แค่ข้อมูลวิชาใน ID ของตัวเอง
                    $sql = "UPDATE opencourse SET course_id=?, section_id=?, term_id=? WHERE opencourse_id=? AND use_id=?";
                    $stmt = $link->prepare($sql);
                    $stmt->bind_param("iiiii", $course_id, $section_id, $term_id, $oc_id, $logged_user_id);
                }

                if ($stmt->execute()) {
                    $_SESSION["oc_success"] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
                }
                $stmt->close();
            }
        }
    }

    header("location: opencourse.php");
    exit;
}
?>