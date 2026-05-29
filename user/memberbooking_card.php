<div class="booking-card border p-3 rounded" style="border: 1px solid #000;">
    <!-- บรรทัดบน: รหัสการจองซ้าย วันที่จองขวา -->

    <small class="d-flex justify-content-end mb-2"><?= date('d/m/Y', strtotime($booking['book_date'])); ?></small>


    <div class="d-flex gap-3">
        <!-- รูปภาพ -->
        <img src="<?= $booking['image_url']; ?>"
            style="width: 120px; height: 90px; object-fit: cover; border-radius: 6px;">

        <!-- ข้อมูลรายละเอียด -->
        <div class="flex-grow-1">
            <p class="mb-1"><strong><?= htmlspecialchars($booking['fullname']); ?></strong></p>
            <p class="mb-1"><?= htmlspecialchars($booking['email']); ?></p>
            <p class="mb-1"><?= htmlspecialchars($booking['phone']); ?></p>
            <p class="mb-1"><?= htmlspecialchars($booking['typ_name'] . ' ห้อง' . $booking['room_name']); ?></p>

            <!-- บรรทัดล่างสุด: วันที่เช็คอิน-เช็คเอาท์ และปุ่ม -->
            <div class="d-flex justify-content-between align-items-center mt-2">
                <p class="mb-0"><?= date('d/m/Y', strtotime($booking['check_in'])); ?> - <?= date('d/m/Y', strtotime($booking['check_out'])); ?></p>

                <?php
                $stateColors = [
                    'B02' => 'pending', // รอยืนยัน - สีส้ม
                    'B03' => 'checkin', // รอเช็คอิน - สีเขียว
                    'C01' => 'cancel',  // ยกเลิกที่ขอคืนเงิน - สีแดง
                    'C02' => 'cancel',  // ยกเลิกทั่วไป - สีแดง
                    'B05' => 'past'     // การจองที่ผ่านมา - สีเทา
                ];
                $btnColor = $stateColors[$booking['bookstate_ID']] ?? 'default';
                if ($booking['bookstate_ID'] === 'B05') {
                    // ปุ่มรีวิว
                    echo '<a href="booking_detail.php?id=' . $booking['booking_ID'] . '" 
              class="btn btn-sm custom-tab-btn ' . $btnColor . ' active">
              ดูรายละเอียด/เขียนรีวิว
          </a>';
                } else {
                    // ปุ่มดูรายละเอียด
                    echo '<a href="booking_detail.php?id=' . $booking['booking_ID'] . '" 
              class="btn btn-sm custom-tab-btn ' . $btnColor . ' active">
              ดูรายละเอียด
          </a>';
                }
                ?>
                <!-- <a href="booking_detail.php?id=<?= $booking['booking_ID']; ?>"
                    class="btn btn-sm custom-tab-btn <?= $btnColor; ?> active">
                    ดูรายละเอียด
                </a> -->
            </div>
        </div>
    </div>
</div>