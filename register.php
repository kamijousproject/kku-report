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
        <h2>สร้าง Account EPM Report</h2>
        <form id="registerForm">
            <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="name" placeholder="ชื่อ-สกุล" required>
            <input type="text" name="faculty_id" placeholder="Faculty ID">
            <input type="text" name="faculty_name" placeholder="Faculty Name">
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">สร้าง Account</button>
        </form>
        <a href="login.php">เข้าสู่ระบบ</a>
    </div>

    <script>
        $(document).ready(function() {
            $("#registerForm").submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "register_process.php",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.status == "success") {
                            Swal.fire({
                                icon: "success",
                                title: "สมัครสมาชิกสำเร็จ!",
                                text: "กรุณาเข้าสู่ระบบ",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "login.php";
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