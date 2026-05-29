<?php
session_start();
include("dbconnect.php");

$memberID  = $_SESSION['member_ID'];
$bookingID = $_GET['booking_id'] ?? null;

$success = false;
$error   = "";

/* ---------- 1) บันทึกรีวิว ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID     = $_POST['booking_ID'];
    $reviewtyp_ID  = $_POST['reviewtyp_ID'] ?? 1;      // ใช้ค่า default
    $score         = $_POST['reviewScore']   ?? null;
    $review        = trim($_POST['review']   ?? '');
    $review_date   = date("Y-m-d");

    if ($bookingID && $score && $review) {
        $connection->begin_transaction();

        $sql  = "INSERT INTO review (booking_ID, reviewtyp_ID, reviewScore, review, review_date)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iiiss", $bookingID, $reviewtyp_ID, $score, $review, $review_date);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // อัปเดตสถานะการจองเป็น R01
            $updateSql  = "UPDATE booking SET bookstate_ID = 'R01' WHERE booking_ID = ? AND member_ID = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("ii", $bookingID, $memberID);
            $updateStmt->execute();

            if ($updateStmt->affected_rows > 0) {
                $connection->commit();
                $success = true;
            } else {
                $connection->rollback();
                $error = "ไม่สามารถอัปเดตสถานะการจองได้";
            }
        } else {
            $connection->rollback();
            $error = "เกิดข้อผิดพลาดในการบันทึกรีวิว";
        }
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}

/* ---------- 2) ตรวจสอบสิทธิ์และสถานะการจอง ---------- */
$sql = "SELECT b.booking_ID, t.typ_name, b.bookstate_ID
        FROM booking b
        JOIN room r ON b.room_ID = r.room_ID
        JOIN typ_room t ON r.typroom_ID = t.typroom_ID
        WHERE b.booking_ID = ? AND b.member_ID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $bookingID, $memberID);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    echo "ไม่สามารถแสดงฟอร์มรีวิว";
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>ให้คะแนนรีวิว</title>
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
        /* ----- Card & Layout ----- */
        .review-card {
            background: #FFFCEE;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
            max-width: 480px;
            margin: 40px auto;
        }

        .review-form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }

        /* ----- Star Rating ----- */
        .star-rating {
            direction: rtl;
            display: flex;
            justify-content: center;
            /* จัดดาวให้อยู่กึ่งกลางแนวนอน */
            font-size: 2rem;
            gap: 5px;
            /* เพิ่มระยะห่างระหว่างดาว */
        }


        .star-rating input {
            display: none;
        }

        .star-rating label {
            color: #ccc;
            cursor: pointer;
            transition: color .2s;
            padding: 0 4px;
        }

        .star-rating input:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #f5c518;
        }

        /* ----- Textarea ----- */
        .custom-textarea {
            background: #eee;
            border: none;
            border-radius: 8px;
            resize: none;
            padding: 12px;
        }

        /* ----- Submit Button ----- */
        .btn-submit {
            display: inline-block;
            padding: 10px 40px;
            font-weight: bold;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="review-card">
        <!-- ปุ่มย้อนกลับ + ชื่อห้อง -->
        <a onclick="window.history.back();" style="cursor:pointer; font-size:15px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <span class="ms-2 fw-bold">| รีวิวการเข้าพักห้อง: <?php echo htmlspecialchars($booking['typ_name']); ?></span>
        <hr>

        <?php if ($success): ?>
            <div class="alert alert-success text-center">ขอบคุณสำหรับการรีวิว!</div>
            <script>
                setTimeout(() => location.href = "member_booking.php", 2000);
            </script>

        <?php elseif ($booking['bookstate_ID'] === 'R01'): ?>
            <div class="alert alert-info text-center">คุณได้ทำการรีวิวการจองนี้แล้ว</div>
            <div class="text-center">
                <a href="member_booking.php" class="btn btn-primary mt-2">กลับหน้าการจอง</a>
            </div>

        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- ---------- ฟอร์มรีวิว ---------- -->
            <form method="POST">
                <input type="hidden" name="booking_ID" value="<?php echo $booking['booking_ID']; ?>">
                <input type="hidden" name="reviewtyp_ID" value="1">

                <div class="card review-form">
                    <!-- ให้คะแนน -->
                    <h5 class="text-center fw-bold mb-2">ให้คะแนน</h5>
                    <div class="star-rating mb-4 text-center">
                        <input id="star5" type="radio" name="reviewScore" value="5"><label for="star5">★</label>
                        <input id="star4" type="radio" name="reviewScore" value="4"><label for="star4">★</label>
                        <input id="star3" type="radio" name="reviewScore" value="3"><label for="star3">★</label>
                        <input id="star2" type="radio" name="reviewScore" value="2"><label for="star2">★</label>
                        <input id="star1" type="radio" name="reviewScore" value="1"><label for="star1">★</label>
                    </div>

                    <!-- เขียนรีวิว -->
                    <h6 class="text-center fw-bold mb-2">เขียนรีวิว</h6>
                    <textarea name="review" rows="5" maxlength="100" placeholder="พิมพ์ข้อความรีวิวที่นี่..."
                        class="form-control custom-textarea mb-3" required></textarea>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-warning btn-submit">ยืนยัน</button>
                </div>
            </form>


        <?php endif; ?>
    </div>

    <?php include("footer.php"); ?>
</body>

</html>