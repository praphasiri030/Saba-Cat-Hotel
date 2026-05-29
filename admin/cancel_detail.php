<?php
session_start();
include('dbconnect.php');

$bookingID = $_GET['id'] ?? null;

if (!$bookingID) {
    echo "ไม่พบข้อมูลการจอง";
    exit;
}

// ดึงข้อมูลการยกเลิกพร้อมข้อมูลที่เกี่ยวข้อง
$sql = "
SELECT 
    b.booking_ID,
    b.check_in,
    b.check_out,
    b.book_price,
    b.bookstate_ID,
    b.note,
    bs.bookState,
    m.fullname,
    m.email,
    m.phone,
    r.room_name,
    t.typ_name,
    c.cancle_date,
    c.canclestate_ID,
    c.bank_name,
    c.bankAc_name,
    c.bank_num,
    c.cancleSlip,
    c.cancle_money,
    p.paymentSlip
FROM booking b
LEFT JOIN member m ON b.member_ID = m.member_ID
LEFT JOIN room r ON b.room_ID = r.room_ID
LEFT JOIN typ_room t ON r.typroom_ID = t.typroom_ID
LEFT JOIN cancle c ON b.booking_ID = c.booking_ID
LEFT JOIN payment p ON b.booking_ID = p.booking_ID
LEFT JOIN booking_state bs ON b.bookstate_ID = bs.bookstate_ID
WHERE b.booking_ID = ?
";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $bookingID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "ไม่พบข้อมูลการจองนี้";
    exit;
}

$data = $result->fetch_assoc();

// ถ้ามีการส่งฟอร์มอัปโหลด
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cancleSlip'])) {
    // ---------- 1. รับค่าที่จำเป็น ----------
    $booking_ID = $_POST['booking_ID'];

    // ---------- 2. ตรวจ error การอัปโหลด ----------
    if ($_FILES['cancleSlip']['error'] === UPLOAD_ERR_OK) {

        // ---------- 3. เตรียมโฟลเดอร์และชื่อไฟล์ ----------
        //เก็บไว้ในโฟลเดอร์ slip/cancel/
        $target_dir = "slip/cancel/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['cancleSlip']['name'], PATHINFO_EXTENSION);
        $file_name      = uniqid('slip_') . '.' . $file_extension;
        $target_file    = $target_dir . $file_name;

        // ---------- 4. ตรวจว่าคือรูปภาพ + ขนาดไม่เกิน 5 MB ----------
        $check = getimagesize($_FILES['cancleSlip']['tmp_name']);
        if ($check !== false && $_FILES['cancleSlip']['size'] <= 5 * 1024 * 1024) {

            /* ---------- 5. ย้ายไฟล์ขึ้นเซิร์ฟเวอร์ ----------
               สำเร็จแล้วประกอบ URL เต็ม (ปรับเป็นโดเมนจริงของคุณภายหลัง)
            */
            if (move_uploaded_file($_FILES['cancleSlip']['tmp_name'], $target_file)) {

                // *** URL เต็มที่จะเก็บใน DB ***
                $imagePath = "http://localhost/Cathotel_Project/" . $target_file;

                // ---------- 6. อัปเดตตาราง cancle ----------
                $sql1  = "UPDATE cancle SET cancleSlip = ?, canclestate_ID = 'C02' WHERE booking_ID = ?";
                $stmt1 = $connection->prepare($sql1);
                $stmt1->bind_param("si", $imagePath, $booking_ID);   // เก็บ URL เต็ม
                $stmt1->execute();

                // ---------- 7. เปลี่ยนสถานะใน booking ----------
                $sql2  = "UPDATE booking SET bookstate_ID = 'C02' WHERE booking_ID = ?";
                $stmt2 = $connection->prepare($sql2);
                $stmt2->bind_param("i", $booking_ID);
                $stmt2->execute();

                // ---------- 8. กลับไปหน้า detail ----------
                header("Location: cancel_detail.php?id=" . $booking_ID);
                exit;
            } else {
                $uploadError = "ไม่สามารถอัปโหลดไฟล์ได้";
            }
        } else {
            $uploadError = "กรุณาเลือกไฟล์รูปภาพไม่เกิน 5 MB";
        }
    } else {
        $uploadError = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
    }
}

?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <title>รายละเอียดการยกเลิก</title>
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
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #F1EEE0;
        }

        /*  ย้ายเงาและมุมโค้งไปใช้ทุกการ์ด  */
        .card-custom {
            background-color: #ffffff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .08);
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

        h5.section-title {
            font-weight: 700;
            margin-bottom: .75rem;
        }

        /*  แบตช์สถานะ  */
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

        /*  ปุ่ม action  */
        .btn-action {
            background-color: #FFE488;
            color: #000;
            font-weight: 600;
            border: none;
            box-shadow: var(--clr-shadow);
        }

        .btn-action:hover {
            background-color: #ffdb54;
            color: #000;
        }

        /*  ปุ่มอัปโหลดสลิป   */
        input[type="file"]::-webkit-file-upload-button {
            background: #FFE488;
            border: none;
            padding: .35rem .85rem;
            border-radius: .5rem;
            font-weight: 600;
            cursor: pointer;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: #ffdb54;
        }

        /*  รูปสลิป  */
        .slip-img {
            width: 80%;
            height: 480px;
            object-fit: contain;
            display: block;
            margin-left: auto;
            margin-right: auto;
            border-radius: .75rem;
            border: 1px solid #e2e2e2;
            background-color: #f9f9f9;
        }


        /*  แถบด้านบน  */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .back-link {
            text-decoration: none;
            color: #000;
            display: flex;
            align-items: center;
            gap: .25rem;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg " style="background-color: #F1EEE0; font-family: 'Noto Sans Thai', sans-serif;">
    <div class="app-wrapper">
        <?php include('left_sidebar.php'); ?>
        <main class="app-main">
            <!--  ข้อความแจ้งเตือนอัปโหลดผิดพลาด  -->
            <?php if (isset($uploadError)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($uploadError) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!--  Header  -->
            <div class="card-header mb-4">
                <div class="row">
                    <div class="d-flex align-items-center gap-2">
                        <a href="cancel_booking.php" class="back-link"><i class="bi bi-arrow-left"></i></a>
                        <h3 class="mb-0 h3-shadow">#<?= htmlspecialchars($data['booking_ID']) ?></h3>
                    </div>
                </div>
            </div>

            <!--  Grid  -->
            <div class="row g-4">
                <!--  LEFT SIDE  -->
                <div class="col-lg-6 d-flex flex-column gap-4">

                    <!--  การ์ดข้อมูลลูกค้า  -->
                    <div class="card card-custom p-4">
                        <h5 class="section-title">ข้อมูลลูกค้า</h5>
                        <p class="mb-0">
                            <?= htmlspecialchars($data['fullname']) ?><br>
                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($data['email']) ?><br>
                            <i class="bi bi-telephone"></i> <?= htmlspecialchars($data['phone']) ?>
                        </p>
                    </div>

                    <!--  การ์ดข้อมูลการจอง  -->
                    <div class="card card-custom p-4">
                        <h5 class="section-title">ข้อมูลการจอง</h5>
                        <div class="row gy-2">
                            <div class="col-sm-6"><strong>ห้องพัก:</strong> <?= htmlspecialchars($data['room_name'] ?? '-') ?></div>
                            <div class="col-sm-6"><strong>ประเภทห้อง:</strong> <?= htmlspecialchars($data['typ_name'] ?? '-') ?></div>
                            <div class="col-sm-6"><strong>เช็คอิน:</strong> <?= $data['check_in'] ?></div>
                            <div class="col-sm-6"><strong>เช็คเอาท์:</strong> <?= $data['check_out'] ?></div>
                            <div class="col-sm-6"><strong>ราคาทั้งหมด:</strong> <?= number_format($data['book_price'], 2) ?> บาท</div>
                            <div class="col-sm-6"><strong>หมายเหตุ:</strong> <?= htmlspecialchars($data['note'] ?? '-') ?></div>
                        </div>
                    </div>

                    <!--  การ์ดข้อมูลการคืนเงิน  -->
                    <div class="card card-custom p-4" id="refund-section">
                        <h5 class="section-title">ข้อมูลการคืนเงิน</h5>
                        <?php if ($data['cancle_money']): ?>
                            <p class="mb-2">
                                วันที่ยกเลิก: <?= $data['cancle_date'] ?? '-' ?><br>
                                ธนาคาร: <?= htmlspecialchars($data['bank_name']) ?><br>
                                ชื่อบัญชี: <?= htmlspecialchars($data['bankAc_name']) ?><br>
                                เลขบัญชี: <?= htmlspecialchars($data['bank_num']) ?><br>
                                จำนวนเงินที่คืน: <span class="fw-bold text-success"><?= number_format($data['cancle_money'], 2) ?> บาท</span>
                            </p>
                            <?php if ($data['bookstate_ID'] !== 'C02'): ?>
                                <form action="cancel_detail.php?id=<?= htmlspecialchars($data['booking_ID']) ?>" method="post" enctype="multipart/form-data" class="mt-3">
                                    <div class="mb-3">
                                        <label for="cancleSlip" class="form-label fw-bold">อัปโหลดหลักฐานการคืนเงิน</label>
                                        <input type="file" class="form-control" id="cancleSlip" name="cancleSlip" accept="image/*" required>
                                        <input type="hidden" name="booking_ID" value="<?= htmlspecialchars($data['booking_ID']) ?>">
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-action px-5">ยืนยัน</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">ยังไม่มีข้อมูลการคืนเงิน</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!--  RIGHT SIDE  -->
                <div class="col-lg-6 d-flex flex-column gap-4">
                    <!--  หลักฐานการชำระเงิน  -->
                    <div class="card card-custom p-4">
                        <h5 class="section-title">หลักฐานการชำระเงิน</h5>
                        <?php if (!empty($data['paymentSlip'])): ?>
                            <img src="<?= htmlspecialchars($data['paymentSlip']) ?>" class="slip-img mb-0" alt="หลักฐานการชำระเงิน">
                        <?php else: ?>
                            <p class="text-muted mb-0">ยังไม่มีหลักฐานการชำระเงิน</p>
                        <?php endif; ?>
                    </div>

                    <!--  หลักฐานการคืนเงิน  -->
                    <div class="card card-custom p-4">
                        <h5 class="section-title">หลักฐานการคืนเงิน</h5>
                        <?php if (!empty($data['cancleSlip'])): ?>
                            <img src="<?= htmlspecialchars($data['cancleSlip']) ?>" class="slip-img mb-0" alt="หลักฐานการคืนเงิน">
                        <?php else: ?>
                            <p class="text-muted mb-0">ยังไม่มีหลักฐานการคืนเงิน</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="/CatHotel_Project/dist/js/adminlte.js"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>