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
    overflow: hidden; /* Prevent body scrolling */
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
    top: 45px; /* Adjust height based on previous row */
    background: #f4f4f4;
    z-index: 999;
}

thead tr:nth-child(3) th {
    position: sticky;
    top: 105px; /* Adjust height based on previous rows */
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
                        <h4>รายงานสรุปแผนกรอบอัตรากำลัง 4 ปีแยกตามประเภท</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปแผนกรอบอัตรากำลัง 4 ปีแยกตามประเภท</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปแผนกรอบอัตรากำลัง 4 ปีแยกตามประเภท</h4>
                                </div>
                                <label for="dropdown1">เลือกส่วนงาน:</label>
                                <select name="dropdown1" id="dropdown1">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <br/>
                                <label for="dropdown2">ปีเริ่มต้น - ปีสิ้นสุด:</label>
                                <select name="dropdown2" id="dropdown2" disabled>
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <br/>
                                
                                <!-- Submit Button -->
                                <button id="submitBtn" disabled>Submit</button>
                                <br/><br/>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">ที่</th>
                                            <th rowspan="2">ส่วนงาน</th>
                                            <th colspan="6">ประเภทบริหาร</th>
                                            <th colspan="6">ประเภทวิชาการ</th>
                                            <th colspan="6">ประเภทวิจัย</th>
                                            <th colspan="6">ประเภทสนับสนุน</th>
                                        </tr>
                                        <tr>
                                            <!-- ประเภทบริหาร -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
                                            <!-- ประเภทวิชาการ -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
                                            <!-- ประเภทวิจัย -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
                                            <!-- ประเภทสนับสนุน -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
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
        let all_data;
        $(document).ready(function() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_framework-summary'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response.wf;                                             
                    const fac = [...new Set(all_data.map(item => item.pname))];
                    let dropdown = document.getElementById("dropdown1");
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
            
        });
        $('#dropdown1').change(function() {
            $('#dropdown2').html('<option value="">เลือกปีเริ่มต้น</option>').prop('disabled', true);
            $('#submitBtn').prop('disabled', true);

            $('#dropdown2').append('<option value="all">2567 - 2571</option>').prop('disabled', false);
        });
        
        $('#dropdown2').change(function() {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });
        $('#submitBtn').click(function() {
            let category = document.getElementById("dropdown1").value;
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
            if(category=="all"){
                data=all_data
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
             
            data.forEach((row, index) => {                   
                const tr = document.createElement('tr');

                const columns = [
                        { key: 'No', value: index+1 },
                        { key: 'Alias_Default', value: row.Alias_Default },
                        
                        { key: 'Actual_type1', value: (row.Actual_type1||0).toLocaleString() },
                        { key: 'wf1', value: 0},
                        { key: 'wf_type1_y1', value: (row.wf_type1_y1||0).toLocaleString() },
                        { key: 'wf_type2_y1', value: (row.wf_type2_y1||0).toLocaleString() },
                        { key: 'wf_type3_y1', value: (row.wf_type3_y1||0).toLocaleString() },
                        { key: 'wf_type4_y1', value: (row.wf_type4_y1||0).toLocaleString() },
                        
                        { key: 'Actual_type2', value: (row.Actual_type2||0).toLocaleString() },
                        { key: 'wf2', value:0},
                        { key: 'wf_type1_y2', value: (row.wf_type1_y2||0).toLocaleString() },
                        { key: 'wf_type2_y2', value: (row.wf_type2_y2||0).toLocaleString() },
                        { key: 'wf_type3_y2', value: (row.wf_type3_y2||0).toLocaleString() },
                        { key: 'wf_type4_y2', value: (row.wf_type4_y2||0).toLocaleString() },

                        { key: 'Actual_type3', value: (row.Actual_type3||0).toLocaleString() },
                        { key: 'wf3', value: 0},
                        { key: 'wf_type1_y3', value: (row.wf_type1_y3||0).toLocaleString() },
                        { key: 'wf_type2_y3', value: (row.wf_type2_y3||0).toLocaleString() },
                        { key: 'wf_type3_y3', value: (row.wf_type3_y3||0).toLocaleString() },
                        { key: 'wf_type4_y3', value: (row.wf_type4_y3||0).toLocaleString() },

                        { key: 'Actual_type4', value: (row.Actual_type4||0).toLocaleString() },
                        { key: 'wf4', value: 0},
                        { key: 'wf_type1_y4', value: (row.wf_type1_y4||0).toLocaleString() },
                        { key: 'wf_type2_y4', value: (row.wf_type2_y4||0).toLocaleString() },
                        { key: 'wf_type3_y4', value: (row.wf_type3_y4||0).toLocaleString() },
                        { key: 'wf_type4_y4', value: (row.wf_type4_y4||0).toLocaleString() },
                    ];

                columns.forEach(col => {
                    const td = document.createElement('td');
                    td.textContent = col.value;
                    tr.appendChild(td);
                });
                tableBody.appendChild(tr);     
            });
            calculateSum();
                
        });
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

        footer.innerHTML='';
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

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            // Add Thai font
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");
            doc.setFontSize(12);
            doc.text("รายงานสรุปแผนกรอบอัตรากำลัง 4 ปีแยกตามประเภท", 10, 10);
            doc.autoTable({
                html: '#reportTable',
                startY: 20,
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
                didParseCell: function(data) {
                    if (data.section === 'body' && data.column.index === 0) {
                        data.cell.styles.halign = 'left'; // จัด text-align left สำหรับคอลัมน์แรก
                    }
                },
                margin: { top: 15, right: 5, bottom: 10, left: 5 },
                tableWidth: 'auto'
            });
            doc.save('รายงานสรุปแผนกรอบอัตรากำลัง 4 ปีแยกตามประเภท.pdf');
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
                            s: { r: rowIndex, c: colIndex },                 // จุดเริ่ม (start)
                            e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 } // จุดจบ (end)
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
</body>

</html>