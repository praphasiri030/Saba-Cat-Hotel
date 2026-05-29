<?php
include("dbconnect.php");
session_start();

// ===== 1: ระบบล็อกอินปกติ =====
$email_error = "";
$password_error = "";

if (isset($_POST['login-button'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM member WHERE email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $memberdata = $result->fetch_assoc();

        if ($memberdata['password'] === $password) {
            $_SESSION['islogin'] = true;
            $_SESSION['member_ID'] = $memberdata['member_ID'];
            $_SESSION['fullname'] = $memberdata['fullname'];
            $_SESSION['email'] = $memberdata['email'];
            $_SESSION['phone'] = $memberdata['phone'];
            header("Location: index.php");
            exit();
        } else {
            $password_error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $email_error = "อีเมลไม่ถูกต้อง";
    }
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

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <meta name="google-signin-client_id" content="72999527776-trblkgd521k62aaeqqiissr45mibjpjc.apps.googleusercontent.com">

    <style>
        .login-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 30px;
            background: #fffcee;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h4 {
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
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: lighter;
            height: 50px;
            width: 100px;
            color: rgb(0, 0, 0);
            line-height: 50px;
            border: 1px solid #000;
            border-right: none;
            border-radius: 5px 0 0 5px;
            background: #fff;
        }

        .field input {
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

        .btn-login {
            display: block;
            width: 200px;
            margin: auto;
            background: #FFE488;
            font-weight: bold;
            color: #000;
        }

        .btn-login i {
            margin-left: 6px;
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

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #000;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid #aaa;
        }

        .divider:not(:empty)::before {
            margin-right: .75em;
        }

        .divider:not(:empty)::after {
            margin-left: .75em;
        }
    </style>

</head>

<body>
    <!-- Navbar Start -->
    <?php include("navbar.php"); ?>
    <!-- Navbar End -->

    <div class="login-container">
        <h4>เข้าสู่ระบบ</h4>
        <p>กรุณาเข้าสู่ระบบเพื่อทำการจอง</p>

        <form name="login-form" action="login.php" method="post">
            <div class="field">
                <label class="fas">อีเมล</label>
                <input
                    name="email"
                    type="email"
                    class="form-control <?php if (!empty($email_error)) echo 'is-invalid'; ?>"
                    placeholder="กรอกอีเมล"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>"
                    required
                    oninvalid="this.setCustomValidity('กรุณากรอกอีเมล')"
                    oninput="this.setCustomValidity('')">

                <?php if (!empty($email_error)): ?>
                    <div class="invalid-feedback">
                        <?php echo $email_error; ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="field">
                <label class="fas">รหัสผ่าน</label>
                <input
                    name="password"
                    type="password"
                    class="form-control <?php if (!empty($password_error)) echo 'is-invalid'; ?>"
                    placeholder="กรอกรหัสผ่าน"
                    required
                    oninvalid="this.setCustomValidity('กรุณากรอกรหัสผ่าน')"
                    oninput="this.setCustomValidity('')">

                <?php if (!empty($password_error)): ?>
                    <div class="invalid-feedback">
                        <?php echo $password_error; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-grid mb-2 mt-3">
                <button name="login-button" type="submit" class="btn btn-login">
                    เข้าสู่ระบบ <i class="fas fa-sign-in-alt"></i>
                </button>
            </div>
            <div class="d-grid mt-2">
                <a href="register.php" class="btn btn-register">
                    ลงทะเบียน <i class="fas fa-user-plus"></i>
                </a>

                <div class="divider">หรือ</div>
            </div>

        </form>


        <div class="d-grid mt-3 d-flex justify-content-center">
            <div id="g_id_onload"
                data-client_id="72999527776-trblkgd521k62aaeqqiissr45mibjpjc.apps.googleusercontent.com"
                data-context="signin"
                data-ux_mode="redirect"
                data-login_uri="http://localhost/UserCathotel_Project/login-with-google.php"
                data-callback="loginWithGoogleSuccess" data-auto_prompt="false">
            </div>
            <div class="g_id_signin"
                data-type="standard"
                data-shape="pill"
                data-theme="outline">
            </div>
        </div>
    </div>
    </div>
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
</body>

</html>