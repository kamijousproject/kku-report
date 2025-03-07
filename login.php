<?php
session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบรายงานงบประมาณ | KKU Report</title>
    <link rel="icon" type="image/png" sizes="16x16" href="images/icon 1.png">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h2>เข้าสู่ระบบ รายงาน EPM</h2>
        <form id="loginForm">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
        <!-- <a href="register.php">Register</a> -->
    </div>

    <script>
        $(document).ready(function() {
            $("#loginForm").submit(function(e) {
                e.preventDefault(); // ป้องกันการโหลดหน้าใหม่

                $.ajax({
                    type: "POST",
                    url: "login_process.php",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.status == "success") {
                            Swal.fire({
                                icon: "success",
                                title: "เข้าสู่ระบบสำเร็จ!",
                                text: "กำลังเปลี่ยนหน้า...",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "index.php";
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "ผิดพลาด!",
                                text: response.message
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>