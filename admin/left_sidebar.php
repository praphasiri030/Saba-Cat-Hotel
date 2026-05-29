<?php
/* ---------- 1) นับ B02 แยกตาม typroom_ID ---------- */
$pendingCounts = [1 => 0, 2 => 0, 3 => 0];   // 1=Standard, 2=Superior, 3=Deluxe

$sql = "
    SELECT t.typroom_ID, COUNT(*) AS qty
    FROM booking b
    JOIN room r      ON r.room_ID     = b.room_ID
    JOIN typ_room t  ON t.typroom_ID  = r.typroom_ID
    WHERE b.bookstate_ID = 'B02'
    GROUP BY t.typroom_ID
";
$result = mysqli_query($connection, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $pendingCounts[$row['typroom_ID']] = $row['qty'];   // ผูกตาม ID
}

/* ---------- 2) ฟังก์ชัน badge ---------- */
function badge($qty)
{
    return $qty > 0 ? ' <span style="color:red !important;">(' . $qty . ')</span>' : '';
}



/* ---------- 3) ชื่อเมนูจับคู่ ID ---------- */
$roomLabel = [
    1 => 'Standard Room',
    2 => 'Superior Room',
    3 => 'Deluxe Room',
];
$currentPage = basename($_SERVER['PHP_SELF']);

?>

<style>
    .app-sidebar {
        background-color: #FFFCEE !important;
        color: #000 !important;
    }

    .app-sidebar,
    .app-sidebar * {
        color: #000 !important;
    }

    .app-sidebar .nav-link:hover {
        color: #444 !important;
    }

    .app-sidebar .nav-link.active {
        background: #fff !important;
        color: #000 !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
        border-radius: 8px;
        font-weight: bold;
    }

    .sidebar-brand {
        padding: 24px 16px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    .sidebar-brand h3 {
        margin: 0;
        font-size: 20px;
    }

    .sidebar-brand h4 {
        margin-top: 8px;
        font-size: 14px;
        color: #555;
    }

    /* เพิ่ม style badge ให้กลมกลืน (ใช้ Bootstrap 5 อยู่แล้ว) */
    .badge {
        font-size: .75rem;
    }
</style>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark" style="font-family: 'Noto Sans Thai', sans-serif;">
    <!-- Brand -->
    <div class="sidebar-brand">
        <h3 class="brand-text">Saba Cat Hotel</h3>
        <h4><?= htmlspecialchars($_SESSION['username']) ?></h4>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <!-- ▸ รายงาน -->
                <li class="nav-item menu-open">
                    <a href="report_day.php"
                        class="nav-link <?= ($currentPage == 'report_month.php' || $currentPage == 'report_year.php') ? 'active' : '' ?>">
                        <i class="nav-icon bi-graph-up-arrow"></i>
                        <p>รายงาน <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="report_month.php"
                                class="nav-link <?= ($currentPage == 'report_month.php') ? 'active' : '' ?>">
                                <p>รายเดือน</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="report_year.php"
                                class="nav-link <?= ($currentPage == 'report_year.php') ? 'active' : '' ?>">
                                <p>รายปี</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ▸ การจอง -->
                <li class="nav-item menu-open">
                    <a href="#"
                        class="nav-link <?= in_array($currentPage, ['booking_standard.php', 'booking_superior.php', 'booking_deluxe.php']) ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>การจอง <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="booking_standard.php"
                                class="nav-link <?= ($currentPage == 'booking_standard.php') ? 'active' : '' ?>">
                                <p><?= $roomLabel[1] ?><?= badge($pendingCounts[1]) ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="booking_superior.php"
                                class="nav-link <?= ($currentPage == 'booking_superior.php') ? 'active' : '' ?>">
                                <p><?= $roomLabel[2] ?><?= badge($pendingCounts[2]) ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="booking_deluxe.php"
                                class="nav-link <?= ($currentPage == 'booking_deluxe.php') ? 'active' : '' ?>">
                                <p><?= $roomLabel[3] ?><?= badge($pendingCounts[3]) ?></p>
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- ▸ การยกเลิก -->
                <li class="nav-item">
                    <a href="cancel_booking.php"
                        class="nav-link <?= ($currentPage == 'cancel_booking.php') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-calendar2-x"></i>
                        <p>การยกเลิก</p>
                    </a>
                </li>

                <!-- ▸ ห้องพัก -->
                <li class="nav-item">
                    <a href="list_room.php"
                        class="nav-link <?= ($currentPage == 'list_room.php') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-buildings"></i>
                        <p>ห้องพัก</p>
                    </a>
                </li>

                <!-- ▸ เช็คอิน-เช็คเอ้าท์ -->
                <li class="nav-item">
                    <a href="checkin_checkout.php"
                        class="nav-link <?= ($currentPage == 'checkin_checkout.php') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-clipboard2-check"></i>
                        <p>เช็คอิน-เช็คเอ้าท์</p>
                    </a>
                </li>

                <!-- ▸ ออกจากระบบ -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <p>ออกจากระบบ</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>