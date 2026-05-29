<?php
include("check_login.php");
include('dbconnect.php');

if (isset($_GET['id'])) {
    $imageID = $_GET['id'];

    // ดึงข้อมูลรูปภาพก่อนลบ
    $sqlGetImage = "SELECT * FROM room_image WHERE image_ID = '$imageID'";
    $result = $connection->query($sqlGetImage);

    if ($result && $result->num_rows > 0) {
        $imageData = $result->fetch_assoc();

        // ลบไฟล์จากโฟลเดอร์image
        $url = $imageData['url'];
        $filePath = str_replace("http://localhost/Cathotel_Project/", "", $url);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // ลบข้อมูลจากฐานข้อมูล
        $sqlDelete = "DELETE FROM room_image WHERE image_ID = '$imageID'";
        if ($connection->query($sqlDelete)) {
            // กลับไปหน้า edit_room.php พร้อม typroom_ID
            header("Location: edit_room.php?id=" . $imageData['typroom_ID']);
            exit();
        } else {
            echo "เกิดข้อผิดพลาดในการลบข้อมูล";
        }
    } 
} 
?>
