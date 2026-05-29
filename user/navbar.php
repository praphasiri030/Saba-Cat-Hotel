<div class="container-fluid bg-light position-relative shadow">
    <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0 px-lg-5">
        <a href="index.php" class="navbar-brand font-weight-bold text-secondary" style="font-size: 40px;">
            <img src="/UserCatHotel_Project/image_room/logo.jpg" alt="Logo" style="height: 80px;">
        </a>

        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
            <div class="navbar-nav font-weight-bold py-0">
                <a href="index.php" class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') echo 'active'; ?>">หน้าแรก</a>
                <a href="service.php" class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'service.php') echo 'active'; ?>">การบริการ</a>
                <a href="room_list.php" class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'room_list.php') echo 'active'; ?>">จองห้องพัก</a>
                <a href="condition.php" class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'condition.php') echo 'active'; ?>">เงื่อนไข</a>
                <a href="review.php" class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'review.php') echo 'active'; ?>">รีวิว</a>

                <?php
                if (isset($_SESSION['islogin']) && $_SESSION['islogin'] == true) {
                    $fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'สมาชิก';
                ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle <?php if (in_array(basename($_SERVER['PHP_SELF']), ['member.php', 'my_booking.php'])) echo 'active'; ?>" data-toggle="dropdown">
                            <?php echo htmlspecialchars($fullname); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="member.php" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> ข้อมูลของฉัน
                            </a>
                            <a href="member_booking.php" class="dropdown-item">
                                <i class="fas fa-calendar-check mr-2"></i> การจอง
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                            </a>
                        </div>

                    </div>
                <?php
                } else {
                ?>
                    <a href="login.php" class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'login.php') echo 'active'; ?>">เข้าสู่ระบบ</a>
                <?php
                }
                ?>


            </div>
        </div>

    </nav>
</div>