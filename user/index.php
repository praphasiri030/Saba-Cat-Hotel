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
        .header-image-container {
            position: relative;
            width: 100%;
            height: 100vh;
            /* 100% of the viewport height */
            overflow: hidden;
        }

        .header-image {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: translate(-50%, -50%);
        }

        .about-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }

        .slider-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .slider-images {
            display: flex;
            height: 100%;
            transition: transform 0.5s ease;
        }

        .slider-images img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
            flex-shrink: 0;
        }

        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 2rem;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            z-index: 100;
        }

        .prev {
            left: 10px;
        }

        .next {
            right: 10px;
        }
    </style>

</head>

<body>
    <!-- Navbar Start -->
    <?php include("navbar.php"); ?>
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="slider-container">
        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
        <div class="slider-images" id="slider">
            <img src="/UserCatHotel_Project/image_room/index1.png" alt="Header Image">
            <img src="/UserCatHotel_Project/image_room/index2.png" alt="Header Image">
            <img src="/UserCatHotel_Project/image_room/index3.png" alt="Header Image">
        </div>
        <button class="next" onclick="moveSlide(1)">&#10095;</button>
    </div>
    <!-- Header End -->



    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container about-container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-7">
                    <h1 class="mb-4">Saba Cat Hotel</h1>
                    <h1>-</h1>
                    <p>โรงแรมแมวที่ดีที่สุดได้มาตรฐาน รับฝากเลี้ยงแมว ราคามิตรภาพระบบปิด ปลอดภัย ห้องแอร์ หลับสบายสิ่งอำนวยความสะดวกครบครัน มีพี่เลี้ยงดูแลใกล้ชิดมีของเล่นมากมาย เปิดเพลงเพราะๆ ดนตรีสำหรับแมวให้น้องเหมียวอารมณ์ดีทั้งวัน
                        โรงแรมมห้องพักกว้างขวางกว่า 50 ห้องที่ออกแบบอย่างเรียบง่ายและสะอาด และมีห้องที่ติดตั้งประตูกระจกบานใหญ่ให้สัตว์เลี้ยงของคุณมีอิสระเสรีอย่างเต็มที่</p>
                    <h1>-</h1>
                </div>
            </div>
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

    <script>
        let currentIndex = 0;

        function moveSlide(direction) {
            const slider = document.getElementById('slider');
            const images = slider.querySelectorAll('img');
            const total = images.length;

            currentIndex += direction;
            if (currentIndex < 0) currentIndex = total - 1;
            if (currentIndex >= total) currentIndex = 0;

            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
        }
    </script>

</body>

</html>