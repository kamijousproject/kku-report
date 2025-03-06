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
                        <h4>รายงานสถานะของแผนงานแต่ละแผน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสถานะของแผนงานแต่ละแผน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสถานะของแผนงานแต่ละแผน</h4>
                                </div>
                                <label for="selectcategory">เลือกส่วนงาน:</label>
                                <select name="selectcategory" id="selectcategory" onchange="selectFilter()">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th class="align-middle" rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">ยุทธศาสตร์</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">แผนงาน/โครงการ</th>
                                                <th colspan="4">สถานะ (Status)</th>
                                                <th class="align-middle" rowspan="2">ปัญหาอุปสรรค/แนวทางแก้ไขปัญหา/ข้อเสนอแนะ</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th>ยังไม่ดำเนินการ</th>
                                                <th>อยู่ระหว่างดำเนินการ</th>
                                                <th>ดำเนินการแล้ว</th>
                                                <th>ยกเลิก</th>
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
                    'command': 'get_kku_planing_status'
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
            let previousFacultyName = '';
            let previousSIName = '';
            let previousSICode = '';
            let previousKSPName = '';

            data.forEach(row => {
                const tr = document.createElement('tr');

                // สำหรับ si_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                const td1 = document.createElement('td');
                td1.style.textAlign = "left";
                td1.textContent = row.fa_name === previousFacultyName ? '' : row.fa_name;
                tr.appendChild(td1);

                // สำหรับ so_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                const td2 = document.createElement('td');
                td2.style.textAlign = "left";
                td2.textContent = row.si_code === previousSICode ? '' : row.si_code;
                tr.appendChild(td2);

                const td3 = document.createElement('td');
                td3.style.textAlign = "left";
                td3.textContent = row.si_name === previousSIName ? '' : row.si_name;
                tr.appendChild(td3);

                const td4 = document.createElement('td');
                td4.style.textAlign = "left";
                td4.textContent = row.Strategic_Project;
                tr.appendChild(td4);

                const td5 = document.createElement('td');
                td5.style.textAlign = "left";
                td5.textContent = row.ksp_name;
                tr.appendChild(td5);

                if (row.Progress_Status === "Not Started") {
                    const td6 = document.createElement('td');
                    td6.style.textAlign = "left";
                    td6.innerHTML = row.Strategic_Project_Progress_Details;
                    tr.appendChild(td6);

                    const td7 = document.createElement('td');
                    td7.style.textAlign = "left";
                    td7.innerHTML = ``;
                    tr.appendChild(td7);

                    const td8 = document.createElement('td');
                    td8.style.textAlign = "left";
                    td8.innerHTML = ``;
                    tr.appendChild(td8);

                    const td9 = document.createElement('td');
                    td9.style.textAlign = "left";
                    td9.innerHTML = ``;
                    tr.appendChild(td9);
                }

                if (row.Progress_Status === "In Progress") {
                    const td6 = document.createElement('td');
                    td6.style.textAlign = "left";
                    td6.innerHTML = ``;
                    tr.appendChild(td6);

                    const td7 = document.createElement('td');
                    td7.style.textAlign = "left";
                    td7.innerHTML = row.Strategic_Project_Progress_Details;
                    tr.appendChild(td7);

                    const td8 = document.createElement('td');
                    td8.style.textAlign = "left";
                    td8.innerHTML = ``;
                    tr.appendChild(td8);

                    const td9 = document.createElement('td');
                    td9.style.textAlign = "left";
                    td9.innerHTML = ``;
                    tr.appendChild(td9);
                }

                if (row.Progress_Status === "Completed") {
                    const td6 = document.createElement('td');
                    td6.style.textAlign = "left";
                    td6.innerHTML = ``;
                    tr.appendChild(td6);

                    const td7 = document.createElement('td');
                    td7.style.textAlign = "left";
                    td7.innerHTML = ``;
                    tr.appendChild(td7);

                    const td8 = document.createElement('td');
                    td8.style.textAlign = "left";
                    td8.innerHTML = row.Strategic_Project_Progress_Details;
                    tr.appendChild(td8);

                    const td9 = document.createElement('td');
                    td9.style.textAlign = "left";
                    td9.innerHTML = ``;
                    tr.appendChild(td9);
                }

                if (row.Progress_Status === "Cancelled") {
                    const td6 = document.createElement('td');
                    td6.style.textAlign = "left";
                    td6.innerHTML = ``;
                    tr.appendChild(td6);

                    const td7 = document.createElement('td');
                    td7.style.textAlign = "left";
                    td7.innerHTML = ``;
                    tr.appendChild(td7);

                    const td8 = document.createElement('td');
                    td8.style.textAlign = "left";
                    td8.innerHTML = ``;
                    tr.appendChild(td8);

                    const td9 = document.createElement('td');
                    td9.style.textAlign = "left";
                    td9.innerHTML = row.Strategic_Project_Progress_Details;
                    tr.appendChild(td9);
                }

                if (!row.Progress_Status) {
                    const td6 = document.createElement('td');
                    td6.style.textAlign = "left";
                    td6.innerHTML = ``;
                    tr.appendChild(td6);

                    const td7 = document.createElement('td');
                    td7.style.textAlign = "left";
                    td7.innerHTML = ``;
                    tr.appendChild(td7);

                    const td8 = document.createElement('td');
                    td8.style.textAlign = "left";
                    td8.innerHTML = ``;
                    tr.appendChild(td8);

                    const td9 = document.createElement('td');
                    td9.style.textAlign = "left";
                    td9.innerHTML = ``;
                    tr.appendChild(td9);
                }
                const td10 = document.createElement('td');
                td10.style.textAlign = "left";
                td10.innerHTML = row.Obstacles;
                tr.appendChild(td10);


                tableBody.appendChild(tr);
                // เก็บค่า fa_name และ so_name ของแถวนี้ไว้ใช้ในการเปรียบเทียบในแถวถัดไป
                previousFacultyName = row.fa_name;
                previousSICode = row.si_code;
                previousSIName = row.si_name;
            });
        }

        // function exportCSV() {
        //     const rows = [];
        //     const table = document.getElementById('reportTable');
        //     for (let row of table.rows) {
        //         const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
        //         rows.push(cells.join(","));
        //     }
        //     const csvContent = "\uFEFF" + rows.join("\n"); // Add BOM
        //     const blob = new Blob([csvContent], {
        //         type: 'text/csv;charset=utf-8;'
        //     });
        //     const url = URL.createObjectURL(blob);
        //     const link = document.createElement('a');
        //     link.setAttribute('href', url);
        //     link.setAttribute('download', 'รายงาน.csv');
        //     link.style.visibility = 'hidden';
        //     document.body.appendChild(link);
        //     link.click();
        //     document.body.removeChild(link);
        // }



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
                        cellWidth: 30
                    },
                    1: {
                        cellWidth: 15
                    },
                    2: {
                        cellWidth: 30
                    },
                    3: {
                        cellWidth: 15
                    },
                    4: {
                        cellWidth: 50
                    },
                    5: {
                        cellWidth: 30
                    },
                    6: {
                        cellWidth: 30
                    },
                    7: {
                        cellWidth: 30
                    },
                    8: {
                        cellWidth: 30
                    },
                    8: {
                        cellWidth: 30
                    },

                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(16);
                    doc.text('รายงานสถานะของแผนงานแต่ละแผน', 14, 15);

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
                        data.cell.styles.halign = 'left';
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
            doc.save('รายงานสถานะของแผนงานแต่ละแผน.pdf');
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

            // สร้างตาราง 2D สำหรับเก็บข้อมูล CSV (+1 เพื่อเพิ่มแถวใหม่ด้านบน)
            let csvMatrix = Array.from({
                length: numRows + 1
            }, () => Array(maxCols).fill('""'));

            // ✅ เพิ่ม "text" ใน cell แรกของ CSV
            csvMatrix[0][0] = `"รายงานสถานะของแผนงานแต่ละแผน"`;

            // ใช้ตัวแปรตรวจสอบว่า cell ไหนถูก merge ไปแล้ว
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

                    let text = cell.innerText.trim().replace(/"/g, '""'); // Escape double quotes

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    // ✅ ขยับ index ข้อมูลลง 1 แถว เพื่อรองรับแถว "text"
                    csvMatrix[rowIndex + 1][colIndex] = `"${text}"`;

                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            cellMap[rowIndex + r][colIndex + c] = true;

                            if (r !== 0 || c !== 0) {
                                csvMatrix[rowIndex + r + 1][colIndex + c] = '""';
                            }
                        }
                    }

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
            link.download = 'รายงานสถานะของแผนงานแต่ละแผน.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }



        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const {
                theadRows,
                theadMerges
            } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            // ============ ส่วนที่ 3: ข้อความพิเศษในแถวแรก (row0) ============
            const row0 = ['รายงานสถานะของแผนงานแต่ละแผน']; // เพิ่มข้อความพิเศษที่แถวแรก

            // สร้าง allRows โดยให้ row0 เป็นแถวแรก
            const allRows = [row0, ...theadRows, ...tbodyRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet
            // ทำการย้ายการ merge เพื่อไม่ให้กระทบกับ row0
            ws['!merges'] = theadMerges.map(merge => ({
                s: {
                    r: merge.s.r + 1,
                    c: merge.s.c
                }, // เลื่อนแถวที่ merge ลงไป 1
                e: {
                    r: merge.e.r + 1,
                    c: merge.e.c
                } // เลื่อนแถวที่ merge ลงไป 1
            }));

            // กำหนดให้ Header (thead) อยู่กึ่งกลาง
            theadRows.forEach((row, rowIndex) => {
                row.forEach((_, colIndex) => {
                    const cellAddress = XLSX.utils.encode_cell({
                        r: rowIndex + 1, // เริ่มจากแถวที่ 1 เพื่อไม่ให้ซ้ำกับ row0
                        c: colIndex
                    });
                    if (!ws[cellAddress]) return;
                    ws[cellAddress].s = {
                        alignment: {
                            horizontal: "center",
                            vertical: "center"
                        }, // จัดให้อยู่กึ่งกลาง
                        font: {
                            bold: true
                        } // ทำให้ header ตัวหนา
                    };
                });
            });

            // ตั้งค่าความกว้างของคอลัมน์ให้พอดีกับเนื้อหา
            ws['!cols'] = new Array(theadRows[0].length).fill({
                wch: 15
            });

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์เป็น .xls
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xls',
                type: 'array'
            });

            // สร้าง Blob + ดาวน์โหลดไฟล์
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสถานะของแผนงานแต่ละแผน.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function parseThead(thead) {
            const theadRows = [];
            const theadMerges = [];

            if (!thead) {
                return {
                    theadRows,
                    theadMerges
                };
            }

            // Map กันการเขียนทับ merge
            const skipMap = {};

            for (let rowIndex = 0; rowIndex < thead.rows.length; rowIndex++) {
                const tr = thead.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    // ข้ามเซลล์ที่ถูก merge ครอบไว้
                    while (skipMap[`${rowIndex},${colIndex}`]) {
                        rowData[colIndex] = "";
                        colIndex++;
                    }

                    const cell = tr.cells[cellIndex];
                    // ไม่แยก <br/> → แค่แทน &nbsp; เป็น space
                    let text = cell.innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length)) // &nbsp; => spaces
                        .replace(/<br\s*\/?>/gi, ' ') // ถ้ามี <br/> ใน thead ก็เปลี่ยนเป็นช่องว่าง (ไม่แตกแถว)
                        .replace(/<\/?[^>]+>/g, '') // ลบ tag อื่น ถ้าเหลือ
                        .trim();

                    rowData[colIndex] = text;

                    // ดู rowSpan/colSpan
                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        // Push merges object
                        theadMerges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            },
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            }
                        });

                        // Mark skipMap
                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r === 0 && c === 0) continue;
                                skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                            }
                        }
                    }
                    colIndex++;
                }
                theadRows.push(rowData);
            }

            return {
                theadRows,
                theadMerges
            };
        }

        /**
         * -----------------------
         * 2) parseTbody: แตก <br/> เป็นหลาย sub-row
         * -----------------------
         * - ไม่ทำ merge (ตัวอย่าง) เพื่อความง่าย
         * - ถ้าใน tbody มี colSpan/rowSpan ต้องประยุกต์ skipMap ต่อเอง
         */
        function parseTbody(tbody) {
            const rows = [];

            if (!tbody) return rows;

            for (const tr of tbody.rows) {
                // เก็บ sub-lines ของแต่ละเซลล์
                const cellLines = [];
                let maxSubLine = 1;

                for (const cell of tr.cells) {
                    // (a) แปลง &nbsp; → space ตามจำนวน
                    // (b) แปลง <br/> → \n เพื่อนำไป split เป็นหลายบรรทัด
                    let html = cell.innerHTML.replace(/(&nbsp;)+/g, match => {
                        const count = match.match(/&nbsp;/g).length;
                        return ' '.repeat(count);
                    });
                    html = html.replace(/<br\s*\/?>/gi, '\n');

                    // (c) ลบแท็กอื่น ๆ (ถ้าต้องการ)
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // (d) split ด้วย \n → ได้หลาย sub-lines
                    const lines = html.split('\n').map(x => x.trimEnd());
                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }
                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];
                    for (const lines of cellLines) {
                        rowData.push(lines[i] || ''); // ถ้าไม่มีบรรทัด => ใส่ว่าง
                    }
                    rows.push(rowData);
                }
            }

            return rows;
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>