<?php

include("check_login.php");

// เชื่อมต่อฐานข้อมูล
include('dbconnect.php');

// funtion ลบข้อมูล 
if (isset($_GET['id']) && $_GET['id'] != "") {
    $sql = " DELETE FROM admin WHERE admin_Id = " . $_GET['id'] . "
    ";

    if ($connection->query($sql)) {
        header("Location: admin_list.php");
        exit();
    }
}

// เขียนคำสั่ง sql
$sql = "SELECT * FROM admin ORDER BY admin_Id DESC";

//สร้างตัวแปรแบบ Array มารับข้อมูลที่ได้จากฐานข้อมูล
$adminList = [];

// & สั่งรันคำสั่ง sql
$result = $connection->query($sql);
if ($result && $result->num_rows > 0) {
    $adminList = $result->fetch_all(MYSQLI_ASSOC);
}
// echo '<pre>';
// print_r($adminList);
// exit;

?>
<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Layout | AdminLTE 4</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="Layout | AdminLTE 4">
    <meta name="author" content="ColorlibHQ">
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS.">
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"><!--end::Primary Meta Tags--><!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"><!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous"><!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous"><!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="/CatHotel_Project/dist/css/adminlte.css"><!--end::Required Plugin(AdminLTE)-->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600&display=swap" rel="stylesheet">

</head> <!--end::Head-->

<!--begin::Body-->
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary"style="font-family: 'Noto Sans Thai', sans-serif;"> <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <!-- <?php include('header.php'); ?> -->

        <!--begin::Sidebar-->
        <?php include('left_sidebar.php'); ?>

        <!--begin::App Main-->
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="container-fluConcertID">
                        <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">ข้อมูลผู้ดูแลระบบ</h3>
                        </div>
                            <div class="col-sm-6 text-end">
                                <button class="btn btn-warning" onclick="location.href='admin_form.php'"><i class="bi bi-plus"></i> เพิ่ม</button>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">ID</th>
                                <!-- <th>Image</th> -->
                                <th>Username</th>
                                <th>Password</th>
                                <!-- <th>Date</th>
                                <th>Time</th> -->
                                <th style="width: 80px">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($adminList  as $index => $adminData) {

                            ?>
                                <tr class="align-middle">
                                    <td><?php echo $adminData['admin_ID']; ?></td>
                                    <!-- <td>
                                        <?php
                                        if ($adminData['image'] != '') {
                                        ?>
                                            <img src="<?php echo $adminData['image']; ?>" style="max-width: 100px;" />
                                        <?php
                                        }
                                        ?>
                                    </td> -->
                                    <td><?php echo $adminData['username']; ?></td>
                                    <td><?php echo $adminData['password']; ?></td>
                                    <!-- <td><?php echo $adminData['create_date']; ?></td>
                                    <td><?php echo $adminData['create_time']; ?></td> -->
                                    <!-- <td><span class="badge text-bg-danger">55%</span></td> -->
                                    <td>
                                        <a href="admin_edit.php?id=<?php echo $adminData['admin_ID']; ?>">
                                            <i class="bi bi-pencil-square" style="cursor: pointer;"></i>
                                        </a>
                                        <!-- <a href="admin_list.php?id=<?php echo $adminData['admin_ID']; ?>"> -->
                                        <i class="bi bi-x-circle" style="cursor: pointer;" onclick="deleteAdmin(<?php echo $adminData['admin_ID']; ?>)"></i>
                                        <!-- </a> -->
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </main> <!--end::App Main-->
        <!--begin::Footer-->
        <!-- <?php include('footer.php'); ?> -->

    </div> <!--end::App Wrapper--> <!--begin::Script--> <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="/CatHotel_Project/dist/js/adminlte.js"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        function deleteAdmin(Id) {
            Swal.fire({
                title: "คุณต้องการลบใช่ หรือ ไม่?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ลบข้อมูล",
                cancelButtonText: "ปิดหน้าต่าง"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin_list.php?id=' + Id;
                    return false;
                }
            });
            return false;
        }
    </script> <!--end::OverlayScrollbars Configure--> <!--end::Script-->
</body><!--end::Body-->

</html>
