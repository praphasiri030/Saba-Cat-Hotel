<?php
session_start();
include 'dbconnect.php';
require_once __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

if (!isset($_GET['booking_ID']) || !is_numeric($_GET['booking_ID'])) {
    exit('Error: booking_ID ไม่ถูกต้อง');
}
$booking_ID = (int) $_GET['booking_ID'];

/* ---------- 2.1 ดึงข้อมูลการจอง + ห้อง + สมาชิก ---------- */
$sqlBooking = "SELECT b.*,
                      r.room_name,
                      t.typ_name,
                      m.fullname, m.email, m.phone,
                      p.payment_amount
               FROM booking b
               JOIN room      r ON r.room_ID     = b.room_ID
               JOIN typ_room  t ON r.typroom_ID  = t.typroom_ID
               JOIN member    m ON m.member_ID   = b.member_ID
               LEFT JOIN payment p ON p.booking_ID = b.booking_ID
               WHERE b.booking_ID = ?";
$stmt = $connection->prepare($sqlBooking);
$stmt->bind_param('i', $booking_ID);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    exit('ไม่พบข้อมูลการจอง');
}

/* ---------- 2.2 ดึง ใบเสร็จ ---------- */
$sqlRc = "SELECT * FROM receipt WHERE booking_ID = ?";
$stmt  = $connection->prepare($sqlRc);
$stmt->bind_param('i', $booking_ID);
$stmt->execute();
$receipt = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$receipt) {
    exit('ไม่พบใบเสร็จของการจองนี้');
}

/* ---------- 2.3 คำนวณเพิ่มเติม ---------- */
$checkIn   = new DateTime($booking['check_in']);
$checkOut  = new DateTime($booking['check_out']);
$totalDays = $checkIn->diff($checkOut)->days;

/* ---------- 2.4 สร้าง HTML สำหรับ mPDF ---------- */
ob_start();
?>
<!DOCTYPE html>
<html lang="th">

<head>
        <meta charset="utf-8">
    <title>ใบเสร็จ - Saba Cat Hotel</title>
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
    
    <style>
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 10pt;
            margin: 0
        }

        .wrap {
            padding: 6mm
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 0 0 4mm
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt
        }

        td {
            padding: 1.5mm 0
        }

        .right {
            text-align: right
        }

        .box {
            border: 1px solid #000;
            padding: 4mm;
            border-radius: 4px
        }
        img.logo {
            width: 90px;
            margin-bottom: .5rem;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="box">
            
            <div class="title"><img src="image_room/logo.jpg" class="logo" alt="SABA Logo"><br>
            ใบเสร็จรับเงิน / Receipt</div>

            <table>
                <tr>
                    <td><strong>เลขที่ใบเสร็จ</strong></td>
                    <td>: <?= $receipt['receipt_num'] ?></td>
                </tr>
                <tr>
                    <td><strong>ออกโดย</strong></td>
                    <td>: <?= htmlspecialchars($receipt['receipt_issuer']) ?></td>
                </tr>
                <tr>
                    <td><strong>วันที่-เวลาออก</strong></td>
                    <td>: <?= date('d/m/Y', strtotime($receipt['receipt_date'])) ?>
                        <?= date('H:i',   strtotime($receipt['receipt_time'])) ?> น.</td>
                </tr>
            </table>

            <hr>

            <table>
                <tr>
                    <td><strong>ชื่อผู้จอง</strong></td>
                    <td>: <?= htmlspecialchars($booking['fullname']) ?></td>
                </tr>
                <tr>
                    <td><strong>อีเมล</strong></td>
                    <td>: <?= htmlspecialchars($booking['email']) ?></td>
                </tr>
                <tr>
                    <td><strong>โทรศัพท์</strong></td>
                    <td>: <?= htmlspecialchars($booking['phone']) ?></td>
                </tr>
                <tr>
                    <td><strong>ห้องพัก</strong></td>
                    <td>: <?= htmlspecialchars($booking['typ_name']) ?> (<?= $booking['room_name'] ?>)</td>
                </tr>
                <tr>
                    <td><strong>เช็คอิน</strong></td>
                    <td>: <?= $checkIn->format('d/m/Y') ?></td>
                </tr>
                <tr>
                    <td><strong>เช็คเอาต์</strong></td>
                    <td>: <?= $checkOut->format('d/m/Y') ?></td>
                </tr>
                <tr>
                    <td><strong>จำนวนวัน</strong></td>
                    <td>: <?= $totalDays ?> วัน</td>
                </tr>
                <tr>
                    <td><strong>จำนวนแมว</strong></td>
                    <td>: <?= $booking['num_catbook'] ?> ตัว</td>
                </tr>
            </table>

            <hr>

            <table>
                <tr>
                    <td>ค่าห้อง (<?= number_format($booking['book_price']) ?>)</td>
                    <td class="right"><?= number_format($booking['book_price'], 2) ?></td>
                </tr>
                <tr>
                    <td>มัดจำ 50%</td>
                    <td class="right">(<?= number_format($booking['payment_amount'], 2) ?>)</td>
                </tr>
                <tr>
                    <td>ยอดคงเหลือชำระวันเช็คอิน</td>
                    <td class="right">(<?= number_format($booking['payment_amount'], 2) ?>)</td>
                </tr>
                <tr>
                    <td class="right"><strong>รวมทั้งสิ้น</strong></td>
                    <td class="right"><strong><?= number_format($booking['book_price'], 2) ?> บาท</strong></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
<?php
$html = ob_get_clean();

/* ---------- 2.5 สร้าง PDF ---------- */
$mpdf = new \Mpdf\Mpdf([
    'mode'          => 'utf-8',
    'format'        => 'A5',
    'margin_left'   => 10,
    'margin_right'  => 10,
    'margin_top'    => 10,
    'margin_bottom' => 10,
    'autoScriptToLang' => true,
    'autoLangToFont'   => true,
]);


$mpdf->WriteHTML($html);
$mpdf->Output("receipt_$booking_ID.pdf", 'I');   // I = เปิดในเบราว์เซอร์
exit;
