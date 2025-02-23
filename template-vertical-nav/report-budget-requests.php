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
                        <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</h4>
                                </div>
                                <div class="row">

                                <!-- ปีบริหารงบประมาณ -->
                                <div class="col-md-3">
                                    <label for="budgetYearSelect">ปีบริหารงบประมาณ:</label>
                                    <select id="budgetYearSelect" class="form-control">
                                            <option value="2568">2568</option>
                                            <option value="2567">2567</option>
                                    </select>
                                </div>
                                <!-- ปุ่มค้นหา -->
                                <div class="col-md-2 d-flex align-items-end">
                                        <button onclick="loadData()" class="btn btn-info w-100">ค้นหา</button>
                                </div>
                        </div>
                        <br>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>งบประมาณรายจ่าย</th>
                                                <th value="fn06">เงินอุดหนุนจากรัฐ</th>
                                                <th value="fn08">เงินนอกงบประมาณ</th>
                                                <th value="fn02">เงินรายได้</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>

                                    <!-- <div class="section">
                                        <p>หน่วยงาน: ........................................</p>
                                        <p>แผนงาน(ผลผลิต): ........................................</p>
                                        <p>แผนงานย่อย(ผลผลิตย่อย/กิจกรรม): ........................................</p>
                                        <p>โครงการ/กิจกรรม: ........................................</p>
                                        <p>งบรายจ่าย: [Expenses Code / Name]</p>
                                        <p>รายการรายจ่าย: [Item Name]</p>
                                    </div> -->
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLS</button>

                                </div>
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
            loadData();
        });

        function loadData() {
            const yearselect = document.getElementById("budgetYearSelect").value;

        $.ajax({
        type: "POST",
        url: "../server/api.php",
        // url: "mockup-api",
        data: {
            'command': 'report-budget-requests',
            'yearselect': yearselect,
        },
        dataType: "json",
        success: function(response) {
            console.log("API Response:", response.budget); // ตรวจสอบข้อมูลที่ได้รับ
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

            if (!response.budget || response.budget.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                return;
            }

            // สร้าง Map เพื่อจัดกลุ่มข้อมูล
            const groupedData = new Map();

            response.budget.forEach(row => {
                const key = `${row.Alias_Default_Parent}|${row.Alias_Default}|${row.plan_name}|${row.sub_plan_name}|${row.project_name}`;

                if (!groupedData.has(key)) {
                    groupedData.set(key, {
                        Alias_Default_Parent: row.Alias_Default_Parent,
                        Alias_Default: row.Alias_Default,
                        plan_name: row.plan_name,
                        sub_plan_name: row.sub_plan_name,
                        project_name: row.project_name,
                        nametype: row.type,
                        alias_default: row.alias_default,
                        KKU_Item_Name: row.KKU_Item_Name,
                        FN06: 0,
                        FN08: 0,
                        FN02: 0
                    });
                }

                // เพิ่มค่า Total_Amount_Quantity ลงในคอลัมน์ที่เหมาะสม
                if (row.Fund === "FN06") {
                    groupedData.get(key).FN06 = row.Total_Amount_Quantity;
                } else if (row.Fund === "FN08") {
                    groupedData.get(key).FN08 = row.Total_Amount_Quantity;
                } else if (row.Fund === "FN02") {
                    groupedData.get(key).FN02 = row.Total_Amount_Quantity;
                }
            });


            // แสดงข้อมูลในตาราง
            groupedData.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td style="text-align: left;">${row.Alias_Default_Parent || '-'}</br>
                    ${row.Alias_Default || '-'}</br>
                    ${row.plan_name || '-'}</br>
                    ${row.sub_plan_name || '-'}</br>
                    ${row.project_name || '-'}</br>
                    ${row.nametype || '-'}</br>
                    ${row.alias_default || '-'}</br>
                    ${row.KKU_Item_Name || '-'}</td>
                    <td>${row.FN06 > 0 ? row.FN06.toLocaleString() : '-'}</td>
                    <td>${row.FN08 > 0 ? row.FN08.toLocaleString() : '-'}</td>
                    <td>${row.FN02 > 0 ? row.FN02.toLocaleString() : '-'}</td>
                `;
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