<?php
include("dbconnect.php");

session_start();

if (isset($_POST['login-button'])) {


    // เขียนคำสั่ง sql ในการดึงข้อมูล
    $sql = " SELECT * FROM admin
    WHERE username = '" . $_POST['username'] . "'
    AND password = '" . $_POST['password'] . "'
    ";
    // echo $sql;
    // exit ();

    // $connection->query($sql) สั่ง run คำสั่ง sql
    // $result สร้างตัวแปรมารับค่า

    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) { // check ไม่ error และได้ค่าออกมา
        $_SESSION['islogin'] = true;
        $_SESSION['username'] = $_POST['username'];
        header("Location: report_day.php");
        // echo $sql;
        // print_r($_SESSION);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ Saba Cat Hotel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- โลโก้ -->
    <link rel="icon" type="image/png" href="image_room/logo.jpg">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- ฟอนต์ภาษาไทย -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600&display=swap" rel="stylesheet">

    <!-- AdminLTE & Plugins -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CatHotel_Project/dist/css/adminlte.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-image: url('image_room/background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            font-family: 'Noto Sans Thai', sans-serif;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.23);
            z-index: -1;
        }

        .login-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 30px;
            background: #FFFCEE;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
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
    </style>
</head> <!--end::Head-->

<!--begin::Body-->

<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="image_room/logo.jpg" alt="Saba Cat Logo" class="logo-img">
        </div>
        <h4>เข้าสู่ระบบ Admin</h4>

        <form name="login-form" action="login.php" method="post">
            <div class="field mb-3">
                <span class="fas">ชื่อผู้ใช้</span>
                <input name="username" type="text" class="form-control" placeholder="ชื่อผู้ใช้">
            </div>
            <div class="field mb-3">
                <span class="fas">รหัสผ่าน</span>
                <input name="password" type="password" class="form-control" placeholder="รหัสผ่าน">
            </div>
            <div class="col-12">
                <div class="d-grid gap-2">
                    <button name="login-button" type="submit" class="btn btn-login">
                        เข้าสู่ระบบ <i class="fas fa-sign-in-alt"></i>
                    </button>
                </div>
            </div> <!-- /.col -->
        </form>
        <!-- </div> -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="../../../dist/js/adminlte.js"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script> <!--end::OverlayScrollbars Configure--> <!--end::Script-->
</body><!--end::Body-->

</html>