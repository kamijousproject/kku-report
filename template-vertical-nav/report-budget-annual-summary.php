<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>

<body class="v-light vertical-nav fix-header fix-sidebar">
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <div id="main-wrapper">
        <?php include('../component/left-nev.php') ?>
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                include('../server/connectdb.php');

                                // Query ข้อมูลจาก table
                                $query = "SELECT 
                                    Account,
                                    Oct, Nov, `Dec`, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep,
                                    Point_of_View,
                                    Data_Load_Cube_Name
                                FROM epm_data";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    echo '<div class="card-title">
                                <h4>รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</h4>
                            </div>
                            <div class="table-responsive">
                                <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">รายการ</th>
                                            <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                            <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                            <th colspan="2">เพิ่ม/ลด</th>
                                        </tr>
                                        <tr>
                                            <th>เงินอุดหนุนจากรัฐ</th>
                                            <th>เงินนอกงบประมาณ</th>
                                            <th>เงินรายได้</th>
                                            <th>รวม</th>
                                            <th>เงินอุดหนุนจากรัฐ (คำขอ)</th>
                                            <th>เงินอุดหนุนจากรัฐ (จัดสรร)</th>
                                            <th>เงินนอกงบประมาณ (คำขอ)</th>
                                            <th>เงินนอกงบประมาณ (จัดสรร)</th>
                                            <th>เงินรายได้ (คำขอ)</th>
                                            <th>เงินรายได้ (จัดสรร)</th>
                                            <th>รวม (คำขอ)</th>
                                            <th>รวม (จัดสรร)</th>
                                            <th>จำนวน</th>
                                            <th>ร้อยละ</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                                // วนลูปข้อมูลและคำนวณ
                                if (!empty($results)) {
                                    foreach ($results as $row) {
                                        // ตัวอย่างการดึงข้อมูลปีปัจจุบัน (ปี 2567)
                                        $current_state_fund = rand(4000, 6000);
                                        $current_external_fund = rand(1500, 2500);
                                        $current_income = rand(2500, 3500);
                                        $current_total = $current_state_fund + $current_external_fund + $current_income;

                                        // ตัวอย่างคำขอปี 2568
                                        $request_state_fund = rand(5000, 7000);
                                        $allocation_state_fund = rand(4800, 6800);
                                        $request_external_fund = rand(2000, 3000);
                                        $allocation_external_fund = rand(1800, 2800);
                                        $request_income = rand(3000, 4000);
                                        $allocation_income = rand(2900, 3900);

                                        $request_total = $request_state_fund + $request_external_fund + $request_income;
                                        $allocation_total = $allocation_state_fund + $allocation_external_fund + $allocation_income;

                                        // การคำนวณเพิ่ม/ลด
                                        $diff_amount = $allocation_total - $current_total;
                                        $diff_percent = ($current_total > 0) ? round(($diff_amount / $current_total) * 100, 2) : 0;

                                        // แสดงผลในตาราง
                                        echo '<tr>
                                                        <td>' . htmlspecialchars($row['Account']) . '</td>
                                                        <td>' . number_format($current_state_fund) . '</td>
                                                        <td>' . number_format($current_external_fund) . '</td>
                                                        <td>' . number_format($current_income) . '</td>
                                                        <td>' . number_format($current_total) . '</td>
                                                        <td>' . number_format($request_state_fund) . '</td>
                                                        <td>' . number_format($allocation_state_fund) . '</td>
                                                        <td>' . number_format($request_external_fund) . '</td>
                                                        <td>' . number_format($allocation_external_fund) . '</td>
                                                        <td>' . number_format($request_income) . '</td>
                                                        <td>' . number_format($allocation_income) . '</td>
                                                        <td>' . number_format($request_total) . '</td>
                                                        <td>' . number_format($allocation_total) . '</td>
                                                        <td>' . number_format($diff_amount) . '</td>
                                                        <td>' . $diff_percent . '%</td>
                                                    </tr>';
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="15">No data available.</td></tr>';
                                                    }

                                                    echo '</tbody>
                                            </table>
                                        </div>';

                                // ปิดการเชื่อมต่อฐานข้อมูล
                                $conn = null;
                                ?>

                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLS</button>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy; <a href="#">KKU</a> 2025</p>
            </div>
        </div>
    </div>
    <script>
        function exportCSV() {
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells.join(","));
            }
            const csvContent = "\uFEFF" + rows.join("\n"); // Add BOM
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape');

            // เพิ่มฟอนต์ภาษาไทย
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal); // ใช้ตัวแปรที่ได้จากไฟล์
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");

            // ตั้งค่าฟอนต์และข้อความ
            doc.setFontSize(12);
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

            // ใช้ autoTable สำหรับสร้างตาราง
            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                styles: {
                    font: "THSarabun", // ใช้ฟอนต์ที่รองรับภาษาไทย
                    fontSize: 10,
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
                bodyStyles: {
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
                headStyles: {
                    fillColor: [102, 153, 225], // สีพื้นหลังของหัวตาราง
                    textColor: [0, 0, 0], // สีข้อความในหัวตาราง
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
            });

            // บันทึกไฟล์ PDF
            doc.save('รายงาน.pdf');
        }

        function exportXLS() {
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells);
            }
            let xlsContent = "<table>";
            rows.forEach(row => {
                xlsContent += "<tr>" + row.map(cell => `<td>${cell}</td>`).join('') + "</tr>";
            });
            xlsContent += "</table>";

            const blob = new Blob([xlsContent], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.xls');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>