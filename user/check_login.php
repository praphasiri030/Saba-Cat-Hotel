<?php
session_start();

// check session login
if(!isset($_SESSION['islogin']) || $_SESSION['islogin'] != true){
      // ถ้าไม่มี session login ให้ redirect ให้กลับไปหน้า login
    header("Location: login.php");
    exit();
}
// ถ้ามี session login ให้ใช้งานได้