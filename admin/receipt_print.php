<?php
session_start();
include 'dbconnect.php';            // เชื่อมต่อฐานข้อมูล
require_once __DIR__ . '/vendor/autoload.php'; // โหลด mPDF
use Mpdf\Mpdf;

if (!isset($_GET['booking_ID']) || !is_numeric($_GET['booking_ID'])) {
    exit('Error: กรุณาระบุ booking_ID ที่ถูกต้อง');
}

$booking_ID = intval($_GET['booking_ID']);

// ดึงข้อมูลการจอง + ผู้จอง + ยอดเงิน
$sql = "SELECT b.*, r.room_name, m.fullname, m.email, m.phone, t.typ_name,
               (SELECT payment_amount FROM payment WHERE booking_ID = b.booking_ID LIMIT 1) AS payment_amount
        FROM booking AS b
        JOIN room AS r ON r.room_ID = b.room_ID
        JOIN typ_room AS t ON t.typroom_ID = r.typroom_ID 
        JOIN member AS m ON m.member_ID = b.member_ID
        WHERE b.booking_ID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $booking_ID);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    exit('ไม่พบข้อมูลการจอง');
}

// คำนวณข้อมูลเสริม
$checkIn   = new DateTime($booking['check_in']);
$checkOut  = new DateTime($booking['check_out']);
$totalDays = $checkIn->diff($checkOut)->days;
$deposit   = (float)$booking['payment_amount'];
$balance   = $deposit;

// สร้าง HTML ใบเสร็จ
ob_start(); ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ใบเสร็จ #<?= sprintf('%06d', $booking_ID) ?></title>
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
    <!-- <div class="container">
        <div class="box">
            <img src="image_room/logo.jpg" class="logo" alt="SABA Logo">
            <h2 class="bold">Standard Room #<?= htmlspecialchars($booking['room_name']) ?></h2>
            <div class="bold" style="margin-top: .5rem;">
                <?= htmlspecialchars($booking['fullname']) ?>
            </div>
            <div class="small" style="margin-top: .25rem;">
                <?= htmlspecialchars($booking['email']) ?><br>
                <?= htmlspecialchars($booking['phone']) ?>
            </div>

            <table style="width: 100%; margin-top: 1.5rem; font-size: 0.95rem;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <strong>เช็คอิน</strong><br><?= $checkIn->format('d F Y') ?><br>
                        <strong>จำนวนวันทั้งหมด</strong><br><?= $totalDays ?><br>
                        <strong>มัดจำ 50%</strong><br><?= $booking['payment_amount'] ?> บาท
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <strong>เช็คเอาท์</strong><br><?= $checkOut->format('d F Y') ?><br>
                        <strong>จำนวนแมว</strong><br><?= $booking['num_catbook'] ?><br>
                        <strong>ยอดที่ต้องชำระวันเช็คอิน</strong><br><?= $booking['payment_amount'] ?> บาท
                    </td>
                </tr>
            </table>


            <div style="text-align: center; margin-top: .5rem;">
                รวม <span class="bold"><?= $booking['book_price'] ?> บาท</span>
            </div>
        </div>
    </div> -->
    <div class="wrap">
        <div class="box">

            <div class="title"><img src="image_room/logo.jpg" class="logo" alt="SABA Logo"><br>
                ใบเสร็จรับเงิน / Receipt
            </div>

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
            </table>

            <hr>

            <table>
                <tr>
                    <td><strong>ห้องพัก</strong></td>
                    <td>: <?= htmlspecialchars($booking['typ_name']) ?> (<?= $booking['room_name'] ?>)</td>
                </tr>
                <tr>
                    <td><strong>เช็คอิน</strong></td>
                    <td>: <?= $checkIn->format('d/m/Y') ?></td>
                </tr>
                <tr>
                    <td><strong>เช็คเอาท์</strong></td>
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

// สร้าง PDF ด้วย mPDF
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
$mpdf->Output('receipt_' . $booking_ID . '.pdf', 'I'); // แสดงผล PDF ในเบราว์เซอร์
exit;
?>