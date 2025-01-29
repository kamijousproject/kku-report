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
                        <h4>รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>คุณวุฒิ</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                                <th>สถานที่ปฏิบัติงาน</th>
                                                <th>อัตราเงินเดือน</th>
                                                <th>แหล่งงบประมาณ</th>
                                                <th>ประเภทสัญญา</th>
                                                <th>ระยะเวลาสัญญา</th>
                                                <th>หมายเหตุอื่นๆ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>ข้าราชการ</td>
                                                <td>วิชาการ</td>
                                                <td>อาจารย์</td>
                                                <td>ปริญญาเอก</td>
                                                <td>12345</td>
                                                <td>กรุงเทพฯ</td>
                                                <td>50,000</td>
                                                <td>งบประมาณแผ่นดิน</td>
                                                <td>ประจำ</td>
                                                <td>ไม่กำหนด</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>พนักงานมหาวิทยาลัย</td>
                                                <td>สนับสนุน</td>
                                                <td>เจ้าหน้าที่วิจัย</td>
                                                <td>ปริญญาโท</td>
                                                <td>67890</td>
                                                <td>เชียงใหม่</td>
                                                <td>35,000</td>
                                                <td>รายได้มหาวิทยาลัย</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>1 ปี</td>
                                                <td>มีการต่อสัญญา</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
        $(document).ready(function() {
            laodData();
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_approval-requests'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.wf);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    
                    response.wf.forEach((row, index) => {
                        const tr = document.createElement('tr');
                        
                        // td1 - running number
                        const td1 = document.createElement('td');
                        td1.textContent = index + 1;
                        tr.appendChild(td1);
                        
                        
                        const td2 = document.createElement('td');
                        td2.textContent = row.Personnel_Type;
                        tr.appendChild(td2);
                        
                        
                        const td3 = document.createElement('td');
                        td3.textContent = row.All_PositionTypes;
                        tr.appendChild(td3);
                        
                        
                        const td4 = document.createElement('td');
                        td4.textContent = row.Position;
                        tr.appendChild(td4);
                        
                        
                        const td5 = document.createElement('td');
                        td5.textContent = row.Position_Qualififcations;
                        tr.appendChild(td5);
                        
                        
                        const td6 = document.createElement('td');
                        td6.textContent = "";
                        tr.appendChild(td6);
                        
                        
                        const td7 = document.createElement('td');
                        td7.textContent = row.Field_of_Study;
                        tr.appendChild(td7);

                        const td8 = document.createElement('td');
                        td8.textContent = row.Salary_Wages_Baht_per_month;
                        tr.appendChild(td8);

                        const td9 = document.createElement('td');
                        td9.textContent = row.Fund_FT;
                        tr.appendChild(td9);

                        const td10 = document.createElement('td');
                        td10.textContent = row.Contract_Type;
                        tr.appendChild(td10);

                        const td11 = document.createElement('td');
                        td11.textContent = row.Current_Age_YR_MT;
                        tr.appendChild(td11);
                        
                        const td12 = document.createElement('td');
                        td12.textContent = "";
                        tr.appendChild(td12);

                        tableBody.appendChild(tr);

                        // เก็บค่า si_name และ so_name ของแถวนี้ไว้ใช้ในการเปรียบเทียบในแถวถัดไป
                        previousSICode = row.si_code;
                        previousSIName = row.si_name;
                        previousSOName = row.so_name;
                        previousSOName = row.so_name;
                    });


                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
        }
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