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
                        <h4>รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</h4>
                                </div>
                                <div class="info-section">
                                    <label for="dropdown1">ส่วนงาน:</label>
                                    <select id="dropdown1">
                                        <option value="">-- เลือกส่วนงาน --</option>
                                    </select>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th class="align-middle" rowspan="4">ลำดับ</th>
                                                <th class="align-middle" rowspan="4">รหัส</th>
                                                <th class="align-middle" rowspan="4">ส่วนงาน/หน่วยงาน</th>
                                                <th class="align-middle" rowspan="4">จำนวนผลลัพธ์/ตัวชี้วัดทั้งหมด</th>
                                                <th class="align-middle" colspan="19">ความสอดคล้องของแผน</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th class="align-middle" colspan="13">แผนยุทธศาสตร์การบริหารมหาวิทยาลัยขอนแก่น</th>
                                                <th class="align-middle" colspan="2">แผนพันธกิจ</th>
                                                <th class="align-middle" colspan="2">แผนสรรหา</th>
                                                <th class="align-middle" colspan="2">แผนสร้างความโดดเด่น</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th class="align-middle" rowspan="2">จำนวน</th>
                                                <th class="align-middle" rowspan="2">ร้อยละ</th>
                                                <th class="align-middle" colspan="11">ยุทธศาสตร์</th>
                                                <th class="align-middle" rowspan="2">จำนวน</th>
                                                <th class="align-middle" rowspan="2">ร้อยละ</th>
                                                <th class="align-middle" rowspan="2">จำนวน</th>
                                                <th class="align-middle" rowspan="2">ร้อยละ</th>
                                                <th class="align-middle" rowspan="2">จำนวน</th>
                                                <th class="align-middle" rowspan="2">ร้อยละ</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
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
        $(document).ready(function() {
            $.ajax({
                type: "POST",
                url: "../server/api.php",
                data: {
                    'command': 'get_faculty_action_plan'
                },
                dataType: "json",
                success: function(response) {

                    response.fac.forEach((row) => {
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="' + row.fcode + '">' + row.faculty + '</option>');
                    });
                }
            });

            $('#dropdown1').change(function() {
                let faculty = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "../server/api.php",
                    data: {
                        'command': 'get_department-indicators',
                        'faculty': faculty
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response.plan);
                        const tableBody = document.querySelector('#reportTable tbody');
                        tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                        response.plan.forEach((row, index) => {
                            const tr = document.createElement('tr');
                            var sum1 = parseInt(row.s1) + parseInt(row.s2) + parseInt(row.s3) + parseInt(row.s4) +
                                parseInt(row.s5) + parseInt(row.s6) + parseInt(row.s7) + parseInt(row.s8) + parseInt(row.s9) +
                                parseInt(row.s10) + parseInt(row.s11);
                            const columns = [{
                                    key: 'No',
                                    value: index + 1
                                },
                                {
                                    key: 'fac_code',
                                    value: (row.Alias_Default).substring(0, 2)
                                },
                                {
                                    key: 'fac',
                                    value: row.Alias_Default.replace(/^(\d{5}) - /, '')
                                },
                                {
                                    key: 'count_okr',
                                    value: parseInt(row.count_okr).toLocaleString()
                                },
                                {
                                    key: 'sum1',
                                    value: parseInt(sum1).toLocaleString()
                                },
                                {
                                    key: 'avg1',
                                    value: (parseFloat((parseFloat(sum1) * 100) / parseFloat(row.count_okr) || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + "%"
                                },
                                {
                                    key: 's1',
                                    value: parseInt(row.s1).toLocaleString()
                                },
                                {
                                    key: 's2',
                                    value: parseInt(row.s2).toLocaleString()
                                },
                                {
                                    key: 's3',
                                    value: parseInt(row.s3).toLocaleString()
                                },
                                {
                                    key: 's4',
                                    value: parseInt(row.s4).toLocaleString()
                                },
                                {
                                    key: 's5',
                                    value: parseInt(row.s5).toLocaleString()
                                },
                                {
                                    key: 's6',
                                    value: parseInt(row.s6).toLocaleString()
                                },
                                {
                                    key: 's7',
                                    value: parseInt(row.s7).toLocaleString()
                                },
                                {
                                    key: 's8',
                                    value: parseInt(row.s8).toLocaleString()
                                },
                                {
                                    key: 's9',
                                    value: parseInt(row.s9).toLocaleString()
                                },
                                {
                                    key: 's10',
                                    value: parseInt(row.s10).toLocaleString()
                                },
                                {
                                    key: 's11',
                                    value: parseInt(row.s11).toLocaleString()
                                },
                                {
                                    key: 'p1',
                                    value: (parseInt(row.count_okr) - sum1).toLocaleString()
                                },
                                {
                                    key: 'p1',
                                    value: (parseFloat(((parseFloat(row.count_okr) - sum1) * 100) / parseFloat(row.count_okr) || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + "%"
                                },
                                {
                                    key: 'dev_plan',
                                    value: parseInt(row.dev_plan).toLocaleString()
                                },
                                {
                                    key: 'avg2',
                                    value: (parseFloat((parseFloat(row.dev_plan) * 100) / parseFloat(row.count_okr) || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + "%"
                                },
                                {
                                    key: 'divis',
                                    value: parseInt(row.divis).toLocaleString()
                                },
                                {
                                    key: 'avg3',
                                    value: (parseFloat((parseFloat(row.divis) * 100) / parseFloat(row.count_okr) || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + "%"
                                },
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
            });
        });


        // function exportCSV() {
        //     const table = document.getElementById('reportTable');
        //     const csvRows = [];

        //     // วนลูปทีละ <tr>
        //     for (const row of table.rows) {
        //         // เก็บบรรทัดย่อยของแต่ละเซลล์
        //         const cellLines = [];
        //         let maxSubLine = 1;

        //         // วนลูปทีละเซลล์ <td>/<th>
        //         for (const cell of row.cells) {
        //             let html = cell.innerHTML;

        //             // 1) แปลง &nbsp; ติดกันให้เป็น non-breaking space (\u00A0) ตามจำนวน
        //             html = html.replace(/(&nbsp;)+/g, (match) => {
        //                 const count = match.match(/&nbsp;/g).length;
        //                 return '\u00A0'.repeat(count); // ex. 3 &nbsp; → "\u00A0\u00A0\u00A0"
        //             });

        //             // 2) แปลง <br/> เป็น \n เพื่อแตกเป็นแถวใหม่ใน CSV
        //             html = html.replace(/<br\s*\/?>/gi, '\n');

        //             // 3) (ถ้าต้องการ) ลบ tag HTML อื่นออก
        //             // html = html.replace(/<\/?[^>]+>/g, '');

        //             // 4) แยกเป็น array บรรทัดย่อย
        //             const lines = html.split('\n').map(x => x.trimEnd());
        //             // ใช้ trimEnd() เฉพาะท้าย ไม่ trim ต้นเผื่อบางคนอยากเห็นช่องว่างนำหน้า

        //             if (lines.length > maxSubLine) {
        //                 maxSubLine = lines.length;
        //             }

        //             cellLines.push(lines);
        //         }

        //         // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
        //         for (let i = 0; i < maxSubLine; i++) {
        //             const rowData = [];

        //             // วนลูปแต่ละเซลล์
        //             for (const lines of cellLines) {
        //                 let text = lines[i] || ''; // ถ้าไม่มีบรรทัดที่ i ก็ว่าง
        //                 // Escape double quotes
        //                 text = text.replace(/"/g, '""');
        //                 // ครอบด้วย ""
        //                 text = `"${text}"`;
        //                 rowData.push(text);
        //             }

        //             csvRows.push(rowData.join(','));
        //         }
        //     }

        //     // รวมเป็น CSV + BOM
        //     const csvContent = "\uFEFF" + csvRows.join("\n");
        //     const blob = new Blob([csvContent], {
        //         type: 'text/csv;charset=utf-8;'
        //     });
        //     const url = URL.createObjectURL(blob);
        //     const link = document.createElement('a');
        //     link.href = url;
        //     link.download = 'report.csv';
        //     document.body.appendChild(link);
        //     link.click();
        //     document.body.removeChild(link);
        //     URL.revokeObjectURL(url);
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
                        cellWidth: 10
                    },
                    1: {
                        cellWidth: 10
                    },
                    2: {
                        cellWidth: 30
                    },
                    3: {
                        cellWidth: 25
                    },
                    4: {
                        cellWidth: 13
                    },
                    5: {
                        cellWidth: 13
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
                        cellWidth: 10
                    },
                    10: {
                        cellWidth: 10
                    },
                    11: {
                        cellWidth: 10
                    },
                    12: {
                        cellWidth: 10
                    },
                    13: {
                        cellWidth: 10
                    },
                    14: {
                        cellWidth: 10
                    },
                    15: {
                        cellWidth: 10
                    },
                    16: {
                        cellWidth: 10
                    },
                    17: {
                        cellWidth: 13
                    },
                    18: {
                        cellWidth: 13
                    },
                    19: {
                        cellWidth: 13
                    },
                    20: {
                        cellWidth: 13
                    },
                    21: {
                        cellWidth: 13
                    },
                    22: {
                        cellWidth: 13
                    },

                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(16);
                    doc.text('รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน', 14, 15);

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
                        const leftAlignedColumns = [2];

                        if (leftAlignedColumns.includes(data.column.index)) {
                            data.cell.styles.halign = 'left';
                        } else {
                            data.cell.styles.halign = 'right';
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
            doc.save('รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน.pdf');
        }

        // function exportXLS() {
        //     const table = document.getElementById('reportTable');

        //     // เก็บข้อมูลแต่ละแถวเป็น Array ของ Array
        //     const rows = [];
        //     // เก็บ Merge (colSpan/rowSpan) ในรูปแบบ SheetJS
        //     const merges = {};

        //     // ใช้ object เก็บว่าส่วนใดถูก merge ไปแล้ว เพื่อเลี่ยงการซ้ำซ้อน
        //     // key = "rowIndex,colIndex" => true/false
        //     const skipMap = {};

        //     for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
        //         const tr = table.rows[rowIndex];
        //         const rowData = [];
        //         let colIndex = 0;

        //         for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
        //             // ข้ามเซลล์ที่อยู่ในพื้นที่ merge แล้ว
        //             while (skipMap[`${rowIndex},${colIndex}`]) {
        //                 rowData.push("");
        //                 colIndex++;
        //             }

        //             const cell = tr.cells[cellIndex];
        //             // เอา innerText หรือจะใช้ innerHTML แปลงเองก็ได้
        //             let cellText = cell.innerText.trim();

        //             // ใส่ข้อมูลลงใน Array
        //             rowData[colIndex] = cellText;

        //             // ตรวจสอบ colSpan / rowSpan
        //             const rowspan = cell.rowSpan || 1;
        //             const colspan = cell.colSpan || 1;

        //             // ถ้ามีการ Merge จริง (มากกว่า 1)
        //             if (rowspan > 1 || colspan > 1) {
        //                 // สร้าง object merge ตามรูปแบบ SheetJS
        //                 const mergeRef = {
        //                     s: {
        //                         r: rowIndex,
        //                         c: colIndex
        //                     }, // จุดเริ่ม (start)
        //                     e: {
        //                         r: rowIndex + rowspan - 1,
        //                         c: colIndex + colspan - 1
        //                     } // จุดจบ (end)
        //                 };

        //                 // เก็บลง merges (รูปแบบเก่าคือ ws['!merges'] = [])
        //                 // แต่ต้องรอใส่หลังสร้าง Worksheet ด้วย SheetJS
        //                 // จึงบันทึกชั่วคราวใน merges พร้อม index
        //                 const mergeKey = `merge_${rowIndex}_${colIndex}`;
        //                 merges[mergeKey] = mergeRef;

        //                 // Mark skipMap กันซ้ำ
        //                 for (let r = 0; r < rowspan; r++) {
        //                     for (let c = 0; c < colspan; c++) {
        //                         if (!(r === 0 && c === 0)) {
        //                             skipMap[`${rowIndex + r},${colIndex + c}`] = true;
        //                         }
        //                     }
        //                 }
        //             }

        //             colIndex++;
        //         }
        //         rows.push(rowData);
        //     }

        //     // สร้าง Workbook
        //     const wb = XLSX.utils.book_new();
        //     // แปลง Array เป็น Worksheet
        //     const ws = XLSX.utils.aoa_to_sheet(rows);

        //     // ใส่ merges เข้า Worksheet (Array)
        //     ws['!merges'] = Object.values(merges);

        //     // เพิ่มชีทใน Workbook
        //     XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        //     // เขียนไฟล์เป็น XLS (BIFF8)
        //     // ใช้ { bookType: 'xls', type: 'array' } เพื่อได้ Buffer Array
        //     const excelBuffer = XLSX.write(wb, {
        //         bookType: 'xls',
        //         type: 'array'
        //     });

        //     // สร้าง Blob เป็นไฟล์ XLS
        //     const blob = new Blob([excelBuffer], {
        //         type: 'application/vnd.ms-excel'
        //     });

        //     // ดาวน์โหลดไฟล์
        //     const url = URL.createObjectURL(blob);
        //     const link = document.createElement('a');
        //     link.href = url;
        //     link.download = 'รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน.xls'; // ชื่อไฟล์ .xls
        //     document.body.appendChild(link);
        //     link.click();
        //     document.body.removeChild(link);
        //     URL.revokeObjectURL(url);
        // }

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

                    // ถ้าข้อความเป็นตัวเลขที่มีศูนย์หน้า (เช่น 04) ให้ใช้วิธีการเก็บแบบข้อความ
                    if (/^0\d+$/.test(text)) {
                        text = `="${text}"`; // ใช้เครื่องหมาย = ทำให้ Excel รับรู้ว่าเป็นข้อความ
                    }

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    // ใส่ค่าข้อมูลในตำแหน่งเริ่มต้นของเซลล์
                    csvMatrix[rowIndex][colIndex] = text;

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

                    colIndex += colspan;
                }
            }

            // สร้างเนื้อหาของ CSV โดยใช้ BOM (Byte Order Mark) เพื่อให้แน่ใจว่า Excel อ่านเป็น UTF-8
            const csvContent = "\uFEFF" + csvMatrix.map(row => row.join(',')).join('\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน.csv';
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
            XLSX.writeFile(wb, "รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน.xlsx");
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>