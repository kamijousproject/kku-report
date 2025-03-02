<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>     
#main-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
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
                        <h4>รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ</h4>
                                    
                                </div>
                                <label for="dropdown1">ประเภทการจัดสรร:</label>
                                <select name="dropdown1" id="dropdown1" >
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <br/>
                                <label for="dropdown2">เลือกส่วนงาน:</label>
                                <select name="dropdown2" id="dropdown2"  disabled>
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
                                                <th nowrap>ลำดับที่</th>
                                                <th nowrap>ประเภท<br/>การจัดสรร</th>
                                                <th>ส่วนงาน</th>
                                                <th>หน่วยงาน</th>
                                                <th nowrap>ชื่อ - นามสกุล</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ประเภทการจ้าง</th>
                                                <th nowrap>เลขประจำ<br/>ตำแหน่ง</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>จำนวนจัดสรร</th>
                                                <th>Job Family</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>กลุ่มบุคลากร</th>
                                                <th>ประเภทสัญญา</th>
                                                <th nowrap>ระยะเวลา<br/>สัญญา</th>
                                                <th nowrap>คุณวุฒิ<br/>ของตำแหน่ง</th>
                                                <th>เงินเดือน</th>
                                                <th>แหล่งงบประมาณ</th>
                                                <th nowrap>งบประมาณ<br/>แผ่นดิน</th>
                                                <th nowrap>งบประมาณ<br/>เงินรายได้คณะ</th>
                                                <th nowrap>งบประมาณ<br/>เงินรายได้ สนอ</th>
                                                <th nowrap>สถานที่<br/>ปฏิบัติงาน</th>
                                                <th nowrap>ระยะเวลา<br/>การจ้าง</th>
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
        $(document).ready(function() {
            let all_data;
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_annual-allocation'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response.wf;
                    const type = [...new Set(response.wf.map(item => item.TYPE))];
                    type.forEach((row) =>{
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="'+row+'">'+row+'</option>');
                    });   
                }
            });
            $('#dropdown1').change(function() {
                let type = $(this).val();
                //console.log(year);
                $('#dropdown2').html('<option value="">-- Loading Categories --</option>').prop('disabled', true)               
                $('#submitBtn').prop('disabled', true);
                var d1=all_data.filter(item=>item.TYPE===type);
                const fac = [...new Set(d1.map(item => item.parent_name))];
                fac.forEach((row) =>{
                    $('#dropdown2').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);                   
                });   
                    
            });
            $('#dropdown2').change(function() {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });


            $('#submitBtn').click(function() {
                let type = $("#dropdown1").val();
                let fac = $("#dropdown2").val();
                const tableBody = document.querySelector('#reportTable tbody');
                tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
                var data=all_data.filter(item=>item.TYPE===type && item.parent_name===fac);
                data.forEach((row, index) => {                   
                    const tr = document.createElement('tr');

                    const columns = [
                        { key: 'No', value: index+1 },
                        { key: 'TYPE', value: row.TYPE },
                        { key: 'parent_name', value: row.parent_name },
                        { key: 'fac', value: row.fac },      
                        { key: 'Workers_Name_Surname', value: row.Workers_Name_Surname2 },                                                            
                        { key: 'Personnel_Type', value: row.Personnel_Type },                              
                        { key: 'Employment_Type', value: row.Employment_Type2 }, 
                        { key: 'Position_Number', value: row.Position_Number2 }, 
                        { key: 'POSITION', value: row.POSITION },
                        { key: 'num', value: row.num },
                        { key: 'Job_Family', value: row.Job_Family2 },   
                        { key: 'All_PositionTypes', value: row.All_PositionTypes },
                        { key: 'Personnel_Group', value: row.Personnel_Group2 },
                        { key: 'Contract_Type', value: row.Contract_Type2 }, 
                        { key: 'period', value: row.Contract_Period_Short_Term },                             
                        { key: 'Position_Qualifications', value: row.Position_Qualifications2 },    
                        { key: 'Salary_rate', value: row.Salary_rate },
                        { key: 'Fund_FT', value: row.Fund_FT },
                        { key: 'Govt_Fund', value: (parseFloat(row.Govt_Fund|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { key: 'Division_Revenue', value: (parseFloat(row.Division_Revenue|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { key: 'OOP_Central_Revenue', value: (parseFloat(row.OOP_Central_Revenue|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },                          
                        { key: 'Location_Code', value: row.Location_Code2 },
                        { key: 'Contract_Period_Short_Term', value: row.Hiring_Start_End_Date2 },                                                                       
                    ];

                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = col.value;
                        tr.appendChild(td);
                    });
                    tableBody.appendChild(tr);     
                });
            });
        });

        
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
                    cellPadding: { top: 1, right: 1, bottom: 1, left: 1 },
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
                    0: { cellWidth: 8 },  // ที่
                    1: { cellWidth: 13 }, // ส่วนงาน/หน่วยงาน
                    2: { cellWidth: 20 }, // ชื่อตำแหน่ง
                    3: { cellWidth: 20 }, // ประเภทตำแหน่ง
                    4: { cellWidth: 15 }, // Job Family
                    // ปีงบประมาณ พ.ศ. 2567
                    5: { cellWidth: 15 },  // ข้าราชการ
                    6: { cellWidth: 12},  // พนง.มข. แผ่นดิน
                    7: { cellWidth: 12 },  // พนง.มข. รายได้
                    8: { cellWidth: 12 },  // ลูกจ้างประจำ
                    9: { cellWidth: 10 },  // ลูกจ้างของ มข.
                    10: { cellWidth: 15 }, // รวม
                    // ปีงบประมาณ พ.ศ. 2568
                    11: { cellWidth: 12 }, // ข้าราชการ
                    12: { cellWidth: 12 }, // พนง.มข. แผ่นดิน
                    13: { cellWidth: 12 }, // พนง.มข. รายได้
                    14: { cellWidth: 12 }, // ลูกจ้างประจำ
                    15: { cellWidth: 12 }, // ลูกจ้างของ มข.
                    16: { cellWidth: 10 }, // รวม
                    // ปีงบประมาณ พ.ศ. 2569
                    17: { cellWidth: 12 }, // ข้าราชการ
                    18: { cellWidth: 10 }, // พนง.มข. แผ่นดิน
                    19: { cellWidth: 10 }, // พนง.มข. รายได้
                    20: { cellWidth: 10 }, // ลูกจ้างประจำ
                    21: { cellWidth: 11 }, // ลูกจ้างของ มข.
                    22: { cellWidth: 11}, // รวม
                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(14);
                    doc.text('รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ', 20, 10);
                    
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
                margin: { top: 15, right: 5, bottom: 10, left: 5 },
                // Automatically calculate table width
                tableWidth: 'auto'
            });

            // Save the PDF
            doc.save('รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ.pdf');
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
                            font: isHeader ? { bold: true } : {} // **ทำให้ Header ตัวหนา**
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
            XLSX.writeFile(wb, 'รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>