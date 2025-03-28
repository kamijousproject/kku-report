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
                        <h4>รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</h4>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="category">เลือกส่วนงาน:</label>
                                        <select name="category" id="category" onchange="fetchData()" class="form-control d-inline-block w-auto" style="margin-bottom: 10px;">
                                            <option value="">-- Loading Categories --</option>
                                        </select>
                                    </div>
                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                    <!-- ปุ่ม -->
                                    <button class="btn btn-primary" onclick="runCmd()">อัพเดทข้อมูล</button>

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
                                                <th rowspan="3">ที่</th>
                                                <th rowspan="3">ส่วนงาน/หน่วยงาน</th>
                                                <th colspan="4">ประเภทบริหาร</th>
                                                <th colspan="4">ประเภทวิชาการ</th>
                                                <th colspan="4">ประเภทวิจัย</th>
                                                <th colspan="4">ประเภทสนับสนุน</th>
                                                <th colspan="4">รวม</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">งบประมาณแผ่นดิน</th>
                                                <th colspan="2">งบประมาณเงินรายได้</th>
                                                <th colspan="2">งบประมาณแผ่นดิน</th>
                                                <th colspan="2">งบประมาณเงินรายได้</th>
                                                <th colspan="2">งบประมาณแผ่นดิน</th>
                                                <th colspan="2">งบประมาณเงินรายได้</th>
                                                <th colspan="2">งบประมาณแผ่นดิน</th>
                                                <th colspan="2">งบประมาณเงินรายได้</th>
                                                <th colspan="2">งบประมาณแผ่นดิน</th>
                                                <th colspan="2">งบประมาณเงินรายได้</th>
                                            </tr>
                                            <tr>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>

                                        </tfoot>
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
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
                'command': 'kku_wf_budget-framework'
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
            data = all_data
        } else {
            data = all_data.filter(item => item.pname === category);
        }
        data.forEach((row, index) => {
            const tr = document.createElement('tr');

            const columns = [{
                    key: 'No',
                    value: index + 1
                },
                {
                    key: 'Alias_Default',
                    value: row.Alias_Default
                },

                {
                    key: 'TYPE1_fund1_num',
                    value: (parseInt(row.TYPE1_fund1_num)).toLocaleString()
                },
                {
                    key: 'TYPE1_fund1',
                    value: (parseInt(row.TYPE1_fund1)).toLocaleString()
                },

                {
                    key: 'TYPE1_fund2_num',
                    value: (parseInt(row.TYPE1_fund2_num)).toLocaleString()
                },
                {
                    key: 'TYPE1_fund2',
                    value: (parseInt(row.TYPE1_fund2)).toLocaleString()
                },

                {
                    key: 'TYPE2_fund1_num',
                    value: (parseInt(row.TYPE2_fund1_num)).toLocaleString()
                },
                {
                    key: 'TYPE2_fund1',
                    value: (parseInt(row.TYPE2_fund1)).toLocaleString()
                },

                {
                    key: 'TYPE2_fund2_num',
                    value: (parseInt(row.TYPE2_fund2_num)).toLocaleString()
                },
                {
                    key: 'TYPE2_fund2',
                    value: (parseInt(row.TYPE2_fund2)).toLocaleString()
                },

                {
                    key: 'TYPE3_fund1_num',
                    value: (parseInt(row.TYPE3_fund1_num)).toLocaleString()
                },
                {
                    key: 'TYPE3_fund1',
                    value: (parseInt(row.TYPE3_fund1)).toLocaleString()
                },

                {
                    key: 'TYPE3_fund2_num',
                    value: (parseInt(row.TYPE3_fund2_num)).toLocaleString()
                },
                {
                    key: 'TYPE3_fund2',
                    value: (parseInt(row.TYPE3_fund2)).toLocaleString()
                },

                {
                    key: 'TYPE4_fund1_num',
                    value: (parseInt(row.TYPE4_fund1_num)).toLocaleString()
                },
                {
                    key: 'TYPE4_fund1',
                    value: (parseInt(row.TYPE4_fund1)).toLocaleString()
                },

                {
                    key: 'TYPE4_fund2_num',
                    value: (parseInt(row.TYPE4_fund2_num)).toLocaleString()
                },
                {
                    key: 'TYPE4_fund2',
                    value: (parseInt(row.TYPE4_fund2)).toLocaleString()
                },

                {
                    key: 'sum_fund1_num',
                    value: (parseInt(row.TYPE1_fund1_num) + parseInt(row.TYPE2_fund1_num) + parseInt(row.TYPE3_fund1_num) + parseInt(row.TYPE4_fund1_num)).toLocaleString()
                },
                {
                    key: 'sum_fund1',
                    value: (parseInt(row.TYPE1_fund1) + parseInt(row.TYPE2_fund1) + parseInt(row.TYPE3_fund1) + parseInt(row.TYPE4_fund1)).toLocaleString()
                },

                {
                    key: 'sum_fund2_num',
                    value: (parseInt(row.TYPE1_fund2_num) + parseInt(row.TYPE2_fund2_num) + parseInt(row.TYPE3_fund2_num) + parseInt(row.TYPE4_fund2_num)).toLocaleString()
                },
                {
                    key: 'sum_fund2',
                    value: (parseInt(row.TYPE1_fund2) + parseInt(row.TYPE2_fund2) + parseInt(row.TYPE3_fund2) + parseInt(row.TYPE4_fund2)).toLocaleString()
                },
            ];

            columns.forEach(col => {
                const td = document.createElement('td');
                td.textContent = col.value;
                tr.appendChild(td);
            });


            tableBody.appendChild(tr);

        });
        calculateSum();
    }

    function calculateSum() {
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        const footer = table.querySelector('tfoot');
        const columns = rows[0].querySelectorAll('td').length;

        // สร้างแถว footer
        let footerRow = document.createElement('tr');
        footerRow.innerHTML = '<td colspan="2">รวม</td>';

        // เริ่มต้นผลรวมแต่ละคอลัมน์
        let sums = new Array(columns - 2).fill(0);

        // คำนวณผลรวม
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (index >= 2) { // "ส่วนงาน/หน่วยงาน"  
                    const value = cell.textContent.replace(/,/g, '');
                    sums[index - 2] += parseFloat(value) || 0;
                }
            });
        });

        // เพิ่มผลรวมลงใน footer
        sums.forEach(sum => {
            footerRow.innerHTML += `<td>${sum.toLocaleString()}</td>`;
        });

        // เพิ่มแถว footer ลงในตาราง
        footer.innerHTML = '';
        footer.append(footerRow);
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
        const doc = new jsPDF('l', 'mm', 'a4'); // A4 landscape

        // Add Thai font
        doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
        doc.addFont("THSarabun.ttf", "THSarabun", "normal");
        doc.setFont("THSarabun");

        // Configure autoTable
        doc.autoTable({
            html: '#reportTable',
            startY: 20,
            theme: 'grid',
            styles: {
                font: "THSarabun",
                fontSize: 6,
                cellPadding: {
                    top: 1,
                    right: 1,
                    bottom: 1,
                    left: 1
                },
                lineWidth: 0.1,
                lineColor: [0, 0, 0],
                minCellHeight: 4
            },
            headStyles: {
                fillColor: [220, 230, 241],
                textColor: [0, 0, 0],
                fontSize: 6,
                fontStyle: 'bold',
                halign: 'center',
                valign: 'middle',
                minCellHeight: 4
            },
            columnStyles: {
                0: {
                    cellWidth: 8
                }, // ที่
                1: {
                    cellWidth: 35
                }, // ส่วนงาน/หน่วยงาน
                // ประเภทบริหาร
                2: {
                    cellWidth: 10
                }, // จำนวน (แผ่นดิน)
                3: {
                    cellWidth: 14
                }, // งบประมาณ (แผ่นดิน)
                4: {
                    cellWidth: 10
                }, // จำนวน (รายได้)
                5: {
                    cellWidth: 14
                }, // งบประมาณ (รายได้)
                // ประเภทวิชาการ
                6: {
                    cellWidth: 10
                }, // จำนวน (แผ่นดิน)
                7: {
                    cellWidth: 14
                }, // งบประมาณ (แผ่นดิน)
                8: {
                    cellWidth: 10
                }, // จำนวน (รายได้)
                9: {
                    cellWidth: 14
                }, // งบประมาณ (รายได้)
                // ประเภทวิจัย
                10: {
                    cellWidth: 10
                }, // จำนวน (แผ่นดิน)
                11: {
                    cellWidth: 14
                }, // งบประมาณ (แผ่นดิน)
                12: {
                    cellWidth: 10
                }, // จำนวน (รายได้)
                13: {
                    cellWidth: 14
                }, // งบประมาณ (รายได้)
                // ประเภทสนับสนุน
                14: {
                    cellWidth: 10
                }, // จำนวน (แผ่นดิน)
                15: {
                    cellWidth: 14
                }, // งบประมาณ (แผ่นดิน)
                16: {
                    cellWidth: 10
                }, // จำนวน (รายได้)
                17: {
                    cellWidth: 14
                }, // งบประมาณ (รายได้)
                // รวม
                18: {
                    cellWidth: 10
                }, // จำนวน (แผ่นดิน)
                19: {
                    cellWidth: 14
                }, // งบประมาณ (แผ่นดิน)
                20: {
                    cellWidth: 10
                }, // จำนวน (รายได้)
                21: {
                    cellWidth: 14
                } // งบประมาณ (รายได้)
            },
            didDrawPage: function(data) {
                // Add header
                doc.setFontSize(12);
                doc.text('รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ', 14, 10);

                // Add footer with page number
                doc.setFontSize(8);
                doc.text(
                    'หน้า ' + doc.internal.getCurrentPageInfo().pageNumber + ' จาก ' + doc.internal.getNumberOfPages(),
                    doc.internal.pageSize.width - 20,
                    doc.internal.pageSize.height - 10, {
                        align: 'right'
                    }
                );
            },
            didParseCell: function(data) {
                // Center align all header cells
                if (data.section === 'head') {
                    data.cell.styles.halign = 'center';
                    data.cell.styles.valign = 'middle';

                    // Adjust font sizes for different header rows
                    if (data.row.index === 0) {
                        data.cell.styles.fontSize = 7; // Main headers
                    } else {
                        data.cell.styles.fontSize = 6; // Sub-headers
                    }
                }

                // Handle body and footer cells
                if (data.section === 'body' || data.section === 'foot') {
                    // Left align department names
                    if (data.column.index <= 1) {
                        data.cell.styles.halign = 'left';
                        data.cell.styles.fontSize = 7;
                    } else {
                        // Right align numeric data
                        data.cell.styles.halign = 'right';
                        data.cell.styles.fontSize = 6;
                    }
                }

                // Style footer row
                if (data.section === 'foot') {
                    data.cell.styles.fontStyle = 'bold';
                    data.cell.styles.textColor = 'black';
                    data.cell.styles.fillColor = [240, 240, 240];
                }
            },
            // Set margins to maximize space
            margin: {
                top: 15,
                right: 5,
                bottom: 15,
                left: 5
            },
            // Use all available width
            tableWidth: 'auto'
        });

        // Save the PDF
        doc.save('รายงานกรอบอัตรากำลัง.pdf');
    }

    function exportXLS() {
        const table = document.getElementById('reportTable');

        // เก็บข้อมูลแต่ละแถวเป็น Array ของ Array
        const rows = [];
        // เก็บ Merge (colSpan/rowSpan) ในรูปแบบ SheetJS
        const merges = {};

        // ใช้ object เก็บว่าส่วนใดถูก merge ไปแล้ว เพื่อเลี่ยงการซ้ำซ้อน
        // key = "rowIndex,colIndex" => true/false
        const skipMap = {};

        for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
            const tr = table.rows[rowIndex];
            const rowData = [];
            let colIndex = 0;

            for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                // ข้ามเซลล์ที่อยู่ในพื้นที่ merge แล้ว
                while (skipMap[`${rowIndex},${colIndex}`]) {
                    rowData.push("");
                    colIndex++;
                }

                const cell = tr.cells[cellIndex];
                // เอา innerText หรือจะใช้ innerHTML แปลงเองก็ได้
                let cellText = cell.innerText.trim();

                // ใส่ข้อมูลลงใน Array
                rowData[colIndex] = cellText;

                // ตรวจสอบ colSpan / rowSpan
                const rowspan = cell.rowSpan || 1;
                const colspan = cell.colSpan || 1;

                // ถ้ามีการ Merge จริง (มากกว่า 1)
                if (rowspan > 1 || colspan > 1) {
                    // สร้าง object merge ตามรูปแบบ SheetJS
                    const mergeRef = {
                        s: {
                            r: rowIndex,
                            c: colIndex
                        }, // จุดเริ่ม (start)
                        e: {
                            r: rowIndex + rowspan - 1,
                            c: colIndex + colspan - 1
                        } // จุดจบ (end)
                    };

                    // เก็บลง merges (รูปแบบเก่าคือ ws['!merges'] = [])
                    // แต่ต้องรอใส่หลังสร้าง Worksheet ด้วย SheetJS
                    // จึงบันทึกชั่วคราวใน merges พร้อม index
                    const mergeKey = `merge_${rowIndex}_${colIndex}`;
                    merges[mergeKey] = mergeRef;

                    // Mark skipMap กันซ้ำ
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
        // แปลง Array เป็น Worksheet
        const ws = XLSX.utils.aoa_to_sheet(rows);

        // ใส่ merges เข้า Worksheet (Array)
        ws['!merges'] = Object.values(merges);

        // เพิ่มชีทใน Workbook
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // เขียนไฟล์เป็น XLS (BIFF8)
        // ใช้ { bookType: 'xls', type: 'array' } เพื่อได้ Buffer Array
        const excelBuffer = XLSX.write(wb, {
            bookType: 'xls',
            type: 'array'
        });

        // สร้าง Blob เป็นไฟล์ XLS
        const blob = new Blob([excelBuffer], {
            type: 'application/vnd.ms-excel'
        });

        // ดาวน์โหลดไฟล์
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'report.xls'; // ชื่อไฟล์ .xls
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>
<!-- Common JS -->
<script src="../assets/plugins/common/common.min.js"></script>
<!-- Custom script -->
<script src="../js/custom.min.js"></script>


</html>