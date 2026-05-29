<?php

session_start();

// เชื่อมต่อฐานข้อมูล
include('dbconnect.php');

$sql = "SELECT DISTINCT typ_room.typroom_ID , typ_room.typ_name , typ_room.detail, typ_room.short_detail, typ_room.price, typ_room.num_cat , room_image.url 
FROM typ_room INNER JOIN room_image ON typ_room.typroom_ID = room_image.typroom_ID 
GROUP BY typ_room.typroom_ID 
ORDER BY typ_room.typroom_ID ASC";

//สร้างตัวแปรแบบ Array มารับข้อมูลที่ได้จากฐานข้อมูล
$adminList = [];

// & สั่งรันคำสั่ง sql
$result = $connection->query($sql);
if ($result && $result->num_rows > 0) {
    $adminList = $result->fetch_all(MYSQLI_ASSOC);
}

// เตรียมข้อมูลห้องทั้งหมด และห้องว่าง แยกตาม typroom_ID
$roomCounts = [];
$date_today = date('Y-m-d');

// ห้องทั้งหมดตามประเภท
$sql_total_by_type = "SELECT typroom_ID, COUNT(*) AS total FROM room GROUP BY typroom_ID";
$result_total_by_type = $connection->query($sql_total_by_type);
while ($row = $result_total_by_type->fetch_assoc()) {
    $roomCounts[$row['typroom_ID']]['total'] = $row['total'];
}

// ห้องว่างวันนี้ตามประเภท
$sql_available_by_type = "
    SELECT typroom_ID, COUNT(*) AS available
    FROM room 
    WHERE room_ID NOT IN (
        SELECT room_ID 
        FROM booking 
        WHERE ('$date_today' BETWEEN check_in AND check_out)
        AND bookstate_ID IN ('B01','B02','B03','B04')
    )
    GROUP BY typroom_ID
";
$result_available_by_type = $connection->query($sql_available_by_type);
while ($row = $result_available_by_type->fetch_assoc()) {
    $roomCounts[$row['typroom_ID']]['available'] = $row['available'];
}


?>
<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta charset="UTF-8">
    <title>ข้อมูลห้องพัก</title>
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
        .app-wrapper,
        .app-main {
            background-color: transparent !important;
        }

        /* Header card */
        .card-header {
            background-color: #fff;
            border-bottom: none;
            /* เอาเส้นขั้นออก */
            padding-bottom: 0;
        }

        /* h3 header */
        .card-header h3 {
            font-weight: bold;
            text-align: center;
            width: 100%;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.1);
            /* เงาที่ด้านล่าง */
        }
    </style>

</head> <!--end::Head--> <!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" style="font-family: 'Noto Sans Thai', sans-serif;"> <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Sidebar-->
        <?php include('left_sidebar.php'); ?>

        <!--begin::App Main-->
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="mb-0 h3-shadow">ห้องพัก</h3>
                        </div>
                    </div>
                </div><!-- /.card-header -->

                <!-- แสดงรายการห้องพัก -->
                <div class="container">
                    <?php foreach ($adminList  as $index => $adminData) { ?>
                        <div class="row align-items-start py-4">
                            <!-- รูปภาพ (ฝั่งซ้าย) -->
                            <div class="col-md-5">
                                <?php
                                if ($adminData['url'] != '') {
                                ?><div style="width: 100%; aspect-ratio: 4 / 3; overflow: hidden;">
                                        <img src="<?php echo $adminData['url']; ?>" style="width: 100%; height: 100%; object-fit: cover;" class="img-fluid" />
                                    </div>
                                <?php
                                }
                                ?>
                            </div>

                            <!-- รายละเอียดห้อง (ฝั่งขวา) -->
                            <?php
                            $tid = $adminData['typroom_ID'];
                            $total = $roomCounts[$tid]['total'] ?? 0;
                            $available = $roomCounts[$tid]['available'] ?? 0;
                            ?>
                            <div class="col-md-7 d-flex flex-column justify-content-between text-start" style="height: 100%;">
                                <div>
                                    <h4 class="fw-semibold"><?php echo $adminData['typ_name']; ?> จำนวน <?php echo $total ?> ห้อง</h4>
                                    <p class="mb-2">- ราคา <?php echo $adminData['price']; ?> บาท</p>
                                    <p class="mb-2">- น้องแมวพักได้ <?php echo $adminData['num_cat']; ?> ตัว/ห้อง</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                    <span class="fw-bold text-uppercase" style="font-weight:bold">ว่าง <?php echo $available ?> ห้อง</span>
                                    <a href="edit_room.php?id=<?php echo $adminData['typroom_ID']; ?>" style="background-color: black; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; display: inline-block;">
                                        แก้ไข
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- เส้นคั่นระหว่างห้อง -->
                        <?php if ($index < count($adminList) - 1) { ?>
                            <hr>
                        <?php } ?>
                    <?php } ?>
                </div>

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