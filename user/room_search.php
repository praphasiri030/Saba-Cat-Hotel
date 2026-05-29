<?php
session_start();
include('dbconnect.php');

// รับค่าจากฟอร์มค้นหา
// ตรวจสอบค่าจาก GET ก่อน ถ้าไม่มีค่อยใช้ POST
$checkin = $_GET['checkin'] ?? $_POST['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? $_POST['checkout'] ?? '';
$num_cat = isset($_GET['num_cat']) ? (int)$_GET['num_cat'] : (isset($_POST['num_cat']) ? (int)$_POST['num_cat'] : 1);

// บันทึกลง Session
$_SESSION['checkin'] = $checkin;
$_SESSION['checkout'] = $checkout;
$_SESSION['num_cat'] = $num_cat;


// แปลงวันที่ให้อยู่ในรูปแบบ วัน-เดือน-ปี
$checkin_fmt = date("d-m-Y", strtotime($checkin));
$checkout_fmt = date("d-m-Y", strtotime($checkout));

// คำนวณจำนวนคืน
$datetime1 = new DateTime($checkin);
$datetime2 = new DateTime($checkout);
$interval = $datetime1->diff($datetime2);
$nights = $interval->days;

$roomData = [];
if ($checkin && $checkout) {
    $sql = "SELECT DISTINCT typ_room.typroom_ID, room.*, typ_room.*
    FROM room
    INNER JOIN typ_room ON room.typroom_ID = typ_room.typroom_ID
    LEFT JOIN booking 
    ON room.room_ID = booking.room_ID
    AND booking.check_in < ?
    AND booking.check_out > ?
    AND booking.bookstate_ID NOT IN ('B05','C02')
    WHERE typ_room.num_cat >= ?
    AND booking.room_ID IS NULL
    GROUP BY typ_room.typroom_ID";

    // $debug_sql = str_replace_array($sql, [$checkout, $checkin, $num_cat]);
    // echo $debug_sql;

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssi", $checkout, $checkin, $num_cat);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $roomData = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// ดึงรูปภาพของประเภทห้อง
$sqlimage = "SELECT `image_ID`, `typroom_ID`, `url` FROM `room_image` ORDER BY image_ID";
$imageList = [];
$resultimage = $connection->query($sqlimage);
if ($resultimage && $resultimage->num_rows > 0) {
    $imageList = $resultimage->fetch_all(MYSQLI_ASSOC);
}

$imagesByRoom = [];
foreach ($imageList as $img) {
    $roomId = $img['typroom_ID'];
    if (!isset($imagesByRoom[$roomId])) {
        $imagesByRoom[$roomId] = [];
    }
    $imagesByRoom[$roomId][] = $img['url'];
}

//ห้องว่างตามวันที่เลือก
$availableCount = [];
foreach ($roomData as $room) {
    $typroom_ID = $room['typroom_ID'];
    $sql_avail = "SELECT COUNT(*) AS available
                  FROM room
                  WHERE typroom_ID = ?
                  AND room_ID NOT IN (
                      SELECT room_ID FROM booking
                      WHERE check_in < ? AND check_out > ? AND bookstate_ID NOT IN ('B05','C02')
                  )";
    $stmt_avail = $connection->prepare($sql_avail);
    $stmt_avail->bind_param("iss", $typroom_ID, $checkout, $checkin);
    $stmt_avail->execute();
    $res_avail = $stmt_avail->get_result()->fetch_assoc();
    $availableCount[$typroom_ID] = $res_avail['available'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>ผลการค้นหาห้องว่าง - Saba Cat Hotel</title>
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

    <div class="container mt-5">
        <!-- ปุ่มกลับ -->
        <div class="d-flex align-items-center mb-4">
            <a href="room_list.php">
                <i class="fas fa-arrow-left" style="font-size: 24px; font-weight: bold; color: black;"></i>
            </a>
            <a>
                |ห้องว่างสำหรับ: <?php echo $checkin_fmt; ?> ถึง <?php echo $checkout_fmt; ?> | <?php echo $nights; ?> คืน
            </a>
        </div>
        <hr class="my-4 border-dark">


        <?php if (empty($roomData)) : ?>
            <div class="alert alert-warning text-center">
                ไม่พบห้องว่างในช่วงเวลาที่เลือก
            </div>
        <?php else : ?>
            <!-- แสดงรายการห้องพัก -->
            <div class="container">
                <?php foreach ($roomData as $index => $room) { ?>
                    <div class="row align-items-start py-4">
                        <!-- รูปภาพ (ฝั่งซ้าย) -->
                        <div class="col-md-5">
                            <?php
                            $roomId = $room['typroom_ID'];
                            if (isset($imagesByRoom[$roomId])) {
                                $images = $imagesByRoom[$roomId];
                                $firstImage = $images[0];
                                echo '<a href="' . $firstImage . '" data-lightbox="room-' . $roomId . '" data-title="' . $room['typ_name'] . '">';
                                echo '<div style="width: 100%; aspect-ratio: 4 / 3; overflow: hidden;">';
                                echo '<img src="' . $firstImage . '" style="width: 100%; height: 100%; object-fit: cover;" class="img-fluid" />';
                                echo '</div>';
                                echo '</a>';
                            } else {
                                echo '<p class="text-muted">ไม่มีรูปภาพ</p>';
                            }
                            ?>
                        </div>

                        <!-- รายละเอียดห้อง (ฝั่งขวา) -->
                        <div class="col-md-7 d-flex flex-column justify-content-between text-start" style="height: 100%;">
                            <div>
                                <h4 class="fw-semibold"><?php echo $room['typ_name']; ?></h4>
                                <p class="mb-2"><?php echo $room['short_detail']; ?></p>
                                <p class="mb-2">- น้องแมวพักได้ <?php echo $room['num_cat']; ?> ตัว/ห้อง</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                <span class="fw-bold text-uppercase" style="font-weight:bold">
                                    ว่าง <?php echo $availableCount[$room['typroom_ID']] ?? 0; ?> ห้อง
                                </span>

                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                <span class="fw-bold text-uppercase" style="font-weight:bold">ราคา <?php echo number_format($room['price']); ?> บาท/คืน</span>
                                <a href="room_detail.php?id=<?php echo $room['typroom_ID']; ?>" style="background-color: black; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; display: inline-block;">
                                    รายละเอียดเพิ่มเติม
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- เส้นคั่นระหว่างห้อง -->
                    <?php if ($index < count($roomData) - 1) { ?>
                        <hr class="my-4 border-dark">
                    <?php } ?>
                <?php } ?>
            </div>

        <?php endif; ?>

    </div>

    <!-- Footer -->
    <?php include("footer.php"); ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="kindergarten-website-template/lib/lightbox/js/lightbox.min.js"></script>
</body>

</html>