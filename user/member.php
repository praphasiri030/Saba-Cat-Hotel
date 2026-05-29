<?php
session_start();
include('dbconnect.php');

$user = null;
$passwordError = "";
$successMessage = "";
$phoneError = "";

//เปลี่ยนรหัสผ่าน
if (isset($_SESSION['member_ID'])) {
    $sql = "SELECT fullname, email, phone, password FROM member WHERE member_ID = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $_SESSION['member_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // ตรวจสอบรหัสผ่านเดิม
    if ($user && $oldPassword === $user['password']) {
        if ($newPassword === $confirmPassword) {
            $updateSql = "UPDATE member SET password = ? WHERE member_ID = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("si", $newPassword, $_SESSION['member_ID']);
            if ($updateStmt->execute()) {
                $successMessage = "เปลี่ยนรหัสผ่านเรียบร้อยแล้ว";
            } else {
                $passwordError = "เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน";
            }
        } else {
            $passwordError = "รหัสผ่านใหม่ไม่ตรงกัน";
        }
    } else {
        $passwordError = "รหัสผ่านเดิมไม่ถูกต้อง";
    }
}

//แก้ไขเบอร์โทร
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_phone'])) {
    $newPhone = trim($_POST['phone']);

    // ตรวจสอบว่าเบอร์นี้ซ้ำกับของคนอื่นหรือไม่
    $checkSql = "SELECT member_ID FROM member WHERE phone = ? AND member_ID != ?";
    $checkStmt = $connection->prepare($checkSql);
    $checkStmt->bind_param("si", $newPhone, $_SESSION['member_ID']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $phoneError = "เบอร์โทรศัพท์นี้มีอยู่ในระบบแล้ว กรุณาใช้เบอร์อื่น";
    } else {
        // ถ้าไม่ซ้ำ ให้อัปเดต
        $updatePhoneSql = "UPDATE member SET phone = ? WHERE member_ID = ?";
        $stmt = $connection->prepare($updatePhoneSql);
        $stmt->bind_param("si", $newPhone, $_SESSION['member_ID']);

        if ($stmt->execute()) {
            $successMessage = "อัปเดตเบอร์โทรศัพท์เรียบร้อยแล้ว";
            $user['phone'] = $newPhone; // อัปเดตค่าที่แสดงผล
        } else {
            $phoneError = "ไม่สามารถอัปเดตเบอร์โทรศัพท์ได้";
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ข้อมูลผู้ใช้ - Saba Cat Hotel</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    <link rel="icon" type="image/png" href="image_room/logo.jpg">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Flaticon Font -->
    <link href="kindergarten-website-template/lib/flaticon/font/flaticon.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="kindergarten-website-template/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="kindergarten-website-template/lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="kindergarten-website-template/css/style.css" rel="stylesheet">

</head>

<body>

    <!-- Navbar -->
    <?php include("navbar.php"); ?>

    <div class="container py-4">
        <!-- ปุ่มย้อนกลับและชื่อห้อง -->
        <div class="d-flex align-items-center mb-4">
            <a href="index.php" style="text-decoration: none; color: inherit;">
                <i class="fas fa-arrow-left mr-2"></i>
            </a>
            <h4>ข้อมูลติดต่อ</h4>
        </div>


        <!-- ฟอร์มข้อมูลส่วนตัว -->
        <div class="container py-4" style="max-width: 720px;">
            <form method="post" action="" style="background-color: #FFFCEE; border-radius: 10px; padding: 20px; border: 1px solid #ccc;">
                <div class="form-group mb-3">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" class="form-control" name="fullname"
                        value="<?php echo htmlspecialchars($user['fullname']); ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label>อีเมล</label>
                    <input type="email" class="form-control" name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label>เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control" name="phone"
                        value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                <?php if ($phoneError): ?>
                    <div class="alert alert-danger"><?php echo $phoneError; ?></div>
                <?php elseif ($successMessage): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>
                <div class="d-flex justify-content-end">
                    <button type="submit" name="update_phone" class="btn btn-warning">บันทึก</button>
                </div>
            </form>
        </div>


        <!-- ฟอร์มเปลี่ยนรหัสผ่าน -->
        <div class="container py-4" style="max-width: 720px;">
            <form method="post" action="" style="background-color: #FFFCEE; border-radius: 10px; padding: 20px; border: 1px solid #ccc;">
                <h5 class="mb-3">เปลี่ยนรหัสผ่าน</h5>

                <?php if ($passwordError): ?>
                    <div class="alert alert-danger"><?php echo $passwordError; ?></div>
                <?php elseif ($successMessage): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>

                <div class="form-group mb-3">
                    <label>รหัสผ่านเดิม</label>
                    <input type="password" class="form-control" name="old_password" required>
                </div>
                <div class="form-group mb-3">
                    <label>รหัสผ่านใหม่</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>
                <div class="form-group mb-3">
                    <label>ยืนยันรหัสผ่านใหม่อีกครั้ง</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" name="change_password" class="btn btn-warning">บันทึก</button>
                </div>
            </form>
        </div>



    </div>


    <!-- Footer -->
    <?php include("footer.php"); ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="kindergarten-website-template/lib/lightbox/js/lightbox.min.js"></script>
</body>

</html>