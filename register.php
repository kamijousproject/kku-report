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
            <!-- <input type="text" name="faculty_id" placeholder="Faculty ID"> -->
            <!-- <input type="text" name="faculty_name" placeholder="Faculty Name"> -->
            <select name="faculty_id">
                <option value="">เลือก ส่วนงาน</option>
                <option value="Faculty-00">มหาวิทยาลัยขอนแก่น</option>
                <option value="Faculty-01">01 :สำนักงานอธิการบดี</option>
                <option value="Faculty-02">02 :คณะวิทยาศาสตร์</option>
                <option value="Faculty-03">03 :คณะเกษตรศาสตร์</option>
                <option value="Faculty-04">04 :คณะวิศวกรรมศาสตร์</option>
                <option value="Faculty-05">05 :คณะศึกษาศาสตร์</option>
                <option value="Faculty-06">06 :คณะพยาบาลศาสตร์</option>
                <option value="Faculty-07">07 :คณะแพทยศาสตร์</option>
                <option value="Faculty-08">08 :คณะมนุษยศาสตร์และสังคมศาสตร์</option>
                <option value="Faculty-09">09 :คณะเทคนิคการแพทย์</option>
                <option value="Faculty-10">10 :บัณฑิตวิทยาลัย</option>
                <option value="Faculty-11">11 :คณะสาธารณสุขศาสตร์</option>
                <option value="Faculty-12">12 :สำนักหอสมุด</option>
                <option value="Faculty-13">13 :คณะทันตแพทยศาสตร์</option>
                <option value="Faculty-14">14 :วิทยาลัยบัณฑิตศึกษาการจัดการ</option>
                <option value="Faculty-15">15 :คณะเภสัชศาสตร์</option>
                <option value="Faculty-16">16 :คณะเทคโนโลยี</option>
                <option value="Faculty-17">17 :สำนักเทคโนโลยีดิจิทัล</option>
                <option value="Faculty-18">18 :คณะสัตวแพทยศาสตร์</option>
                <option value="Faculty-19">19 :คณะสถาปัตยกรรมศาสตร์</option>
                <option value="Faculty-20">20 :สำนักบริการวิชาการ</option>
                <option value="Faculty-21">21 :สำนักงานสภามหาวิทยาลัย</option>
                <option value="Faculty-22">22 :คณะบริหารธุรกิจและการบัญชี</option>
                <option value="Faculty-23">23 :สำนักบริหารและพัฒนาวิชาการ</option>
                <option value="Faculty-24">24 :คณะศิลปกรรมศาสตร์</option>
                <option value="Faculty-25">25 :วิทยาลัยการปกครองท้องถิ่น</option>
                <option value="Faculty-26">26 :วิทยาลัยนานาชาติ</option>
                <option value="Faculty-27">27 :คณะเศรษฐศาสตร์</option>
                <option value="Faculty-28">28 :คณะสหวิทยาการ</option>
                <option value="Faculty-29">29 :วิทยาลัยการคอมพิวเตอร์</option>
                <option value="Faculty-30">30 :คณะนิติศาสตร์</option>
            </select>
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