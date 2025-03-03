<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    .table-responsive {
        max-height: 30rem;
        /* กำหนดความสูงให้ตารางมี Scroll */
        overflow-y: auto;
    }

    .table thead th {
        position: sticky;
        background-color: #F2F2F2;
        top: 0;
        z-index: 10;
    }

    #reportTable th {
        background-color: #F2F2F2;
    }

    .table thead tr th {
        z-index: 11;
    }

    .table thead tr:first-child th {
        /* ให้แถวแรก (th ที่ colspan) ตรึงที่ด้านบน */
        position: sticky;
        top: 0;
        background: #F2F2F2;
        z-index: 10;
        border-bottom: 1px solid #ffffff;
        /* เพิ่มเส้นขอบใต้ */
    }

    .table thead tr:nth-child(2) th {
        /* ให้แถวที่สอง (th ที่มี day column) ตรึงอยู่ที่ด้านบน */
        position: sticky;
        top: 45.4px;
        background: #F2F2F2;
        z-index: 9;
        border-bottom: 1px solid #ffffff;
        /* เพิ่มเส้นขอบใต้ */
    }

    /* ให้แถวที่สองไม่ถูกบดบังด้วยแถวแรก */
    .table thead tr:nth-child(2) th {
        z-index: 9;
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
                        <h4>รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน</h4>
                                </div>
                                <label for="selectcategory">เลือกส่วนงาน:</label>
                                <select name="selectcategory" id="selectcategory" onchange="selectFilter()">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">ยุทธศาสตร์</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">กลยุทธ์</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">ผลลัพธ์สำคัญ</th>
                                                <th class="align-middle" rowspan="2">ค่าเป้าหมาย</th>
                                                <th class="align-middle" rowspan="2">หน่วยนับ</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">แผนงาน/โครงการ</th>
                                                <th class="align-middle" rowspan="2">กรอบวงเงินงบประมาณ (บาท)</th>
                                                <th colspan="2">ระยะเวลาที่ดำเนินการ</th>
                                                <th class="align-middle" rowspan="2">ระดับและการปรับใช้</th>
                                                <th class="align-middle" rowspan="2">ผู้รับผิดชอบ</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th>วันเริ่มต้น</th>
                                                <th>วันสิ้นสุด</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let report_plan_status = [];
        let filterdata = []
        let categories = new Set();
        $(document).ready(function() {
            laodData();
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/api.php",
                data: {
                    'command': 'get_indicator_comparison'
                },
                dataType: "json",
                success: function(response) {
                    report_plan_status = response.plan;
                    // console.log(response.plan);
                    response.plan.forEach(data => {
                        categories.add(data.fa_name);

                    })
                    const categorySelect = document.getElementById("selectcategory");

                    // เพิ่มตัวเลือกทั้งหมด
                    categorySelect.innerHTML = '<option value="">-- ทั้งหมด --</option>';

                    // เพิ่มตัวเลือกสำหรับแต่ละ fa_name ที่ไม่ซ้ำ
                    categories.forEach(category => {
                        const option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        categorySelect.appendChild(option);
                    });
                    writeBody(response.plan);
                    // console.log(response.plan);



                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
        }

        function selectFilter() {
            const selectedCategory = document.getElementById('selectcategory').value;
            if (selectedCategory === "") {
                filterdata = report_plan_status;
                writeBody(filterdata);
            } else {
                // filter ข้อมูลที่ fa_name ตรงกับค่าที่เลือก
                filterdata = report_plan_status.filter(item => item.fa_name === selectedCategory);
                writeBody(filterdata);
            }
            document.querySelector('.table-responsive').scrollTop = 0;
        }


        function writeBody(data) {
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

            let previousSICode = '';
            let previousSIName = '';
            let previousSOCode = '';
            let previousSOName = '';
            let previousOKRCode = '';
            let previousOKRName = '';
            data.forEach(row => {
                const tr = document.createElement('tr');

                const createCell = (text, align = "left") => {
                    const td = document.createElement('td');
                    td.textContent = text;
                    td.style.textAlign = align; // กำหนดให้ชิดซ้าย
                    return td;
                };

                const td1 = createCell(row.si_code === previousSICode ? '' : row.si_code);
                tr.appendChild(td1);

                const td2 = createCell(row.si_name === previousSIName ? '' : row.si_name);
                tr.appendChild(td2);

                const td3 = createCell(row.Strategic_Object === previousSOCode ? '' : row.Strategic_Object);
                tr.appendChild(td3);

                const td4 = createCell(row.so_name === previousSOName ? '' : row.so_name);
                tr.appendChild(td4);

                const td5 = createCell(row.OKR);
                tr.appendChild(td5);

                const td6 = createCell(row.okr_name);
                tr.appendChild(td6);

                const td7 = createCell(row.Target_OKR_Objective_and_Key_Result, "right");
                tr.appendChild(td7);

                const td8 = createCell(row.UOM);
                tr.appendChild(td8);

                const td9 = createCell(row.Strategic_Project);
                tr.appendChild(td9);

                const td10 = createCell(row.ksp_name);
                tr.appendChild(td10);

                const td11 = createCell(Number(row.Budget_Amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }), "right");
                tr.appendChild(td11);

                const td12 = createCell(row.Start_Date);
                tr.appendChild(td12);

                const td13 = createCell(row.End_Date);
                tr.appendChild(td13);

                const td14 = createCell(row.Tiers_Deploy);
                tr.appendChild(td14);

                const td15 = createCell(row.Responsible_person);
                tr.appendChild(td15);


                tableBody.appendChild(tr);

                // เก็บค่า si_name และ so_name ของแถวนี้ไว้ใช้ในการเปรียบเทียบในแถวถัดไป
                previousSICode = row.si_code;
                previousSIName = row.si_name;
                previousSOCode = row.Strategic_Object;
                previousSOName = row.so_name;
            });
        }


        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('l', 'mm', [305, 215.9]); // Legal landscape size

            // Add Thai font
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");

            // Configure autoTable
            doc.autoTable({
                html: '#reportTable',
                startY: 25,
                theme: 'grid',
                styles: {
                    font: "THSarabun",
                    fontSize: 8,
                    cellPadding: 1,
                    lineWidth: 0.1,
                    lineColor: [0, 0, 0],
                    minCellHeight: 6
                },
                headStyles: {
                    fillColor: [220, 230, 241],
                    textColor: [0, 0, 0],
                    fontSize: 8,
                    fontStyle: 'bold',
                    halign: 'center',
                    valign: 'middle',
                    minCellHeight: 12
                },
                columnStyles: {
                    0: {
                        cellWidth: 10
                    },
                    1: {
                        cellWidth: 30
                    },
                    2: {
                        cellWidth: 10
                    },
                    3: {
                        cellWidth: 30
                    },
                    4: {
                        cellWidth: 10
                    },
                    5: {
                        cellWidth: 30
                    },
                    6: {
                        cellWidth: 10
                    },
                    7: {
                        cellWidth: 10
                    },
                    8: {
                        cellWidth: 10
                    },
                    9: {
                        cellWidth: 30
                    },
                    10: {
                        cellWidth: 30
                    },
                    11: {
                        cellWidth: 20
                    },
                    12: {
                        cellWidth: 20
                    },
                    13: {
                        cellWidth: 20
                    },
                    14: {
                        cellWidth: 20
                    },

                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(16);
                    doc.text('รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน', 14, 15);

                    // Add footer with page number
                    doc.setFontSize(10);
                    doc.text(
                        'หน้า ' + doc.internal.getCurrentPageInfo().pageNumber + ' จาก ' + doc.internal.getNumberOfPages(),
                        doc.internal.pageSize.width - 20,
                        doc.internal.pageSize.height - 10, {
                            align: 'right'
                        }
                    );
                },
                // Handle cell styles
                didParseCell: function(data) {
                    // Center align all header cells
                    if (data.section === 'head') {
                        data.cell.styles.halign = 'center';
                        data.cell.styles.valign = 'middle';
                        data.cell.styles.cellPadding = 1;
                    }

                    // Center align all body cells except the second column (ส่วนงาน/หน่วยงาน)
                    if (data.section === 'body') {
                        if (data.column.index === 6 || data.column.index === 10) {
                            data.cell.styles.halign = 'right';
                        } else {
                            data.cell.styles.halign = 'left';
                        }
                    }

                    // Style footer row
                    if (data.section === 'foot') {
                        data.cell.styles.fontStyle = 'bold';
                        data.cell.styles.fillColor = [240, 240, 240];
                        if (data.column.index !== 1) {
                            data.cell.styles.halign = 'center';
                        }
                    }
                },
                // Handle table width
                margin: {
                    top: 25,
                    right: 7,
                    bottom: 15,
                    left: 7
                },
                tableWidth: 'auto'
            });

            // Save the PDF
            doc.save('รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน.pdf');
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

            // สร้างตาราง 2D สำหรับเก็บข้อมูล CSV
            let csvMatrix = Array.from({
                length: numRows
            }, () => Array(maxCols).fill(""));

            // ใช้ตัวแปรตรวจสอบว่า cell ไหนถูก merge ไปแล้ว
            let cellMap = Array.from({
                length: numRows
            }, () => Array(maxCols).fill(false));

            for (let rowIndex = 0; rowIndex < numRows; rowIndex++) {
                const row = table.rows[rowIndex];
                let colIndex = 0;

                for (const cell of row.cells) {
                    // ขยับไปยังช่องที่ยังไม่มีข้อมูล
                    while (cellMap[rowIndex][colIndex]) {
                        colIndex++;
                    }

                    let text = cell.innerText.trim().replace(/"/g, '""'); // Escape double quotes

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    // ใส่ค่าข้อมูลในตำแหน่งเริ่มต้นของเซลล์
                    csvMatrix[rowIndex][colIndex] = `"${text}"`;

                    // ทำเครื่องหมายว่าช่องนี้ถูกครอบคลุมโดย cell ที่ merge
                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            cellMap[rowIndex + r][colIndex + c] = true;

                            // ช่องที่ถูก merge (ไม่ใช่ช่องแรกของ cell) ให้เป็นว่าง
                            if (r !== 0 || c !== 0) {
                                csvMatrix[rowIndex + r][colIndex + c] = '""';
                            }
                        }
                    }

                    // ขยับ index ไปยังคอลัมน์ถัดไป
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
            link.download = 'รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');
            const numRows = table.rows.length;

            let maxCols = 0;
            for (let row of table.rows) {
                let colCount = 0;
                for (let cell of row.cells) {
                    colCount += cell.colSpan || 1;
                }
                maxCols = Math.max(maxCols, colCount);
            }

            let sheetData = Array.from({
                length: numRows
            }, () => Array(maxCols).fill(""));

            let cellMap = Array.from({
                length: numRows
            }, () => Array(maxCols).fill(false));

            for (let rowIndex = 0; rowIndex < numRows; rowIndex++) {
                const row = table.rows[rowIndex];
                let colIndex = 0;

                for (const cell of row.cells) {
                    while (cellMap[rowIndex][colIndex]) {
                        colIndex++;
                    }

                    let text = cell.innerText.trim();

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    sheetData[rowIndex][colIndex] = text;

                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            cellMap[rowIndex + r][colIndex + c] = true;
                            if (r !== 0 || c !== 0) {
                                sheetData[rowIndex + r][colIndex + c] = null; // Null เพื่อแสดงการ merge
                            }
                        }
                    }

                    colIndex += colspan;
                }
            }

            // สร้าง WorkSheet และ WorkBook
            const ws = XLSX.utils.aoa_to_sheet(sheetData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Report");

            // บันทึกไฟล์
            XLSX.writeFile(wb, "รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน.xlsx");
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>