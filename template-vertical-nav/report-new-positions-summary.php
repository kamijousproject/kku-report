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
                        <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย (อัตราใหม่) รายตำแหน่ง</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="15">ส่วนงาน/หน่วยงาน</th>
                                            <th colspan="3">ข้อมูลเฉพาะผู้เกษียณอายุราชการ/ชาวต่างประเทศ</th>
                                            <th colspan="2">ข้อมูลสำคัญสำหรับทุกประเภทบุคลากร</th>
                                        </tr>
                                        <tr>
                                            <th >ลำดับ</th>
                                            <th >ส่วนงาน/หน่วยงาน</th>
                                            <th >ประเภทบุคลากร</th>
                                            <th >ประเภทการจ้าง</th>
                                            <th >ประเภทตำแหน่ง</th>
                                            <th >กลุ่มบุคลากร</th>
                                            <th >ชื่อตำแหน่ง</th>
                                            <th  nowrap>กลุ่มตำแหน่ง<br/>Job Family</th>
                                            <th >คุณวุฒิอัตรา</th>
                                            <th >ประเภทสัญญา</th>
                                            <th >ระยะเวลาสัญญา</th>
                                            <th >จำนวนอัตราที่ขอ</th>
                                            <th >เงินเดือน/ค่าจ้าง</th>
                                            <th >แหล่งงบประมาณ</th>
                                            <th  nowrap>สาขาวิชา(ตำแหน่งอาจารย์)/<br/>สถานที่ปฏิบัติงาน(ตำแหน่งอื่น)</th>
                                            <th >ผู้ครองตำแหน่ง</th>
                                            <th >ตำแหน่งทางวิชาการ</th>
                                            <th >ระยะเวลาการจ้าง</th>
                                            <th >เหตุผลจำเพาะ</th>
                                            <th  nowrap>แนบรายละเอียด <br/>Link file detail</th>
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
        let all_data;
        $(document).ready(function() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_positions-summary'
                },
                dataType: "json",
                success: function(response) {
                    data_current=response.wf;
                    //console.log(data_current);
                    
                    all_data=response.wf;
                    //console.log(data_current);
                    //console.log(data_new);                           
                    const fac = [...new Set(all_data.map(item => item.pname))];
                    let dropdown = document.getElementById("category");
                    dropdown.innerHTML = '<option value="">-- Select --</option>';
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

        function fetchData() {
           
            let category = document.getElementById("category").value;
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
            let data= all_data.filter(item=>item.pname===category);
            data.forEach((row, index) => {                   
                const tr = document.createElement('tr');

                const columns = [
                    { key: 'No', value: index+1 },
                    { key: 'Alias_Default', value: row.Alias_Default },
                    { key: 'Personnel_Type', value: row.Personnel_Type },
                    { key: 'Employment_Type', value: row.Employment_Type },      
                    { key: 'All_PositionTypes', value: row.All_PositionTypes },                                                            
                    { key: 'Personnel_Group', value: row.Personnel_Group },                              
                    { key: 'Position', value: row.Position }, 
                    { key: 'Job_Family', value: row.Job_Family }, 
                    { key: 'Position_Qualififcations', value: row.Position_Qualififcations },
                    { key: 'Contract_Type', value: row.Contract_Type },
                    { key: 'period', value: "" },   
                    { key: 'Requested_HC_unit', value: row.Requested_HC_unit },
                    { key: 'Salary_Wages_Baht_per_month', value: (parseFloat(row.Salary_Wages_Baht_per_month|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')  },
                    { key: 'Fund_FT', value: row.Fund_FT }, 
                    { key: 'Field_of_Study', value: row.Field_of_Study },                             
                    { key: 'Workers_Name_Surname', value: row.Workers_Name_Surname },    
                    { key: 'Academic_Position', value: row.Academic_Position },
                    { key: 'Hiring_Start_End_Date', value: row.Hiring_Start_End_Date },
                    { key: 'Specific_reasons', value: row.Specific_reasons },
                    { key: 'Additional_Information', value: row.Additional_Information },
                                                                                        
                ];

                columns.forEach(col => {
                    const td = document.createElement('td');
                    td.textContent = col.value;
                    tr.appendChild(td);
                });
                tableBody.appendChild(tr);     
                    
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
            link.download = 'รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย (อัตราใหม่) รายตำแหน่ง.csv';
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
    doc.save('รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย (อัตราใหม่) รายตำแหน่ง.pdf');
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
            XLSX.writeFile(wb, 'รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย (อัตราใหม่) รายตำแหน่ง.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>