<?php
session_start();
include('dbconnect.php');

// ดึงข้อมูลการจองทั้งหมดพร้อมสมาชิก และสถานะการจ่ายเงิน
$sql = "
SELECT 
    b.booking_ID, 
    b.check_in, 
    b.check_out, 
    b.book_date, 
    b.book_price,
    m.fullname, 
    m.email, 
    m.phone,
    bs.bookstate, 
    b.bookstate_ID,
    t.typ_name
FROM booking b
LEFT JOIN member m ON b.member_ID = m.member_ID
LEFT JOIN booking_state bs ON b.bookstate_ID = bs.bookstate_ID
LEFT JOIN room r ON b.room_ID = r.room_ID
LEFT JOIN typ_room t ON r.typroom_ID = t.typroom_ID
ORDER BY b.book_date DESC

";

$result = $connection->query($sql);
$bookings = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ยกเลิกการจอง</title>
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
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-cancel {
            background-color: #dc3545;
        }

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

        /* ปุ่ม */
        .custom-tab-btn {
            background-color: #fff;
            /* สีขาว */
            color: #000;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .custom-tab-btn:hover {
            background-color: #FFE488;
            /* สีเหลืองอ่อนเมื่อ hover */
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .custom-tab-btn.active {
            background-color: #FFE488;
            /* สีเหลืองเข้มตอน active */
            color: #000;
            font-weight: bold;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
    </style>


</head>

<body class="layout-fixed sidebar-expand-lg " style="background-color: #F1EEE0; font-family: 'Noto Sans Thai', sans-serif;">
    <div class="app-wrapper">
        <?php include('left_sidebar.php'); ?>
        <main class="app-main">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <h3 class="mb-0 h3-shadow">รายการยกเลิก</h3>
                    </div>
                </div>
            </div>

            <div class="mt-3 mb-4">
                <div class="row g-2">
                    <div class="col-4 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn active" data-bs-target="#allbooking">ทั้งหมด</a>
                    </div>
                    <div class="col-4 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn" data-bs-target="#comfirmation">รอตรวจสอบ</a>
                    </div>
                    <div class="col-4 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn" data-bs-target="#confirmed">ยกเลิกแล้ว</a>
                    </div>
                </div>
            </div>

            <?php
            $all = array_filter($bookings, fn($b) => in_array($b['bookstate_ID'], ['C01', 'C02', 'C03']));
            $pending = array_filter($bookings, fn($b) => $b['bookstate_ID'] == 'C01');
            $confirmed = array_filter($bookings, fn($b) => in_array($b['bookstate_ID'],['C02', 'C03']));


            $tabs = [
                'allbooking' => $all,
                'comfirmation' => $pending,
                'confirmed' => $confirmed
            ];

            foreach ($tabs as $tabID => $tabBookings):
            ?>
                <div class="card-body mt-3" id="<?= $tabID ?>">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-hover">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>รหัสจอง</th>
                                    <th>ลูกค้า</th>
                                    <th>รายละเอียด</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tabBookings as $b): ?>
                                    <tr>
                                        <td class="text-center">#<?= $b['booking_ID']; ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($b['fullname']); ?></strong><br>
                                            <small><i class="bi bi-envelope"></i> <?= $b['email']; ?></small><br>
                                            <small><i class="bi bi-telephone"></i> <?= $b['phone']; ?></small>
                                        </td>
                                        <td>
                                            <?= $b['typ_name']; ?><br>
                                            <i class="bi bi-door-open"></i> เช็คอิน: <?= $b['check_in']; ?><br>
                                            <i class="bi bi-calendar-event"></i> จอง: <?= date('d M Y', strtotime($b['book_date'])); ?><br>
                                            <i class="bi bi-box-arrow-in-left"></i> เช็คเอาท์: <?= $b['check_out']; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?= $b['bookstate'] ?? 'ไม่มีสถานะ'; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="cancel_detail.php ?id=<?= $b['booking_ID']; ?>" class="btn btn-danger btn-sm">
                                                รายละเอียด
                                            </a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($tabBookings)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">ไม่มีข้อมูลการจอง</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="/CatHotel_Project/dist/js/adminlte.js"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".tab-btn");
            const sections = {
                "#allbooking": document.getElementById("allbooking"),
                "#comfirmation": document.getElementById("comfirmation"),
                "#confirmed": document.getElementById("confirmed")
            };

            // แสดงเฉพาะอันแรกเริ่มต้น
            Object.values(sections).forEach(section => {
                if (section) section.style.display = "none";
            });
            if (sections["#allbooking"]) sections["#allbooking"].style.display = "block";

            buttons.forEach(button => {
                button.addEventListener("click", function() {
                    // ลบ active จากทุกปุ่ม
                    buttons.forEach(btn => btn.classList.remove("active"));
                    this.classList.add("active");

                    // ซ่อนทุก section
                    Object.values(sections).forEach(section => {
                        if (section) section.style.display = "none";
                    });

                    // แสดง section ที่เลือก
                    const targetId = this.getAttribute("data-bs-target");
                    if (sections[targetId]) sections[targetId].style.display = "block";
                });
            });
        });
    </script>

</body>

</html>