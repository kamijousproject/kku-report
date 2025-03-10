<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        Swal.fire({
            icon: "success",
            title: "ออกจากระบบสำเร็จ!",
            text: "กำลังกลับไปยังหน้าเข้าสู่ระบบ...",
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "login.php";
        });
    </script>
</body>

</html>