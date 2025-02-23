<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #87AFC7;
        /* สีฟ้าอ่อน */
    }
</style>

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
                        <h4>รายงานแสดงการเปรียบเทียบการประมาณการรายได้กับรายได้จริง</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานแสดงการเปรียบเทียบการประมาณการรายได้กับรายได้จริง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>ปีงบที่ต้องการเปรียบเทียบ 2567-2568</h4>
                                    <h5>ส่วนงาน/หน่วยงาน: ...............................</h5>
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
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2">รวมรายรับจริง</th>
                                            </tr>
                                            <tr>
                                                <th value="q1">ไตรมาสที่ 1</th>
                                                <th value="q2">ไตรมาสที่ 2</th>
                                                <th value="q3">ไตรมาสที่ 3</th>
                                                <th value="q4">ไตรมาสที่ 4</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                    </table>
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
                    'command': 'report-revenue-estimation-comparison',
                    'yearselect': yearselect,
                },
                dataType: "json",
                success: function(response) {
                    console.log("API Response:", response.revenue); // ตรวจสอบข้อมูลที่ได้รับ

                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    // ใช้ Map เพื่อจัดกลุ่มข้อมูล
                    const groupedData = new Map();

                    response.revenue.forEach(item => {
                        const key = `${item.plan_name}-${item.sub_plan_name}-${item.project_name}-${item.sub_type}`;

                        if (!groupedData.has(key)) {
                            groupedData.set(key, {
                                plan_name: item.plan_name,
                                sub_plan_name: item.sub_plan_name,
                                project_name: item.project_name,
                                sub_type: item.sub_type,
                                kku_items: new Set(), // ใช้ Set() ป้องกันค่าซ้ำ
                                q1: 0,
                                q2: 0,
                                q3: 0,
                                q4: 0
                            });
                        }

                        let data = groupedData.get(key);
                        data.kku_items.add(item.KKU_Item_Name); // เพิ่ม KKU_Item_Name ลงใน Set
                        data.q1 += parseFloat(item.Q1_Spending_Plan) || 0;
                        data.q2 += parseFloat(item.Q2_Spending_Plan) || 0;
                        data.q3 += parseFloat(item.Q3_Spending_Plan) || 0;
                        data.q4 += parseFloat(item.Q4_Spending_Plan) || 0;
                    });

                    // วนลูปเพื่อสร้างแถวของตาราง
                    groupedData.forEach((data) => {
                        const totalActual = data.q1 + data.q2 + data.q3 + data.q4;

                        // หาก totalActual เป็น 0 จะข้ามการแสดงแถวนี้
                        if (totalActual === 0) return;

                        const kkuItemList = Array.from(data.kku_items).join("</br> "); // แปลง Set เป็น String

                        const row = `<tr>
                                        <td style="text-align: left;">${data.plan_name} </br> ${data.sub_plan_name} </br> ${data.project_name} </br> ${data.sub_type} <br> <strong>${kkuItemList}</strong></td>
                                        <td></td>
                                        <td>${data.q1.toLocaleString()}</td>
                                        <td>${data.q2.toLocaleString()}</td>
                                        <td>${data.q3.toLocaleString()}</td>
                                        <td>${data.q4.toLocaleString()}</td>
                                        <td>${totalActual.toLocaleString()}</td>
                                    </tr>`;

                        tableBody.innerHTML += row;
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