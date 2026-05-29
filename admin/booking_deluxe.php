<?php
session_start();
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && isset($_POST['booking_ID'])) {
    $booking_ID = intval($_POST['booking_ID']);

    // อัปเดตสถานะเป็นยืนยันแล้ว (B03)
    $stmt = $connection->prepare("UPDATE booking SET bookstate_ID = 'B03' WHERE booking_ID = ?");
    $stmt->bind_param("i", $booking_ID);
    $stmt->execute();
    $stmt->close();

    // redirect เพื่อป้องกัน form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel']) && isset($_POST['booking_ID'])) {
    $booking_ID = intval($_POST['booking_ID']);

    // อัปเดตสถานะเป็นยกเลิก (C03) ยกเลิกโดยแอดมิน
    $stmt = $connection->prepare("UPDATE booking SET bookstate_ID = 'C03' WHERE booking_ID = ?");
    $stmt->bind_param("i", $booking_ID);
    $stmt->execute();
    $stmt->close();

    // redirect เพื่อป้องกัน form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// ดึงข้อมูลการจองทั้งหมดพร้อมสมาชิก และสถานะการจ่ายเงิน
$sql = "
SELECT 
    b.booking_ID, b.check_in, b.check_out, b.book_date, b.book_price,
    m.fullname, m.email, m.phone,
    p.payment_amount, p.paymentSlip, p.payment_date, ps.payState_ID, ps.paymentState,
    bs.bookstate, b.bookstate_ID
FROM booking b
LEFT JOIN member m ON b.member_ID = m.member_ID
LEFT JOIN payment p ON b.booking_ID = p.booking_ID
LEFT JOIN payment_state ps ON p.payState_ID = ps.payState_ID
LEFT JOIN booking_state bs ON b.bookstate_ID = bs.bookstate_ID
LEFT JOIN room r ON b.room_ID = r.room_ID
WHERE r.typroom_ID = 3
ORDER BY b.book_date DESC
";

$result = $connection->query($sql);
$bookings = $result->fetch_all(MYSQLI_ASSOC);

// จำนวนห้องที่รอยืนยัน
$sql = "SELECT COUNT(*) AS total
FROM booking
JOIN room ON booking.room_ID = room.room_ID
JOIN typ_room ON room.typroom_ID = typ_room.typroom_ID
WHERE booking.bookstate_ID = 'B02'
AND typ_room.typroom_ID = 3
";
$result = mysqli_query($connection, $sql);
$row = mysqli_fetch_assoc($result);
$waitingCount = $row['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>การจองห้อง Deluxe</title>
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
                        <h3 class="mb-0 h3-shadow">รายการจอง Deluxe</h3>
                    </div>
                </div>
            </div>

            <div class="mt-3 mb-4">
                <div class="row g-2">
                    <div class="col-4 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn active" data-bs-target="#allbooking">ทั้งหมด</a>
                    </div>
                    <div class="col-4 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn" data-bs-target="#comfirmation">รอยืนยัน
                            <span class="badge bg-danger ms-1"><?= $waitingCount ?></span>
                        </a>
                    </div>
                    <div class="col-4 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn" data-bs-target="#confirmed">ยืนยันแล้ว</a>
                    </div>
                </div>
            </div>

            <?php
            $all = array_filter($bookings, fn($b) => in_array($b['bookstate_ID'], ['B02', 'B03']));
            $pending = array_filter($bookings, fn($b) => $b['bookstate_ID'] == 'B02');
            $confirmed = array_filter($bookings, fn($b) => $b['bookstate_ID'] == 'B03');


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
                                    <th>ยอดที่ต้องชำระ</th>
                                    <th>หลักฐานชำระ</th>
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
                                            <i class="bi bi-calendar-event"></i> จอง: <?= date('d M Y', strtotime($b['book_date'])); ?><br>
                                            <i class="bi bi-door-open"></i> เช็คอิน: <?= $b['check_in']; ?><br>
                                            <i class="bi bi-box-arrow-in-left"></i> เช็คเอาท์: <?= $b['check_out']; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?= $b['bookstate'] ?? 'ไม่มีสถานะ'; ?></span>
                                        </td>
                                        <td class="text-end"><?= number_format($b['payment_amount'], 0); ?> บาท</td>
                                        <td class="text-center">
                                            <?php if (!empty($b['paymentSlip'])): ?>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#slipModal<?= $b['booking_ID']; ?>">
                                                    <i class="bi bi-receipt"></i> ดูสลิป
                                                </button>
                                                <div class="modal fade" id="slipModal<?= $b['booking_ID']; ?>" tabindex="-1" aria-labelledby="slipModalLabel<?= $b['booking_ID']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="slipModalLabel<?= $b['booking_ID']; ?>">หลักฐานการชำระเงิน</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="<?= $b['paymentSlip']; ?>" alt="Slip" class="img-fluid rounded">
                                                                <p class="mt-2 text-muted">วันที่ชำระ: <?= $b['payment_date'] ?? 'ไม่ระบุ'; ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">ไม่มี</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($b['bookstate_ID'] != 'B03'): ?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="booking_ID" value="<?= $b['booking_ID']; ?>">
                                                    <button type="submit" name="confirm" class="btn btn-success btn-sm mb-1" onclick="return confirm('ยืนยันการชำระเงินสำหรับรหัสจอง #<?= $b['booking_ID']; ?> ?')">
                                                        <i class="bi bi-check-circle"></i> ยืนยัน
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('คุณต้องการยกเลิกรายการจอง #<?= $b['booking_ID']; ?> หรือไม่?');">
                                                <input type="hidden" name="booking_ID" value="<?= $b['booking_ID']; ?>">
                                                <button type="submit" name="cancel" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-circle"></i> ยกเลิก
                                                </button>
                                            </form>

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