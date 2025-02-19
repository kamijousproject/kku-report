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
                //console.log(faculty);
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
                                    var str='<tr><td rowspan="14" >'+$('#dropdown1 option:selected').text()+'</td>'+
                                        '<td nowrap style="text-align: left;">'+row.pillar_name+'</td>'+
                                        '<td>'+(parseFloat(sums.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td>'+(parseFloat(sums.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td>'+(parseFloat(sums.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td></tr>';
                                    tableBody.insertAdjacentHTML('beforeend', str);
                                }else{
                                    var str='<tr>'+
                                        '<td nowrap style="text-align: left;">'+row.pillar_name+'</td>'+
                                        '<td>'+(parseFloat(sums.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td>'+(parseFloat(sums.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td>'+(parseFloat(sums.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td></tr>';
                                    tableBody.insertAdjacentHTML('beforeend', str);
                                }
                                
                            }
                            else{
                                var str='<tr>'+
                                        '<td nowrap style="text-align: left;">'+'&nbsp;'.repeat(8)+row.pillar_name+'</td>'+
                                        '<td>'+(parseFloat(row.Budget_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td>'+(parseFloat(row.Allocated_budget || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td>'+
                                        '<td>'+(parseFloat(row.Actual_Spend_Amount || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+'</td></tr>';
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
        doc.text("รายงานการใช้จ่ายงบประมาณตามแผนงาน", 10, 10);

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