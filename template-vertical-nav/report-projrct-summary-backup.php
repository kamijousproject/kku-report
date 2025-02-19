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
                        <h4>รายงานสรุปรายโครงการ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปรายโครงการ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายโครงการ</h4>
                                </div>
                                <div class="row">
                                    <!-- ปีที่จัดสรรงบประมาณ -->
                                    <div class="col-md-3">
                                        <label for="allocationYearSelect">ปีที่จัดสรรงบประมาณ:</label>
                                        <select id="allocationYearSelect" class="form-control">
                                            <option value="FY25">FY25</option>
                                        </select>
                                    </div>

                                    <!-- ปีบริหารงบประมาณ -->
                                    <div class="col-md-2">
                                        <label for="budgetYearSelect">ปีบริหารงบประมาณ:</label>
                                        <select id="budgetYearSelect" class="form-control">
                                            <option value="2568">2568</option>
                                        </select>
                                    </div>
                                    <!-- Scenario -->
                                    <div class="col-md-3">
                                        <label for="scenarioSelect">ประเภทงบประมาณ:</label>
                                        <select id="scenarioSelect" class="form-control">
                                            <option value="Annual Budget Plan">Annual Budget Plan</option>
                                            <option value="Mid Year1 Allocated Plan">Mid Year1 Allocated Plan</option>
                                            <option value="Mid Year2 Allocated Plan">Mid Year2 Allocated Plan</option>
                                        </select>
                                    </div>

                                    <!-- Fund -->
                                    <div class="col-md-3">
                                        <label for="fundSelect">แหล่งเงิน:</label>
                                        <select id="fundSelect" class="form-control">
                                            <option value="FN02">FN02</option>
                                            <option value="FN06" selected>FN06</option>
                                        </select>
                                    </div>

                                    <!-- ปุ่มค้นหา -->
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button onclick="loadData()" class="btn btn-info w-100">ค้นหา</button>
                                    </div>
                                </div>
                                <br>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr class="">
                                                <th rowspan="3">โครงการ/กิจกรรม</th>
                                                <th colspan="22">งบประมาณ</th>
                                                <th rowspan="3">รวมงบประมาณ</th>
                                            </tr>
                                            <tr class="">
                                                <th colspan="11">1. ค่าใช้จ่ายบุคลากร</th>
                                                <th colspan="6">2. ค่าใช้จ่ายดำเนินงาน</th>
                                                <th colspan="3">3. ค่าใช้จ่ายลงทุน</th>
                                                <th rowspan="2" value="">4. ค่าใช้จ่ายเงินอุดหนุนการดำเนินงาน</th>
                                                <th rowspan="2" value="5500000000">5. ค่าใช้จ่ายอื่น</th>
                                            </tr>
                                            <tr class="">
                                                <th value="5101010000">1.1 เงินเดือนข้าราชการและลูกจ้างประจำ</th>
                                                <th value="5101020000">1.2 ค่าจ้างพนักงานมหาวิทยาลัย</th>
                                                <th value="5101030000">1.3 ค่าจ้างลูกจ้างมหาวิทยาลัย</th>
                                                <th value="5101040000">1.4 เงินกองทุนสำรองเพื่อผลประโยชน์พนักงานและสวัสดิการผู้ปฏิบัติงานในมหาวิทยาลัยขอนแก่น</th>
                                                <th value="5101040100">เงินสมทบประกันสังคมส่วนของนายจ้าง</th>
                                                <th value="5101040200">เงินสมทบกองทุนสำรองเลี้ยงชีพของนายจ้าง</th>
                                                <th value="5101040300">เงินชดเชยกรณีเลิกจ้าง</th>
                                                <th value="5101040400">เงินสมทบกองทุนเงินทดแทน</th>
                                                <th value="5101040500">สมทบกองทุนบำเหน็จบำนาญ(กบข.)</th>
                                                <th value="5101040600">สมทบกองทุนสำรองเลี้ยงชีพ (กสจ.)</th>
                                                <th value="5101040700">สวัสดิการอื่น ๆ</th>
                                                <th value="5203010000">ค่าตอบแทน</th>
                                                <th value="5203020000">ค่าใช้สอย</th>
                                                <th value="5203030000">ค่าวัสดุ</th>
                                                <th value="5203040000">ค่าสาธารณูปโภค</th>
                                                <th value="5201000000">ค่าใช้จ่ายด้านการฝึกอบรม</th>
                                                <th value="5202000000">ค่าใช้จ่ายเดินทาง</th>
                                                <th value="1207000000">ค่าครุภัณฑ์</th>
                                                <th value="1206000000">ค่าที่ดินและสิ่งก่อสร้าง</th>
                                                <th value="1205000000">ค่าที่ดิน</th>
                                            </tr>
                                        </thead>
                                        <tbody>

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
            loadData();
        });

        function loadData() {
            const scenario = document.getElementById("scenarioSelect").value;
            const fund = document.getElementById("fundSelect").value;

            $.ajax({
                type: "POST",
                url: "../server/api.php",
                data: {
                    'command': 'report-project-summary',
                    'scenario': scenario,
                    'fund': fund
                },
                dataType: "json",
                success: function(response) {
                    console.log("API Response:", response.plan);

                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    const headerCells = document.querySelectorAll("#reportTable thead th[value]");
                    console.log("Valid header cells:", headerCells.length);

                    response.plan.forEach(row => {
                        const tr = document.createElement('tr');

                        // ✅ คอลัมน์แรก (โครงการ/กิจกรรม)
                        const td1 = document.createElement('td');
                        td1.innerHTML = `
                    <div style="font-weight: bold;">${row.pillar_name || "-"}</div> <!-- ✅ Pillar อยู่แยกส่วนบน -->
                    <div>
                        ${row.faculty_name}<br>
                        ${row.plan_name}<br>
                        ${row.sub_plan_name}<br>
                        <strong>${row.project_name}</strong>  <!-- ✅ ตัวหนาให้ดูชัด -->
                    </div>
                `;
                        tr.appendChild(td1);

                        let totalSum = 0;
                        let hasValue = false;

                        // ✅ คอลัมน์งบประมาณ → ค่าตัวเลขจะอยู่กับ `row.project_name`
                        headerCells.forEach(th => {
                            const td = document.createElement('td');
                            const parentValue = th.getAttribute("value");

                            if (row.parent === parentValue) {
                                const value = parseFloat(row.Total_Amount_Quantity) || 0;
                                td.textContent = value.toLocaleString();
                                totalSum += value;
                                if (value > 0) hasValue = true;
                            } else {
                                td.textContent = "-";
                            }

                            tr.appendChild(td);
                        });

                        // ✅ คอลัมน์รวมงบประมาณ
                        const totalTd = document.createElement('td');
                        totalTd.textContent = totalSum.toLocaleString();
                        totalTd.style.fontWeight = "bold";
                        tr.appendChild(totalTd);

                        if (totalSum > 0 || hasValue) {
                            tableBody.appendChild(tr); // ✅ แสดงเฉพาะแถวที่มีค่า
                        }
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
            link.setAttribute('download', 'รายงานการจัดสรรเงินรายงวด.csv');
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
            doc.save('รายงานการจัดสรรเงินรายงวด.pdf');
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
            link.setAttribute('download', 'รายงานการจัดสรรเงินรายงวด.xls');
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