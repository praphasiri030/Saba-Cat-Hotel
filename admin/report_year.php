<?php
include("dbconnect.php");

session_start();

if (!isset($_SESSION['islogin']) || $_SESSION['islogin'] != true) {
    header("Location: login.php?status=error");
    exit();
}

date_default_timezone_set('Asia/Bangkok'); // ตั้งเขตเวลาไทย
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// ดึงปีที่มีในฐานข้อมูล
$year_query = "SELECT DISTINCT YEAR(book_date) AS year FROM booking ORDER BY year DESC";
$year_result = $connection->query($year_query);

//จองปีนี้
$sql_booking_today = "
    SELECT COUNT(*) AS total 
    FROM booking 
    WHERE YEAR(book_date) = '$selected_year' 
    AND bookstate_ID IN ('B01','B02','B03','B04','B05')
";
$result_booking_today = $connection->query($sql_booking_today);
$booking_today = ($result_booking_today->num_rows > 0) ? $result_booking_today->fetch_assoc()['total'] : 0;

//ยกเลิกปีนี้
$sql_cancel_today = "
    SELECT COUNT(*) AS total 
    FROM booking 
    WHERE YEAR(book_date) = '$selected_year' 
    AND bookstate_ID = 'C02'
";
$result_cancel_today = $connection->query($sql_cancel_today);
$cancel_today = ($result_cancel_today->num_rows > 0) ? $result_cancel_today->fetch_assoc()['total'] : 0;

// รายได้ปีนี้
$sql_income_today = "
    SELECT SUM(payment_amount) AS total 
    FROM payment 
    WHERE YEAR(payment_date) = '$selected_year'
";
$result_income_today = $connection->query($sql_income_today);
$income_today = ($result_income_today->num_rows > 0) ? number_format($result_income_today->fetch_assoc()['total'], 2) : "0.00";

// การคืนเงินปีนี้
$sql_refund_today = "
    SELECT SUM(cancle_money) AS total 
    FROM cancle 
    WHERE YEAR(cancle_date) = '$selected_year'
";
$result_refund_today = $connection->query($sql_refund_today);
$refund_today = ($result_refund_today->num_rows > 0) ? number_format($result_refund_today->fetch_assoc()['total'], 2) : "0.00";

// เตรียมข้อมูลกราฟจำนวนการจองรายปี
$room_types = [
    1 => 'Standard',
    2 => 'Superior',
    3 => 'Deluxe'
];

$booking_data = [
    1 => array_fill(1, 12, 0),
    2 => array_fill(1, 12, 0),
    3 => array_fill(1, 12, 0),
];

$sql = "
    SELECT 
        MONTH(b.book_date) AS month_num,
        t.typroom_ID,
        COUNT(*) AS total_booking
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE YEAR(b.book_date) = ?
      AND b.bookstate_ID IN ('B01','B02','B03','B04','B05')
    GROUP BY t.typroom_ID, MONTH(b.book_date)
";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $month = (int)$row['month_num'];
    $type = (int)$row['typroom_ID'];
    $total = (int)$row['total_booking'];
    $booking_data[$type][$month] = $total;
}

// ส่งไป JS
$js_standard = json_encode(array_values($booking_data[1]));
$js_superior = json_encode(array_values($booking_data[2]));
$js_deluxe = json_encode(array_values($booking_data[3]));


// เตรียมข้อมูลกราฟรายได้รายปี
$revenue_data = [
    1 => array_fill(1, 12, 0),  // Standard
    2 => array_fill(1, 12, 0),  // Superior
    3 => array_fill(1, 12, 0),  // Deluxe
];

$sql = "
    SELECT 
        MONTH(b.book_date) AS month_num,
        t.typroom_ID,
        SUM(b.book_price) AS total_revenue
    FROM booking b
    INNER JOIN room r ON b.room_ID = r.room_ID
    INNER JOIN typ_room t ON r.typroom_ID = t.typroom_ID
    WHERE YEAR(b.book_date) = ?
      AND b.bookstate_ID IN ('B01','B02','B03','B04','B05')
    GROUP BY t.typroom_ID, MONTH(b.book_date)
";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $month = (int)$row['month_num'];
    $type = (int)$row['typroom_ID'];
    $total = (float)$row['total_revenue'];
    $revenue_data[$type][$month] = $total;
}

// ส่งไป JavaScript
$js_revenue_standard = json_encode(array_values($revenue_data[1]));
$js_revenue_superior = json_encode(array_values($revenue_data[2]));
$js_revenue_deluxe = json_encode(array_values($revenue_data[3]));


?>
<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta charset="UTF-8">
    <title>รายงานรายปี</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- โลโก้-->
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
                <h3 class="mb-0 h3-shadow">รายงานรายปี</h3>
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
                                <?php echo "ปี " . ($selected_year); ?>
                            </h3>
                        </div>

                        <!-- ตัวเลือกปี -->
                        <!-- <div class="col-sm-6 text-end">
                            <select id="year-picker" class="form-control d-inline-block w-auto">
                                <?php
                                $current_year = date('Y');
                                $start_year = 2020; // หรือปีเริ่มระบบ
                                for ($y = $current_year; $y >= $start_year; $y--) {
                                    $selected = ($y == (isset($_GET['year']) ? $_GET['year'] : $current_year)) ? 'selected' : '';
                                    echo "<option value=\"$y\" $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div> -->

                        <form method="get">
                            <label for="year">เลือกปี:</label>
                            <select name="year" id="year" onchange="this.form.submit()">
                                <?php while ($y = $year_result->fetch_assoc()): ?>
                                    <option value="<?= $y['year'] ?>" <?= ($selected_year == $y['year']) ? 'selected' : '' ?>>
                                        <?= $y['year'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div> <!--end::Row-->
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
                                    <h3 class="card-title"><?php echo "รายงานจำนวนการจองห้องพักแยกตามประเภทห้อง ปี " . ($selected_year); ?></h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="bookingYearChart" style="height:250px; width:100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- กราฟรายได้ -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3 class="card-title"><?php echo "รายได้จากการจองห้องพักแยกตามประเภท ปี " . ($selected_year); ?></h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueYearChart" style="height:250px; width:100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <script>
                            const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

                            const dataStandard = <?= $js_standard ?>;
                            const dataSuperior = <?= $js_superior ?>;
                            const dataDeluxe = <?= $js_deluxe ?>;

                            const ctx = document.getElementById('bookingYearChart').getContext('2d');
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: months,
                                    datasets: [{
                                            label: 'Standard',
                                            data: dataStandard,
                                            borderColor: 'rgba(226, 170, 0, 1)',
                                            backgroundColor: 'rgba(226, 170, 0, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        },
                                        {
                                            label: 'Superior',
                                            data: dataSuperior,
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        },
                                        {
                                            label: 'Deluxe',
                                            data: dataDeluxe,
                                            borderColor: 'rgba(255, 99, 132, 1)',
                                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'จำนวนครั้ง'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'เดือน'
                                            }
                                        }
                                    }
                                }
                            });
                        </script>
                        <script>
                            // กราฟรายได้และคืนเงินแยกตามประเภทห้อง

                            const revenueChartCtx = document.getElementById('revenueYearChart').getContext('2d');

                            new Chart(revenueChartCtx, {
                                type: 'line',
                                data: {
                                    labels: months, // เดือนภาษาไทย
                                    datasets: [{
                                            label: 'Standard',
                                            data: <?= $js_revenue_standard ?>,
                                            borderColor: 'rgba(226, 170, 0, 1)',
                                            backgroundColor: 'rgba(226, 170, 0, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        },
                                        {
                                            label: 'Superior',
                                            data: <?= $js_revenue_superior ?>,
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        },
                                        {
                                            label: 'Deluxe',
                                            data: <?= $js_revenue_deluxe ?>,
                                            borderColor: 'rgba(255, 99, 132, 1)',
                                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                            fill: true,
                                            tension: 0.3
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'รายได้ (บาท)'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'เดือน'
                                            }
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

    <!-- เลือกปีที่ต้องการดูรายงาน -->
    <script>
        document.getElementById('year-picker').addEventListener('change', function() {
            const selectedYear = this.value;
            window.location.href = "?year=" + selectedYear;
        });
    </script>


</body><!--end::Body-->

</html>