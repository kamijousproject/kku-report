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
                        <h4>รายงานการสรุป คำขออัตราใหม่และอัตราเดิม (หลังจากส่วนงาน/หน่วยงาน กรอกคำขออัตราเดิมและอัตราใหม่)</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการสรุป คำขออัตราใหม่และอัตราเดิม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>(หลังจากส่วนงาน/หน่วยงาน กรอกคำขออัตราเดิมและอัตราใหม่)</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">ที่</th>
                                            <th rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                            <th rowspan="2">ประเภทคำขอ</th>
                                            <th rowspan="2">ชื่อ - นามสกุล</th>
                                            <th rowspan="2">ประเภทบุคลากร</th>
                                            <th rowspan="2">ประเภทการจ้าง</th>
                                            <th rowspan="2">เลขประจำตำแหน่ง</th>
                                            <th rowspan="2">ชื่อตำแหน่ง</th>
                                            <th rowspan="2">จำนวนที่ขอ</th>
                                            <th rowspan="2">Job Family</th>
                                            <th rowspan="2">ประเภทตำแหน่ง</th>
                                            <th rowspan="2">กลุ่มบุคลากร</th>
                                            <th rowspan="2">ประเภทสัญญา</th>
                                            <th rowspan="2">ระยะเวลาสัญญา</th>
                                            <th rowspan="2">คุณวุฒิของตำแหน่ง</th>
                                            <th rowspan="2">สถานะอัตรา</th>
                                            <th rowspan="2">เงินเดือน</th>
                                            <th rowspan="2">แหล่งงบประมาณ</th>
                                            <th rowspan="2">งบประมาณแผ่นดิน</th>
                                            <th rowspan="2">งบประมาณเงินรายได้</th>
                                            <th rowspan="2">งบประมาณเงินรายได้ สนอ.</th>
                                            <th rowspan="2">สาขาวิชา/สถานที่ปฏิบัติงาน</th>
                                            <th rowspan="2">สถานะการปฏิบัติงาน</th>
                                            <th rowspan="2">วันที่เกษียณ</th>
                                            <th colspan="2">ผลการประเมิน</th>                                           
                                            <th>ประสงค์จ้างต่อเนื่อง</th>
                                            <th rowspan="2">ระยะเวลาการจ้าง</th>
                                        </tr>
                                        <tr>
                                            <th>ผลประเมิน</th>
                                            <th>ร้อยละ</th>
                                            <th>ใช่/ไม่ใช่</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>สัญญาประจำ</td>
                                                <td>สมชาย ใจดี</td>
                                                <td>วิชาการ</td>
                                                <td>ประจำ</td>
                                                <td>12345</td>
                                                <td>อาจารย์</td>
                                                <td>1</td>
                                                <td>Teaching</td>
                                                <td>วิชาการ</td>
                                                <td>บุคลากรสายวิชาการ</td>
                                                <td>ประจำ</td>
                                                <td>5 ปี</td>
                                                <td>ปริญญาเอก</td>
                                                <td>ว่าง</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>สมหญิง สมศรี</td>
                                                <td>สนับสนุน</td>
                                                <td>ชั่วคราว</td>
                                                <td>67890</td>
                                                <td>นักวิจัย</td>
                                                <td>1</td>
                                                <td>Research</td>
                                                <td>สนับสนุน</td>
                                                <td>บุคลากรสายสนับสนุน</td>
                                                <td>ชั่วคราว</td>
                                                <td>1 ปี</td>
                                                <td>ปริญญาโท</td>
                                                <td>เต็ม</td>
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
                    'command': 'kku_wf_new-vs-old-positions'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.wf);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    response.wf.forEach((row, index) => {                   
                        const tr = document.createElement('tr');

                        const columns = [
                            { key: 'No', value: index + 1 },
                            { key: 'Alias_Default', value: row.Alias_Default },
                            { key: 'TYPE', value: row.TYPE },
                            { key: 'Workers_Name_Surname', value: row.Workers_Name_Surname },
                            { key: 'Personnel_Type', value: row.Personnel_Type },
                            { key: 'Employment_Type', value: row.Employment_Type },
                            { key: 'Position_Number', value: row.Position_Number },
                            { key: 'Position', value: row.Position },
                            { key: 'Requested_HC_unit', value: row.Requested_HC_unit },
                            { key: 'Job_Family', value: row.Job_Family },
                            { key: 'All_PositionTypes', value: row.All_PositionTypes },
                            { key: 'Personnel_Group', value: row.Personnel_Group },
                            { key: 'Contract_Type', value: row.Contract_Type },
                            { key: 'Contract_Period_Short_Term', value: row.Contract_Period_Short_Term },
                            { key: 'Position_Qualifications', value: row.Position_Qualifications },
                            { key: 'rate_status', value: row.rate_status },
                            { key: 'salary_rate', value: row.salary_rate },
                            { key: 'fund_ft', value: row.fund_ft },
                            { key: 'govt_fund', value: row.govt_fund },
                            { key: 'division_revenue', value: row.division_revenue },
                            { key: 'oop_central_revenue', value: row.oop_central_revenue },
                            { key: 'Location_Code', value: row.Location_Code },
                            { key: 'Position_Status', value: row.Position_Status },
                            { key: 'Vacant_From_Which_Date', value: row.Vacant_From_Which_Date },
                            { key: 'Performance_Evaluation', value: row.Performance_Evaluation },
                            { key: 'Performance_Evaluation_Percentage', value: row.Performance_Evaluation_Percentage },                            
                            { key: 'Wish_to_Continue_Employement', value: row.Wish_to_Continue_Employement },
                            { key: 'Hiring_Start_End_Date', value: row.Hiring_Start_End_Date },

                        ];

                        columns.forEach(col => {
                            const td = document.createElement('td');
                            td.textContent = col.value;
                            tr.appendChild(td);
                        });
                        tableBody.appendChild(tr);     
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