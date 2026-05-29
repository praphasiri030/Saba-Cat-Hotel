<?php
session_start();
include('dbconnect.php');

$memberID = $_SESSION['member_ID'];

// ดึงข้อมูลการจองทั้งหมดของสมาชิก
$sql = "
    SELECT 
    b.booking_ID, b.book_date, b.check_in, b.check_out, b.num_catbook, b.note, b.book_price, b.bookstate_ID,
    t.typ_name, t.short_detail, t.price,
    m.member_ID, m.fullname, m.email, m.phone, r.room_name,
    (SELECT url FROM room_image WHERE room_image.typroom_ID = t.typroom_ID LIMIT 1) AS image_url
FROM booking b
LEFT JOIN room r ON b.room_ID = r.room_ID
LEFT JOIN typ_room t ON r.typroom_ID = t.typroom_ID
LEFT JOIN member m ON b.member_ID = m.member_ID
WHERE b.member_ID = ?
ORDER BY b.book_date DESC

";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $memberID);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายการจองของฉัน | Saba Cat Hotel</title>
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
        .booking-card {
            border: 1px solid #000;
            border-radius: 5px;
            padding: 16px;
            margin-bottom: 20px;
            background-color: #fff;
        }

        /* ปุ่ม */
        .custom-tab-btn {
            background-color: #000;
            /* สีดำเริ่มต้น */
            color: #fff;
            border-radius: 5px;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .custom-tab-btn.pending.active {
            background-color: #A44F00;
        }

        .custom-tab-btn.checkin.active {
            background-color: #115500;
        }

        .custom-tab-btn.cancel.active {
            background-color: #A40000;
        }

        .custom-tab-btn.past.active {
            background-color: #919191;
        }

        .custom-tab-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-4">
        <h2 class="mb-4 fw-bold">การจองของฉัน</h2>
        <hr style="border: 1px solid #000;">


        <div class="mt-3 mb-4">
            <div class="row g-2">
                <div class="col-3 text-center">
                    <a class="btn custom-tab-btn pending w-100 py-2 tab-btn active" data-bs-target="#pending">รอยืนยัน</a>
                </div>
                <div class="col-3 text-center">
                    <a class="btn custom-tab-btn checkin w-100 py-2 tab-btn" data-bs-target="#checkin">รอเช็คอิน</a>
                </div>
                <div class="col-3 text-center">
                    <a class="btn custom-tab-btn cancel w-100 py-2 tab-btn" data-bs-target="#cancel">การจองที่ยกเลิก</a>
                </div>
                <div class="col-3 text-center">
                    <a class="btn custom-tab-btn past w-100 py-2 tab-btn" data-bs-target="#past">การจองที่ผ่านมา</a>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="bookingTabsContent">

            <!-- Tab: รอยืนยัน -->
            <div class="booking-tab-content" id="pending">
                <?php
                $pending = array_filter($bookings, fn($b) => $b['bookstate_ID'] == 'B02');
                if (count($pending) > 0):
                    foreach ($pending as $booking): ?>
                        <?php include 'memberbooking_card.php'; ?>
                    <?php endforeach;
                else: ?>
                    <div class="alert alert-info">ยังไม่มีการจองรอยืนยัน</div>
                <?php endif; ?>
            </div>

            <!-- Tab: รอเช็คอิน -->
            <div class="booking-tab-content" id="checkin">
                <?php
                $confirmed = array_filter($bookings, fn($b) => in_array($b['bookstate_ID'], ['B03', 'B04']));
                if (count($confirmed) > 0):
                    foreach ($confirmed as $booking): ?>
                        <?php include 'memberbooking_card.php'; ?>
                    <?php endforeach;
                else: ?>
                    <div class="alert alert-info">ยังไม่มีการจองรอเช็คอิน</div>
                <?php endif; ?>
            </div>

            <!-- Tab: ยกเลิก -->
            <div class="booking-tab-content" id="cancel">
                <?php
                $cancelled = array_filter($bookings, fn($b) => in_array($b['bookstate_ID'], ['C01', 'C02']));
                if (count($cancelled) > 0):
                    foreach ($cancelled as $booking): ?>
                        <?php include 'memberbooking_card.php'; ?>
                    <?php endforeach;
                else: ?>
                    <div class="alert alert-info">ยังไม่มีการจองที่ยกเลิก</div>
                <?php endif; ?>
            </div>

            <!-- Tab: ผ่านมาแล้ว -->
            <div class="booking-tab-content" id="past">
                <?php
                $past = array_filter($bookings, fn($b) => $b['bookstate_ID'] == 'B05');
                if (count($past) > 0):
                    foreach ($past as $booking): ?>
                        <?php include 'memberbooking_card.php'; ?>
                    <?php endforeach;
                else: ?>
                    <div class="alert alert-info">ยังไม่มีการจองที่เสร็จสมบูรณ์</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <?php include("footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".tab-btn");
            const sections = {
                "#pending": document.getElementById("pending"),
                "#checkin": document.getElementById("checkin"),
                "#cancel": document.getElementById("cancel"),
                "#past": document.getElementById("past")
            };

            // แสดงเฉพาะอันแรกเริ่มต้น
            Object.values(sections).forEach(section => {
                if (section) section.style.display = "none";
            });
            if (sections["#pending"]) sections["#pending"].style.display = "block";

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