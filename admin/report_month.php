<?php
include("dbconnect.php");

session_start();

if (!isset($_SESSION['islogin']) || $_SESSION['islogin'] != true) {
    header("Location: login.php?status=error");
    exit();
}
// ตรวจสอบว่ามีการเลือกวันที่หรือไม่
date_default_timezone_set('Asia/Bangkok'); // ตั้งเขตเวลาไทย
$selected_month = isset($_GET['selected_month']) && $_GET['selected_month'] != '' ? substr($_GET['selected_month'], 0, 7) : date('Y-m'); // Y-m เช่น 2025-05




//จองวันนี้
$sql_booking_today = "
    SELECT COUNT(*) AS total 
    FROM booking 
    WHERE DATE_FORMAT(book_date, '%Y-%m') = '$selected_month' 
    AND bookstate_ID IN ('B01','B02','B03','B04','B05')
";
$result_booking_today = $connection->query($sql_booking_today);
$booking_today = ($result_booking_today->num_rows > 0) ? $result_booking_today->fetch_assoc()['total'] : 0;

//รายงานจองวันนี้ Standard
$sql_bookstandard_today = "
    SELECT COUNT(*) AS total 
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(b.book_date, '%Y-%m') = '$selected_month' 
    AND b.bookstate_ID IN ('B01','B02','B03','B04','B05')
    AND t.typroom_ID = 1
";

$result_bookstandard_today = $connection->query($sql_bookstandard_today);
$bookstandard_today = ($result_bookstandard_today->num_rows > 0) ? $result_bookstandard_today->fetch_assoc()['total'] : 0;

//รายงานจองวันนี้ Superior
$sql_booksuperior_today = "
    SELECT COUNT(*) AS total 
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(b.book_date, '%Y-%m') = '$selected_month' 
    AND b.bookstate_ID IN ('B01','B02','B03','B04','B05')
    AND t.typroom_ID = 2
";

$result_booksuperior_today = $connection->query($sql_booksuperior_today);
$booksuperior_today = ($result_booksuperior_today->num_rows > 0) ? $result_booksuperior_today->fetch_assoc()['total'] : 0;

//รายงานจองวันนี้ Deluxe
$sql_bookdeluxe_today = "
    SELECT COUNT(*) AS total 
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(b.book_date, '%Y-%m') = '$selected_month' 
    AND b.bookstate_ID IN ('B01','B02','B03','B04','B05')
    AND t.typroom_ID = 3
";

$result_bookdeluxe_today = $connection->query($sql_bookdeluxe_today);
$bookdeluxe_today = ($result_bookdeluxe_today->num_rows > 0) ? $result_bookdeluxe_today->fetch_assoc()['total'] : 0;

//ยกเลิกวันนี้
$sql_cancel_today = "
    SELECT COUNT(*) AS total 
    FROM booking 
    WHERE DATE_FORMAT(book_date, '%Y-%m') = '$selected_month' 
    AND bookstate_ID = 'C02'
";
$result_cancel_today = $connection->query($sql_cancel_today);
$cancel_today = ($result_cancel_today->num_rows > 0) ? $result_cancel_today->fetch_assoc()['total'] : 0;

//ยกเลิกวันนี้ standard
$sql_cancelstandard_today = "
    SELECT COUNT(*) AS total 
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(b.book_date, '%Y-%m') = '$selected_month' 
    AND b.bookstate_ID = 'C02'
    AND t.typroom_ID = 1
";
$result_cancelstandard_today = $connection->query($sql_cancelstandard_today);
$cancelstandard_today = ($result_cancelstandard_today->num_rows > 0) ? $result_cancelstandard_today->fetch_assoc()['total'] : 0;

//ยกเลิกวันนี้ superior
$sql_cancelsuperior_today = "
    SELECT COUNT(*) AS total 
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(b.book_date, '%Y-%m') = '$selected_month' 
    AND b.bookstate_ID = 'C02'
    AND t.typroom_ID = 2
";
$result_cancelsuperior_today = $connection->query($sql_cancelsuperior_today);
$cancelsuperior_today = ($result_cancelsuperior_today->num_rows > 0) ? $result_cancelsuperior_today->fetch_assoc()['total'] : 0;

//ยกเลิกวันนี้ deluxe
$sql_canceldeluxe_today = "
    SELECT COUNT(*) AS total 
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(b.book_date, '%Y-%m') = '$selected_month' 
    AND b.bookstate_ID = 'C02'
    AND t.typroom_ID = 3
";
$result_canceldeluxe_today = $connection->query($sql_canceldeluxe_today);
$canceldeluxe_today = ($result_canceldeluxe_today->num_rows > 0) ? $result_canceldeluxe_today->fetch_assoc()['total'] : 0;

// รายได้วันนี้
$sql_income_today = "
    SELECT SUM(payment_amount) AS total 
    FROM payment payment_date
    WHERE DATE_FORMAT(payment_date, '%Y-%m') = '$selected_month'
";
$result_income_today = $connection->query($sql_income_today);
$income_today = ($result_income_today->num_rows > 0) ? number_format($result_income_today->fetch_assoc()['total'], 2) : "0.00";

// รายได้วันนี้ standard
$sql_incomestandard_today = "
    SELECT SUM(p.payment_amount) AS total
    FROM payment p
    INNER JOIN booking b ON p.booking_ID = b.booking_ID
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(p.payment_date, '%Y-%m') = '$selected_month'
    AND t.typroom_ID = 1
";
$result_incomestandard_today = $connection->query($sql_incomestandard_today);
$incomestandard_today = ($result_incomestandard_today->num_rows > 0) ? (float)$result_incomestandard_today->fetch_assoc()['total'] : 0;

// รายได้วันนี้ superior
$sql_incomesuperior_today = "
    SELECT SUM(p.payment_amount) AS total
    FROM payment p
    INNER JOIN booking b ON p.booking_ID = b.booking_ID
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(p.payment_date, '%Y-%m') = '$selected_month'
    AND t.typroom_ID = 2
";
$result_incomesuperior_today = $connection->query($sql_incomesuperior_today);
$incomesuperior_today = ($result_incomesuperior_today->num_rows > 0) ? (float)$result_incomesuperior_today->fetch_assoc()['total'] : 0;

// รายได้วันนี้ deluxe
$sql_incomedeluxe_today = "
    SELECT SUM(p.payment_amount) AS total
    FROM payment p
    INNER JOIN booking b ON p.booking_ID = b.booking_ID
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(p.payment_date, '%Y-%m') = '$selected_month'
    AND t.typroom_ID = 3
";
$result_incomedeluxe_today = $connection->query($sql_incomedeluxe_today);
$incomedeluxe_today = ($result_incomedeluxe_today->num_rows > 0) ? (float)$result_incomedeluxe_today->fetch_assoc()['total'] : 0;

// การคืนเงินวันนี้
$sql_refund_today = "
    SELECT SUM(cancle_money) AS total 
    FROM cancle 
    WHERE DATE_FORMAT(cancle_date, '%Y-%m') = '$selected_month'
";
$result_refund_today = $connection->query($sql_refund_today);
$refund_today = ($result_refund_today->num_rows > 0) ? number_format($result_refund_today->fetch_assoc()['total'], 2) : "0.00";

// การคืนเงินวันนี้ standard
$sql_refundstandard_today = "
    SELECT SUM(c.cancle_money) AS total 
    FROM cancle c
    INNER JOIN booking b ON c.booking_ID = b.booking_ID
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(c.cancle_date, '%Y-%m') = '$selected_month'
    AND t.typroom_ID = 1
";
$result_refundstandard_today = $connection->query($sql_refundstandard_today);
$refundstandard_today = ($result_refundstandard_today->num_rows > 0) ? (float)$result_refundstandard_today->fetch_assoc()['total'] : 0;

// การคืนเงินวันนี้ superior
$sql_refundsuperior_today = "
    SELECT SUM(c.cancle_money) AS total 
    FROM cancle c
    INNER JOIN booking b ON c.booking_ID = b.booking_ID
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(c.cancle_date, '%Y-%m') = '$selected_month'
    AND t.typroom_ID = 2
";
$result_refundsuperior_today = $connection->query($sql_refundsuperior_today);
$refundsuperior_today = ($result_refundsuperior_today->num_rows > 0) ? (float)$result_refundsuperior_today->fetch_assoc()['total'] : 0;

// การคืนเงินวันนี้ deluxe
$sql_refunddeluxe_today = "
    SELECT SUM(c.cancle_money) AS total 
    FROM cancle c
    INNER JOIN booking b ON c.booking_ID = b.booking_ID
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE DATE_FORMAT(c.cancle_date, '%Y-%m') = '$selected_month'
    AND t.typroom_ID = 3
";
$result_refunddeluxe_today = $connection->query($sql_refunddeluxe_today);
$refunddeluxe_today = ($result_refunddeluxe_today->num_rows > 0) ? (float)$result_refunddeluxe_today->fetch_assoc()['total'] : 0;

?>
<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta charset="UTF-8">
    <title>รายงงานรายเดือน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- โลโก้กำหนดเอง -->
    <link rel="icon" type="image/png" href="image_room/logo.jpg"> <!-- เปลี่ยน path ตามไฟล์จริง -->

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- ฟอนต์ภาษาไทย -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600&display=swap" rel="stylesheet">

    <!-- AdminLTE & Plugins -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CatHotel_Project/dist/css/adminlte.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Background หน้า */
        body.layout-fixed.sidebar-expand-lg {
            background-color: #F1EEE0 !important;
        }

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
    <div class="app-wrapper"> <!--begin::Header-->
        <div class="card-header">
            <div class="col-12">
                <h3 class="mb-0 h3-shadow">รายงานรายเดือน</h3>
            </div>
        </div>
        <?php
        include('left_sidebar.php');
        ?>
        <!--begin::App Main-->
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">
                                <?php
                                // ฟังก์ชันแปลงเดือนและปีเป็นภาษาไทย
                                function formatThaiMonthYear($date)
                                {
                                    $months = [
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
                                    $timestamp = strtotime($date);
                                    $month = $months[date('n', $timestamp) - 1];
                                    $year = date('Y', $timestamp) + 543;

                                    return "$month $year";
                                }

                                echo formatThaiMonthYear($selected_month);
                                ?>
                            </h3>
                        </div>
                        <!-- ตัวเลือกเดือนและปี -->
                        <div class="col-sm-6 text-end">
                            <input type="month" id="month-picker" class="form-control d-inline-block w-auto"
                                value="<?php echo date('Y-m', strtotime($selected_month)); ?>"
                                max="<?php echo date('Y-m'); ?>">
                        </div>
                    </div>
                    <!--end::Row-->
                </div> <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">

                    <!--begin::Row for Box 3-6-->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- Small Box Widget 3: การจอง -->
                            <div class="small-box text-bg-warning">
                                <div class="inner">
                                    <h3><?php echo $booking_today; ?></h3>
                                    <p>การจอง</p>
                                </div>
                                <!-- SVG icon -->
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Small Box Widget 4: การยกเลิก -->
                            <div class="small-box text-bg-danger">
                                <div class="inner">
                                    <h3><?php echo $cancel_today; ?></h3>
                                    <p>การยกเลิก</p>
                                </div>
                                <!-- SVG icon -->
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Small Box Widget 5: รายได้ทั้งหมด -->
                            <div class="small-box text-bg-warning">
                                <div class="inner">
                                    <h3><?php echo $income_today; ?></h3>
                                    <p>รายได้ทั้งหมด</p>
                                </div>
                                <!-- SVG icon -->
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Small Box Widget 6: การคืนเงิน -->
                            <div class="small-box text-bg-danger">
                                <div class="inner">
                                    <h3><?php echo $refund_today; ?></h3>
                                    <p>การคืนเงิน</p>
                                </div>
                                <!-- SVG icon -->
                            </div>
                        </div>
                    </div>
                    <!--end::Row-->

                    <!--begin::Row-->
                    <div class="row">
                        <!-- กราฟการจองห้องพัก -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">การจองห้องพัก</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="bookingChart" style="height:250px; width:100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- กราฟรายได้ -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">รายได้</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueChart" style="height:250px; width:100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- กราฟการจองห้องพัก -->
                        <script>
                            const ctx1 = document.getElementById('bookingChart').getContext('2d');
                            const bookingChart = new Chart(ctx1, {
                                type: 'bar',
                                data: {
                                    labels: ['Standard Room', 'Superior Room', 'Deluxe Room'],
                                    datasets: [{
                                            label: 'การจอง',
                                            data: [<?php echo $bookstandard_today; ?>, <?php echo $booksuperior_today; ?>, <?php echo $bookdeluxe_today; ?>],
                                            backgroundColor: 'rgba(226, 170, 0, 1)',

                                            borderWidth: 1,
                                            barThickness: 30,
                                            categoryPercentage: 0.6,
                                            barPercentage: 0.7,

                                        },
                                        {
                                            label: 'ยกเลิก',
                                            data: [<?php echo $cancelstandard_today; ?>, <?php echo $cancelsuperior_today; ?>, <?php echo $canceldeluxe_today; ?>],
                                            backgroundColor: 'rgba(255, 209, 132, 1)',
                                            borderWidth: 1,
                                            barThickness: 30,
                                            categoryPercentage: 0.6,
                                            barPercentage: 0.7,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>

                        <!-- กราฟรายได้ -->
                        <script>
                            const ctx2 = document.getElementById('revenueChart').getContext('2d');
                            const revenueChart = new Chart(ctx2, {
                                type: 'bar',
                                data: {
                                    labels: ['Standard Room', 'Superior Room', 'Deluxe Room'],
                                    datasets: [{
                                            label: 'รายได้',
                                            data: [<?php echo $incomestandard_today; ?>, <?php echo $incomesuperior_today; ?>, <?php echo $incomedeluxe_today; ?>],
                                            backgroundColor: 'rgba(80, 137, 188, 1)',
                                            borderWidth: 1,
                                            barThickness: 30,
                                            categoryPercentage: 0.6,
                                            barPercentage: 0.7,
                                        },
                                        {
                                            label: 'ยกเลิก',
                                            data: [<?php echo $refundstandard_today; ?>, <?php echo $refundsuperior_today; ?>, <?php echo $refunddeluxe_today; ?>],
                                            backgroundColor: 'rgba(151, 185, 224, 1)',
                                            borderWidth: 1,
                                            barThickness: 30,
                                            categoryPercentage: 0.6,
                                            barPercentage: 0.7,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </div> <!-- /.card -->

            </div> <!-- /.row (main row) -->
    </div> <!--end::Container-->
    </div> <!--end::App Content-->
    </main> <!--end::App Main-->
    </div> <!--end::App Wrapper--> <!--begin::Script--> <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="/CatHotel_Project/dist/js/adminlte.js"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
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
    </script> <!--end::OverlayScrollbars Configure--> <!-- OPTIONAL SCRIPTS --> <!-- sortablejs -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script> <!-- sortablejs -->
    <script>
        const connectedSortables =
            document.querySelectorAll(".connectedSortable");
        connectedSortables.forEach((connectedSortable) => {
            let sortable = new Sortable(connectedSortable, {
                group: "shared",
                handle: ".card-header",
            });
        });

        const cardHeaders = document.querySelectorAll(
            ".connectedSortable .card-header",
        );
        cardHeaders.forEach((cardHeader) => {
            cardHeader.style.cursor = "move";
        });
    </script> <!-- apexcharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js" integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script> <!-- ChartJS -->

    </script> <!-- jsvectormap -->
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js" integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js" integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script> <!-- jsvectormap -->

    <!-- เลือกเดือนปีที่ต้องการดูรายงาน -->
    <script>
        document.getElementById('month-picker').addEventListener('change', function() {
            const selectedMonth = this.value;
            // ตัวอย่าง: ส่งค่ากลับด้วย URL
            window.location.href = "?selected_month=" + selectedMonth + "-01";
        });
    </script>

</body><!--end::Body-->

</html>