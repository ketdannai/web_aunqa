<?php
session_start();
require_once "../config.php";

// ตรวจสอบความปลอดภัยเบื้องต้น
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    exit;
}

$current_user = $_SESSION["use_id"];
$user_role = $_SESSION["use_role"];
$is_admin = ($user_role == 'admin');
$upload_dir = "../uploads_piclab/";

// สร้างโฟลเดอร์ถ้ายังไม่มี
if (!is_dir($upload_dir)) { 
    mkdir($upload_dir, 0777, true); 
}

// --- ฟังก์ชันใหม่: AJAX สำหรับลบรูปภาพรายใบ ---
if (isset($_GET['delete_single_pic'])) {
    $lab_id = intval($_GET['lab_id']);
    $file_to_delete = $_GET['file'];
    
    // ตรวจสอบสิทธิ์
    $res = $link->query("SELECT use_id, lab_pic FROM laboratory WHERE lab_id = $lab_id");
    $data = $res->fetch_assoc();
    
    if ($data && ($is_admin || $data['use_id'] == $current_user)) {
        $pics = explode(',', $data['lab_pic']);
        // กรองชื่อไฟล์ที่ต้องการลบออก
        $new_pics = array_filter($pics, function($p) use ($file_to_delete) {
            return trim($p) !== trim($file_to_delete);
        });
        
        $new_string = implode(',', $new_pics);
        
        // อัปเดตฐานข้อมูล
        $stmt = $link->prepare("UPDATE laboratory SET lab_pic = ? WHERE lab_id = ?");
        $stmt->bind_param("si", $new_string, $lab_id);
        
        if ($stmt->execute()) {
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
    $res = $link->query("SELECT use_id, lab_pic FROM laboratory WHERE lab_id = $id");
    $data = $res->fetch_assoc();

    if ($data && ($is_admin || $data['use_id'] == $current_user)) {
        if (!empty($data['lab_pic'])) {
            $pics = explode(',', $data['lab_pic']);
            foreach ($pics as $p) {
                $file_path = $upload_dir . trim($p);
                if (file_exists($file_path)) unlink($file_path);
            }
        }
        $link->query("DELETE FROM laboratory WHERE lab_id = $id");
        $_SESSION["lab_success"] = "ลบข้อมูลห้องปฏิบัติการสำเร็จ";
    }
    header("location: laboratory.php");
    exit;
}

// 2. จัดการการเพิ่มและแก้ไขข้อมูล (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $lab_id = intval($_POST['lab_id']);
    $use_id = $_POST['use_id'];
    $lab_name = mysqli_real_escape_string($link, $_POST['lab_name']);
    $lab_num = mysqli_real_escape_string($link, $_POST['lab_num']);
    $lab_durable = mysqli_real_escape_string($link, $_POST['lab_durable']);
    $lab_status = $_POST['lab_status'];

    // --- จัดการอัปโหลดรูปภาพใหม่ ---
    $uploaded_names = [];
    if (!empty($_FILES['lab_pics']['name'][0])) {
        foreach ($_FILES['lab_pics']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['lab_pics']['error'][$key] == 0) {
                $ext = pathinfo($_FILES['lab_pics']['name'][$key], PATHINFO_EXTENSION);
                $new_name = "lab_" . date('Ymd_His') . "_" . uniqid() . "." . $ext;
                if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                    $uploaded_names[] = $new_name;
                }
            }
        }
    }
    $new_pics_upload_string = implode(",", $uploaded_names);

    // --- กรณีเพิ่มข้อมูล ---
    if ($action == 'add') {
        $sql = "INSERT INTO laboratory (lab_name, lab_num, lab_durable, lab_status, use_id, lab_pic) 
                VALUES ('$lab_name', '$lab_num', '$lab_durable', '$lab_status', '$use_id', '$new_pics_upload_string')";
        if ($link->query($sql)) $_SESSION["lab_success"] = "เพิ่มข้อมูลสำเร็จ";
    } 
    
    // --- กรณีแก้ไขข้อมูล ---
    else if ($action == 'edit') {
        $res_check = $link->query("SELECT use_id, lab_pic FROM laboratory WHERE lab_id = $lab_id");
        $check = $res_check->fetch_assoc();

        if ($check && ($is_admin || $check['use_id'] == $current_user)) {
            
            // ตรรกะใหม่: รวมรูปภาพใหม่ที่อัปโหลดเข้ากับรายการรูปภาพเดิม (ถ้ามี)
            $current_pics = $check['lab_pic'];
            if (!empty($new_pics_upload_string)) {
                $final_pic_string = (!empty($current_pics)) ? $current_pics . "," . $new_pics_upload_string : $new_pics_upload_string;
            } else {
                $final_pic_string = $current_pics;
            }

            $sql = "UPDATE laboratory SET 
                    lab_name='$lab_name', 
                    lab_num='$lab_num', 
                    lab_durable='$lab_durable', 
                    lab_status='$lab_status', 
                    use_id='$use_id',
                    lab_pic='$final_pic_string' 
                    WHERE lab_id=$lab_id";

            if ($link->query($sql)) $_SESSION["lab_success"] = "แก้ไขข้อมูลสำเร็จ";
        }
    }
    header("location: laboratory.php");
    exit;
}
?>