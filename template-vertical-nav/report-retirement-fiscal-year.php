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
                                            <th rowspan="3">ส่วนงาน/หน่วยงาน<br>(คณะ L.1 / สนอ L.3)</th>
                                            <th rowspan="3">ชื่อตำแหน่ง</th>
                                            <th rowspan="3">ประเภทตำแหน่ง</th>
                                            <th rowspan="3">Job Family</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2567</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2568</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2569</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2570</th>
                                            <th rowspan="3">รวมจำนวนอัตราเกษียณอายุราชการ 4 ปี</th>
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
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2568 -->
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2569 -->
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>                                           
                                            <!-- ปีงบประมาณ พ.ศ. 2570 -->
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>                                           
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
            let category = document.getElementById("category").value;
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_retirement-fiscal-year',
                    'slt':category
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.wf);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
                    
                    response.wf.forEach((row, index) => {                   
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
                            { key: 'personnel_type', value: row.All_PositionTypes??row.All_PositionTypes_y2??row.All_PositionTypes_y3??row.All_PositionTypes_y4 },  
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

                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
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
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF('landscape');

        // เพิ่มฟอนต์ภาษาไทย
        doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal); // ใช้ตัวแปรที่ได้จากไฟล์
        doc.addFont("THSarabun.ttf", "THSarabun", "normal");
        doc.setFont("THSarabun");

        // ตั้งค่าฟอนต์และข้อความ
        doc.setFontSize(12);
        doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

        // ใช้ autoTable สำหรับสร้างตาราง
        doc.autoTable({
            html: '#reportTable',
            startY: 20,
            styles: {
                font: "THSarabun", // ใช้ฟอนต์ที่รองรับภาษาไทย
                fontSize: 10,
                lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                lineWidth: 0.5, // ความหนาของเส้นขอบ
            },
            bodyStyles: {
                lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                lineWidth: 0.5, // ความหนาของเส้นขอบ
            },
            headStyles: {
                fillColor: [102, 153, 225], // สีพื้นหลังของหัวตาราง
                textColor: [0, 0, 0], // สีข้อความในหัวตาราง
                lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                lineWidth: 0.5, // ความหนาของเส้นขอบ
            },
        });

        // บันทึกไฟล์ PDF
        doc.save('รายงาน.pdf');
    }

    function exportXLS() {
    const table = document.getElementById('reportTable');

    const rows = [];
    const merges = {};
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
            rowData[colIndex] = cellText;

            const rowspan = cell.rowSpan || 1;
            const colspan = cell.colSpan || 1;

            if (rowspan > 1 || colspan > 1) {
                const mergeRef = {
                    s: { r: rowIndex, c: colIndex },
                    e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 }
                };

                const mergeKey = `merge_${rowIndex}_${colIndex}`;
                merges[mergeKey] = mergeRef;

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

    // Create Workbook
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);

    // Apply merges
    ws['!merges'] = Object.values(merges);

    // Apply header styles to the first few rows (all header rows)
    const totalHeaderRows = table.tHead.rows.length; // Get the number of header rows
    for (let R = 0; R < totalHeaderRows; R++) {
        for (let C = 0; C < rows[R].length; C++) {
            const cellRef = XLSX.utils.encode_cell({ r: R, c: C });

            if (ws[cellRef]) {
                ws[cellRef].s = {
                    alignment: { horizontal: "center", vertical: "center" }, // Center text
                    font: { bold: true, name: "Arial", sz: 12 }, // Bold + Font
                    fill: { fgColor: { rgb: "FFFFCC" } }, // Light yellow background
                    border: {
                        top: { style: "thin", color: { rgb: "000000" } },
                        bottom: { style: "thin", color: { rgb: "000000" } },
                        left: { style: "thin", color: { rgb: "000000" } },
                        right: { style: "thin", color: { rgb: "000000" } }
                    }
                };
            }
        }
    }

    // Append sheet and write file
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    const excelBuffer = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

    // Download file
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'report.xlsx'; // Change to .xlsx for proper styling support
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