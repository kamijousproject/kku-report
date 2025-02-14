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
                        <h4>รายงานโครงการ/กิจกรรม</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานโครงการ/กิจกรรม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>ตารางข้อมูลแผนงานและโครงการ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                                <th rowspan="2">แผนงาน (ผลผลิต)</th>
                                                <th rowspan="2">แผนงานย่อย (ผลผลิตย่อย/กิจกรรม)</th>
                                                <th rowspan="2">โครงการ/กิจกรรม</th>
                                                <th rowspan="2">แหล่งงบประมาณ</th>
                                                <th rowspan="2">งบประมาณ</th>
                                                <th colspan="3">KPI For Project</th>
                                                <th rowspan="2">วัตถุประสงค์</th>
                                                <th rowspan="2">ผลผลิต</th>
                                                <th rowspan="2">ผลลัพธ์</th>
                                                <th rowspan="2">ผลกระทบ</th>
                                                <th colspan="4">ความเชื่อมโยงกับระบบแผนงาน</th>
                                            </tr>
                                            <tr>
                                                <th>KPI Name For Project</th>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>KPI Unit For Project</th>
                                                <th>ประเด็นยุทธศาสตร์ / พันธกิจ Mission </th>
                                                <th>OKRs</th>
                                                <th>Good Governance</th>
                                                <th>SDGs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
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
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_project-activities'
                },
                dataType: "json",
                success: function(response) {
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า


                        response.bgp.forEach((row, index) => {                   
                            const tr = document.createElement('tr');

                            const columns = [
                                { key: 'Alias_Default', value: row.Alias_Default },
                                { key: 'Plan_Name', value: row.plan_name },
                                { key: 'Sub_Plan_Name', value: row.sub_plan_name },
                                { key: 'Project_Name', value: row.project_name },
                                { key: 'Fund', value: row.Fund },
                                { key: 'Total_Amount_Quantity', value: row.Total_Amount_Quantity },
                                { key: 'Proj_KPI_Name', value: row.Proj_KPI_Name },
                                { key: 'Proj_KPI_Target', value: row.Proj_KPI_Target },
                                { key: 'UoM_for_Proj_KPI', value: row.UoM_for_Proj_KPI },
                                { key: 'Objective', value: row.Objective },
                                { key: 'Project_Output', value: row.Project_Output },
                                { key: 'Project_Outcome', value: row.Project_Outcome },
                                { key: 'Project_Impact', value: row.Project_Impact },
                                { key: 'Pillar_Name', value: row.pillar_name },
                                { key: 'OKR_Name', value: row.okr_name },
                                { key: 'Principles_of_Good_Governance', value: row.Principles_of_good_governance },
                                { key: 'SDGs', value: row.SDGs }
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