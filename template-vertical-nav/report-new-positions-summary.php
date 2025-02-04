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
                        <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย (อัตราใหม่) รายตำแหน่ง</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="15">ส่วนงาน/หน่วยงาน</th>
                                            <th colspan="3">ข้อมูลเฉพาะผู้เกษียณอายุราชการ/ชาวต่างประเทศ</th>
                                            <th colspan="2">ข้อมูลสำคัญสำหรับทุกประเภทบุคลากร</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">ลำดับ</th>
                                            <th rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                            <th rowspan="2">ประเภทบุคลากร</th>
                                            <th rowspan="2">ประเภทการจ้าง</th>
                                            <th rowspan="2">ประเภทตำแหน่ง</th>
                                            <th rowspan="2">กลุ่มบุคลากร</th>
                                            <th rowspan="2">ชื่อตำแหน่ง</th>
                                            <th rowspan="2">กลุ่มตำแหน่ง Job Family</th>
                                            <th rowspan="2">คุณวุฒิอัตรา</th>
                                            <th rowspan="2">ประเภทสัญญา</th>
                                            <th rowspan="2">ระยะเวลาสัญญา</th>
                                            <th rowspan="2">จำนวนอัตราที่ขอ</th>
                                            <th rowspan="2">เงินเดือน / ค่าจ้าง</th>
                                            <th rowspan="2">แหล่งงบประมาณ</th>
                                            <th rowspan="2">สาขาวิชา (ตำแหน่งอาจารย์) / สถานที่ปฏิบัติงาน (ตำแหน่งอื่น)</th>
                                            <th rowspan="2">ผู้ครองตำแหน่ง</th>
                                            <th rowspan="2">ตำแหน่งทางวิชาการ</th>
                                            <th rowspan="2">ระยะเวลาการจ้าง</th>
                                            <th rowspan="2">เหตุผลจำเพาะ</th>
                                            <th rowspan="2">แนบรายละเอียด Link file detail</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>วิชาการ</td>
                                                <td>สัญญาประจำ</td>
                                                <td>อาจารย์</td>
                                                <td>บุคลากรสายวิชาการ</td>
                                                <td>อาจารย์</td>
                                                <td>Teaching</td>
                                                <td>ปริญญาเอก</td>
                                                <td>ประจำ</td>
                                                <td>5 ปี</td>
                                                <td>1</td>
                                                <td>50,000</td>
                                                <td>งบประมาณมหาวิทยาลัย</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>สนับสนุน</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>นักวิจัย</td>
                                                <td>บุคลากรสายสนับสนุน</td>
                                                <td>นักวิจัย</td>
                                                <td>Research</td>
                                                <td>ปริญญาโท</td>
                                                <td>ชั่วคราว</td>
                                                <td>1 ปี</td>
                                                <td>1</td>
                                                <td>40,000</td>
                                                <td>งบประมาณแผ่นดิน</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
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
                    'command': 'kku_wf_positions-summary'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.wf);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    response.wf.forEach((row, index) => {                   
                        const tr = document.createElement('tr');

                        const columns = [
                            { key: 'No', value: index+1 },
                            { key: 'Alias_Default', value: row.Alias_Default },
                            { key: 'Personnel_Type', value: row.Personnel_Type },
                            { key: 'Employment_Type', value: row.Employment_Type },      
                            { key: 'All_PositionTypes', value: row.All_PositionTypes },                                                            
                            { key: 'Personnel_Group', value: row.Personnel_Group },                              
                            { key: 'Position', value: row.Position }, 
                            { key: 'Job_Family', value: row.Job_Family }, 
                            { key: 'Position_Qualififcations', value: row.Position_Qualififcations },
                            { key: 'Contract_Type', value: row.Contract_Type },
                            { key: 'period', value: "" },   
                            { key: 'Requested_HC_unit', value: row.Requested_HC_unit },
                            { key: 'Salary_Wages_Baht_per_month', value: row.Salary_Wages_Baht_per_month },
                            { key: 'Fund_FT', value: row.Fund_FT }, 
                            { key: 'Field_of_Study', value: row.Field_of_Study },                             
                            { key: 'Workers_Name_Surname', value: row.Workers_Name_Surname },    
                            { key: 'Academic_Position', value: row.Academic_Position },
                            { key: 'Hiring_Start_End_Date', value: row.Hiring_Start_End_Date },
                            { key: 'Specific_reasons', value: row.Specific_reasons },
                            { key: 'Additional_Information', value: row.Additional_Information },
                                                                                                
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