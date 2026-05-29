<?php
session_start();
include('dbconnect.php');


// รับค่าจาก query string
$roomTypeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$roomName = $_GET['room_name'] ?? '';


$user = null;
if (isset($_SESSION['member_ID'])) {
    $sql = "SELECT fullname, email, phone FROM member WHERE member_ID = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $_SESSION['member_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}



// ตรวจสอบว่ามีข้อมูลห้องพักหรือไม่
$sql = "SELECT * FROM typ_room WHERE typroom_ID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $roomTypeId);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

// ดึงรูปภาพ
$sqlimg = "SELECT url FROM room_image WHERE typroom_ID = ? ORDER BY image_ID";
$stmtimg = $connection->prepare($sqlimg);
$stmtimg->bind_param("i", $roomTypeId);
$stmtimg->execute();
$resimg = $stmtimg->get_result();
$images = $resimg->fetch_all(MYSQLI_ASSOC);

$datetime1 = new DateTime($_SESSION['checkin']);
$datetime2 = new DateTime($_SESSION['checkout']);
$interval = $datetime1->diff($datetime2);
$nights = $interval->days;


// ดึงรหัสห้อง (room_ID) จาก room_name และ typroom_ID
$sqlRoom = "SELECT room_ID FROM room WHERE room_name = ? AND typroom_ID = ?";
$stmtRoom = $connection->prepare($sqlRoom);
$stmtRoom->bind_param("si", $roomName, $roomTypeId);
$stmtRoom->execute();
$resRoom = $stmtRoom->get_result();
$selectedRoom = $resRoom->fetch_assoc();

if (!$selectedRoom) {
    // ถ้าไม่พบห้อง ให้แจ้ง error หรือ redirect
    die("ไม่พบข้อมูลห้องที่เลือก");
}


//บันทึกการจองลง database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // เตรียมข้อมูลจาก session
    $roomID = $selectedRoom['room_ID'];
    $memberID = $_SESSION['member_ID'];
    $checkin = $_SESSION['checkin'];
    $checkout = $_SESSION['checkout'];
    $numCats = $_SESSION['num_cat'];
    $notes = $_POST['notes'];
    $bookDate = date("Y-m-d");
    $price = $nights * $room['price'];

    // Insert ลงตาราง booking
    $sql = "INSERT INTO booking (room_ID, member_ID, bookstate_ID, book_date, check_in, check_out, num_catbook, note, book_price)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $bookstate = 'B01';
    $stmt->bind_param("sissssisd", $roomID, $memberID, $bookstate, $bookDate, $checkin, $checkout, $numCats, $notes, $price);

    if ($stmt->execute()) {
        $bookingID = $stmt->insert_id;
        header("Location: room_payment.php?booking_id=$bookingID");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึก: " . $stmt->error;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>จองห้องพัก - Saba Cat Hotel</title>
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
            <a href="room_detail.php?id=<?php echo $roomTypeId; ?>" style="text-decoration: none; color: inherit;">
                <i class="fas fa-arrow-left mr-2"></i>
            </a>
            <h2 class="fw-bold mb-0">| <?php echo $room['typ_name']; ?></h2>
        </div>

        <div class="container py-4" style="max-width: 720px;">

            <!-- กล่องสรุปห้อง -->
            <div style="background-color: #FFFCEE; border: 1px solid #ccc; border-radius: 10px; padding: 20px; margin-bottom: 30px;">
                <div class="d-flex gap-3 align-items-start">
                    <img src="<?php echo $images[0]['url']; ?>" style="width: 120px; height: 90px; object-fit: cover; border-radius: 6px;">
                    <div>
                        <h5><?php echo $room['typ_name'] . " ห้อง " . htmlspecialchars($roomName); ?></h5>
                        <ul style="padding-left: 1rem;">
                            <li>น้องแมวพักได้ <?php echo $room['num_cat']; ?> ตัว</li>
                            <li>ห้องขนาด <?php echo $room['short_detail']; ?></li>
                            <h6 class="text-end">ราคารวม (<?php echo number_format($room['price']); ?> x <?php echo $nights; ?> วัน): <strong><?php echo number_format($nights * $room['price']); ?> บาท</strong></h6>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <p class="mb-1">เช็คอิน</p>
                        <strong><?php echo date('d/m/Y', strtotime($_SESSION['checkin'])); ?></strong>
                    </div>
                    <div class="col-6">
                        <p class="mb-1">เช็คเอาต์</p>
                        <strong><?php echo date('d/m/Y', strtotime($_SESSION['checkout'])); ?></strong>
                    </div>
                </div>
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <p class="mb-1">จำนวนวันทั้งหมด</p>
                        <strong><?php echo $nights; ?> วัน</strong>
                    </div>
                    <div class="col-6">
                        <p class="mb-1">จำนวนแมว</p>
                        <strong><?php echo $_SESSION['num_cat']; ?> ตัว</strong>
                    </div>
                </div>

            </div>

            <!-- ฟอร์มกรอกข้อมูล -->
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
                        value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                </div>
                <div class="form-group mb-4">
                    <label>คำขอพิเศษหรือรายละเอียดเพิ่มเติม</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="พิมพ์ที่นี่..."></textarea>
                    <small class="text-danger d-block mt-2">* กรุณาชำระเงินภายใน 1 ชั่วโมง</small>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" name="submit" class="btn btn-warning">ชำระเงิน ➔</button>

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