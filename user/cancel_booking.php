<?php
session_start();
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['canclestate_ID'])) {
    // ----- กรณียืนยันการยกเลิก -----
    $bookingID = $_POST['booking_id'];
    $canclestate = $_POST['canclestate_ID'];

    if ($canclestate === 'C01') {
        // กรณีมีเงินคืน
        $refundAmount = $_POST['refund_amount'];
        $bankName = $_POST['refund_method'];
        $accountName = $_POST['account_name'];
        $accountNumber = $_POST['account_number'];
        $today = date('Y-m-d');

        $stmt = $connection->prepare("
            INSERT INTO cancle (booking_ID, canclestate_ID, cancle_date, bank_name, bankAc_name, bank_num, cancle_money)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssi", $bookingID, $canclestate, $today, $bankName, $accountName, $accountNumber, $refundAmount);
        $stmt->execute();

        //อัพเดตสถานะ booking**
        $stmt_update = $connection->prepare("UPDATE booking SET bookstate_ID = 'C01' WHERE booking_ID = ?");
        $stmt_update->bind_param("i", $bookingID);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // กรณีไม่มีเงินคืน → อัปเดต booking เป็นยกเลิก (C02)
        $stmt = $connection->prepare("UPDATE booking SET bookstate_ID = 'C02' WHERE booking_ID = ?");
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();
    }

    // กลับไปหน้าประวัติ หรือแสดงข้อความสำเร็จ
    header("Location: member_booking.php");
    exit();
}

// ----- แสดงฟอร์มยกเลิก (กรณี GET) -----
if (!isset($_POST['booking_id'])) {
    echo "ไม่พบข้อมูลการจอง";
    exit();
}

$bookingID = $_POST['booking_id'];
$memberID = $_SESSION['member_ID'];

// ดึงข้อมูลการจอง
$sql = "
SELECT b.*, 
       m.fullname, m.email, m.phone,
       t.typ_name, t.short_detail, t.price,
       DATEDIFF(b.check_out, b.check_in) AS total_days,
       (SELECT url FROM room_image WHERE room_image.typroom_ID = t.typroom_ID LIMIT 1) AS image_url,
       p.payment_amount,
       p.payment_date
FROM booking b
LEFT JOIN member m ON b.member_ID = m.member_ID
LEFT JOIN room r ON b.room_ID = r.room_ID
LEFT JOIN typ_room t ON r.typroom_ID = t.typroom_ID
LEFT JOIN payment p ON b.booking_ID = p.booking_ID
WHERE b.booking_ID = ? AND b.member_ID = ?
LIMIT 1
";

$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $bookingID, $memberID);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo "ไม่พบข้อมูลการจอง";
    exit();
}

// คำนวณราคาทั้งหมด และเงื่อนไขการคืนเงิน
$totalPrice = $booking['price'] * $booking['total_days'];
$today = new DateTime();
$checkIn = new DateTime($booking['check_in']);
$diffDays = $today->diff($checkIn)->days;

$deposit = $booking['payment_amount'];
if ($checkIn < $today) {
    $refundAmount = 0;
    $refundNote = "เลยวันเข้าพักแล้ว ไม่สามารถคืนเงินได้";
} else if ($diffDays >= 15) {
    $refundAmount = $deposit;
    $refundNote = "แจ้งยกเลิกการจองก่อนการเข้าพัก ไม่น้อยกว่า 15 วัน คืนเงิน 100%";
} else if ($diffDays >= 7) {
    $refundAmount = $deposit * 0.5;
    $refundNote = "แจ้งยกเลิกการจองก่อนการเข้าพัก ไม่น้อยกว่า 7 วัน คืนเงิน 50% ";
} else {
    $refundAmount = 0;
    $refundNote = "แจ้งยกเลิกการจองก่อนการเข้าพัก น้อยกว่า 7 วัน ไม่สามารถคืนเงินได้";
}
?>



<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ยกเลิกการจอง | Saba Cat Hotel</title>
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
        .card {
            border: 1px solid #000;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            background-color: #FFFCEE;
        }

        .detail-box {
            background-color: #FFF;
            border-radius: 10px;
            padding: 15px;
            margin: 0 auto 30px auto;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-cancel {
            display: block;
            width: 200px;
            margin: auto;
            background: #FFE488;
            font-weight: bold;
            color: #000;
            border-radius: 6px;
            border-color: #000;
        }
    </style>
</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-4">
        <div class="card">
            <a onclick="window.history.back();" style="margin-bottom: 15px; font-size: 28px; cursor: pointer;">
                <i class="fas fa-arrow-left"></i> | ยกเลิกการจอง
            </a>
            <hr style="border: none; height: 1px; background-color: #000; margin: 10px 0;">
           
            <h3 class="mt-3 mb-4" style="text-align: center;">รายละเอียดการยกเลิกการจอง</h3>

            <div class="detail-box">
                <img src="<?php echo $booking['image_url']; ?>" width="150" style="border-radius: 8px;">
                <div>
                    <h5>1x <?php echo $booking['typ_name']; ?></h5>
                    <p>ราคาห้อง (<?php echo number_format($booking['price']); ?> x <?php echo $booking['total_days']; ?> วัน): <?php echo number_format($totalPrice); ?> บาท</p>
                    <p>มัดจำ 50%: <?php echo number_format($deposit); ?> บาท</p>
                    <p>วันที่เข้าพัก: <?php echo date('d-m-Y', strtotime($booking['check_in'])); ?> | วันที่ยกเลิก: <?php echo date('d-m-Y'); ?></p>
                    <p><strong>เงินคืน : <?php echo number_format($refundAmount); ?> บาท</strong> (<?php echo $refundNote; ?>)</p>
                </div>
            </div>

            <!-- แสดงปุ่มหรือฟอร์มขึ้นอยู่กับเงื่อนไข -->
            <?php if ($refundAmount > 0): ?>
                <!-- ฟอร์มกรอกข้อมูลคืนเงิน -->
                <form action="cancel_booking.php" method="POST">
                    <input type="hidden" name="booking_id" value="<?php echo $bookingID; ?>">
                    <input type="hidden" name="refund_amount" value="<?php echo $refundAmount; ?>">
                    <input type="hidden" name="canclestate_ID" value="C01">

                    <div class="detail-box">
                        <label for="refund_method">ช่องทางการคืนเงิน</label>
                        <select name="refund_method" required>
                            <option value="">เลือกธนาคาร</option>
                            <option value="พร้อมเพย์">พร้อมเพย์</option>
                            <option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>
                            <option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
                            <option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>
                            <option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
                        </select>
                    </div>

                    <div class="detail-box">
                        <label for="account_name">ชื่อบัญชีรับเงิน</label>
                        <input type="text" name="account_name" value="<?php echo $booking['fullname']; ?>" required>
                    </div>

                    <div class="detail-box">
                        <label for="account_number">หมายเลขบัญชี / พร้อมเพย์</label>
                        <input type="text" name="account_number" required>
                    </div>

                    <button type="submit" class="btn btn-cancel">ยืนยันการยกเลิก</button>
                </form>
            <?php else: ?>
                <!-- ถ้าไม่มีเงินคืน แสดงปุ่มเฉย ๆ -->
                <form action="cancel_booking.php" method="POST">
                    <input type="hidden" name="booking_id" value="<?php echo $bookingID; ?>">
                    <input type="hidden" name="canclestate_ID" value="C02">
                    <button type="submit" class="btn btn-cancel">ยืนยันการยกเลิก</button>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <?php include("footer.php"); ?>
</body>

</html>