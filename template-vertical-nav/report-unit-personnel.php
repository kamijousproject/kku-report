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
                        <h4>รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">ที่</th>
                                                <th rowspan="3">ส่วนงาน/หน่วยงาน</th>
                                                <th colspan="3">ข้าราชการ</th>
                                                <th >ลูกจ้างประจำ</th>
                                                <th colspan="10">พนักงานมหาวิทยาลัยงบประมาณแผ่นดิน</th>
                                                <th colspan="10">พนักงานมหาวิทยาลัยงบประมาณเงินรายได้</th>
                                                <th colspan="7">ลูกจ้างของมหาวิทยาลัย</th>
                                            </tr>
                                            <tr>
                                                <th >วิชาการ</th>
                                                <th >สนับสนุน</th>
                                                <th rowspan="2">รวม</th>
                                                <th >สนับสนุน</th>
                                                <th >บริหาร</th>
                                                <th colspan="2">วิชาการ</th>
                                                <th colspan="2">วิจัย</th>
                                                <th colspan="2">สนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th rowspan="2">รวมทั้งหมด</th>
                                                <th >บริหาร</th>
                                                <th colspan="2">วิชาการ</th>
                                                <th colspan="2">วิจัย</th>
                                                <th colspan="2">สนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th rowspan="2">รวมทั้งหมด</th>
                                                <th colspan="2">วิจัย</th>
                                                <th colspan="2">สนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th rowspan="2">รวมทั้งหมด</th>
                                            </tr>
                                            <tr>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
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
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
        $(document).ready(function() {
            laodData();
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'list-faculty'
                },
                dataType: "json",
                success: function(response) {
                    let dropdown = document.getElementById("category");
                    dropdown.innerHTML = '<option value="">-- Select --</option>';
                    response.wf.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category.Parent;
                        option.textContent = category.Alias_Default;
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
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_unit-personnel'
                },
                dataType: "json",
                success: function(response) {
                    let category = document.getElementById("category").value;
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                        let data=response.wf.filter(r => r.parent === category)
                        data.forEach((row, index) => {                   
                            const tr = document.createElement('tr');

                            const columns = [
                                { key: 'No', value: index+1 },
                                { key: 'Alias_Default', value: row.Alias_Default },
                                
                                { key: 'c1', value: row.c1 },
                                { key: 'c2', value: row.c2 },
                                { key: 'sum1', value: parseInt(row.c1)+parseInt(row.c2) },
                                
                                { key: 'c3', value: row.c3 },
                                
                                { key: 'c4', value: row.c4 },
                                { key: 'c5', value: row.c5 },
                                { key: 'c6', value: row.c6 },
                                { key: 'c7', value: row.c7 },
                                { key: 'c8', value: row.c8 },
                                { key: 'c9', value: row.c9 },
                                { key: 'c10', value: row.c10 },
                                { key: 'sum2', value: parseInt(row.c4)+parseInt(row.c5)+parseInt(row.c7)+parseInt(row.c9) },
                                { key: 'sum3', value: parseInt(row.c6)+parseInt(row.c8)+parseInt(row.c10) },
                                { key: 'sum4', value: parseInt(row.c4)+parseInt(row.c5)+parseInt(row.c6)+parseInt(row.c7)+parseInt(row.c8)+parseInt(row.c9)+parseInt(row.c10) },
                                
                                { key: 'c11', value: row.c11 },
                                { key: 'c12', value: row.c12 },
                                { key: 'c13', value: row.c13 },
                                { key: 'c14', value: row.c14 },                                
                                { key: 'c15', value: row.c15 },
                                { key: 'c16', value: row.c16 },                               
                                { key: 'c17', value: row.c17 },
                                { key: 'sum5', value: parseInt(row.c11)+parseInt(row.c12)+parseInt(row.c14)+parseInt(row.c16) },
                                { key: 'sum6', value: parseInt(row.c13)+parseInt(row.c15)+parseInt(row.c17) },
                                { key: 'sum7', value: parseInt(row.c11)+parseInt(row.c12)+parseInt(row.c13)+parseInt(row.c14)+parseInt(row.c15)+parseInt(row.c16)+parseInt(row.c17) },

                                { key: 'c18', value: row.c18 },                                
                                { key: 'c19', value: row.c19 },
                                { key: 'c20', value: row.c20 },                               
                                { key: 'c21', value: row.c21 },
                                { key: 'sum8', value: parseInt(row.c18)+parseInt(row.c20) },
                                { key: 'sum9', value: parseInt(row.c19)+parseInt(row.c21)},
                                { key: 'sum10', value: parseInt(row.c18)+parseInt(row.c19)+parseInt(row.c20)+parseInt(row.c21)},
                                
                                
                            ];

                            columns.forEach(col => {
                                const td = document.createElement('td');
                                td.textContent = col.value;
                                tr.appendChild(td);
                            });


                            tableBody.appendChild(tr);
                            
                        });
                        calculateSum();

                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
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
                sums[index - 2] += parseFloat(cell.textContent) || 0;
            }
            });
        });

        // เพิ่มผลรวมลงใน footer
        sums.forEach(sum => {
            footerRow.innerHTML += `<td>${sum}</td>`;
        });

        // เพิ่มแถว footer ลงในตาราง
        footer.appendChild(footerRow);
        }
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
            link.download = 'report.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function exportPDF() {
            const { jsPDF } = window.jspdf;
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
                    0: { cellWidth: 8 },  // ที่
                    1: { cellWidth: 35 }, // ส่วนงาน/หน่วยงาน
                    // ข้าราชการ
                    2: { cellWidth: 8 },  // วิชาการ
                    3: { cellWidth: 8 },  // สนับสนุน
                    4: { cellWidth: 8 },  // รวม
                    // ลูกจ้างประจำ
                    5: { cellWidth: 8 },  // สนับสนุน
                    // พนักงานมหาวิทยาลัยงบประมาณแผ่นดิน
                    6: { cellWidth: 8 },  // บริหาร
                    7: { cellWidth: 8 },  // วิชาการ-คนครอง
                    8: { cellWidth: 8 },  // วิชาการ-อัตราว่าง
                    9: { cellWidth: 8 },  // วิจัย-คนครอง
                    10: { cellWidth: 8 }, // วิจัย-อัตราว่าง
                    11: { cellWidth: 8 }, // สนับสนุน-คนครอง
                    12: { cellWidth: 8 }, // สนับสนุน-อัตราว่าง
                    13: { cellWidth: 8 }, // รวม-คนครอง
                    14: { cellWidth: 8 }, // รวม-อัตราว่าง
                    15: { cellWidth: 8 }, // รวมทั้งหมด
                    // พนักงานมหาวิทยาลัยงบประมาณเงินรายได้
                    16: { cellWidth: 8 }, // บริหาร
                    17: { cellWidth: 8 }, // วิชาการ-คนครอง
                    18: { cellWidth: 8 }, // วิชาการ-อัตราว่าง
                    19: { cellWidth: 8 }, // วิจัย-คนครอง
                    20: { cellWidth: 8 }, // วิจัย-อัตราว่าง
                    21: { cellWidth: 8 }, // สนับสนุน-คนครอง
                    22: { cellWidth: 8 }, // สนับสนุน-อัตราว่าง
                    23: { cellWidth: 8 }, // รวม-คนครอง
                    24: { cellWidth: 8 }, // รวม-อัตราว่าง
                    25: { cellWidth: 8 }, // รวมทั้งหมด
                    // ลูกจ้างของมหาวิทยาลัย
                    26: { cellWidth: 8 }, // วิจัย-คนครอง
                    27: { cellWidth: 8 }, // วิจัย-อัตราว่าง
                    28: { cellWidth: 8 }, // สนับสนุน-คนครอง
                    29: { cellWidth: 8 }, // สนับสนุน-อัตราว่าง
                    30: { cellWidth: 8 }, // รวม-คนครอง
                    31: { cellWidth: 8 }, // รวม-อัตราว่าง
                    32: { cellWidth: 8 }  // รวมทั้งหมด
                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(16);
                    doc.text('รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน', 14, 15);
                    
                    // Add footer with page number
                    doc.setFontSize(10);
                    doc.text(
                        'หน้า ' + doc.internal.getCurrentPageInfo().pageNumber + ' จาก ' + doc.internal.getNumberOfPages(),
                        doc.internal.pageSize.width - 20, 
                        doc.internal.pageSize.height - 10,
                        { align: 'right' }
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
                        if (data.column.index !== 1) {
                            data.cell.styles.halign = 'center';
                        }
                        // Left align the ส่วนงาน/หน่วยงาน column
                        if (data.column.index === 1) {
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
                margin: { top: 25, right: 7, bottom: 15, left: 7 },
                tableWidth: 'auto'
            });

            // Save the PDF
            doc.save('รายงานอัตรากำลัง.pdf');
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
                rowData[colIndex] = {
                    v: cellText,
                    s: {
                        alignment: {
                            vertical: "top",
                            horizontal: "left"
                        }
                    }
                };

                const rowspan = cell.rowSpan || 1;
                const colspan = cell.colSpan || 1;

                if (rowspan > 1 || colspan > 1) {
                    merges.push({
                        s: { r: rowIndex, c: colIndex },
                        e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 }
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
        XLSX.writeFile(wb, 'report.xlsx');
    }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>