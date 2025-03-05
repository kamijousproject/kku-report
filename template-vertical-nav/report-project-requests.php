<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>     
#main-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
}

/* .content-body {
    flex-grow: 1;
    overflow: hidden; 
    display: flex;
    flex-direction: column;
} */

.container {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}


.table-responsive {
    flex-grow: 1;
    overflow-y: auto; /* Scrollable content only inside table */
    max-height: 60vh; /* Set a fixed height */
    border: 1px solid #ccc;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

thead tr:nth-child(1) th {
    position: sticky;
    top: 0;
    background: #f4f4f4;
    z-index: 1000;
}

thead tr:nth-child(2) th {
    position: sticky;
    top: 44px; /* Adjust height based on previous row */
    background: #f4f4f4;
    z-index: 999;
}

thead tr:nth-child(3) th {
    position: sticky;
    top: 89px; /* Adjust height based on previous rows */
    background: #f4f4f4;
    z-index: 998;
}
.nowrap {
    white-space: nowrap;
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
                        <h4>รายงานสรุปคำขอรายโครงการ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอรายโครงการ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอรายโครงการ</h4>
                                </div>
                                <div class="info-section">
                                    <label for="dropdown1">ปีบริหารงบประมาณ:</label>
                                    <select id="dropdown1">
                                        <option value="">เลือกปีบริหารงบประมาณ</option>
                                    </select>
                                    <br />
                                    <!-- Dropdown 2 (Changes based on Dropdown 1) -->
                                    <label for="dropdown2">ประเภทงบประมาณ:</label>
                                    <select id="dropdown2" disabled>
                                        <option value="">เลือกประเภทงบประมาณ</option>
                                    </select>
                                    <br />
                                    <!-- Dropdown 3 (Changes based on Dropdown 2) -->
                                    <label for="dropdown3">แหล่งเงิน:</label>
                                    <select id="dropdown3" disabled>
                                        <option value="">เลือกแหล่งเงิน</option>
                                    </select>
                                    <br />
                                    <!-- Dropdown 3 (Changes based on Dropdown 2) -->
                                    <label for="dropdown4">ส่วนงาน/หน่วยงาน:</label>
                                    <select id="dropdown4" disabled>
                                        <option value="">เลือกส่วนงาน/หน่วยงาน</option>
                                    </select>
                                    <br />
                                    <!-- Submit Button -->
                                    <button id="submitBtn" disabled>Submit</button>
                                </div>
                                <br />
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">ที่</th>
                                                <th rowspan="2">โครงการ/กิจกรรม</th>
                                                <th rowspan="2">ประเด็นยุทธศาสตร์</th>
                                                <th rowspan="2">OKR</th>
                                                <th rowspan="2">แผนงาน (ผลผลิต)</th>
                                                <th rowspan="2" nowrap>แผนงานย่อย<br/>(ผลผลิตย่อย/กิจกรรม)</th>
                                                <th colspan="5">งบประมาณ</th>
                                                <th rowspan="2">รวมงบประมาณ</th>
                                                <th colspan="4" nowrap>แผนการใช้จ่ายงบประมาณ</th>
                                            </tr>
                                            <tr>
                                                <th nowrap>1.ค่าใช้จ่ายบุคลากร</th>
                                                <th nowrap>2.ค่าใช้จ่ายดำเนินงาน</th>
                                                <th nowrap>3.ค่าใช้จ่ายลงทุน</th>
                                                <th nowrap>4.ค่าใช้จ่ายอุดหนุนการดำเนินงาน</th>
                                                <th nowrap>5.ค่าใช้จ่ายอื่น</th>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
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
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
        $(document).ready(function () {

            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'get_fiscal_year'
                },
                dataType: "json",
                success: function (response) {

                    response.bgp.forEach((row) => {
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="' + row.y + '">' + row.y + '</option>');
                    });
                }
            });


            $('#dropdown1').change(function () {
                let year = $(this).val();
                //console.log(year);
                $('#dropdown2').html('<option value="">เลือกประเภทงบประมาณ</option>').prop('disabled', true);
                $('#dropdown3').html('<option value="">เลือกแหล่งเงิน</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'get_scenario',
                        'fiscal_year': year
                    },
                    dataType: "json",
                    success: function (response) {
                        //console.log(response);
                        response.fac.forEach((row) => {
                            $('#dropdown2').append('<option value="' + row.scenario + '">' + row.scenario + '</option>').prop('disabled', false);
                        });
                    }
                    ,
                    error: function (jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });


            $('#dropdown2').change(function () {
                let year = $("#dropdown1").val();
                let scenario = $("#dropdown2").val();
                //console.log(year);               
                $('#dropdown3').html('<option value="">เลือกแหล่งเงิน</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'get_fund',
                        'fiscal_year': year,
                        'scenario': scenario
                    },
                    dataType: "json",
                    success: function (response) {
                        //console.log(response);
                        response.fund.forEach((row) => {
                            $('#dropdown3').append('<option value="' + row.f + '">' + row.f + '</option>').prop('disabled', false);
                        });
                    }
                    ,
                    error: function (jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });
            $('#dropdown3').change(function () {
                let fund = $('#dropdown3').val();
                var year = $('#dropdown1').val();
                var scenario = $('#dropdown2').val();
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                //console.log(year);
                //console.log(fund);
                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'get_faculty',
                        'fiscal_year': year,
                        'fund': fund,
                        'scenario': scenario
                    },
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        response.fac.forEach((row) => {
                            $('#dropdown4').append('<option value="' + row.faculty + '">' + row.faculty + '</option>').prop('disabled', false);

                        });
                    }
                    ,
                    error: function (jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });

            $('#dropdown4').change(function () {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });


            $('#submitBtn').click(function () {
                let year = $('#dropdown1').val();
                let fund = $('#dropdown3').val();
                let faculty = $('#dropdown4').val();
                var scenario = $('#dropdown2').val();
                console.log(year);
                console.log(fund);
                console.log(faculty);
                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'kku_bgp_project-requests',
                        'fiscal_year': year,
                        'fund': fund,
                        'faculty': faculty,
                        'scenario': scenario
                    },
                    dataType: "json",
                    success: function (response) {
                        const tableBody = document.querySelector('#reportTable tbody');
                        tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                        console.log(response.bgp);
                        response.bgp.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            const columns = [
                                { key: 'No', value: index + 1 },
                                //{ key: 'Alias_Default', value: row.Alias_Default },
                                //{ key: 'Faculty', value: row.faculty },
                                //{ key: 'Project', value: row.project },
                                { key: 'Project_Name', value: row.project_name },
                                { key: 'KKU_Strategic_Plan_LOV', value: row.pillar_name },
                                { key: 'OKRs_LOV', value: row.okr_name },
                                //{ key: 'Fund', value: row.fund },
                                //{ key: 'Plan', value: row.plan },
                                { key: 'Plan_Name', value: row.plan_name },
                                //{ key: 'Sub_Plan', value: row.sub_plan },
                                { key: 'Sub_Plan_Name', value: row.sub_plan_name },


                                // ค่าใช้จ่ายแต่ละประเภท
                                { key: 'Personnel_Expenses', value: parseInt(row.a1).toLocaleString() }, // ค่าใช้จ่ายบุคลากร
                                { key: 'Operating_Expenses', value: parseInt(row.a2).toLocaleString() }, // ค่าใช้จ่ายดำเนินงาน
                                { key: 'Investment_Expenses', value: parseInt(row.a3).toLocaleString() }, // ค่าใช้จ่ายลงทุน
                                { key: 'Subsidy_Operating_Expenses', value: parseInt(row.a4).toLocaleString() }, // ค่าใช้จ่ายเงินอุดหนุนดำเนินงาน
                                { key: 'Other_Expenses', value: parseInt(row.a5).toLocaleString() }, // ค่าใช้จ่ายอื่น
                                { key: 'sum', value: (parseInt(row.a1) + parseInt(row.a2) + parseInt(row.a3) + parseInt(row.a4) + parseInt(row.a5)).toLocaleString() },
                                // แผนการใช้จ่ายรายไตรมาส
                                { key: 'Q1_Spending_Plan', value: parseInt(row.q1).toLocaleString() },
                                { key: 'Q2_Spending_Plan', value: parseInt(row.q2).toLocaleString() },
                                { key: 'Q3_Spending_Plan', value: parseInt(row.q3).toLocaleString() },
                                { key: 'Q4_Spending_Plan', value: parseInt(row.q4).toLocaleString() }
                            ];
                            columns.forEach(col => {
                                const td = document.createElement('td');
                                td.textContent = col.value;
                                tr.appendChild(td);
                            });


                            tableBody.appendChild(tr);

                        });
                    },
                    error: function (jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });
        });
        function exportCSV() {
            const table = document.getElementById('reportTable');
            const numRows = table.rows.length;

            // คำนวณจำนวนคอลัมน์สูงสุดที่เกิดจากการ merge (colspan)
            let maxCols = 0;
            for (let row of table.rows) {
                let colCount = 0;
                for (let cell of row.cells) {
                    colCount += cell.colSpan || 1;
                }
                maxCols = Math.max(maxCols, colCount);
            }

            // สร้างตาราง 2D เก็บค่าจากตาราง HTML
            let csvMatrix = Array.from({ length: numRows }, () => Array(maxCols).fill(null));

            // ใช้ตัวแปรตรวจสอบว่ามี cell ไหนถูก merge
            let cellMap = Array.from({ length: numRows }, () => Array(maxCols).fill(false));

            for (let rowIndex = 0; rowIndex < numRows; rowIndex++) {
                const row = table.rows[rowIndex];
                let colIndex = 0;

                for (const cell of row.cells) {
                    // ขยับไปช่องว่างที่ยังไม่มีข้อมูล (เผื่อช่องก่อนหน้าถูก merge)
                    while (cellMap[rowIndex][colIndex]) {
                        colIndex++;
                    }

                    let text = cell.textContent.trim().replace(/"/g, '""'); // Escape double quotes

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    // ใส่ข้อมูลลงในช่องเริ่มต้นของ cell ที่ merge
                    csvMatrix[rowIndex][colIndex] = `"${text}"`;

                    // ทำเครื่องหมายว่า cell นี้ครอบคลุมพื้นที่ไหนบ้าง
                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            cellMap[rowIndex + r][colIndex + c] = true;

                            // ช่องที่ไม่ใช่ช่องเริ่มต้นของเซลล์ merge ให้เป็นว่าง (เพื่อไม่ให้ข้อมูลซ้ำ)
                            if (r !== 0 || c !== 0) {
                                csvMatrix[rowIndex + r][colIndex + c] = '""';
                            }
                        }
                    }

                    // ขยับ index ไปยังเซลล์ถัดไป
                    colIndex += colspan;
                }
            }

            // แปลงข้อมูลเป็น CSV
            const csvContent = "\uFEFF" + csvMatrix.map(row => row.join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'report.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        function getFilterValues() {
            return {
                year: document.getElementById('dropdown1').options[document.getElementById('dropdown1').selectedIndex].text,
                budgetType: document.getElementById('dropdown2').options[document.getElementById('dropdown2').selectedIndex].text,
                fundSource: document.getElementById('dropdown3').options[document.getElementById('dropdown3').selectedIndex].text,
                department: document.getElementById('dropdown4').options[document.getElementById('dropdown4').selectedIndex].text
            };
        }
        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            // Add Thai font
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");

            const filterValues = getFilterValues();

            // เพิ่มหัวรายงาน
            doc.setFontSize(16);
            doc.text("รายงานสรุปคำขอรายโครงการ", 150, 10, { align: 'center' });

            // เพิ่มข้อมูลจาก dropdown
            doc.setFontSize(10);
            doc.text(`ปีบริหารงบประมาณ: ${filterValues.year}`, 15, 20);
            doc.text(`ประเภทงบประมาณ: ${filterValues.budgetType}`, 15, 25);
            doc.text(`แหล่งเงิน: ${filterValues.fundSource}`, 150, 20);
            doc.text(`ส่วนงาน/หน่วยงาน: ${filterValues.department}`, 150, 25);

            doc.autoTable({
                html: '#reportTable',
                startY: 35,
                theme: 'grid',
                styles: {
                    font: "THSarabun",
                    fontSize: 7,
                    cellPadding: 1,
                    lineWidth: 0.1,
                    lineColor: [0, 0, 0],
                    minCellHeight: 5
                },
                headStyles: {
                    fillColor: [220, 230, 241],
                    textColor: [0, 0, 0],
                    fontSize: 7,
                    fontStyle: 'bold',
                    halign: 'center',
                    valign: 'middle'
                },
                columnStyles: {
                    0: { halign: 'left' },  // คอลัมน์แรกให้ชิดซ้าย
                },
                didParseCell: function (data) {
                    if (data.section === 'body' && data.column.index === 0) {
                        data.cell.styles.halign = 'left'; // จัด text-align left สำหรับคอลัมน์แรก
                    }
                },
                margin: { top: 15, right: 5, bottom: 10, left: 5 },
                tableWidth: 'auto'
            });
            doc.save('รายงานสรุปคำขอรายโครงการ.pdf');
        }



        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ดึงค่าจาก dropdown
            const filterValues = getFilterValues();

            // สร้างข้อมูลสำหรับหัวรายงาน (4 แถวแรก)
            const headerRows = [
                [{ v: "รายงานสรุปคำขอรายโครงการ", s: { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } } }],
                [
                    { v: "ปีบริหารงบประมาณ:", s: { font: { bold: true } } },
                    { v: filterValues.year }
                ],
                [
                    { v: "ประเภทงบประมาณ:", s: { font: { bold: true } } },
                    { v: filterValues.budgetType }
                ],
                [
                    { v: "แหล่งเงิน:", s: { font: { bold: true } } },
                    { v: filterValues.fundSource },
                    { v: "ส่วนงาน/หน่วยงาน:", s: { font: { bold: true } } },
                    { v: filterValues.department }
                ],
                [] // ว่างไว้ 1 แถว
            ];

            // สร้างข้อมูลจากตาราง
            const rows = [];
            const merges = [];
            const skipMap = {};

            // จัดการกับการรวมเซลล์ในส่วนหัวรายงาน
            merges.push({ s: { r: 0, c: 0 }, e: { r: 0, c: 5 } }); // รวมเซลล์หัวรายงาน

            // ปรับ offset สำหรับตาราง (เพิ่มจำนวนแถวหัวรายงาน)
            const rowOffset = headerRows.length;

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const tr = table.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    while (skipMap[`${rowIndex + rowOffset},${colIndex}`]) {
                        rowData.push("");
                        colIndex++;
                    }

                    const cell = tr.cells[cellIndex];
                    let cellText = cell.innerText.trim();

                    // เช็คว่าเป็น Header หรือไม่
                    const isHeader = tr.parentNode.tagName.toLowerCase() === "thead";

                    rowData[colIndex] = {
                        v: cellText,
                        s: {
                            alignment: {
                                vertical: "top",
                                horizontal: isHeader ? "center" : "left" // **Header = Center, Body = Left**
                            },
                            font: isHeader ? { bold: true } : {} // **ทำให้ Header ตัวหนา**
                        }
                    };

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        merges.push({
                            s: { r: rowIndex + rowOffset, c: colIndex },
                            e: { r: rowIndex + rowOffset + rowspan - 1, c: colIndex + colspan - 1 }
                        });

                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (!(r === 0 && c === 0)) {
                                    skipMap[`${rowIndex + rowOffset + r},${colIndex + c}`] = true;
                                }
                            }
                        }
                    }

                    colIndex++;
                }
                rows.push(rowData);
            }

            // รวมข้อมูลหัวรายงานและข้อมูลตาราง
            const allRows = [...headerRows, ...rows];

            // สร้าง Workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // นำ merges ไปใช้
            ws['!merges'] = merges;

            // กำหนดความกว้างของคอลัมน์
            const cols = [];
            // กำหนดความกว้างตามต้องการ
            cols.push({ wch: 10 }); // ที่
            cols.push({ wch: 30 }); // โครงการ/กิจกรรม
            cols.push({ wch: 45 }); // ประเด็นยุทธศาสตร์
            cols.push({ wch: 20 }); // OKR
            cols.push({ wch: 35 }); // แผนงาน
            cols.push({ wch: 35 }); // แผนงานย่อย
            // คอลัมน์ที่เหลือความกว้าง 15
            for (let i = 0; i < 10; i++) {
                cols.push({ wch: 15 });
            }
            ws['!cols'] = cols;

            // เพิ่ม Worksheet ลงใน Workbook
            XLSX.utils.book_append_sheet(wb, ws, "รายงานสรุปคำขอรายโครงการ");

            // เขียนไฟล์ Excel
            XLSX.writeFile(wb, `รายงานสรุปคำขอรายโครงการ_${filterValues.year}.xlsx`);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>