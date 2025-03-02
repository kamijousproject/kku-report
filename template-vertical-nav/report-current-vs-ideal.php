<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        <h4>รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน</th>
                                                <th>Job Family</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>กรอบพึงมี</th>
                                                <th>อัตราปัจจุบัน</th>
                                                <th>ขาด / เกิน</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body">
                                            
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
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.28/dist/jspdf.plugin.autotable.min.js"></script>


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
                    'command': 'kku_wf_current-vs-ideal'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response.wf;                        
                    const fac = [...new Set(all_data.map(item => item.Alias_Default))];
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
            //let resultDiv = document.getElementById("result");
      
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // Clear old data

            let prevAlias = null;
            let prevName = null;
            let aliasRowSpan = {};
            let nameRowSpan = {};
            let data;
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.Alias_Default===category);
            }
            // **Step 1: Calculate Rowspan Counts Before Rendering**
            data.forEach(row => {
                let aliasKey = row.Alias_Default;
                let nameKey = row.code;

                // Count occurrences for Alias_Default (Column 2)
                if (!aliasRowSpan[aliasKey]) {
                    aliasRowSpan[aliasKey] = all_data.filter(r => r.Alias_Default === aliasKey).length;
                }
                if (!nameRowSpan[aliasKey]) {
                    nameRowSpan[aliasKey] = {}; // สร้าง object ว่างสำหรับ fac
                }
                
                // Count occurrences for Name (Column 3), but only within the same Alias_Default
                if (!nameRowSpan[nameKey]) {
                    nameRowSpan[aliasKey][nameKey] = all_data.filter(r => r.Alias_Default === aliasKey && r.code === nameKey).length;
                }
            });
            console.log(nameRowSpan);
            // **Step 2: Generate Table Rows**
            data.forEach((row, index) => {                   
                const tr = document.createElement('tr');
                var sym = parseInt(row.count_person) > parseInt(row.wf) 
                    ? "+" + (parseInt(row.count_person) - parseInt(row.wf)) 
                    : "-" + Math.abs(parseInt(row.count_person) - parseInt(row.wf));

                let currentAlias = row.Alias_Default;
                let currentName = row.code;

                // **Step 3: Always Add "No" Column (Index)**
                const tdNo = document.createElement('td');
                tdNo.textContent = index + 1;
                tr.appendChild(tdNo);

                // **Step 4: Create Table Cells with Rowspan Handling**
                if (currentAlias !== prevAlias) {
                    const tdAlias = document.createElement('td');
                    tdAlias.textContent = currentAlias;
                    tdAlias.rowSpan = aliasRowSpan[currentAlias]; // Apply Rowspan
                    tr.appendChild(tdAlias);
                    prevAlias = currentAlias; // Update previous alias
                }

                if (currentName !== prevName) {
                    const tdName = document.createElement('td');
                    tdName.textContent = row.name;
                    tdName.rowSpan = nameRowSpan[currentAlias][currentName]; // Apply Rowspan
                    tr.appendChild(tdName);
                    prevName = currentName; // Update previous name
                }

                // **Step 5: Ensure Proper Column Order**
                const tdPosition = document.createElement('td');
                tdPosition.textContent = row.position;
                tr.appendChild(tdPosition);

                const tdC1 = document.createElement('td');
                tdC1.textContent = row.wf;
                tr.appendChild(tdC1);

                const tdC2 = document.createElement('td');
                tdC2.textContent = row.count_person;
                tr.appendChild(tdC2);

                const tdC3 = document.createElement('td');
                tdC3.textContent = sym;
                tr.appendChild(tdC3);

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
    
    function prepareTableData() {
            const table = document.getElementById('reportTable');
            const headers = [];
            const body = [];
            
            // ดึงข้อมูลส่วนหัวตาราง
            const headerRow = table.querySelector('thead tr');
            headerRow.querySelectorAll('th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            
            // ดึงข้อมูลและจัดการกับ rowspan/colspan
            const rows = table.querySelectorAll('tbody tr');
            let rowspanTracker = {};
            
            rows.forEach((row, rowIndex) => {
                const rowData = [];
                let dataIndex = 0;
                
                // เพิ่มเซลล์ที่มี rowspan จากแถวก่อนหน้า
                for (let i = 0; i < headers.length; i++) {
                    if (rowspanTracker[i] && rowspanTracker[i].count > 0) {
                        rowData.push({
                            content: rowspanTracker[i].content,
                            rowSpan: 0, // นับไปแล้วในแถวก่อนหน้า
                            colSpan: 1
                        });
                        rowspanTracker[i].count--;
                        dataIndex++;
                    }
                }
                
                // ประมวลผลเซลล์ในแถวปัจจุบัน
                row.querySelectorAll('td').forEach((cell, cellIndex) => {
                    // หาตำแหน่งที่ถูกต้องโดยพิจารณา rowspan
                    while (rowData[dataIndex] !== undefined) {
                        dataIndex++;
                    }
                    
                    const rowspan = parseInt(cell.getAttribute('rowspan')) || 1;
                    const colspan = parseInt(cell.getAttribute('colspan')) || 1;
                    
                    rowData[dataIndex] = {
                        content: cell.textContent.trim(),
                        rowSpan: rowspan,
                        colSpan: colspan
                    };
                    
                    // ติดตาม rowspan สำหรับแถวต่อไป
                    if (rowspan > 1) {
                        rowspanTracker[dataIndex] = {
                            content: cell.textContent.trim(),
                            count: rowspan - 1
                        };
                    }
                    
                    dataIndex += colspan;
                });
                
                body.push(rowData);
            });
            
            return { headers, body };
        }

        // ฟังก์ชันสร้าง PDF ด้วย jsPDF และ AutoTable
        function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');

    // Add Thai font
    doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
    doc.addFont("THSarabun.ttf", "THSarabun", "normal");
    doc.setFont("THSarabun");
    doc.setFontSize(12);
    doc.text('รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง', 14, 10);
    // ก่อนสร้างตาราง เราต้องแปลงข้อมูลจากตาราง HTML
    const tableElement = document.getElementById('reportTable');
    const tableRows = Array.from(tableElement.querySelectorAll('tr'));
    
    // สร้างข้อมูลสำหรับ autoTable โดยรวมค่า rowspan ด้วย
    const tableData = [];
    const tableHeaders = [];
    
    // ดึงข้อมูลหัวตาราง
    const headerCells = tableRows[0].querySelectorAll('th');
    headerCells.forEach(cell => {
        tableHeaders.push(cell.textContent.trim());
    });
    
    // สร้างอาร์เรย์เก็บข้อมูลการรวมแถว (rowspan)
    let spanningCells = {}; // เก็บข้อมูล cell ที่มีการรวมแถว
    
    // แปลงข้อมูลแถวให้เป็นรูปแบบที่ autoTable รองรับ
    for (let i = 1; i < tableRows.length; i++) { // เริ่มที่ 1 เพื่อข้ามหัวตาราง
        const row = tableRows[i];
        const cells = row.querySelectorAll('td');
        const rowData = {};
        
        let cellIndex = 0;
        for (let j = 0; j < headerCells.length; j++) {
            // ตรวจสอบว่ามี cell ที่ถูก span มาจากแถวก่อนหน้าหรือไม่
            if (spanningCells[j] && spanningCells[j].rowsLeft > 0) {
                rowData[tableHeaders[j]] = spanningCells[j].content;
                spanningCells[j].rowsLeft--;
                continue;
            }
            
            // ถ้าไม่มี cell ที่ถูก span มา ให้ใช้ cell ปัจจุบัน
            const cell = cells[cellIndex++];
            if (!cell) continue; // กรณีไม่มี cell นี้ให้ข้าม
            
            const content = cell.textContent.trim();
            rowData[tableHeaders[j]] = content;
            
            // บันทึกข้อมูล rowspan หากมี
            if (cell.hasAttribute('rowspan')) {
                const rowSpan = parseInt(cell.getAttribute('rowspan'));
                if (rowSpan > 1) {
                    spanningCells[j] = {
                        content: content,
                        rowsLeft: rowSpan - 1
                    };
                }
            }
        }
        
        tableData.push(rowData);
    }

    doc.autoTable({
        head: [tableHeaders],
        body: tableData.map(row => tableHeaders.map(header => row[header] || '')),
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
        willDrawCell: function(data) {
            // ตรวจสอบว่าเป็นเซลล์ที่ต้องซ่อนเส้นขอบด้านล่างหรือไม่ (เนื่องจากมี rowspan)
            if (data.section === 'body') {
                const rowIndex = data.row.index;
                const colIndex = data.column.index;
                
                // ถ้าเซลล์นี้เป็นส่วนหนึ่งของ rowspan ให้ซ่อนเส้นขอบด้านล่าง
                if (spanningCells[colIndex] && spanningCells[colIndex].rowsLeft > 0 && 
                    rowIndex < tableData.length - 1) {
                    // ซ่อนเส้นขอบด้านล่าง
                    data.cell.styles.lineWidth = [0.1, 0.1, 0, 0.1]; // [top, right, bottom, left]
                }
            }
        },
        margin: { top: 15, right: 5, bottom: 10, left: 5 },
        tableWidth: 'auto'
    });
    doc.save('รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง.pdf');
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
                            font: isHeader ? {  } : {} // **ทำให้ Header ตัวหนา**
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
            XLSX.writeFile(wb, 'รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>