<?php
include("dbconnect.php");

// รับค่าจากฟอร์ม
$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
if ($password !== $confirm_password) {
    echo "<script>
        alert('รหัสผ่านไม่ตรงกัน! โปรดตรวจสอบรหัสผ่านและลองใหม่อีกครั้ง');
        window.history.back();
    </script>";
    exit;
}

// ตรวจสอบอีเมลซ้ำ
$stmt = $connection->prepare("SELECT member_ID FROM member WHERE email = ?");
if (!$stmt) {
    die('Prepare failed: ' . $connection->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<script>
        alert('อีเมลนี้มีผู้ใช้งานอยู่แล้ว!');
        window.history.back();
    </script>";
    exit;
}
$stmt->close();

// ตรวจสอบเบอร์โทรซ้ำ
$stmt = $connection->prepare("SELECT member_ID FROM member WHERE phone = ?");
if (!$stmt) {
    die('Prepare failed: ' . $connection->error);
}
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<script>
        alert('เบอร์โทรศัพท์นี้มีผู้ใช้งานแล้ว!');
        window.history.back();
    </script>";
    exit;
}
$stmt->close();

// เข้ารหัสรหัสผ่าน
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// บันทึกข้อมูลลงฐานข้อมูล
$stmt = $connection->prepare("INSERT INTO member (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die('Prepare failed: ' . $connection->error);
}
$stmt->bind_param("ssss", $fullname, $email, $phone, $password);

if ($stmt->execute()) {
    echo "<script>
        alert('สมัครสมาชิกสำเร็จ! Login เพื่อเข้าสู่ระบบ');
        window.location.href = 'login.php';
    </script>";
} else {
    echo "<script>
        alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        window.history.back();
    </script>";
}

$stmt->close();
$connection->close();
?>
