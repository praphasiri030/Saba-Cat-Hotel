<?php
session_start();
include('dbconnect.php');


// 1) จัดการ POST: เช็คอิน / เช็คเอาท์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['booking_ID'])) {

    $booking_ID = intval($_POST['booking_ID']);
    $action     = $_POST['action'];          // checkin | checkout

    // --- เช็ค action ---
    if ($action === 'checkin' || $action === 'checkout') {

        // 1) กำหนดสถานะใหม่
        $new_state = $action === 'checkin' ? 'B04' : 'B05';

        // 2) อัปเดต booking
        $stmt = $connection->prepare("UPDATE booking SET bookstate_ID = ? WHERE booking_ID = ?");
        $stmt->bind_param('si', $new_state, $booking_ID);
        $stmt->execute();
        $stmt->close();

        // 3) ถ้าเช็คอิน ➜ สร้างใบเสร็จ
        if ($action === 'checkin') {
            $prefix = 'RC-' . date('Ymd') . '-';
            $qry = $connection->prepare("SELECT COUNT(*) FROM receipt WHERE receipt_num LIKE CONCAT(?, '%')");
            $qry->bind_param('s', $prefix);
            $qry->execute();
            $qry->bind_result($n);
            $qry->fetch();
            $qry->close();

            $receiptNum = $prefix . str_pad($n + 1, 4, '0', STR_PAD_LEFT);
            $issuer     = $_SESSION['staff_name'] ?? 'System';

            $ins = $connection->prepare(
                "INSERT INTO receipt (receipt_num, receipt_issuer, receipt_date, booking_ID)
                 VALUES (?, ?, CURDATE(), ?)"
            );
            $ins->bind_param('ssi', $receiptNum, $issuer, $booking_ID);
            $ins->execute();
            $ins->close();
        }

        // 4) redirect กลับหน้าเดิม + msg=success
        $page = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: {$page}?msg=success");
        exit;
    }

    // ---- action ไม่ถูกต้อง → redirect invalid เฉพาะเคสนี้ ----
    $page = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: {$page}?msg=invalid");
    exit;
}

// ดึงข้อมูลการจองทั้งหมดพร้อมสมาชิก และสถานะการจ่ายเงิน
$sql = "
SELECT 
    b.booking_ID, b.check_in, b.check_out, b.book_date, b.book_price, b.num_catbook, 
    m.fullname, m.email, m.phone,
    p.payment_amount, p.paymentSlip, p.payment_date, ps.payState_ID, ps.paymentState,
    bs.bookstate, b.bookstate_ID, t.typ_name, r.room_name
FROM booking b
LEFT JOIN member m ON b.member_ID = m.member_ID
LEFT JOIN payment p ON b.booking_ID = p.booking_ID
LEFT JOIN payment_state ps ON p.payState_ID = ps.payState_ID
LEFT JOIN booking_state bs ON b.bookstate_ID = bs.bookstate_ID
LEFT JOIN room r ON b.room_ID = r.room_ID
LEFT JOIN typ_room t ON r.typroom_ID = t.typroom_ID
ORDER BY 
    CASE WHEN b.check_in = CURDATE() THEN 0 ELSE 1 END,
    b.check_in ASC
";


$result = $connection->query($sql);
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>เช็คอิน-เช็คเอาท์</title>
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
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden !important;
        }

        body.layout-fixed.sidebar-expand-lg {
            background-color: #F1EEE0 !important;
            font-family: 'Noto Sans Thai', sans-serif;
            overflow-x: hidden;
            /* ป้องกัน scroll แนวนอน */
            max-width: 100vw;
            /* จำกัดความกว้างไม่ให้ล้น viewport */
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            display: block;
        }


        .app-wrapper,
        .app-main {
            background-color: transparent !important;
        }

        /* Header card */
        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding-bottom: 0;
        }

        /* Header h3 */
        .card-header h3 {
            font-weight: bold;
            text-align: center;
            width: 100%;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.1);
        }

        /* Custom tab buttons */
        .custom-tab-btn {
            background-color: #fff;
            color: #000;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .custom-tab-btn:hover {
            background-color: #FFE488;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .custom-tab-btn.active {
            background-color: #FFE488;
            color: #000;
            font-weight: bold;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Badge colors */
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
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg">
    <div class="app-wrapper">
        <?php include('left_sidebar.php'); ?>

        <main class="app-main">
            <div class="card-header">
                <div class="col-12">
                    <h3>Check in - Check out</h3>
                </div>
            </div>

            <div class="mt-3 mb-4">
                <div class="row g-2">
                    <div class="col-6 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn active" data-bs-target="#checkin">เช็คอิน</a>
                    </div>
                    <div class="col-6 text-center">
                        <a class="btn custom-tab-btn w-100 py-2 tab-btn" data-bs-target="#checkout">เช็คเอาท์</a>
                    </div>
                </div>
            </div>

            <?php
            // แยก booking ตามสถานะ
            $checkin = array_filter($bookings, fn($b) => $b['bookstate_ID'] == 'B03');
            $checkout = array_filter($bookings, fn($b) => $b['bookstate_ID'] == 'B04');


            $tabs = [
                'checkin' => $checkin,
                'checkout' => $checkout,
            ];

            foreach ($tabs as $tabID => $tabBookings):
                // กรองชื่อ
                if (!empty($_GET[$tabID . '_name'])) {
                    $keyword = strtolower(trim($_GET[$tabID . '_name']));
                    $tabBookings = array_filter($tabBookings, function ($b) use ($keyword) {
                        return strpos(strtolower($b['fullname']), $keyword) !== false;
                    });
                }

                // กรองวันที่จอง
                if (!empty($_GET[$tabID . '_date'])) {
                    $dateFilter = $_GET[$tabID . '_date'];
                    $tabBookings = array_filter($tabBookings, function ($b) use ($dateFilter) {
                        return date('Y-m-d', strtotime($b['book_date'])) === $dateFilter;
                    });
                }
            ?>
                <div class="card-body mt-3" id="<?= $tabID ?>">
                    <div class="table-responsive">
                        <!-- ฟอร์มค้นหา -->
                        <form class="row g-2 mb-3" method="get">
                            <div class="col-md-9">
                                <input type="text" name="<?= $tabID ?>_name" class="form-control" placeholder="ค้นหาชื่อลูกค้า" value="<?= htmlspecialchars($_GET[$tabID . '_name'] ?? '') ?>" />
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="<?= $tabID ?>_date" class="form-control" value="<?= htmlspecialchars($_GET[$tabID . '_date'] ?? '') ?>" />
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-dark w-100">ค้นหา</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle table-hover">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>รหัสจอง</th>
                                        <th>ชื่อ</th>
                                        <th>เช็คอิน</th>
                                        <th>เช็คเอาท์</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tabBookings)): ?>
                                        <?php foreach ($tabBookings as $b): ?>
                                            <tr>
                                                <td class="text-center">#<?= $b['booking_ID']; ?></td>
                                                <td class="text-center"><?= htmlspecialchars($b['fullname']); ?></td>
                                                <!-- วันที่เช็คอิน -->
                                                <td class="text-center" style="color:#115500">
                                                    <?= date('d-m-Y', strtotime($b['check_in'])); ?>
                                                </td>
                                                <!-- วันที่เช็คเอาท์ -->
                                                <td class="text-center" style="color:#A40000">
                                                    <?= date('d-m-Y', strtotime($b['check_out'])); ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $today = date('Y-m-d');
                                                    $checkin_date = date('Y-m-d', strtotime($b['check_in']));
                                                    $checkout_date = date('Y-m-d', strtotime($b['check_out']));
                                                    $canCheckin = ($today === $checkin_date);
                                                    $canCheckout = ($today === $checkout_date);
                                                    ?>

                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="booking_ID" value="<?= $b['booking_ID']; ?>" />

                                                        <?php if ($tabID === 'checkin'): ?>
                                                            <input type="hidden" name="action" value="checkin" />
                                                            <button type="button" class="btn btn-success btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#checkinModal<?= $b['booking_ID']; ?>" <?= $canCheckin ? '' : 'disabled'; ?>>
                                                                <i class="bi bi-door-open"></i> เช็คอิน
                                                            </button>
                                                            <?php if (!$canCheckin): ?>
                                                                <div class="text-danger small">สามารถเช็คอินได้ในวันที่ <?= date('d F Y', strtotime($b['check_in'])); ?></div>
                                                            <?php endif; ?>

                                                            <!-- Modal เช็คอิน -->
                                                            <div class="modal fade" id="checkinModal<?= $b['booking_ID']; ?>" tabindex="-1" aria-labelledby="checkinLabel<?= $b['booking_ID']; ?>" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content text-center">
                                                                        <div class="modal-header justify-content-between">
                                                                            <a href="receipt_print.php?booking_ID=<?= $b['booking_ID']; ?>" target="_blank" class="btn btn-outline-dark btn-sm" title="พิมพ์ใบเสร็จ">
                                                                                <i class="bi bi-file-earmark-arrow-down"></i>
                                                                            </a>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>

                                                                        <div class="modal-body">
                                                                            <div class="border rounded p-3 mx-auto" style="max-width: 400px;">
                                                                                <img src="image_room/logo.jpg" alt="SABA Logo" style="width: 80px;" class="mb-3">
                                                                                <h5 class="fw-bold">Standard Room #<?= $b['room_name']; ?></h5>
                                                                                <div class="fw-bold mt-2"><?= $b['fullname']; ?></div>
                                                                                <div class="text-muted small">
                                                                                    <i class="bi bi-envelope"></i> <?= $b['email']; ?><br>
                                                                                    <i class="bi bi-telephone"></i> <?= $b['phone']; ?>
                                                                                </div>

                                                                                <div class="row text-start mt-3">
                                                                                    <div class="col-6">เช็คอิน<br><strong><?= date('d F Y', strtotime($b['check_in'])); ?></strong></div>
                                                                                    <div class="col-6">เช็คเอาท์<br><strong><?= date('d F Y', strtotime($b['check_out'])); ?></strong></div>
                                                                                </div>
                                                                                <div class="row text-start mt-2">
                                                                                    <div class="col-6">จำนวนวันทั้งหมด<br><strong><?= (new DateTime($b['check_in']))->diff(new DateTime($b['check_out']))->days; ?></strong></div>
                                                                                    <div class="col-6">จำนวนแมว<br><strong><?= $b['num_catbook']; ?></strong></div>
                                                                                </div>
                                                                                <div class="row text-start mt-2">
                                                                                    <div class="col-6">มัดจำ50%<br><strong><?= number_format($b['payment_amount'], 0); ?> บาท</strong></div>
                                                                                    <div class="col-6">ยอดที่ต้องชำระวันเช็คอิน<br><strong><?= number_format($b['payment_amount'], 0); ?> บาท</strong></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-footer justify-content-center">
                                                                            <form method="post" action="checkin_checkout.php">
                                                                                <input type="hidden" name="booking_ID" value="<?= $b['booking_ID']; ?>">
                                                                                <input type="hidden" name="action" value="checkin">
                                                                                <button type="submit" class="btn btn-dark">เช็คอิน</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        <?php elseif ($tabID === 'checkout'): ?>
                                                            <input type="hidden" name="action" value="checkout" />
                                                            <button type="button" class="btn btn-dark btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#checkoutModal<?= $b['booking_ID']; ?>" <?= $canCheckout ? '' : 'disabled'; ?>>
                                                                <i class="bi bi-door-closed"></i> เช็คเอาท์
                                                            </button>
                                                            <?php if (!$canCheckout): ?>
                                                                <div class="text-danger small">สามารถเช็คเอาท์ได้ในวันที่ <?= date('d F Y', strtotime($b['check_out'])); ?></div>
                                                            <?php endif; ?>

                                                            <!-- Modal เช็คเอาท์ -->
                                                            <div class="modal fade" id="checkoutModal<?= $b['booking_ID']; ?>" tabindex="-1" aria-labelledby="checkoutLabel<?= $b['booking_ID']; ?>" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content text-center">
                                                                        <div class="modal-header justify-content-between">
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="border rounded p-3 mx-auto" style="max-width: 400px;">
                                                                                <img src="image_room/logo.jpg" alt="SABA Logo" style="width: 80px;" class="mb-3">
                                                                                <h5 class="fw-bold">Standard Room #<?= $b['room_name']; ?></h5>
                                                                                <div class="fw-bold mt-2"><?= $b['fullname']; ?></div>
                                                                                <div class="text-muted small">
                                                                                    <i class="bi bi-envelope"></i> <?= $b['email']; ?><br>
                                                                                    <i class="bi bi-telephone"></i> <?= $b['phone']; ?>
                                                                                </div>

                                                                                <div class="row text-start mt-3">
                                                                                    <div class="col-6">เช็คอิน<br><strong><?= date('d F Y', strtotime($b['check_in'])); ?></strong></div>
                                                                                    <div class="col-6">เช็คเอาท์<br><strong><?= date('d F Y', strtotime($b['check_out'])); ?></strong></div>
                                                                                </div>
                                                                                <div class="row text-start mt-2">
                                                                                    <div class="col-6">จำนวนวันทั้งหมด<br><strong><?= (new DateTime($b['check_in']))->diff(new DateTime($b['check_out']))->days; ?></strong></div>
                                                                                    <div class="col-6">จำนวนแมว<br><strong><?= $b['num_catbook']; ?></strong></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-footer justify-content-center">
                                                                            <form method="post" action="checkin_checkout.php">
                                                                                <input type="hidden" name="booking_ID" value="<?= $b['booking_ID']; ?>">
                                                                                <input type="hidden" name="action" value="checkout">
                                                                                <button type="submit" class="btn btn-dark">เช็คเอาท์</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </form>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">ไม่พบข้อมูล</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();

                // ปิด tab เดิมทั้งหมด
                document.querySelectorAll('.card-body').forEach(tab => tab.style.display = 'none');

                // ลบคลาส active ทุกปุ่ม
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

                // แสดง tab ใหม่
                const target = btn.getAttribute('data-bs-target').substring(1);
                document.getElementById(target).style.display = 'block';

                // ตั้งปุ่มเป็น active
                btn.classList.add('active');
            });
        });

        // เริ่มต้นแสดง tab แรก
        document.querySelectorAll('.card-body').forEach(tab => tab.style.display = 'none');
        document.getElementById('checkin').style.display = 'block';
    </script>
</body>

</html>