<?php
session_start();
require_once "../config.php";

// ตรวจสอบความปลอดภัยเบื้องต้น
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/login.php");
    exit;
}

$logged_in_user_id = $_SESSION["use_id"];
$user_role = $_SESSION["use_role"];
$is_admin = ($user_role == 'admin');
$upload_dir = "../uploads_picser/";

// สร้างโฟลเดอร์ถ้ายังไม่มี
if (!is_dir($upload_dir)) { 
    mkdir($upload_dir, 0777, true); 
}

// --- ฟังก์ชันใหม่: ลบรูปภาพรายใบ (เรียกผ่าน AJAX จากหน้า services.php) ---
if (isset($_GET['delete_single_pic'])) {
    $serv_id = intval($_GET['serv_id']);
    $file_to_delete = $_GET['file'];
    
    // ตรวจสอบสิทธิ์ก่อนลบ
    $stmt = $link->prepare("SELECT use_id, serv_pic FROM services WHERE serv_id = ?");
    $stmt->bind_param("i", $serv_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    
    if ($data && ($is_admin || $data['use_id'] == $logged_in_user_id)) {
        $pics = explode(',', $data['serv_pic']);
        // กรองเอาชื่อไฟล์ที่ไม่ต้องการออก
        $new_pics = array_filter($pics, function($p) use ($file_to_delete) {
            return trim($p) !== trim($file_to_delete);
        });
        
        $new_string = implode(',', $new_pics);
        
        $update_stmt = $link->prepare("UPDATE services SET serv_pic = ? WHERE serv_id = ?");
        $update_stmt->bind_param("si", $new_string, $serv_id);
        
        if ($update_stmt->execute()) {
            $file_path = $upload_dir . trim($file_to_delete);
            if (file_exists($file_path)) unlink($file_path);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    exit;
}

// 1. จัดการการลบข้อมูลทั้งชุด (GET)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    $res = $link->query("SELECT use_id, serv_pic FROM services WHERE serv_id = $id");
    $data = $res->fetch_assoc();

    if ($data && ($is_admin || $data['use_id'] == $logged_in_user_id)) {
        if (!empty($data['serv_pic'])) {
            $old_pics = explode(',', $data['serv_pic']);
            foreach ($old_pics as $p) {
                $file_path = $upload_dir . trim($p);
                if (file_exists($file_path)) unlink($file_path);
            }
        }

        $stmt = $link->prepare("DELETE FROM services WHERE serv_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION["serv_success"] = "ลบข้อมูลงานบริการวิชาการสำเร็จ";
        }
        $stmt->close();
    }
    header("location: services.php");
    exit;
}

// 2. จัดการการเพิ่มและแก้ไขข้อมูล (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $serv_name = $_POST['serv_name'];
    $target_use_id = intval($_POST['use_id']);

    // --- ส่วนจัดการรูปภาพใหม่ ---
    $uploaded_names = [];
    if (!empty($_FILES['serv_pics']['name'][0])) {
        foreach ($_FILES['serv_pics']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['serv_pics']['error'][$key] == 0) {
                $ext = pathinfo($_FILES['serv_pics']['name'][$key], PATHINFO_EXTENSION);
                $new_name = "ser_" . date('Ymd_His') . "_" . uniqid() . "." . $ext;
                if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                    $uploaded_names[] = $new_name;
                }
            }
        }
    }
    $new_pics_upload_string = implode(",", $uploaded_names);

    // --- กรณีเพิ่มข้อมูล ---
    if ($action == 'add') {
        $stmt = $link->prepare("INSERT INTO services (serv_name, use_id, serv_pic) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $serv_name, $target_use_id, $new_pics_upload_string);
        if ($stmt->execute()) {
            $_SESSION["serv_success"] = "เพิ่มข้อมูลสำเร็จ";
        }
        $stmt->close();
    } 
    
    // --- กรณีแก้ไขข้อมูล ---
    else if ($action == 'edit') {
        $sid = intval($_POST['serv_id']);
        
        $check_res = $link->query("SELECT use_id, serv_pic FROM services WHERE serv_id = $sid");
        $check = $check_res->fetch_assoc();
        
        if ($check && ($is_admin || $check['use_id'] == $logged_in_user_id)) {
            
            // รวมรูปภาพ: เอารูปภาพใหม่ที่อัปโหลด ไปต่อท้ายรูปภาพเดิมที่มีอยู่ในฐานข้อมูล
            $current_pics = $check['serv_pic'];
            if (!empty($new_pics_upload_string)) {
                $final_pic_string = (!empty($current_pics)) ? $current_pics . "," . $new_pics_upload_string : $new_pics_upload_string;
            } else {
                $final_pic_string = $current_pics;
            }

            if ($is_admin) {
                $stmt = $link->prepare("UPDATE services SET serv_name = ?, use_id = ?, serv_pic = ? WHERE serv_id = ?");
                $stmt->bind_param("sisi", $serv_name, $target_use_id, $final_pic_string, $sid);
            } else {
                $stmt = $link->prepare("UPDATE services SET serv_name = ?, serv_pic = ? WHERE serv_id = ? AND use_id = ?");
                $stmt->bind_param("ssii", $serv_name, $final_pic_string, $sid, $logged_in_user_id);
            }

            if ($stmt->execute()) {
                $_SESSION["serv_success"] = "แก้ไขข้อมูลสำเร็จ";
            }
            $stmt->close();
        }
    }
    
    header("location: services.php");
    exit;
}
?>