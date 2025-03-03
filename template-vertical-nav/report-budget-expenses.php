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
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>เสาหลัก/ยุทธศาสตร์/กลยุทธ์</th>
                                                <th>กรอบวงเงินงบประมาณ (บาท)</th>
                                                <th>งบประมาณที่ได้รับการจัดสรร (บาท)</th>
                                                <th>งบประมาณที่ใช้ (บาท)</th>
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
                    response.fac.forEach((row) =>{
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="'+row.fcode+'">'+row.faculty+'</option>');
                    });   
                }
            });


            $('#dropdown1').change(function() {
                let faculty = $(this).val();
                console.log(faculty);
                $.ajax({
                    type: "POST",
                    url: "../server/api.php",
                    data: {
                        'command': 'get_kku_budget_expenses',
                        'faculty':faculty
                    },
                    dataType: "json",
                    success: function(response) {
                        
                        console.log(response);
                        const tableBody = document.querySelector('#reportTable tbody');
                        tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                        
                        
                        response.plan.forEach((row, index) => {
                            if( row.parent=="F00T1-Strategic"){
                                console.log(1);
                                const ch = response.plan.filter(item =>item.parent === row.pillar_id);
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
                                        Budget_Amount: acc.Budget_Amount + parseValue(item.Budget_Amount??'0'),
                                        Allocated_budget: acc.Allocated_budget + parseValue(item.Allocated_budget??'0'),
                                        Actual_Spend_Amount: acc.Actual_Spend_Amount + parseValue(item.Actual_Spend_Amount??'0')
                                    };
                                }, {
                                    Budget_Amount: 0, Allocated_budget: 0, Actual_Spend_Amount: 0
                                    
                                });
                                if(index+1==1){
                                    var str='<tr><td nowrap style="text-align: left;">'+$('#dropdown1 option:selected').text()+'</td>'+
                                        '<td nowrap style="text-align: left;">'+row.pillar_name+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(sums.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(sums.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(sums.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td></tr>';
                                    tableBody.insertAdjacentHTML('beforeend', str);
                                }else{
                                    var str='<tr><td >'+
                                        '<td nowrap style="text-align: left;">'+row.pillar_name+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(sums.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(sums.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(sums.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td></tr>';
                                    tableBody.insertAdjacentHTML('beforeend', str);
                                }
                                
                            }
                            else{
                                var str='<tr><td >'+
                                        '<td nowrap style="text-align: left;">'+'&nbsp;'.repeat(8)+row.pillar_name+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(row.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(row.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td style="text-align: right;">'+(parseFloat(row.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td></tr>';
                                tableBody.insertAdjacentHTML('beforeend', str);
                            }
                            
                        });

                        
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

        // วนลูปทีละ <tr>
        for (const row of table.rows) {
            // เก็บบรรทัดย่อยของแต่ละเซลล์
            const cellLines = [];
            let maxSubLine = 1;

            // วนลูปทีละเซลล์ <td>/<th>
            for (const cell of row.cells) {
                let html = cell.innerHTML;

                // 1) แปลง &nbsp; ติดกันให้เป็น non-breaking space (\u00A0) ตามจำนวน
                html = html.replace(/(&nbsp;)+/g, (match) => {
                    const count = match.match(/&nbsp;/g).length;
                    return '\u00A0'.repeat(count); // ex. 3 &nbsp; → "\u00A0\u00A0\u00A0"
                });

                // 2) แปลง <br/> เป็น \n เพื่อแตกเป็นแถวใหม่ใน CSV
                html = html.replace(/<br\s*\/?>/gi, '\n');

                // 3) (ถ้าต้องการ) ลบ tag HTML อื่นออก
                // html = html.replace(/<\/?[^>]+>/g, '');

                // 4) แยกเป็น array บรรทัดย่อย
                const lines = html.split('\n').map(x => x.trimEnd());
                // ใช้ trimEnd() เฉพาะท้าย ไม่ trim ต้นเผื่อบางคนอยากเห็นช่องว่างนำหน้า

                if (lines.length > maxSubLine) {
                    maxSubLine = lines.length;
                }

                cellLines.push(lines);
            }

            // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
            for (let i = 0; i < maxSubLine; i++) {
                const rowData = [];

                // วนลูปแต่ละเซลล์
                for (const lines of cellLines) {
                    let text = lines[i] || ''; // ถ้าไม่มีบรรทัดที่ i ก็ว่าง
                    // Escape double quotes
                    text = text.replace(/"/g, '""');
                    // ครอบด้วย ""
                    text = `"${text}"`;
                    rowData.push(text);
                }

                csvRows.push(rowData.join(','));
            }
        }

        // รวมเป็น CSV + BOM
        const csvContent = "\uFEFF" + csvRows.join("\n");
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
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
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', [305, 215.9]); // Legal landscape size

    // เพิ่มฟอนต์ภาษาไทย
    doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
    doc.addFont("THSarabun.ttf", "THSarabun", "normal");
    doc.setFont("THSarabun");

    // จัดการตาราง
    doc.autoTable({
        html: '#reportTable',
        startY: 25,
        theme: 'grid',
        styles: {
            font: "THSarabun",
            fontSize: 8,
            cellPadding: 1.5,
            lineWidth: 0.1,
            lineColor: [0, 0, 0],
            minCellHeight: 6
        },
        headStyles: {
            fillColor: [220, 230, 241],
            textColor: [0, 0, 0],
            fontSize: 9,
            fontStyle: 'bold',
            halign: 'center',
            valign: 'middle',
            minCellHeight: 12
        },
        columnStyles: {
            0: { cellWidth: 50 },
            1: { cellWidth: 150 },
            2: { cellWidth: 30 },
            3: { cellWidth: 30 },
            4: { cellWidth: 30 }
        },
        margin: { top: 25, right: 7, bottom: 15, left: 7 },
        tableWidth: 'auto',
        
        didParseCell: function (data) {
            // ตรวจจับ rowspan
            if (data.cell.rowSpan > 1) {
                data.cell.styles.valign = 'middle'; // จัดให้อยู่ตรงกลาง
                data.cell.styles.halign = 'center';
            }

            // จัดแนวข้อมูล
            if (data.section === 'head') {
                data.cell.styles.halign = 'center';
            }
            if (data.section === 'body') {
                if ([2, 3, 4].includes(data.column.index)) {
                    data.cell.styles.halign = 'right';
                } else {
                    data.cell.styles.halign = 'left';
                }
            }
        },

        didDrawCell: function (data) {
            // ตรวจจับเซลล์ที่ข้ามหน้าใหม่
            if (data.row.section === 'body' && data.cell.raw.hasAttribute('rowspan')) {
                let cellY = data.cell.y;
                let pageHeight = doc.internal.pageSize.height;

                // ถ้าตำแหน่งเซลล์ต่ำกว่าขอบล่างของหน้า ให้ขึ้นหน้าใหม่
                if (cellY + data.cell.height > pageHeight - 20) {
                    doc.addPage();
                    doc.autoTable.previous.finalY = 25; // รีเซ็ตตำแหน่ง Y
                }
            }
        },

        didDrawPage: function (data) {
            // หัวกระดาษ
            doc.setFontSize(16);
            doc.text('รายงานการใช้จ่ายงบประมาณตามแผนงาน', 14, 15);

            // หมายเลขหน้า
            doc.setFontSize(10);
            doc.text(
                'หน้า ' + doc.internal.getCurrentPageInfo().pageNumber + ' จาก ' + doc.internal.getNumberOfPages(),
                doc.internal.pageSize.width - 20,
                doc.internal.pageSize.height - 10, {
                    align: 'right'
                }
            );
        }
    });

    // บันทึกเป็นไฟล์ PDF
    doc.save('รายงานการใช้จ่ายงบประมาณ.pdf');
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

            const allRows = [...theadRows, ...tbodyRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet
            ws['!merges'] = theadMerges;

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