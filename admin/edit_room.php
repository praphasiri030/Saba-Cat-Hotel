<?php
session_start();
include('dbconnect.php');

if (isset($_POST['submit-button'])) {
    // Your existing upload and update logic here

    $sql = "UPDATE typ_room SET 
    typ_name = '" . $_POST['typ_name'] . "',
    num_cat = '" . $_POST['num_cat'] . "',
    price = '" . $_POST['price'] . "',
    short_detail = '" . $_POST['short_detail'] . "',
    detail = '" . $_POST['detail'] . "'
    WHERE typroom_ID = '" . $_POST['id'] . "'";

    if ($connection->query($sql)) {
        // header("Location:list_room.php");
        // exit();
    }
    $imagePath = "";

    // ตรวจสอบว่าไฟล์ถูกอัปโหลดหรือไม่
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // กำหนดเส้นทางที่จะเก็บไฟล์ที่อัปโหลด
        $target_dir = "image/";

        // ดึงนามสกุลไฟล์
        $file_extension = pathinfo(basename($_FILES["image"]["name"]), PATHINFO_EXTENSION);

        $target_file = $target_dir . uniqid() . '.' . $file_extension;

        // ตรวจสอบว่าเป็นไฟล์รูปภาพหรือไม่
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
            if ($_FILES["image"]["size"] <= 5000000) {
                // อนุญาตเฉพาะบางประเภทของไฟล์รูปภาพ
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $imagePath = "http://localhost/Cathotel_Project/" . $target_file;

                    $sqlAddAdminImage = "INSERT INTO `room_image`(`typroom_ID`, `url`) 
                    VALUES ('" . $_POST['id'] . "','" . $imagePath . "')";

                    if ($connection->query($sqlAddAdminImage)) {
                        // header("Location: admin_edit.php?id=" . $_POST['id']);
                        // exit();
                    }
                }
            }
        }
    }
    header("Location:list_room.php");
    exit();
    // echo '<script>alert("บันทึกข้อมูลไม่สำเร็จ")</script>';
}


$sql = "SELECT *
  FROM typ_room
  INNER JOIN room_image ON typ_room.typroom_ID = room_image.typroom_ID
  WHERE typ_room.typroom_ID = " . $_GET['id'] . "
  ORDER BY typ_room.typroom_ID ASC";

$adminData = [];
$result = $connection->query($sql);
if ($result && $result->num_rows > 0) {
    $adminData = $result->fetch_assoc();
}

$sqlimage = "SELECT `image_ID`, `typroom_ID`, `url`
FROM `room_image`
WHERE typroom_ID = " . $_GET['id'] . "";

$resultimage = $connection->query($sqlimage);
if ($resultimage && $resultimage->num_rows > 0) {
    $imageList = $resultimage->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลห้องพัก</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- โลโก้-->
    <link rel="icon" type="image/png" href="image_room/logo.jpg">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- ฟอนต์ภาษาไทย -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600&display=swap" rel="stylesheet">

    <!-- AdminLTE & Plugins -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CatHotel_Project/dist/css/adminlte.css">


    <style>
        /* Background หน้า */
        body.layout-fixed.sidebar-expand-lg {
            background-color: #F1EEE0 !important;
        }

        .app-wrapper,
        .app-main {
            background-color: transparent !important;
        }

        /* Header card */
        .card-header {
            background-color: #fff;
            border-bottom: none;
            /* เอาเส้นขั้นออก */
            padding-bottom: 0;
        }

        /* h3 header */
        .card-header h3 {
            font-weight: bold;
            text-align: center;
            width: 100%;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.1);
            /* เงาที่ด้านล่าง */
        }

        .image-list,
        #previewContainer {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            /* 4 คอลัมน์ */
            gap: 16px;
            margin-top: 20px;
            /* เพิ่มระยะห่างจากแท็บด้านบน */
            max-width: 100%;
        }

        .image-item,
        .image-upload-box {
            width: 100%;
            aspect-ratio: 1 / 1;
            /* สัดส่วน 1:1 สำหรับสี่เหลี่ยมจัตุรัส */
            position: relative;
            border: 1px solid #000;
            overflow: hidden;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .delete-button {
            position: absolute;
            top: 4px;
            right: 6px;
            background-color: rgba(255, 0, 0, 0.7);
            color: #fff;
            border: none;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 50%;
            padding: 0 6px;
        }

        .image-upload-box:hover {
            background-color: #eee;
        }

        .image-upload-box input[type="file"] {
            display: none;
        }


        .nav-tabs .nav-item {
            flex: 1;
            text-align: center;
        }

        .nav-tabs .nav-link.active {
            border-bottom: 2px solid #000;
            font-weight: bold;
            background-color: transparent;
            color: #000;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #000;
        }

        .form-box {
            font-weight: bold;
            background-color: #FFF;
            border-radius: 10px;
            border: 1px solid #000;
            padding: 20px;
            margin: 20px auto 20px auto;
            /* เว้นบน-ล่างไม่เยอะเกิน */
            width: 90%;
            min-height: auto;
        }

        .form-input {
            font-weight: lighter;
            border: none;
            padding: 20px;
            width: 100%;
            min-height: auto;
        }

        .btn {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #000;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-save {
            display: block;
            width: 200px;
            margin: auto;
            background: #FFE488;
            font-weight: bold;
            color: #000;
        }

        .btn-save i {
            margin-left: 6px;
        }

        .btn-back {
            display: block;
            width: 200px;
            margin: auto;
            background: #E5E5E5;
            font-weight: bold;
            color: #000;
        }

        .btn-back i {
            margin-right: 10px;
        }
    </style>

</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" style="font-family: 'Noto Sans Thai', sans-serif;">
    <div class="app-wrapper">
        <?php include('left_sidebar.php'); ?>
        <main class="app-main">
            <div class="card-header">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto">
                        <a href="list_room.php" class="btn btn-back"><i class="fas fa-arrow-left"></i>กลับ</a>
                    </div>
                    <div class="col text-center">
                        <h3 class="mb-0 h3-shadow">แก้ไขห้องพัก</h3>
                    </div>
                    <div class="col-auto">
                        <button name="submit-button" type="submit" form="editRoomForm" class="btn btn-save">บันทึก <i class="fas fa-save"></i></button>
                    </div>
                </div>
                <ul class="nav nav-tabs mt-3 d-flex" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#des">รายละเอียด</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#img">รูปภาพ</a>
                    </li>
                </ul>

            </div>


            <div class="app-content">
                <div class="container-fluid">
                    <form action="edit_room.php" method="post" name="demo_form" enctype="multipart/form-data" id="editRoomForm">
                        <input name="id" type="hidden" value="<?php echo $adminData['typroom_ID']; ?>">
                        <div class="card-body tab-content">
                            <div id="des" class="tab-pane active">
                                <div class="form-box">
                                    <label class="form-label">ชื่อประเภท :</label>
                                    <input name="typ_name" type="text" class="form-input" value="<?php echo $adminData['typ_name']; ?>">
                                </div>
                                <div class="form-box">
                                    <label class="form-label">จำนวนแมวสูงสุด :</label>
                                    <input name="num_cat" type="text" class="form-input" value="<?php echo $adminData['num_cat']; ?>">
                                </div>
                                <div class="form-box">
                                    <label>ราคาห้องพัก :</label>
                                    <input name="price" type="text" class="form-input" value="<?php echo $adminData['price']; ?>">
                                </div>
                                <div class="form-box">
                                    <label class="form-label">รายละเอียดย่อ :</label>
                                    <input name="short_detail" type="text" class="form-input" value="<?php echo $adminData['short_detail']; ?>">
                                </div>
                                <div class="form-box">
                                    <label class="form-label">รายละเอียด :</label>
                                    <!-- <input name="detail" type="text" class="form-control" value="<?php echo $adminData['detail']; ?>"> -->
                                    <textarea name="detail" type="text" class="form-input" rows="5"><?php echo $adminData['detail']; ?></textarea>
                                </div>
                            </div>
                            <div id="img" class="tab-pane">
                                <div class="image-list">
                                    <!-- รูปภาพเดิม -->
                                    <?php foreach ($imageList as $index => $img) { ?>
                                        <div class="image-item">
                                            <img src="<?php echo $img['url']; ?>" alt="Image">
                                            <div class="delete-button" onclick="location.href='deleteroom_img.php?id=<?php echo $img['image_ID']; ?>'">×</div>
                                        </div>
                                    <?php } ?>

                                    <!-- กล่องเพิ่มรูปภาพ -->
                                    <label class="image-upload-box">
                                        <span>+ เพิ่มรูป</span>
                                        <input type="file" name="image" accept="image/*" id="imageInput">
                                    </label>
                                </div>

                                <!-- แสดง preview -->
                                <div id="previewContainer" class="image-list mt-2"></div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>


        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="/CatHotel_Project/dist/js/adminlte.js"></script>

    <script>
        document.getElementById("imageInput").addEventListener("change", function(event) {
            const previewContainer = document.getElementById("previewContainer");
            previewContainer.innerHTML = ""; // เคลียร์ของเก่า

            const file = event.target.files[0];
            if (file && file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageItem = document.createElement("div");
                    imageItem.classList.add("image-item");

                    const img = document.createElement("img");
                    img.src = e.target.result;

                    imageItem.appendChild(img);
                    previewContainer.appendChild(imageItem);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>

</html>