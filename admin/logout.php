<?php
session_start();  // เริ่มต้น session

// ลบข้อมูลทั้งหมดใน session
session_unset();

// ทำลาย session ทั้งหมด (จะทำให้ session id หมดอายุ)
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้า index หรือ login
header("Location: report_day.php");  // หรือใช้ "Location: login.php" ถ้าต้องการให้กลับไปที่หน้า login
exit();  // หยุดการทำงานของสคริปต์หลังจาก redirect
?>
