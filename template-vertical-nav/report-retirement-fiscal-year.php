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
                        <h4>รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย)</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย)</h4>
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
                                            <th rowspan="3" nowrap>ส่วนงาน/หน่วยงาน</th>
                                            <th rowspan="3" nowrap>ชื่อตำแหน่ง</th>
                                            <th rowspan="3" nowrap>ประเภทตำแหน่ง</th>
                                            <th rowspan="3">Job Family</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2567</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2568</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2569</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2570</th>
                                            <th rowspan="3" nowrap>รวมจำนวน<br/>อัตราเกษียณ<br/>อายุราชการ 4 ปี</th>
                                        </tr>
                                        <tr>
                                            <!-- ปีงบประมาณ พ.ศ. 2567 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2568 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2569 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2570 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                        </tr>
                                        <tr>
                                            <!-- ปีงบประมาณ พ.ศ. 2567 -->
                                            <th nowrap>แผ่นดิน</th>
                                            <th nowrap>รายได้</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2568 -->
                                            <th nowrap>แผ่นดิน</th>
                                            <th nowrap>รายได้</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2569 -->
                                            <th nowrap>แผ่นดิน</th>
                                            <th nowrap>รายได้</th>                                           
                                            <!-- ปีงบประมาณ พ.ศ. 2570 -->
                                            <th nowrap>แผ่นดิน</th>
                                            <th nowrap>รายได้</th>                                           
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
            laodData();
            
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_retirement-fiscal-year'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response.wf;                                             
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
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
            data.forEach((row, index) => {                   
                const tr = document.createElement('tr');
                var sum1=parseInt(row.p1 ?? 0)+parseInt(row.p2?? 0)+parseInt(row.p3?? 0)+parseInt(row.p4?? 0)+parseInt(row.p5?? 0);
                var sum2=parseInt(row.p1_y2?? 0)+parseInt(row.p2_y2?? 0)+parseInt(row.p3_y2?? 0)+parseInt(row.p4_y2?? 0)+parseInt(row.p5_y2?? 0);
                var sum3=parseInt(row.p1_y3?? 0)+parseInt(row.p2_y3?? 0)+parseInt(row.p3_y3?? 0)+parseInt(row.p4_y3?? 0)+parseInt(row.p5_y3?? 0);
                var sum4=parseInt(row.p1_y4?? 0)+parseInt(row.p2_y4?? 0)+parseInt(row.p3_y4?? 0)+parseInt(row.p4_y4?? 0)+parseInt(row.p5_y4?? 0);
                var sum5=sum1+sum2+sum3+sum4;                      
                const columns = [
                    { key: 'No', value: index+1 },
                    { key: 'Faculty', value: row.Alias_Default??row.Alias_Default_y2??row.Alias_Default_y3??row.Alias_Default_y4 },                           
                    { key: 'Position', value: row.POSITION??row.POSITION_y2??row.POSITION_y3??row.POSITION_y4 },
                    { key: 'personnel_type', value: row.all_position_types??row.all_position_types_y2??row.all_position_types_y3??row.all_position_types_y4 },  
                    { key: 'Job_Family', value: row.Job_Family??row.Job_Family_y2??row.Job_Family_y3??row.Job_Family_y4 },
                    { key: 'p1', value: row.p1?? 0 },                            
                    { key: 'p2', value: row.p2 ?? 0},
                    { key: 'p3', value: row.p3 ?? 0},
                    { key: 'p4', value: row.p4 ?? 0},     
                    { key: 'p5', value: row.p5 ?? 0},      
                    { key: 'sum1', value: sum1 },
                    { key: 'p1_y2', value: row.p1_y2 ?? 0},                            
                    { key: 'p2_y2', value: row.p2_y2 ?? 0},
                    { key: 'p3_y2', value: row.p3_y2 ?? 0},
                    { key: 'p4_y2', value: row.p4_y2 ?? 0},     
                    { key: 'p5_y2', value: row.p5_y2 ?? 0},      
                    { key: 'sum2', value: sum2 },
                    { key: 'p1_y3', value: row.p1_y3 ?? 0},                            
                    { key: 'p2_y3', value: row.p2_y3 ?? 0},
                    { key: 'p3_y3', value: row.p3_y3 ?? 0},
                    { key: 'p4_y3', value: row.p4_y3 ?? 0},     
                    { key: 'p5_y3', value: row.p5_y3 ?? 0},      
                    { key: 'sum3', value: sum3 },
                    { key: 'p1_y4', value: row.p1_y4 ?? 0},                            
                    { key: 'p2_y4', value: row.p2_y4 ?? 0},
                    { key: 'p3_y4', value: row.p3_y4 ?? 0},
                    { key: 'p4_y4', value: row.p4_y4 ?? 0},     
                    { key: 'p5_y4', value: row.p5_y4 ?? 0},      
                    { key: 'sum4', value: sum4 },    
                    { key: 'sum5', value: sum5 },                                                                         
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
        footerRow.innerHTML = '<td colspan="5">รวมทั้งสิ้น</td>';

        // เริ่มต้นผลรวมแต่ละคอลัมน์
        let sums = new Array(columns - 5).fill(0); 

        // คำนวณผลรวม
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
            if (index >= 5) { // "ส่วนงาน/หน่วยงาน"  
                const value = cell.textContent.replace(/,/g, '');             
                sums[index - 5] += parseFloat(value) || 0;
            }
            });
        });

        // เพิ่มผลรวมลงใน footer
        sums.forEach(sum => {
            footerRow.innerHTML += `<td>${sum.toLocaleString()}</td>`;
        });

        // เพิ่มแถว footer ลงในตาราง
        footer.innerHTML='';
        footer.append(footerRow);
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
            link.download = 'รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย).csv';
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
                    0: { cellWidth: 5 },  // ที่
                    1: { cellWidth: 21 }, // ส่วนงาน/หน่วยงาน
                    2: { cellWidth: 20 }, // ชื่อตำแหน่ง
                    3: { cellWidth: 20 }, // ประเภทตำแหน่ง
                    4: { cellWidth: 18 }, // Job Family
                    // ปีงบประมาณ พ.ศ. 2567
                    5: { cellWidth: 8 },  // ข้าราชการ
                    6: { cellWidth: 8},  // พนง.มข. แผ่นดิน
                    7: { cellWidth: 8 },  // พนง.มข. รายได้
                    8: { cellWidth: 8 },  // ลูกจ้างประจำ
                    9: { cellWidth: 8 },  // ลูกจ้างของ มข.
                    10: { cellWidth: 8 }, // รวม
                    // ปีงบประมาณ พ.ศ. 2568
                    11: { cellWidth: 8 }, // ข้าราชการ
                    12: { cellWidth: 8 }, // พนง.มข. แผ่นดิน
                    13: { cellWidth: 8 }, // พนง.มข. รายได้
                    14: { cellWidth: 8 }, // ลูกจ้างประจำ
                    15: { cellWidth: 8 }, // ลูกจ้างของ มข.
                    16: { cellWidth: 8 }, // รวม
                    // ปีงบประมาณ พ.ศ. 2569
                    17: { cellWidth: 8 }, // ข้าราชการ
                    18: { cellWidth: 8 }, // พนง.มข. แผ่นดิน
                    19: { cellWidth: 8 }, // พนง.มข. รายได้
                    20: { cellWidth: 8 }, // ลูกจ้างประจำ
                    21: { cellWidth: 8 }, // ลูกจ้างของ มข.
                    22: { cellWidth: 8 }, // รวม
                    // ปีงบประมาณ พ.ศ. 2570
                    23: { cellWidth: 8 }, // ข้าราชการ
                    24: { cellWidth: 8 }, // พนง.มข. แผ่นดิน
                    25: { cellWidth: 8 }, // พนง.มข. รายได้
                    26: { cellWidth: 8 }, // ลูกจ้างประจำ
                    27: { cellWidth: 8 }, // ลูกจ้างของ มข.
                    28: { cellWidth: 8 }, // รวม
                    // รวม 4 ปี
                    29: { cellWidth: 8 } // รวมทั้งหมด
                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(14);
                    doc.text('รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย)', 20, 10);
                    
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
            doc.save('รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ.pdf');
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
            XLSX.writeFile(wb, 'รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย).xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>