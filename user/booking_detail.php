<?php
session_start();
include('dbconnect.php');

$bookingID = $_GET['id'];
$memberID = $_SESSION['member_ID'];

// ดึงข้อมูลการจอง
$sql = "
SELECT 
    b.*, 
    m.fullname, m.email, m.phone,
    t.typ_name, t.short_detail, t.price,
    s.bookState,
    p.payment_amount, p.paymentSlip,
    c.cancleSlip, r.room_name, 
    (SELECT url 
     FROM room_image 
     WHERE room_image.typroom_ID = t.typroom_ID 
     LIMIT 1) AS image_url,
    DATEDIFF(b.check_out, b.check_in) AS total_days
FROM booking b
LEFT JOIN member m ON b.member_ID = m.member_ID
LEFT JOIN room r ON b.room_ID = r.room_ID
LEFT JOIN typ_room t ON r.typroom_ID = t.typroom_ID
LEFT JOIN booking_state s ON b.bookstate_ID = s.bookstate_ID 
LEFT JOIN payment p ON b.booking_ID = p.booking_ID
LEFT JOIN cancle c ON b.booking_ID = c.booking_ID
WHERE b.booking_ID = ? AND b.member_ID = ?
LIMIT 1;

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
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายละเอียดการจอง | Saba Cat Hotel</title>
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


        .btn-danger {
            background-color: #d9534f;
            border: none;
        }
    </style>
</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-4">

        <div class="card">
            <a onclick="window.history.back();" style="margin-bottom: 15px; font-size: 28px; cursor: pointer;">
                <i class="fas fa-arrow-left"></i> | การจอง
            </a>
            <hr style="border: none; height: 1px; background-color: #000; margin: 10px 0;">

            <div class="detail-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span><strong>สถานะ:</strong>
                        <span><?php echo $booking['bookState']; ?></span>
                    </span>

                    <?php
                    $state = $booking['bookstate_ID'];
                    if ($state === 'B02' || $state === 'B03'): ?>
                        <form action="cancel_booking.php" method="POST">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_ID']; ?>">
                            <button type="submit" class="btn btn-danger">ยกเลิกการจอง</button>
                        </form>
                    <?php elseif ($state === 'B05'): ?>
                        <a href="review_form.php?booking_id=<?php echo $booking['booking_ID']; ?>" class="btn btn-warning">ให้คะแนนรีวิว</a>
                    <?php elseif ($state === 'R01'): ?>
                        <span class="badge bg-success">รีวิวแล้ว</span>
                    <?php endif; ?>


                </div>
            </div>

            <!-- กล่องสรุปห้อง -->
            <div class="detail-box">
                <div class="d-flex gap-3 align-items-start">
                    <div>
                        <h4><?php echo $booking['fullname']; ?></h4>
                        <p><strong>อีเมล:</strong> <?php echo $booking['email']; ?> | <strong>เบอร์โทร:</strong> <?php echo $booking['phone']; ?></p>
                    </div>
                </div>
                <hr>
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <p class="mb-1">เช็คอิน</p>
                        <strong><?php echo date('d F Y', strtotime($booking['check_in'])); ?></strong>
                    </div>
                    <div class="col-6">
                        <p class="mb-1">เช็คเอาต์</p>
                        <strong><?php echo date('d F Y', strtotime($booking['check_out'])); ?></strong>
                    </div>
                </div>
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <p class="mb-1">จำนวนวันทั้งหมด</p>
                        <strong><?php echo $booking['total_days']; ?> วัน</strong>
                    </div>
                    <div class="col-6">
                        <p class="mb-1">จำนวนแมว</p>
                        <strong><?php echo $booking['num_catbook']; ?> ตัว</strong>
                    </div>
                </div>

            </div>
            <div class="detail-box">
                <h5><?php echo $booking['typ_name']. ' ห้อง'. $booking['room_name']; ?></h5>
                <div class="d-flex gap-3 align-items-start">
                    <img src="<?php echo $booking['image_url']; ?>" style="width: 150px; height: 100px; object-fit: cover; border-radius: 6px;">
                    <div>
                        <p>ราคาห้อง (<?php echo number_format($booking['price']); ?> x <?php echo $booking['total_days']; ?> วัน)</p>
                        <p class="mb-0">มัดจำ 50%: <?php echo number_format($booking['payment_amount']); ?> บาท</p>
                        <p class="mb-0">ชำระวันเช็คอิน <?php echo number_format($booking['payment_amount']); ?> บาท</p>
                        <p><strong>ยอดรวมค่าห้อง <?php echo number_format($booking['book_price']); ?> บาท</strong></p>
                    </div>
                </div>
                <?php
                $state = $booking['bookstate_ID'];
                if ($state === 'B04' || $state === 'B05' || $state === 'R01'):
                ?>
                    <div class="d-flex justify-content-end mt-3">
                        <a class="btn btn-primary"
                            href="receipt_print.php?booking_ID=<?= $booking['booking_ID'] ?>"
                            target="_blank">
                            พิมพ์ใบเสร็จ
                        </a>
                    </div>
                <?php endif; ?>

            </div>


            <div class="detail-box">
                <h5>หลักฐานการชำระเงิน</h5>
                <div style="display: flex; justify-content: center;">
                    <img src="<?php echo $booking['paymentSlip']; ?>" style="max-width: 100%; height: auto;">
                </div>

            </div>

            <?php
            $state = $booking['bookstate_ID'];
            if ($state === 'C01' || $state === 'C02'):
            ?>
                <div class="detail-box">
                    <h5>หลักฐานการคืน</h5>
                    <div style="display: flex; justify-content: center;">
                        <?php if (!empty($booking['cancleSlip'])): ?>
                            <img src="<?php echo $booking['cancleSlip']; ?>" style="max-width: 100%; height: auto;">
                        <?php else: ?>
                            <p>ไม่มีการคืนเงิน</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>


        </div>
    </div>

    <?php include("footer.php"); ?>
</body>

</html>