<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include('dbconnect.php');


// รับค่าจาก query string
$bookingId = $_GET['booking_id'] ?? 0;
if (!$bookingId) {
    die("ไม่พบข้อมูลการจอง");
}

// ดึงข้อมูล booking จาก booking_id
$sql = "SELECT b.*, r.room_name, t.typ_name, t.price FROM booking b
        JOIN room r ON b.room_ID = r.room_ID
        JOIN typ_room t ON r.typroom_ID = t.typroom_ID
        WHERE b.booking_ID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$res = $stmt->get_result();
$booking = $res->fetch_assoc();

// ===== ตรวจว่าหมดเวลา 60 นาทีแล้วหรือยัง =====
$createdAt = new DateTime($booking['book_created_at'], new DateTimeZone('Asia/Bangkok'));
$expireAt  = clone $createdAt;
$expireAt->modify('+60 minutes'); //กำหนดว่าหมดเวลาภายในกี่นาที
$now = new DateTime();


// ถ้าหมดเวลาและยังไม่ชำระ => ลบการจอง
if ($now >= $expireAt && $booking['bookstate_ID'] === 'B01') {
    // 1) ลบ booking
    $del = $connection->prepare("DELETE FROM booking WHERE booking_ID = ?");
    $del->bind_param("i", $bookingId);
    $del->execute();
    $del->close();
    // 2) แจ้งเตือนแล้วกลับหน้าแรก
    echo "<script>
            alert('หมดเวลาชำระเงิน 60 นาที การจองถูกยกเลิก');
            window.location.href='index.php';
          </script>";
    exit;
}

// คำนวณราคาทั้งหมดและมัดจำ 50%
$total = $booking['book_price']; // ยอดรวมค่าห้อง
$deposit = $total * 0.5; // มัดจำ 50%

// URL ของ WSDL ที่จะใช้
// ตั้งค่า URL WSDL สำหรับเรียก API ของ KU เพื่อสร้าง QR
$wsdl = "https://fin.ku.th/qr/xml/qr.wsdl";
$qrImage = ""; // ตัวแปรสำหรับเก็บ QR ที่จะใช้แสดง

try {
    // สร้าง SOAP Client เพื่อเรียกใช้งาน Web Service
    $client = new SoapClient($wsdl);

    // สร้างข้อมูลคำสั่งซื้อ
    $order = [
        'order_date' => date('Y-m-d H:i:s'), // วันที่คำสั่งซื้อ
        'total_price' => number_format($deposit, 2, '.', ''), // มัดจำ (ทศนิยม 2 ตำแหน่ง)
        'order_id' => str_pad($bookingId, 17, '0', STR_PAD_LEFT), // สุ่มเลข 8 หลัก (เลขล้วน)
        'member_id' => $_SESSION['member_id'] ?? '123456' // รหัสสมาชิกจาก session (เลขล้วน)
    ];



    // สร้างพารามิเตอร์ส่งไปยัง API เพื่อสร้าง QR
    $params = array(
        "expireDate" => $expireAt->format('dmy'), // คิวอาร์หมดอายุในอีก 60 นาที
        "appCode" => "99",
        "transactionId" => uniqid(), // รหัสธุรกรรมแบบไม่ซ้ำ
        "amount" => $order['total_price'], // ยอดชำระ
        "ref1Prefix" => str_pad($order['order_id'], 17, '0', STR_PAD_LEFT), // // REF1 = เลขออเดอร์ 17 หลัก (เลขล้วน)
        "ref2Prefix" => str_pad($order['member_id'], 6, '0', STR_PAD_LEFT),  // REF2 = เลขสมาชิก 6 หลัก
        "billerSuffix" => "87", // รหัสบิลเลอร์ของ KU
        "callbackUrl" => "" // ไม่ต้องการรับ callback
    );


    // เรียกใช้ API ฟังก์ชัน getOeaQr
    $response = $client->__soapCall("getOeaQr", array($params));

    // ตรวจสอบว่ามีข้อมูล QR กลับมาหรือไม่
    if (isset($response->qrResult) && isset($response->qrResult->content)) {
        $qrImage = $response->qrResult->content; // ได้ QR แบบ base64 image
    } else {
        echo "<pre>ผิดพลาดในการรับข้อมูล QR: ";
        print_r($response);
        echo "</pre>";
        $qrImage = ""; // ไม่สามารถรับ QR ได้
    }
} catch (SoapFault $e) {
    $qrImage = ""; // หากมีข้อผิดพลาด ไม่แสดง QR
}


//บันทึกข้อมูลการชำระเงิน
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่าแนบไฟล์และ booking_id ถูกส่งมาถูกต้อง
    if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] == 0 && $bookingId) {
        $target_dir = "slip/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES["payment_slip"]["name"], PATHINFO_EXTENSION);
        $file_name = uniqid('slip_') . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        $imageFileType = strtolower($file_extension);
        $check = getimagesize($_FILES["payment_slip"]["tmp_name"]);

        if ($check !== false && $_FILES["payment_slip"]["size"] <= 5000000) {
            if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $target_file)) {
                $imagePath = "http://localhost/UserCathotel_Project/" . $target_file;

                // เตรียมข้อมูลสำหรับบันทึกลงตาราง payment
                $payment_amount = $deposit;
                $payment_date = date('Y-m-d');
                $payment_time = date('Y-m-d H:i:s');
                $ref = uniqid('ref'); // สร้างเลขอ้างอิงเฉพาะ
                $payState_ID = 'B02'; // รอยืนยันการจอง

                $stmt = $connection->prepare("INSERT INTO payment (booking_ID, payState_ID, payment_amount, payment_date, paymentSlip, ref, payment_time)
                                              VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isdssss", $bookingId, $payState_ID, $payment_amount, $payment_date, $imagePath, $ref, $payment_time);



                if ($stmt->execute()) {
                    // อัพเดทสถานะ booking
                    $updateBooking = $connection->prepare("UPDATE booking SET bookstate_ID = 'B02' WHERE booking_ID = ?");
                    $updateBooking->bind_param("i", $bookingId);
                    $updateBooking->execute();
                    $updateBooking->close();


                    echo "<script>alert('อัปโหลดหลักฐานเรียบร้อยแล้ว รอการตรวจสอบ'); window.location.href='booking_completed.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');</script>";
                }
            }
        } else {
            echo "<script>alert('กรุณาเลือกไฟล์รูปภาพไม่เกิน 5MB');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ชำระเงิน - Saba Cat Hotel</title>
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

</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-4" style="max-width: 700px;">
        <h4 class="fw-bold mb-3"> ชำระเงิน</h4>

        <div class="p-4" style="background-color: #FFFCEE; border-radius: 10px; border: 1px solid #ccc;">
            <h5 class="text-center fw-bold mb-3">รายละเอียดการชำระเงิน</h5>
            <p style="color: red;">**การจองห้องพักจะต้องมีการมัดจำ 50% ของยอดรวมค่าห้อง ส่วนยอดคงเหลือ นำมาชำระในวันที่ส่งน้องแมวค่ะ**</p>

            <!-- แสดงยอดค่าห้อง -->
            <p>ยอดรวมค่าห้อง : <?php echo number_format($total); ?> บาท</p>
            <p>มัดจำ 50% : <?php echo number_format($deposit); ?> บาท</p>
            <h5 class="fw-bold text-dark">ยอดที่ต้องชำระ : <?php echo number_format($deposit); ?> บาท</h5>

            <!-- แสดง QR Payment -->
            <div class="text-center my-4">
                <p class="fw-bold">สแกนเพื่อจ่าย</p>
                <img src="<?php echo $qrImage; ?>" style="max-width: 400px;" />
                <div id="countdownBox"></div>
            </div>

            <form action="room_payment.php?booking_id=<?php echo $bookingId; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label class="fw-bold" for="inputGroupFile02">หลักฐานการโอนเงิน</label>
                    <input type="file" class="form-control" id="inputGroupFile02" name="payment_slip" accept="slip/*" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-warning px-5">ยืนยัน</button>
                </div>
            </form>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <script>
        // ======== COUNTDOWN =========
        const expireTs = <?php echo $expireAt->getTimestamp() * 1000; ?>; // ms
        const countdownEl = document.createElement('p');
        countdownEl.className = 'text-danger fw-bold mt-2';
        document.querySelector('#countdownBox').appendChild(countdownEl); // ใส่ใต้ QR

        const tick = () => {
            const now = Date.now();
            let diff = expireTs - now;
            if (diff <= 0) {
                cancelBooking(); // เรียก AJAX ยกเลิก
                return;
            }
            const mins = Math.floor(diff / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            countdownEl.textContent = `กรุณาชำระเงินภายใน ${mins} นาที ${secs} วินาที`;
        };
        tick();
        const timer = setInterval(tick, 1000);

        // ======== AJAX cancel =========
        function cancelBooking() {
            clearInterval(timer);
            fetch('room_cancel.php?booking_id=<?php echo $bookingId; ?>')
                .then(r => r.text())
                .then(() => {
                    alert('หมดเวลาชำระเงิน 60 นาที การจองถูกยกเลิก');
                    window.location.href = 'index.php';
                });
        }
    </script>

</body>

</html>