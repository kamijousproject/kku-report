<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "";
    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $file_name;

    // ตรวจสอบว่าไฟล์เป็น CSV
    $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
    if ($file_type != "csv") {
        die("Only CSV files are allowed.");
    }

    // ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // เรียก Python script เพื่ออัปโหลดข้อมูลลง SQL
        $command = "python insertdata.py " . escapeshellarg($target_file);
        $process = proc_open($command, [
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ], $pipes);

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error_output = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            // ตรวจสอบผลลัพธ์จาก Python script
            if (strpos($output, 'successfully imported') !== false) {
                // Redirect พร้อมส่งข้อความสำเร็จไปยัง index.php
                header("Location: ../template-vertical-nav/index.php?status=success");
                exit();
            } else {
                // Redirect พร้อมส่งข้อความข้อผิดพลาดไปยัง index.php
                header("Location: ../template-vertical-nav/index.php?status=error&message=" . urlencode($error_output));
                exit();
            }
        }
    } else {
        // Redirect พร้อมแสดงข้อผิดพลาดการอัปโหลด
        header("Location: ../template-vertical-nav/index.php?status=error&message=File upload failed.");
        exit();
    }
}
