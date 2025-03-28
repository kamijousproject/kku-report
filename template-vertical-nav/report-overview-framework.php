<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    #main-wrapper {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    .content-body {
        flex-grow: 1;
        overflow: hidden;
        /* Prevent body scrolling */
        display: flex;
        flex-direction: column;
    }

    .container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }


    .table-responsive {
        flex-grow: 1;
        overflow-y: auto;
        /* Scrollable content only inside table */
        max-height: 60vh;
        /* Set a fixed height */
        border: 1px solid #ccc;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
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
        top: 45px;
        /* Adjust height based on previous row */
        background: #f4f4f4;
        z-index: 999;
    }

    thead tr:nth-child(3) th {
        position: sticky;
        top: 105px;
        /* Adjust height based on previous rows */
        background: #f4f4f4;
        z-index: 998;
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
                        <h4>รายงานสรุปผลการจัดสรรกรอบอตัรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปผลการจัดสรรกรอบอตัรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปผลการจัดสรรกรอบอตัรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย</h4>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="category">เลือกส่วนงาน:</label>
                                        <select name="category" id="category" onchange="fetchData()">
                                            <option value="">-- Loading Categories --</option>
                                        </select>
                                    </div>
                                    <!-- โหลด SweetAlert2 (ใส่ใน <head> หรือก่อนปิด </body>) -->
                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                    <!-- ปุ่ม -->
                                    <button class="btn btn-primary" onclick="runCmd()" style="margin-bottom: 10px;">อัพเดทข้อมูล</button>

                                    <script>
                                        function runCmd() {
                                            // แสดง SweetAlert ขณะกำลังรัน .cmd
                                            Swal.fire({
                                                title: 'กำลังอัปเดตข้อมูล',
                                                text: 'กรุณารอสักครู่...',
                                                allowOutsideClick: false,
                                                didOpen: () => {
                                                    Swal.showLoading(); // แสดง loading spinner
                                                }
                                            });

                                            // เรียก PHP เพื่อรัน .cmd
                                            fetch('/kku-report/server/automateEPM/workforce/run_cmd_workforce.php')
                                                .then(response => response.text())
                                                .then(result => {
                                                    // เมื่อทำงานเสร็จ ปิด loading แล้วแสดงผลลัพธ์
                                                    Swal.fire({
                                                        title: 'อัปเดตข้อมูลเสร็จสิ้น',
                                                        html: result, // ใช้ .html เพื่อแสดงผลเป็น <br>
                                                        icon: 'success'
                                                    });
                                                })
                                                .catch(error => {
                                                    Swal.fire({
                                                        title: 'เกิดข้อผิดพลาด',
                                                        text: 'ไม่สามารถอัปเดตข้อมูลได้',
                                                        icon: 'error'
                                                    });
                                                    console.error(error);
                                                });
                                        }
                                    </script>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" nowrap>ส่วนงาน/<br />หน่วยงาน</th>
                                                <th rowspan="2">ปีงบประมาณที่จัดสรร</th>
                                                <th rowspan="2">ประเภทอัตรา</th>
                                                <th rowspan="2">ประเภทบุคลากร</th>
                                                <th rowspan="2" nowrap>ชื่อ - นามสกุล</th>
                                                <th rowspan="2">ประเภทการจ้าง</th>
                                                <th rowspan="2">ประเภทตำแหน่ง</th>
                                                <th rowspan="2">กลุ่มตำแหน่ง</th>
                                                <th rowspan="2" nowrap>Job Family</th>
                                                <th rowspan="2">ชื่อตำแหน่ง</th>
                                                <th rowspan="2" nowrap>คุณวุฒิของ<br />ตำแหน่ง</th>
                                                <th rowspan="2" nowrap>เลขประจำ<br />ตำแหน่ง</th>
                                                <th rowspan="2">ประเภทสัญญา</th>
                                                <th rowspan="2" nowrap>ระยะเวลา<br />สัญญา</th>
                                                <th rowspan="2">สถานที่ปฏิบัติงาน</th>
                                                <th rowspan="2">แหล่งงบประมาณ</th>
                                                <th colspan="4">จำนวนงบประมาณ</th>
                                                <th rowspan="2" nowrap>ระยะเวลา<br />การจ้าง</th>
                                            </tr>
                                            <tr>
                                                <th>เงินเดือน</th>
                                                <th nowrap>งบประมาณ<br />แผ่นดิน</th>
                                                <th nowrap>งบประมาณ<br />เงินรายได้คณะ</th>
                                                <th nowrap>งบประมาณเงินรายได้<br />สำนักงานอธิการบดี</th>
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
        let all_data;
        $(document).ready(function() {
            laodData();

        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_overview-framework'
                },
                dataType: "json",
                success: function(response) {
                    all_data = response.wf;
                    const fac = [...new Set(all_data.map(item => item.pname))];
                    let dropdown = document.getElementById("category");
                    dropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
                    fac.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        dropdown.appendChild(option);
                    });
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });

        }

        function fetchData() {
            let category = document.getElementById("category").value;

            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
            let data;
            if (category == "all") {
                data = all_data;
            } else {
                data = all_data.filter(item => item.pname === category);
            }
            data.forEach((row, index) => {
                const tr = document.createElement('tr');

                const columns = [

                    {
                        key: 'Alias_Default',
                        value: row.Alias_Default
                    },
                    {
                        key: 'fiscal_year',
                        value: "2568"
                    },
                    {
                        key: 'TYPE',
                        value: row.TYPE
                    },
                    {
                        key: 'Personnel_Type',
                        value: row.Personnel_Type
                    },
                    {
                        key: 'Workers_Name_Surname',
                        value: row.Workers_Name_Surname2
                    },
                    {
                        key: 'Employment_Type',
                        value: row.Employment_Type
                    },
                    {
                        key: 'All_PositionTypes',
                        value: row.All_PositionTypes
                    },
                    {
                        key: 'Personnel_Group',
                        value: row.Personnel_Group
                    },
                    {
                        key: 'Job_Family',
                        value: row.Job_Family
                    },
                    {
                        key: 'POSITION',
                        value: row.POSITION
                    },
                    {
                        key: 'Position_Qualifications',
                        value: row.Position_Qualifications2
                    },
                    {
                        key: 'Position_Number',
                        value: row.Position_Number
                    },
                    {
                        key: 'Contract_Type',
                        value: row.Contract_Type
                    },
                    {
                        key: 'Contract_Period_Short_Term',
                        value: row.Contract_Period_Short_Term
                    },
                    {
                        key: 'Location_Code',
                        value: row.Location_Code
                    },
                    {
                        key: 'Fund_FT',
                        value: row.Fund_FT
                    },
                    {
                        key: 'Salary_rate',
                        value: (parseFloat(row.Salary_rate || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                    },
                    {
                        key: 'Govt_Fund',
                        value: (parseFloat(row.Govt_Fund || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                    },
                    {
                        key: 'Division_Revenue',
                        value: (parseFloat(row.Division_Revenue || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                    },
                    {
                        key: 'OOP_Central_Revenue',
                        value: (parseFloat(row.OOP_Central_Revenue || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                    },
                    {
                        key: 'Contract_Period',
                        value: row.Hiring_Start_End_Date
                    },
                ];

                columns.forEach(col => {
                    const td = document.createElement('td');
                    td.textContent = col.value;
                    tr.appendChild(td);
                });
                tableBody.appendChild(tr);
            });


        }

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
            let csvMatrix = Array.from({
                length: numRows
            }, () => Array(maxCols).fill(null));

            // ใช้ตัวแปรตรวจสอบว่ามี cell ไหนถูก merge
            let cellMap = Array.from({
                length: numRows
            }, () => Array(maxCols).fill(false));

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
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'report.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            // Using A3 landscape to fit all columns
            const doc = new jsPDF('l', 'mm', 'a4');

            // Add Thai font
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");

            // Configure autoTable with optimized settings
            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                theme: 'grid',
                styles: {
                    font: "THSarabun",
                    fontSize: 7,
                    cellPadding: {
                        top: 1,
                        right: 1,
                        bottom: 1,
                        left: 1
                    },
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
                    valign: 'middle',
                    minCellHeight: 5
                },
                // Column widths optimized for retirement report
                columnStyles: {
                    0: {
                        cellWidth: 25
                    }, // ส่วนงาน/หน่วยงาน
                    1: {
                        cellWidth: 15
                    }, // ปีงบประมาณที่จัดสรร
                    2: {
                        cellWidth: 16
                    }, // ประเภทอัตรา
                    3: {
                        cellWidth: 20
                    }, // ประเภทบุคลากร
                    4: {
                        cellWidth: 25
                    }, // ชื่อ - นามสกุล
                    5: {
                        cellWidth: 12
                    }, // ประเภทการจ้าง
                    6: {
                        cellWidth: 11
                    }, // ประเภทตำแหน่ง
                    7: {
                        cellWidth: 15
                    }, // กลุ่มตำแหน่ง
                    8: {
                        cellWidth: 10
                    }, // Job Family
                    9: {
                        cellWidth: 16
                    }, // ชื่อตำแหน่ง
                    10: {
                        cellWidth: 12
                    }, // คุณวุฒิของตำแหน่ง
                    11: {
                        cellWidth: 10
                    }, // เลขประจำตำแหน่ง
                    12: {
                        cellWidth: 10
                    }, // ประเภทสัญญา
                    13: {
                        cellWidth: 8
                    }, // ระยะเวลาสัญญา
                    14: {
                        cellWidth: 14
                    }, // สถานที่ปฏิบัติงาน
                    15: {
                        cellWidth: 14
                    }, // แหล่งงบประมาณ
                    16: {
                        cellWidth: 11
                    }, // เงินเดือน
                    17: {
                        cellWidth: 10
                    }, // งบประมาณแผ่นดิน
                    18: {
                        cellWidth: 8
                    }, // งบประมาณเงินรายได้คณะ
                    19: {
                        cellWidth: 12
                    }, // งบประมาณเงินรายได้สำนักงานอธิการบดี
                    20: {
                        cellWidth: 12
                    } // ระยะเวลาการจ้าง
                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(14);
                    doc.text('รายงานสรุปผลการจัดสรรกรอบอัตรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย', 20, 10);

                    // Add footer with page number
                    doc.setFontSize(10);
                    /* doc.text(
                        'หน้า ' + doc.internal.getCurrentPageInfo().pageNumber + ' จาก ' + doc.internal.getNumberOfPages(),
                        doc.internal.pageSize.width - 20, 
                        doc.internal.pageSize.height - 10,
                        { align: 'right' }
                    ); */

                    // Add current date
                    const today = new Date();
                    const dateStr = today.toLocaleDateString('th-TH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    doc.text('วันที่พิมพ์: ' + dateStr, 10, doc.internal.pageSize.height - 5);
                },
                // Handle cell styles
                didParseCell: function(data) {
                    // Center align all header cells
                    if (data.section === 'head') {
                        data.cell.styles.halign = 'center';
                        data.cell.styles.valign = 'middle';
                        data.cell.styles.lineBreak = true;

                        // Properly handle <br> tags in header cells
                        if (typeof data.cell.raw === 'string' && data.cell.raw.includes('<br>')) {
                            data.cell.text = data.cell.raw.replace(/<br>/g, '\n');
                        }
                    }

                    // Handle body cells
                    if (data.section === 'body') {
                        // First column (ID) - center align
                        if (data.column.index === 0) {
                            data.cell.styles.halign = 'center';
                        }
                        // Text columns - left align
                        else if (data.column.index >= 1 && data.column.index <= 4) {
                            data.cell.styles.halign = 'left';
                        }
                        // Number columns - center align
                        else {
                            data.cell.styles.halign = 'center';
                        }
                    }

                    // Footer row
                    if (data.section === 'foot') {
                        data.cell.styles.fontStyle = 'bold';
                        data.cell.styles.textColor = 'DimGray';
                        data.cell.styles.fillColor = 'white';
                        // First column left align
                        if (data.column.index <= 4) {
                            data.cell.styles.halign = 'center';
                        } else {
                            data.cell.styles.halign = 'center';
                        }
                    }
                },
                // Handle text wrapping for cells with <br>
                willDrawCell: function(data) {
                    if (data.section === 'head' || data.section === 'body') {
                        // Replace <br> with newlines for proper rendering
                        if (typeof data.cell.text === 'string') {
                            data.cell.text = data.cell.text.replace(/<br\s*\/?>/gi, '\n');
                        } else if (Array.isArray(data.cell.text)) {
                            data.cell.text = data.cell.text.map(line =>
                                typeof line === 'string' ? line.replace(/<br\s*\/?>/gi, '\n') : line
                            );
                        }
                    }
                },
                // Use fitted margins
                margin: {
                    top: 15,
                    right: 5,
                    bottom: 10,
                    left: 5
                },
                // Automatically calculate table width
                tableWidth: 'auto'
            });

            // Save the PDF
            doc.save('รายงานสรุปผลการจัดสรรกรอบอัตรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย.pdf');
        }


        function exportXLS() {
            const table = document.getElementById('reportTable');

            const rows = [];
            const merges = [];
            const skipMap = {};

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const tr = table.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    while (skipMap[`${rowIndex},${colIndex}`]) {
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
                            font: isHeader ? {
                                bold: true
                            } : {} // **ทำให้ Header ตัวหนา**
                        }
                    };

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        merges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            },
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            }
                        });

                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (!(r === 0 && c === 0)) {
                                    skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                                }
                            }
                        }
                    }

                    colIndex++;
                }
                rows.push(rowData);
            }

            // สร้าง Workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(rows);

            // นำ merges ไปใช้
            ws['!merges'] = merges;

            // เพิ่ม Worksheet ลงใน Workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์ Excel
            XLSX.writeFile(wb, 'รายงานสรุปผลการจัดสรรกรอบอัตรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>