<?php
session_start();
include 'dbconnect.php';

$bookingId = intval($_GET['booking_id'] ?? 0);
if (!$bookingId) exit('no id');

// ลบเฉพาะรายการที่ยังรอจ่าย (B01) และอายุเกิน 60 นาที
$sql = "
    DELETE FROM booking 
    WHERE booking_ID = ? 
      AND bookstate_ID = 'B01'
      AND TIMESTAMPDIFF(MINUTE, book_created_at, NOW()) >= 60
";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
echo 'done';
