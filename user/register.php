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

        .register-container {
            max-width: 450px;
            margin: 40px auto;
            padding: 30px;
            background: #fffcee;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin: 0 0 10px;
        }

        form {
            margin-top: 30px;
        }

        form .field {
            margin-top: 20px;
            display: flex;
        }

        .field .fas {
            font-family: 'Noto Sans Thai', sans-serif;;
            font-size: small;
            height: 50px;
            width: 150px;
            color: rgb(0, 0, 0);
            line-height: 50px;
            border: 1px solid #000;
            border-right: none;
            border-radius: 5px 0 0 5px;
            background: #fff;
        }

        .field input {
            font-size: smaller;
            height: 50px;
            width: 100%;
            outline: none;
            padding: 0 15px;
            border-radius: 0 5px 5px 0;
            border: 1px solid #444;
            background: #F4F4F4;
        }

        .btn {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #000;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-register {
            display: block;
            width: 150px;
            margin: auto;
            padding: 6px;
            background: #000;
            color: white;
            margin-top: 10px;
            font-weight: bold;
        }

        .btn-register i {
            margin-left: 6px;
        }

        .btn-back {
            display: block;
            width: 150px;
            margin: auto;
            padding: 6px;
            background: #D9D9D9;
            color: black;
            margin-top: 10px;
            font-weight: bold;
        }

        .btn-back i {
            margin-right: 15px;
        }
    </style>

</head>

<body>
    <!-- Navbar Start -->
    <?php include("navbar.php"); ?>
    <!-- Navbar End -->

    <div class="register-container">
        <h4>ลงทะเบียน</h4>
        <p>กรุณากรอกข้อมูลของคุณเพื่อสร้างบัญชี</p>

        <!-- ข้อมูลจะถูกส่งไปที่ไฟล์ register_process.php เพื่อประมวลผล บันทึกลงฐานข้อมูล -->
        <form action="register_process.php" method="post" onsubmit="return validatePassword();">
            <div class="field">
                <label for="fullname" class="fas">ชื่อ-นามสกุล</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="กรอกชื่อ-นามสกุล" required>
            </div>
            <div class="field">
                <label for="email" class="fas">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="กรอกอีเมล" required>
            </div>
            <div class="field">
                <label for="phone" class="fas">เบอร์โทรศัพท์</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="กรอกเบอร์โทรศัพท์" required pattern="[0-9]{10}">
            </div>
            <div class="field">
                <label for="password" class="fas">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่าน" required minlength="6" maxlength="15">
            </div>
            <div class="field">
                <label for="confirm_password" class="fas">ยืนยันรหัสผ่าน</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="ยืนยันการกรอกรหัสผ่าน" required minlength="6" maxlength="15">
                <div id="password-error" class="text-danger mt-1" style="display: none;">รหัสผ่านไม่ตรงกัน</div>
            </div>
            <div class="row mt-3">
                <div class="col-6">
                    <a href="login.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> กลับ</a>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-register">
                        ลงทะเบียน <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            </div>

        </form>
    </div>



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
        //ฟังก์ชัน validatePassword() เพื่อ ตรวจสอบว่ารหัสผ่านกับยืนยันรหัสผ่านตรงกัน
        function validatePassword() {
            const pw = document.getElementById("password").value;
            const confirmPw = document.getElementById("confirm_password").value;
            const errorDiv = document.getElementById("password-error");

            if (pw !== confirmPw) {
                errorDiv.style.display = "block";
                return false;
            }
            errorDiv.style.display = "none";
            return true;
        }
    </script>

</body>

</html>