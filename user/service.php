<?php
session_start();
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

    <style>
        .service-card {
            padding: 10px;
            text-align: center;
            background-color: #FFFCEE;
            margin-bottom: 1.5rem;
            height: 100%;
            /* เพื่อให้เท่ากันแม้ความสูงไม่เท่า */
        }

        .service-card img {
            width: 100%;
            aspect-ratio: 1 / 1;
            /* กำหนดสัดส่วน 1:1 */
            object-fit: cover;
            /* ครอบรูปให้พอดีโดยไม่บิด */
        }
    </style>

</head>

<body>
    <!-- Navbar Start -->
    <?php include("navbar.php"); ?>
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="container-fluid px-0 px-md-5 mb-4" style="background-color: #FFFCEE;">
        <div class="row align-items-center px-3">
            <div class="col-lg-6 text-center text-lg-left py-5">
                <h1 class="font-weight-bold" style="font-size: 36px; color: #000;">Our Service การบริการของเรา</h1>
                <p style="font-size: 18px; color: #000; font-weight: 300;">
                    Saba Cat Hotel มีการบริการและสถานที่ที่ยอดเยี่ยม ด้วยการบริการที่ใส่ใจลูกค้าทุกคน
                    สัตว์เลี้ยงที่รักของคุณจะไม่มีความเครียด และได้รับการดูแลด้วยสิ่งอำนวยความสะดวกมากมายในโรงแรมของเรา
                </p>
            </div>

            <div class="col-lg-6 text-center text-lg-right">
                <img src="/UserCatHotel_Project/image_room/service1.jpg" style="max-width: 70%; height: auto;">
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Facilities Start -->
    <div class="container-fluid pt-5">
        <div class="container pb-3">
            <div class="row">
                <div class="col-lg-4 col-md-6 pb-1">
                    <div class="service-card">
                        <img src="/UserCatHotel_Project/image_room/service2.png" alt="Facility 1" class="mb-3" style="max-width: 80%; height: auto;">
                        <p class="m-0">มีกล้องวงจรปิด พนักงานคอยดูตลอด 24 ชั่วโมง</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 pb-1">
                    <div class="service-card">
                        <img src="/UserCatHotel_Project/image_room/service3.png" alt="Facility 2" class="mb-3" style="max-width: 80%; height: auto;">
                        <p class="m-0">น้ำดื่มสะอาดผ่านการกรองอย่างดี</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 pb-1">
                    <div class="service-card">
                        <img src="/UserCatHotel_Project/image_room/service4.png" alt="Facility 3" class="mb-3" style="max-width: 80%; height: auto;">
                        <p class="m-0">เปิดเครื่องปรับอากาศให้น้องแมวตลอด 24 ชั่วโมง</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 pb-1">
                    <div class="service-card">
                        <img src="/UserCatHotel_Project/image_room/service5.png" alt="Facility 4" class="mb-3" style="max-width: 80%; height: auto;">
                        <p class="m-0">มีการถ่ายภาพส่งให้ดูทุกวันทางไลน์</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 pb-1">
                    <div class="service-card">
                        <img src="/UserCatHotel_Project/image_room/service6.png" alt="Facility 5" class="mb-3" style="max-width: 80%; height: auto;">
                        <p class="m-0">มีพนักงานคอยดูแล เล่นด้วย เช้า-กลางวัน-เย็น</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 pb-1">
                    <div class="service-card">
                        <img src="/UserCatHotel_Project/image_room/service7.png" alt="Facility 6" class="mb-3" style="max-width: 80%; height: auto;">
                        <p class="m-0">ปล่อยมาเล่นด้านนอกแบบส่วนตัว ไม่รวมกับบ้านอื่น พื้นที่ส่วนกลางที่ออกแบบ มาให้ปีนป่ายได้อย่างอิสระ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Facilities End -->


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
</body>

</html>