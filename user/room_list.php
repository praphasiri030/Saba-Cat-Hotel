<?php
session_start();
// เชื่อมต่อฐานข้อมูล
include('dbconnect.php');

// ดึงข้อมูลห้องพัก
$sql = "SELECT * FROM typ_room ORDER BY typroom_ID ASC";
$roomData = [];
$result = $connection->query($sql);
if ($result && $result->num_rows > 0) {
    $roomData = $result->fetch_all(MYSQLI_ASSOC);
}

// ดึงข้อมูลรูปภาพ
$sqlimage = "SELECT `image_ID`, `typroom_ID`, `url` FROM `room_image` ORDER BY image_ID";
$imageList = [];
$resultimage = $connection->query($sqlimage);
if ($resultimage && $resultimage->num_rows > 0) {
    $imageList = $resultimage->fetch_all(MYSQLI_ASSOC);
}

// จัดกลุ่มรูปภาพตาม typroom_ID เพื่อให้ง่ายต่อการเรียกใช้งาน
$imagesByRoom = [];
foreach ($imageList as $img) {
    $roomId = $img['typroom_ID'];
    if (!isset($imagesByRoom[$roomId])) {
        $imagesByRoom[$roomId] = [];
    }
    $imagesByRoom[$roomId][] = $img['url'];
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Saba Cat Hotel</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    <link rel="icon" type="image/png" href="image_room/logo.jpg">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <!-- ใช้ Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Flaticon Font -->
    <link href="kindergarten-website-template/lib/flaticon/font/flaticon.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="kindergarten-website-template/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="kindergarten-website-template/lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="kindergarten-website-template/css/style.css" rel="stylesheet">

    <!-- Lightbox CSS -->
    <link href="kindergarten-website-template/lib/lightbox/css/lightbox.min.css" rel="stylesheet">


    <style>
        /* CSS สำหรับกล่องค้นหา */
        .form-container {
            border: 1px solid #000;
            padding: 20px;
            max-width: 900px;
            margin: 20px auto;
            font-family: "Inter", sans-serif;
            border-radius: 8px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            /* ระยะห่างระหว่างกล่อง */
            flex-wrap: wrap;
            /* พับบรรทัดเมื่อหน้าจอแคบ */
            align-items: flex-end;
            /* ให้ปุ่มอยู่ล่างสุดของแถว */
        }

        .form-group {
            font-family: 'Noto Sans Thai', sans-serif;;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 150px;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-button {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }


        .form-button button {
            width: 100%;
            /* ให้ปุ่มกว้างเท่าช่อง input อื่น */
            padding: 10px;
            font-size: 16px;
            background-color: #FFF5D0;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <?php include("navbar.php"); ?>
    <!-- Navbar End -->

    <!-- About Start -->
    <!-- แบบฟอร์มค้นหาห้องว่าง -->
    <div class="form-container">
        <form name="room_search-form" action="room_search.php" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="checkin">เช็คอิน</label>
                    <input type="date" id="checkin" min="" name="checkin" required>
                </div>

                <div class="form-group">
                    <label for="checkout">เช็คเอาท์</label>
                    <input type="date" id="checkout" min="" name="checkout" required>
                </div>

                <div class="form-group">
                    <label for="num_cat">จำนวนแมว</label>
                    <input type="number" id="num_cat" name="num_cat" min="1" max="4" value="1">
                </div>
                <div class="form-group">
                    <div class="form-button button">
                        <label>&nbsp;</label> <!-- ช่องว่างให้ความสูงเท่า label อื่น -->
                        <button type="submit" style="font-weight: bold;">ดูห้องว่าง➔</button>
                    </div>

                </div>

            </div>
        </form>
    </div>

    <div class="container-fluid" style="background-color: #FFFCEE; min-height: 100vh;">
        <div class="row justify-content-center py-4">
            <?php foreach ($roomData as $room) { ?>
                <div class="col-12 col-md-10 col-lg-8 mb-4">
                    <div class="row align-items-start py-4">
                        <!-- รูปภาพ (ฝั่งซ้าย) -->
                        <div class="image-list" style="width: 40%; padding-right: 20px;"> <!-- เพิ่ม padding-right ที่นี่ -->
                            <?php
                            $roomId = $room['typroom_ID'];
                            if (isset($imagesByRoom[$roomId])) {
                                $images = $imagesByRoom[$roomId];
                                $firstImage = $images[0];

                                // รูปใหญ่แรกที่ขนาดเท่ากัน
                                echo '<a href="' . $firstImage . '" data-lightbox="room-' . $roomId . '" data-title="' . $room['typ_name'] . '">';
                                echo '<div style="width: 100%; aspect-ratio: 4 / 3; overflow: hidden;">';
                                echo '<img src="' . $firstImage . '" style="width: 100%; height: 100%; object-fit: cover;" class="img-fluid" />';
                                echo '</div>';
                                echo '</a>';

                                // รูปย่อยอื่น ๆ
                                if (count($images) > 1) {
                                    echo '<div class="d-flex flex-wrap gap-1 mt-2">';
                                    foreach (array_slice($images, 1) as $imgUrl) {
                                        echo '<a href="' . $imgUrl . '" data-lightbox="room-' . $roomId . '" data-title="' . $room['typ_name'] . '">';
                                        echo '<img src="' . $imgUrl . '" style="width: 80px; height: 80px; object-fit: cover;" />';
                                        echo '</a>';
                                    }
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">ไม่มีรูปภาพ</p>';
                            }
                            ?>
                        </div>

                        <!-- รายละเอียดห้อง (ฝั่งขวา) -->
                        <div class="room-details text-start" style="width: 60%;">
                            <h4 class="mb-2"><?php echo $room['typ_name']; ?></h4>
                            <p class="card-text mb-2">- น้องแมวพักได้ <?php echo $room['num_cat']; ?> ตัว/ห้อง</p>
                            <p class="card-text mb-2">- ราคา <?php echo $room['price']; ?> บาท/วัน</p>
                            <p class="card-text mb-2">- <?php echo $room['short_detail']; ?></p>
                            <p class="card-text"><?php echo $room['detail']; ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>



    <!-- About End -->


    <!-- Footer Start -->
    <?php
    include("footer.php"); ?>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary p-3 back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="kindergarten-website-template/lib/easing/easing.min.js"></script>
    <script src="kindergarten-website-template/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="kindergarten-website-template/lib/isotope/isotope.pkgd.min.js"></script>
    <script src="kindergarten-website-template/lib/lightbox/js/lightbox.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- Lightbox JavaScript -->
    <script src="kindergarten-website-template/lib/lightbox/js/lightbox.min.js"></script>


    <script>
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');

        const minDate = `${yyyy}-${mm}-${dd}`;
        document.getElementById("checkin").min = minDate;
        document.getElementById("checkout").min = minDate;
    </script>

</body>

</html>