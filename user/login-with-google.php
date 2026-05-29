<?php
include("dbconnect.php");

require_once 'google-api-php-client/vendor/autoload.php';
$client = new Google_Client(['client_id' => '72999527776-trblkgd521k62aaeqqiissr45mibjpjc.apps.googleusercontent.com']);

$credential = $_POST['credential'];


try {
    $payload = $client->verifyIdToken($credential);
    if ($payload) {
        $personID = $payload['sub'];
        $email = $payload['email'];

        // ตรวจสอบว่าผู้ใช้มีอยู่ในฐานข้อมูลหรือไม่
        $sql = "SELECT * FROM member WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result2 = $stmt->get_result();

        if ($result2->num_rows > 0) {
            // ผู้ใช้มีอยู่แล้ว
            session_start();
            $_SESSION['islogin'] = true;
            $row = $result2->fetch_assoc();
            $_SESSION['member_ID'] = $row['member_ID'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['phone'] = $row['phone'];
            echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'เข้าสู่ระบบเสร็จสมบูรณ์!',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php';
                    }
                });
            }
        </script>";
        } else {
            // ผู้ใช้ยังไม่ลงทะเบียน
            $sql = "INSERT INTO member (email, fullname) VALUES (?, ?)";
            $stmt = $connection->prepare($sql);
            $firstname = $payload['given_name'];
            $lastname = $payload['family_name'];
            $fullname = $firstname . ' ' . $lastname;
            $stmt->bind_param('ss', $email, $fullname);
            if ($stmt->execute()) {
                session_start();
                $result = $stmt->get_result();
                $sql = "SELECT * FROM member WHERE email = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result3 = $stmt->get_result();
                $row = $result3->fetch_assoc();
                $_SESSION['islogin'] = true;
                $_SESSION['member_ID'] = $row['member_ID'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['phone'] = $row['phone'];
                echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'เข้าสู่ระบบเสร็จสมบูรณ์!',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php';
                    }
                });
            }
        </script>";
            } else {
                echo json_encode(['error' => 'Failed to register user']);
            }
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid ID token']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}

?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>