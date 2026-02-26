<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"])) exit;

$upload_dir = "../uploads_picdev/";
if (!is_dir($upload_dir)) { 
    mkdir($upload_dir, 0777, true); 
}

// --- ส่วนใหม่: รองรับการลบรูปภาพทีละรูป (เรียกผ่าน AJAX จากหน้า development.php) ---
if (isset($_GET['delete_single_pic'])) {
    $dev_id = intval($_GET['dev_id']);
    $file_to_delete = $_GET['file'];
    $response = ['success' => false];

    // 1. ดึงข้อมูลรูปภาพปัจจุบันจาก Database
    $res = $link->query("SELECT dev_pic FROM development WHERE dev_id = $dev_id");
    $data = $res->fetch_assoc();

    if ($data) {
        $pics = explode(',', $data['dev_pic']);
        // 2. กรองเอาชื่อไฟล์ที่ต้องการลบออก
        $new_pics = array_filter($pics, function($p) use ($file_to_delete) {
            return trim($p) !== trim($file_to_delete);
        });

        $new_pic_string = implode(',', $new_pics);
        
        // 3. อัปเดต Database ด้วยรายชื่อรูปที่เหลือ
        $update = $link->query("UPDATE development SET dev_pic = '$new_pic_string' WHERE dev_id = $dev_id");
        
        if ($update) {
            // 4. ลบไฟล์จริงออกจากโฟลเดอร์
            $file_path = $upload_dir . $file_to_delete;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $response['success'] = true;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- ส่วนจัดการ POST (เพิ่ม/แก้ไขข้อมูล) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $dev_id = isset($_POST['dev_id']) ? intval($_POST['dev_id']) : null;
    $use_id = $_POST['use_id'];
    $dev_name = mysqli_real_escape_string($link, $_POST['dev_name']);
    $dev_date = mysqli_real_escape_string($link, $_POST['dev_date']);
    $dev_at = mysqli_real_escape_string($link, $_POST['dev_at']);
    $dev_obj = mysqli_real_escape_string($link, $_POST['dev_obj']);
    
    // รับค่ากลุ่มเรียน 1-5
    $sections = [];
    $counts = [];
    for($i=1; $i<=5; $i++){
        $sections[$i] = !empty($_POST["section_id$i"]) ? $_POST["section_id$i"] : "NULL";
        $counts[$i] = !empty($_POST["count_id$i"]) ? intval($_POST["count_id$i"]) : 0;
    }

    // จัดการอัปโหลดรูปภาพใหม่ (ถ้ามี)
    $uploaded_files = [];
    if (!empty($_FILES['dev_pics']['name'][0])) {
        foreach ($_FILES['dev_pics']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['dev_pics']['error'][$key] == 0) {
                $file_name = $_FILES['dev_pics']['name'][$key];
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_file_name = "dev_" . uniqid() . "_" . time() . "." . $ext;
                
                if (move_uploaded_file($tmp_name, $upload_dir . $new_file_name)) {
                    $uploaded_files[] = $new_file_name;
                }
            }
        }
    }
    $new_pics_string = implode(',', $uploaded_files);

    if ($action == 'add') {
        $sql = "INSERT INTO development (dev_name, dev_date, dev_at, dev_obj, use_id, dev_pic, 
                section_id1, count_id1, section_id2, count_id2, section_id3, count_id3, 
                section_id4, count_id4, section_id5, count_id5) 
                VALUES ('$dev_name', '$dev_date', '$dev_at', '$dev_obj', '$use_id', '$new_pics_string', 
                {$sections[1]}, {$counts[1]}, {$sections[2]}, {$counts[2]}, {$sections[3]}, {$counts[3]}, 
                {$sections[4]}, {$counts[4]}, {$sections[5]}, {$counts[5]})";
        
        if ($link->query($sql)) $_SESSION["dev_success"] = "บันทึกข้อมูลสำเร็จ";
    } 
    else if ($action == 'edit') {
        // กรณีแก้ไข: ดึงรูปภาพเดิมมาเพื่อรวมกับรูปใหม่ที่เพิ่งอัปโหลด
        $old_data = $link->query("SELECT dev_pic FROM development WHERE dev_id = $dev_id")->fetch_assoc();
        $old_pics = !empty($old_data['dev_pic']) ? $old_data['dev_pic'] : "";

        if (!empty($new_pics_string)) {
            // ถ้ารูปเดิมมีอยู่ ให้เอาอันใหม่ไปต่อท้ายด้วยเครื่องหมาย ,
            $final_pic_string = (!empty($old_pics)) ? $old_pics . "," . $new_pics_string : $new_pics_string;
        } else {
            // ถ้าไม่มีการอัปโหลดใหม่ ให้ใช้รูปเดิมที่เหลืออยู่ใน DB
            $final_pic_string = $old_pics;
        }

        $sql = "UPDATE development SET 
                dev_name='$dev_name', dev_date='$dev_date', dev_at='$dev_at', dev_obj='$dev_obj', 
                use_id='$use_id', dev_pic = '$final_pic_string',
                section_id1={$sections[1]}, count_id1={$counts[1]}, section_id2={$sections[2]}, count_id2={$counts[2]}, 
                section_id3={$sections[3]}, count_id3={$counts[3]}, section_id4={$sections[4]}, count_id4={$counts[4]}, 
                section_id5={$sections[5]}, count_id5={$counts[5]} 
                WHERE dev_id = $dev_id";
        
        if ($link->query($sql)) $_SESSION["dev_success"] = "แก้ไขข้อมูลสำเร็จ";
    }
    
    header("location: development.php");
    exit;
}

// กรณีลบข้อมูลทั้งหมด
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // ลบรูปภาพออกจาก Folder ก่อนลบ Database
    $data = $link->query("SELECT dev_pic FROM development WHERE dev_id = $id")->fetch_assoc();
    if(!empty($data['dev_pic'])){
        foreach(explode(',', $data['dev_pic']) as $file){
            if(file_exists($upload_dir . trim($file))) unlink($upload_dir . trim($file));
        }
    }
    $link->query("DELETE FROM development WHERE dev_id = $id");
    $_SESSION["dev_success"] = "ลบข้อมูลสำเร็จ";
    header("location: development.php");
    exit;
}
?>