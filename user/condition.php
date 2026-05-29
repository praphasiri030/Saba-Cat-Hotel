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
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
            border-top: 1px solid #dee2e6;
            border-radius: .25rem;
            margin-bottom: 1.5rem;
            height: 100%;
        }

        .terms-header {
            width: 100vw;
            margin-left: calc(-50vw + 50%);
            padding: 20px;
            font-weight: bold;
            background-color: #FFFCEE;
            border-bottom: 1px solid #ccc;
            /* เส้นคั่นด้านล่าง */
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .terms-content {
            width: 100%;
            margin-left: calc(-50vw + 50%);
            padding: 0;
            /* เอา padding ออกเมื่อปิด */
            background-color: #f8f9fa;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.4s ease, padding 0.4s ease;
            /* เพิ่ม transition ให้ padding ด้วย */
            border-bottom: 1px solid #ccc;
        }

        .terms-content.open {
            max-height: 500px;
            padding: 20px;
            /* เพิ่ม padding เฉพาะตอนเปิด */
        }

        .terms-header.active .arrow-icon {
            transform: rotate(180deg);
            /* หมุนลูกศรเมื่อ active */
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <?php include("navbar.php"); ?>
    <!-- Navbar End -->


    <!-- Header Start -->
    <div class="position-relative mb-4">
        <!-- เส้นคาดพื้นหลังพาดหลังทั้งข้อความและรูป -->
        <div class="position-absolute w-100"
            style="height: 250px; background-color: #FFFCEE; top: 50%; transform: translateY(-50%); z-index: 1; box-shadow: 0 4px 20px rgba(57, 46, 4, 0.15);">
        </div>

        <!-- เนื้อหาทั้งหมดอยู่ด้านหน้า -->
        <div class="container-fluid px-0 px-md-5 position-relative" style="z-index: 2;">
            <div class="row align-items-center px-3">
                <!-- ข้อความ -->
                <div class="col-lg-6 text-center text-lg-left py-5">
                    <h1 class="font-weight-bold" style="font-size: 36px; color: #000;">Term & Condition</h1>
                    <h1 class="font-weight-bold" style="font-size: 28px; color: #000;">ข้อกำหนดและเงื่อนไข</h1>
                </div>
                <!-- รูปภาพ -->
                <div class="col-lg-6 text-center py-4">
                    <img src="/UserCatHotel_Project/image_room/condition1.jpg"
                        style="max-width: 60%; height: auto; position: relative; z-index: 3;">
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->



    <!-- Facilities Start -->
    <div class="container mt-5">
        <div class="terms-header" onclick="toggleContent('terms1', this)">
            สิ่งที่ต้องเตรียมมาในวันเข้าพัก
            <i class="fas fa-chevron-down arrow-icon"></i>
        </div>
        <div id="terms1" class="terms-content">
            <ul>
                <li>ใบวัคซีน</li>
                <li>สำเนาบัตรประชาชนเจ้าของแมว</li>
                <li>นำน้องใส่ตระกร้าหรือกระเป๋า มิดชิดเมื่อนำน้องมาส่ง</li>
                <li>อาหารที่น้องทานประจำ</li>
                <li>ทรายแมว (แต่ถ้าไม่สะดวกนำมาเอง เรามีแบ่งขายให้ ราคาตามท้องตลาดทั่วไป ส่วนกระบะทราย เรามีให้อยู่แล้วค่ะ)</li>
                <li>ของเล่นที่น้องชอบ ผ้าหรือเบาะนอนที่น้องคุ้นเคย</li>
            </ul>
        </div>

        <div class="terms-header" onclick="toggleContent('terms2', this)">
            วิธีการจองห้องพัก/การคืนเงิน
            <i class="fas fa-chevron-down arrow-icon"></i>
        </div>
        <div id="terms2" class="terms-content">
            <p>วิธีการจองห้องพัก</p>
            <ul>
                <li>เลือกวัน เช็คอิน-เช็คเอาท์ และจำนวนแมว กดค้นหา</li>
                <li>เลือกประเภทห้องพักที่ต้องการ และเลขห้องพัก</li>
                <li>จองโดยชำระเงินมัดจำ 50 % ของยอดรวมราคาห้อง (ชำระเงินภายใน 1 ชั่วโมงเพื่อยืนยันการจอง)</li>
            </ul>
            <p>เงื่อนไขการคืนเงิน</p>
            <ul>
                <li>แจ้งยกเลิกการจองก่อนการเข้าพัก ไม่น้อยกว่า 15 วัน คืนเงิน 100%</li>
                <li>แจ้งยกเลิกการจองก่อนการเข้าพัก ไม่น้อยกว่า 7 วัน คืนเงิน 50%</li>
                <li>แจ้งยกเลิกการจองก่อนการเข้าพัก น้อยกว่า 7 วัน ทางโรงแรมขอสงวนสิทธิ์การคืนเงินทั้งหมด</li>
            </ul>
        </div>

        <div class="terms-header" onclick="toggleContent('terms3', this)">
            เงื่อนไขการเข้าพัก
            <i class="fas fa-chevron-down arrow-icon"></i>
        </div>
        <div id="terms3" class="terms-content">
            <ul>
                <li>โรงแรมเปิดทำการเวลา 09:00-20:00 สามารถเช็คอิน-เช็คเอาท์ได้ตามช่วงเวลาดังกล่าว</li>
                <li>แมวทุกตัวต้องได้รับการฉีดวัคซีนป้องกันโรคขั้นพื้นฐาน (โรคพิษสุนัขบ้า โรคลิวคิเมีย โรคหัดแมว) โดยต้องนำใบวัคซีนของแมวมาด้วยในวันที่ส่งแมว</li>
                <li>แมวต้องมีอายุ 3 เดือนขึ้นไป</li>
                <li>เป็นแมวที่เลี้ยงมาจากระบบปิดเท่านั้น (ระบบปิด หมายถึง เลี้ยงในบ้านเท่านั้น) เพื่อป้องกันในเรื่องของเห็บ หมัด และพาหะโรคต่าง ๆ ที่จะแพร่สู่แมวตัวอื่น ๆ</li>
                <li>กรณีแมวป่วยระหว่างพักกับเรา จะแจ้งให้เจ้าของทราบโดยทันที และสามารถพาแมวไปโรงพยาบาลสัตว์ (ค่าใช้จ่ายตามบิลของโรงพยาบาล เจ้าของแมวรับผิดชอบค่าใช้จ่ายส่วนนี้เอง)</li>
                <li>หากแมวเสียชีวิต หรือป่วยในระหว่างพักหรือหลังพัก ทางเราไม่ขอรับผิดชอบใดๆทั้งสิ้น เพราะก่อนแมวเข้าพัก ทางโรงแรมได้สอบถามประวัติทางด้านสุขภาพ การฉีดวัคซีนของแมวทุกๆตัว ซึ่งในระหว่างที่พักกับทางโรงแรมมั่นใจในการดูแลในเรื่องความสะอาด ปลอดภัย และสุขภาพอนามัยที่ดี</li>
                <li>ไม่รับแมวตั้งครรภ์ที่ใกล้คลอด เนื่องจากต้องได้รับการดูแลอย่างใกล้ชิด</li>
                <li>ไม่รับแมวที่อยู่ในช่วงติดสัด มีอาการหง่าว สเปรย์ ฉี่นอกกระบะทราย</li>
                <li>ไม่รับแมวที่สุขภาพไม่แข็งแรง มีโรคประจำตัว โรคผิวหนัง หรือ โรคติดต่อทุกชนิด</li>
                <li>ไม่รับแมวดุ และ กัด จนไม่สามารถให้อาหารและเก็บกระบะทรายได้</li>
                <li>กรณีพบเจอ เห็บ หมัด เชื้อรา ไรในหู หรือโรคติดต่ออื่นๆ ทางโรงแรมปรับ 500 บาท</li>
            </ul>
        </div>

        <div class="terms-header" onclick="toggleContent('terms4', this)">
            การรับ-ส่งน้องแมว
            <i class="fas fa-chevron-down arrow-icon"></i>
        </div>
        <div id="terms4" class="terms-content">
            <ul>
                <li>การมารับแมวกลับ จะต้องแสดงใบเสร็จการเข้าพัก เพื่อเป็นการยืนยันตัวตนในการรับกลับ</li>
                <li>หากตั้งใจไม่มารับแมวตามกำหนด ทางโรงแรมจะดำเนินคดี ตาม พรบ.คุ้มครองสัตว์ และปรับเป็น 2 เท่า ของค่าใช้จ่ายทั้งหมด</li>
                <li>นำน้องใส่ตระกร้าหรือกระเป๋า มิดชิดเมื่อนำน้องมาส่ง</li>
                <li>มารับมาส่งน้องแมวในช่วง โรงแรมเปิดทำการเท่านั้น เวลา 09:00-20:00 </li>
            </ul>
        </div>

        <div class="terms-header" onclick="toggleContent('terms5', this)">
            การนัดเยี่ยมน้องแมว
            <i class="fas fa-chevron-down arrow-icon"></i>
        </div>
        <div id="terms5" class="terms-content">
            <ul>
                <li>หากต้องการมาเยี่ยมน้องแมว ให้นัดล่วงหน้าทาง Line</li>
                <li>เข้าเยี่ยมน้องแมวได้ตามเวลา โรงแรมเปิดทำการเท่านั้น เวลา 09:00-20:00</li>
            </ul>
        </div>
    </div>



    <!-- Footer Start -->
    <?php include("footer.php"); ?>
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
        function toggleContent(termId, headerElement) {
            const content = document.getElementById(termId);
            const isOpen = content.classList.contains('open');

            // ซ่อนทั้งหมด
            document.querySelectorAll('.terms-content').forEach(el => {
                el.classList.remove('open');
                el.previousElementSibling.classList.remove('active');
            });

            // เปิดอันเดียวถ้ายังไม่เปิด
            if (!isOpen) {
                content.classList.add('open');
                headerElement.classList.add('active');
            }
        }
    </script>
</body>

</html>