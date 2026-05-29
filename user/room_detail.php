<?php
session_start();
include('dbconnect.php');

// รับค่าจาก query string
$roomTypeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;


//แสดงห้องพักที่ว่าง
$checkin = $_SESSION['checkin'] ?? '';
$checkout = $_SESSION['checkout'] ?? '';

$sqlRoomList = "
    SELECT room.room_name
    FROM room
    INNER JOIN typ_room ON room.typroom_ID = typ_room.typroom_ID
    LEFT JOIN booking 
        ON room.room_ID = booking.room_ID
        AND booking.check_in < ?
        AND booking.check_out > ?
    WHERE room.typroom_ID = ?
      AND booking.room_ID IS NULL
";

$stmtRoomList = $connection->prepare($sqlRoomList);
$stmtRoomList->bind_param("ssi", $checkout, $checkin, $roomTypeId);
$stmtRoomList->execute();
$resRoomList = $stmtRoomList->get_result();
$availableRooms = $resRoomList->fetch_all(MYSQLI_ASSOC);


$query = http_build_query([
    'checkin' => $_SESSION['checkin'] ?? '',
    'checkout' => $_SESSION['checkout'] ?? '',
    'num_cat' => $_SESSION['num_cat'] ?? ''
]);

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>รายละเอียดห้อง - Saba Cat Hotel</title>
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
            <a href="room_search.php?<?= http_build_query([
                                            'checkin' => $_SESSION['checkin'] ?? '',
                                            'checkout' => $_SESSION['checkout'] ?? '',
                                            'num_cat' => $_SESSION['num_cat'] ?? ''
                                        ]) ?>" style="text-decoration: none; color: inherit;">
                <i class="fas fa-arrow-left mr-2"></i>
            </a>

            <h2 class="fw-bold mb-0">| <?php echo $room['typ_name']; ?></h2>
        </div>

        <div class="row">
            <!-- ซ้าย: รูปใหญ่ + แกลเลอรี -->
            <div class="col-md-6">
                <!-- รูปใหญ่ -->
                <a href="<?php echo $images[0]['url']; ?>" data-lightbox="room">
                    <div style="aspect-ratio: 4/3; overflow: hidden;">
                        <img src="<?php echo $images[0]['url']; ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                    </div>
                </a>

                <!-- แกลเลอรี -->
                <?php if (count($images) > 1) : ?>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php foreach (array_slice($images, 1) as $img) : ?>
                            <a href="<?php echo $img['url']; ?>" data-lightbox="room">
                                <img src="<?php echo $img['url']; ?>" style="width: 80px; height: 80px; object-fit: cover;">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ขวา: กล่องสรุปการจอง -->
            <div class="col-md-6">
                <div style="background-color: #FFFCEE; padding: 20px; border: 1px solid #000; max-width: 250px;">
                    <p style="margin: 0;">ราคา</p>
                    <h5 style="font-weight: bold;"><?php echo number_format($room['price']); ?> บาท/คืน</h5>

                    <!-- วันที่เช็คอิน -->
                    <?php
                    function formatDateThaiFull($strDate)
                    {
                        $months = [
                            "",
                            "มกราคม",
                            "กุมภาพันธ์",
                            "มีนาคม",
                            "เมษายน",
                            "พฤษภาคม",
                            "มิถุนายน",
                            "กรกฎาคม",
                            "สิงหาคม",
                            "กันยายน",
                            "ตุลาคม",
                            "พฤศจิกายน",
                            "ธันวาคม"
                        ];

                        $timestamp = strtotime($strDate);
                        $day = date("j", $timestamp);
                        $month = $months[date("n", $timestamp)];
                        $year = date("Y", $timestamp) + 543;

                        return "{$day} {$month} {$year}";
                    }
                    ?>
                    <div style="border: 1px solid #aaa; padding: 8px 10px; margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <span><?php echo formatDateThaiFull($_SESSION['checkin']); ?></span>
                        <i class="fas fa-calendar-alt"></i>
                    </div>

                    <!-- วันที่เช็คเอาท์ -->
                    <div style="border: 1px solid #aaa; border-top: none; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center;">
                        <span><?php echo formatDateThaiFull($_SESSION['checkout']); ?></span>
                        <i class="fas fa-calendar-alt"></i>
                    </div>

                    <!-- จำนวนแมว -->
                    <div style="border: 1px solid #aaa; border-top: none; padding: 8px 10px; display: flex; justify-content: space-between;">
                        <span>แมว</span>
                        <span><?php echo $_SESSION['num_cat']; ?> ตัว</span>
                    </div>

                    <!-- จำนวนคืน -->
                    <div style="border: 1px solid #aaa; border-top: none; padding: 8px 10px; display: flex; justify-content: space-between;">
                        <span>จำนวน</span>
                        <span><?php echo $nights; ?> คืน</span>
                    </div>

                    <!-- รวมราคา -->
                    <div style="background-color: #F5F0E6; padding: 10px 10px; margin-top: 10px; display: flex; justify-content: space-between; font-weight: bold;">
                        <span>รวม</span>
                        <span><?php echo number_format($nights * $room['price']); ?> บาท</span>
                    </div>

                    <?php if (isset($_SESSION['member_ID'])) : ?>
                        <form action="room_booking.php" method="get" style="margin-top: 10px;">
                            <input type="hidden" name="id" value="<?php echo $room['typroom_ID']; ?>">
                            <select name="room_name" class="form-control my-2" required>
                                <option value="">-- เลือกเลขห้อง --</option>
                                <?php foreach ($availableRooms as $roomOption): ?>
                                    <option value="<?php echo $roomOption['room_name']; ?>">
                                        ห้อง <?php echo $roomOption['room_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-dark btn-block" style="border-radius: 0;">จองเลย</button>
                        </form>
                    <?php else : ?>
                        <button onclick="redirectToLogin()" class="btn btn-dark btn-block mt-3" style="border-radius: 0; background-color: #000;">จองเลย</button>

                        <script>
                            function redirectToLogin() {
                                alert("กรุณาเข้าสู่ระบบก่อนทำการจอง");
                                window.location.href = "login.php";
                            }
                        </script>
                    <?php endif; ?>
                </div>
            </div>


        </div>

        <!-- รายละเอียด -->
        <div class="mt-4">
            <h5>รายละเอียด :</h5>
            <ul>
                <li>น้องแมวพักได้ <?php echo $room['num_cat']; ?> ตัว/ห้อง</li>
                <li><?php echo $room['short_detail']; ?></li>
                <li><?php echo nl2br($room['detail']); ?></li>
            </ul>
        </div>
        <h5>
            ข้อกำหนดและเงื่อนไข :
            <a href="condition.php" style="font-size: 0.9em; font-weight: 300; margin-left: 10px; color: #5D4902; text-decoration: underline;">
                อ่านข้อกำหนดและเงื่อนไขของเรา
            </a>
        </h5>



    </div>


    <!-- Footer -->
    <?php include("footer.php"); ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="kindergarten-website-template/lib/lightbox/js/lightbox.min.js"></script>
</body>

</html>