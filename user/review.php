<?php
session_start();
include('dbconnect.php');

// ดึงข้อมูลรีวิวทั้งหมด พร้อมข้อมูลห้อง และสมาชิก
$sql = "
    SELECT r.reviewScore, r.review, r.review_date,
           m.fullname,
           b.num_catbook, b.check_in, b.check_out,
           t.typ_name
    FROM review r
    JOIN booking b ON r.booking_ID = b.booking_ID
    JOIN member m ON b.member_ID = m.member_ID
    JOIN room ro ON b.room_ID = ro.room_ID
    JOIN typ_room t ON ro.typroom_ID = t.typroom_ID
    ORDER BY r.review_date DESC
";
$result = $connection->query($sql);
$reviews = $result->fetch_all(MYSQLI_ASSOC);

// ดึงค่าเฉลี่ยรีวิว
$avgSql = "SELECT AVG(reviewScore) AS avg_score FROM review";
$avgResult = $connection->query($avgSql);
$avgScore = $avgResult->fetch_assoc()['avg_score'];

function renderStars($score)
{
    $fullStars = floor($score);
    $halfStar = ($score - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    return str_repeat('<i class="fas fa-star star-lg"></i>', $fullStars) .
        ($halfStar ? '<i class="fas fa-star-half-alt star-lg"></i>' : '') .
        str_repeat('<i class="far fa-star star-lg"></i>', $emptyStars);
}



?>



<!DOCTYPE html>
<html lang="th">


<head>
    <meta charset="UTF-8">
    <title>รีวิวจากลูกค้า | Saba Cat Hotel</title>
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
        .review-card {
            background: #ffffff;
            padding: 20px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .review-box {
            background: #fff8e7;
            padding: 20px;
            border-radius: 12px;
            height: 100%;
        }

        .star-lg {
            font-size: 2.5rem;
            /* ขนาดใหญ่ */
            color: gold;
        }

        .stars i {
            font-size: 1rem;
            /* ขนาดปกติสำหรับรีวิวแต่ละรายการ */
            color: gold;
        }
    </style>
</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-5 text-center">
        <h1 class="fw-bold"><?= number_format($avgScore, 1) ?></h1>
        <div class="text-center"><?= renderStars($avgScore) ?></div>
        <h3 class="fw-bold mt-3">รีวิวจากผู้ที่เคยใช้บริการ</h3>
    </div>

    <div class="container pb-5">
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="row">
                        <!-- ฝั่งซ้าย -->
                        <div class="col-md-3 text-start text-md-start mb-3 mb-md-0">
                            <div class="fs-4 fw-bold">
                                <?= number_format($review['reviewScore'], 1) ?><i class="fas fa-star" style="color: #FFD43B;"></i>
                            </div>
                            <div class="text-muted small">แมว <?= $review['num_catbook'] ?> ตัว</div>
                            <div class="text-muted small">ห้อง <?= htmlspecialchars($review['typ_name']) ?></div>
                            <div class="text-muted small">
                                เข้าพัก <?= date('j', strtotime($review['check_in'])) ?> คืน
                                เมื่อเดือน <?= date('F Y', strtotime($review['check_in'])) ?>
                            </div>
                        </div>

                        <!-- ฝั่งขวา -->
                        <div class="col-md-9">
                            <div class="review-box">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2">
                                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                    </div>
                                    <div class="fw-bold"><?= htmlspecialchars($review['fullname']) ?></div>
                                </div>
                                <div class="stars mb-2 text-warning">
                                    <?php
                                    $stars = intval($review['reviewScore']);
                                    echo str_repeat('<i class="fas fa-star"></i>', $stars);
                                    echo str_repeat('<i class="far fa-star"></i>', 5 - $stars);
                                    ?>
                                </div>
                                <div class="text-body">
                                    <?= nl2br(htmlspecialchars($review['review'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="alert alert-info">ยังไม่มีรีวิว</div>
        <?php endif; ?>
    </div>

    <?php include("footer.php"); ?>
</body>

</html>