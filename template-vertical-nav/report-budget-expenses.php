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
                        <h4>รายงานการใช้จ่ายงบประมาณตามแผนงาน</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการใช้จ่ายงบประมาณตามแผนงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการใช้จ่ายงบประมาณตามแผนงาน</h4>
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
                                                <th class="align-middle" rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                                <th class="align-middle" rowspan="2">เสาหลัก/ยุทธศาสตร์/กลยุทธ์</th>
                                                <th class="align-middle" rowspan="2">กรอบวงเงินงบประมาณ (บาท)</th>
                                                <th class="align-middle" rowspan="2">งบประมาณที่ได้รับการจัดสรร (บาท)</th>
                                                <th class="align-middle" rowspan="2">งบประมาณที่ใช้ (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot id="reportTableFooter">
                                            <!-- แถวรวมจะถูกเพิ่มที่นี่ -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let totalBudgetAmount = 0;
        let totalAllocatedBudget = 0;
        let totalActualSpendAmount = 0;
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
                totalBudgetAmount = 0
                totalAllocatedBudget = 0
                totalActualSpendAmount = 0
                let faculty = $(this).val();
                let apiname = 'get_fac_budget_expenses';
                console.log(faculty);
                if (faculty === 'KKU Strategic Dept') {
                    apiname = 'get_kku_budget_expenses'

                } else if (!faculty) {
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = '';
                    const tableFooter = document.querySelector('#reportTableFooter');
                    tableFooter.innerHTML = "";
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "../server/api.php",
                    data: {
                        'command': apiname,
                        'faculty': faculty
                    },
                    dataType: "json",
                    success: function(response) {

                        console.log(response);
                        const tableBody = document.querySelector('#reportTable tbody');
                        tableBody.innerHTML = ''; // ล้างข้อมูลเก่า



                        response.plan.forEach((row, index) => {
                            if (row.parent == "F00T1-Strategic") {
                                console.log(1);
                                const ch = response.plan.filter(item => item.parent === row.pillar_id);
                                console.log(ch);
                                const parseValue = (value) => {
                                    if (value === null || value === undefined) {
                                        return 0;
                                    }
                                    if (typeof value === 'string') {
                                        const number = parseFloat(value.replace(/,/g, ''));
                                        return isNaN(number) ? 0 : number;
                                    } else if (typeof value === 'number') {
                                        // ถ้า value เป็นตัวเลขอยู่แล้ว ส่งคืนค่านั้น
                                        return value;
                                    }
                                    // สำหรับกรณีอื่นๆ คืนค่า 0
                                    return 0;
                                };
                                const sums = ch.reduce((acc, item) => {
                                    return {
                                        Budget_Amount: acc.Budget_Amount + parseValue(item.Budget_Amount ?? '0'),
                                        Allocated_budget: acc.Allocated_budget + parseValue(item.Allocated_budget ?? '0'),
                                        Actual_Spend_Amount: acc.Actual_Spend_Amount + parseValue(item.Actual_Spend_Amount ?? '0')
                                    };
                                }, {
                                    Budget_Amount: 0,
                                    Allocated_budget: 0,
                                    Actual_Spend_Amount: 0

                                });
                                totalBudgetAmount += sums.Budget_Amount;
                                totalAllocatedBudget += sums.Allocated_budget;
                                totalActualSpendAmount += sums.Actual_Spend_Amount;
                                if (index + 1 == 1) {
                                    var str = '<tr><td nowrap style="text-align: left;">' + $('#dropdown1 option:selected').text() + '</td>' +
                                        '<td nowrap style="text-align: left;">' + row.pillar_name + '</td>' +
                                        '<td style="text-align: right;">' + (parseFloat(sums.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td>' +
                                        '<td style="text-align: right;">' + (parseFloat(sums.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td>' +
                                        '<td style="text-align: right;">' + (parseFloat(sums.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td></tr>';
                                    tableBody.insertAdjacentHTML('beforeend', str);
                                } else {
                                    var str = '<tr><td >' +
                                        '<td nowrap style="text-align: left;">' + row.pillar_name + '</td>' +
                                        '<td style="text-align: right;">' + (parseFloat(sums.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td>' +
                                        '<td style="text-align: right;">' + (parseFloat(sums.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td>' +
                                        '<td style="text-align: right;">' + (parseFloat(sums.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td></tr>';
                                    tableBody.insertAdjacentHTML('beforeend', str);
                                }

                            } else {
                                var str = '<tr><td >' +
                                    '<td nowrap style="text-align: left;">' + '&nbsp;'.repeat(8) + row.pillar_name + '</td>' +
                                    '<td style="text-align: right;">' + (parseFloat(row.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td>' +
                                    '<td style="text-align: right;">' + (parseFloat(row.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td>' +
                                    '<td style="text-align: right;">' + (parseFloat(row.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</td></tr>';
                                tableBody.insertAdjacentHTML('beforeend', str);
                            }

                        });

                        // เพิ่มแถวผลรวมไปยัง <tfoot>
                        const tableFooter = document.querySelector('#reportTableFooter');
                        tableFooter.innerHTML = "";
                        // เพิ่มแถวใน footer สำหรับผลรวม
                        const footerRow = document.createElement('tr');
                        const createCell = (text, align = "left") => {
                            const td = document.createElement("td");
                            td.textContent = text;
                            td.style.textAlign = align; // กำหนดการจัดตำแหน่งข้อความ
                            return td;
                        };

                        const footerTd1 = document.createElement('td');
                        footerTd1.textContent = 'รวม';
                        footerTd1.colSpan = 2;
                        footerRow.appendChild(footerTd1);


                        const footerTd2 = createCell(Number(totalBudgetAmount).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }), "right");
                        footerRow.appendChild(footerTd2);


                        const footerTd3 = createCell(Number(totalAllocatedBudget).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }), "right");
                        footerRow.appendChild(footerTd3);

                        const footerTd4 = createCell(Number(totalActualSpendAmount).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }), "right");
                        footerRow.appendChild(footerTd4);



                        tableFooter.appendChild(footerRow);


                    },
                    error: function(jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });
        });



        function exportCSV() {
            const table = document.getElementById('reportTable');
            const csvRows = [];

            // วนลูปที่แต่ละแถว (thead, tbody, tfoot)
            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const row = table.rows[rowIndex];
                const rowData = [];

                // เช็คว่าเป็นแถวสุดท้าย (tfoot) หรือไม่
                if (rowIndex === table.rows.length - 1) {
                    // สำหรับแถว tfoot ให้เลื่อนข้อมูล
                    const text1 = row.cells[0] ? row.cells[0].innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length))
                        .replace(/<br\s*\/?>/gi, ' ')
                        .replace(/<\/?[^>]+>/g, '').trim() : '';

                    const text2 = ''; // ให้ col2 เป็น "ค่าว่าง" (empty string)

                    const text3 = row.cells[1] ? row.cells[1].innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length))
                        .replace(/<br\s*\/?>/gi, ' ')
                        .replace(/<\/?[^>]+>/g, '').trim() : '';

                    const text4 = row.cells[2] ? row.cells[3].innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length))
                        .replace(/<br\s*\/?>/gi, ' ')
                        .replace(/<\/?[^>]+>/g, '').trim() : '';

                    const text5 = row.cells[3] ? row.cells[3].innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length))
                        .replace(/<br\s*\/?>/gi, ' ')
                        .replace(/<\/?[^>]+>/g, '').trim() : '';

                    // เลื่อนข้อมูลจาก col2 ไป col3, col3 ไป col4, col4 ไป col5
                    rowData.push(`"${text1}"`); // col1
                    rowData.push(`"${text2}"`); // col2 เป็นค่าว่าง
                    rowData.push(`"${text3}"`); // col3 (ข้อมูลจากเดิม col2)
                    rowData.push(`"${text4}"`); // col4 (ข้อมูลจากเดิม col3)
                    rowData.push(`"${text5}"`); // col5 (ข้อมูลจากเดิม col4)
                } else {
                    // สำหรับแถวอื่นๆ (thead, tbody) ไม่ทำการ merge
                    for (const cell of row.cells) {
                        let text = cell.innerHTML
                            .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length))
                            .replace(/<br\s*\/?>/gi, ' ')
                            .replace(/<\/?[^>]+>/g, '').trim();

                        // Escape double quotes
                        text = text.replace(/"/g, '""');
                        // ครอบด้วย ""
                        rowData.push(`"${text}"`);
                    }
                }

                // เพิ่มแถวข้อมูลลงใน csvRows
                csvRows.push(rowData.join(','));
            }

            // สร้าง CSV และ BOM
            const csvContent = "\uFEFF" + csvRows.join("\n");

            // สร้าง Blob และดาวน์โหลดไฟล์ CSV
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานการใช้จ่ายงบประมาณตามแผนงาน.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
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
                        cellWidth: 50
                    },
                    1: {
                        cellWidth: 150
                    },
                    2: {
                        cellWidth: 30
                    },
                    3: {
                        cellWidth: 30
                    },
                    4: {
                        cellWidth: 30
                    },
                },
                didDrawPage: function(data) {
                    // Header
                    doc.setFontSize(16);
                    doc.text('รายงานการใช้จ่ายงบประมาณตามแผนงาน', 14, 15);

                    // Footer (แสดงแค่ที่ท้ายตาราง)
                    const pageSize = doc.internal.pageSize;
                    const pageWidth = pageSize.width;
                    const pageHeight = pageSize.height;

                    // ข้อความ footer (แก้หมายเลขหน้า)
                    const currentPage = doc.internal.getCurrentPageInfo().pageNumber;
                    const totalPages = doc.internal.getNumberOfPages();

                    doc.setFontSize(10);
                    doc.text(
                        'หน้า ' + currentPage + ' จาก ' + totalPages,
                        pageWidth - 20,
                        pageHeight - 10, {
                            align: 'right'
                        }
                    );
                },
                // Footer row content for table with background color #F2F2F2
                foot: [
                    [{
                            content: 'รวม',
                            colSpan: 2,

                        },
                        {
                            content: totalActualSpendAmount,

                        },
                        {
                            content: totalBudgetAmount,

                        },
                        {
                            content: totalAllocatedBudget,

                        }
                    ]
                ],
                didParseCell: function(data) {
                    // Center align all header cells
                    if (data.section === 'head') {
                        data.cell.styles.halign = 'center';
                        data.cell.styles.valign = 'middle';
                        data.cell.styles.cellPadding = 1;
                    }

                    // Center align all body cells except the second column (ส่วนงาน/หน่วยงาน)
                    if (data.section === 'body') {
                        if (data.column.index === 2 || data.column.index === 3 || data.column.index === 4) {
                            data.cell.styles.halign = 'right';
                        } else {
                            data.cell.styles.halign = 'left';
                        }
                    }

                    // Style footer row
                    if (data.section === 'foot') {
                        data.cell.styles.fontStyle = 'bold';
                        data.cell.styles.fillColor = '#dbe5f1'; // Add the background color for the footer cells
                        data.cell.styles.textColor = [0, 0, 0];
                        if (data.column.index === 0) {
                            data.cell.styles.halign = 'center'; // Align the first and last columns to center
                        } else {
                            data.cell.styles.halign = 'right'; // Align other columns to right
                        }
                    }
                },

                margin: {
                    top: 25,
                    right: 7,
                    bottom: 25,
                    left: 7
                },
                tableWidth: 'auto'
            });

            // Save the PDF
            doc.save('รายงานการใช้จ่ายงบประมาณตามแผนงาน.pdf');
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

            // ============ ส่วนที่ 3: ประมวลผล TFOOT (รองรับ Merge) ============ 
            let tfootRows = [];
            let tfootMerges = [];
            let tfoot = table.tFoot;

            if (tfoot && tfoot.rows.length > 0) {
                const parsedTfoot = parseTfoot(tfoot);
                tfootRows = parsedTfoot.tfootRows; // เปลี่ยนชื่อเป็น tfootRows
                tfootMerges = parsedTfoot.tfootMerges; // เปลี่ยนชื่อเป็น tfootMerges
            }

            // รวมทุกแถว (thead + tbody + tfoot)
            const allRows = [...theadRows, ...tbodyRows, ...tfootRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead และ tfoot ลงใน sheet
            ws['!merges'] = [...theadMerges, ...tfootMerges];

            // กำหนดให้ Header (thead) อยู่กึ่งกลาง
            theadRows.forEach((row, rowIndex) => {
                row.forEach((_, colIndex) => {
                    const cellAddress = XLSX.utils.encode_cell({
                        r: rowIndex,
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
            link.download = 'รายงานการใช้จ่ายงบประมาณตามแผนงาน.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }


        function parseTfoot(tfoot) {
            const tfootRows = [];
            const tfootMerges = [];

            if (!tfoot) {
                return {
                    tfootRows,
                    tfootMerges
                };
            }

            // Map กันการเขียนทับ merge
            const skipMap = {};

            for (let rowIndex = 0; rowIndex < tfoot.rows.length; rowIndex++) {
                const tr = tfoot.rows[rowIndex];
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
                        .replace(/<br\s*\/?>/gi, ' ') // ถ้ามี <br/> ใน tfoot ก็เปลี่ยนเป็นช่องว่าง (ไม่แตกแถว)
                        .replace(/<\/?[^>]+>/g, '') // ลบ tag อื่น ถ้าเหลือ
                        .trim();

                    rowData[colIndex] = text;

                    // ดู rowSpan/colSpan
                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        // Push merges object
                        tfootMerges.push({
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
                tfootRows.push(rowData);
            }

            return {
                tfootRows,
                tfootMerges
            };
        }



        /**
         * -----------------------
         * 1) parseThead: รองรับ merge
         * -----------------------
         * - ใช้ skipMap จัดการ colSpan/rowSpan
         * - ไม่แยก <br/> เป็นแถวใหม่ (โดยทั่วไป header ไม่ต้องแตกแถว)
         * - ถ้า thead มีหลาย <tr> ก็จะได้หลาย row
         * - return: { theadRows: [][] , theadMerges: [] }
         */
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