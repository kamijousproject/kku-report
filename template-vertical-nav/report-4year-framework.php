<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>     
    thead th {
        position: sticky;
        top: 0;
/*             background: #f4f4f4; */
        z-index: 1000; /* Keep header above table */
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
                        <h4>รายงานกรอบอัตรากำลังระยะเวลา 4 ปี</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานกรอบอัตรากำลังระยะเวลา 4 ปี</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานกรอบอัตรากำลังระยะเวลา 4 ปี</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">ส่วนงาน/หน่วยงาน</th>
                                                <th colspan="10">2567</th>
                                                <th colspan="10">2568</th>
                                                <th colspan="10">2569</th>  
                                                <th colspan="10">2570</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">ประเภทบริหาร</th>
                                                <th colspan="2">ประเภทวิชาการ</th>
                                                <th colspan="2">ประเภทวิจัย</th>
                                                <th colspan="2">ประเภทสนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th colspan="2">ประเภทบริหาร</th>
                                                <th colspan="2">ประเภทวิชาการ</th>
                                                <th colspan="2">ประเภทวิจัย</th>
                                                <th colspan="2">ประเภทสนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th colspan="2">ประเภทบริหาร</th>
                                                <th colspan="2">ประเภทวิชาการ</th>
                                                <th colspan="2">ประเภทวิจัย</th>
                                                <th colspan="2">ประเภทสนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th colspan="2">ประเภทบริหาร</th>
                                                <th colspan="2">ประเภทวิชาการ</th>
                                                <th colspan="2">ประเภทวิจัย</th>
                                                <th colspan="2">ประเภทสนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                            </tr>
                                           
                                            <tr>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
                                                <th>แผน</th>
                                                <th>ผล</th>
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
            //let resultDiv = document.getElementById("result");
            console.log(category);
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_4year-framework',
                    'slt':category
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.wf);
                    console.log(response.faculty);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า


                        response.wf.forEach((row, index) => {                   
                            const tr = document.createElement('tr');

                            const columns = [
                                { key: 'Alias_Default', value: row.Alias_Default },
                                { key: 'TYPE1_year1', value: (parseInt(row.TYPE1_year1)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE2_year1', value: (parseInt(row.TYPE2_year1)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE3_year1', value: (parseInt(row.TYPE3_year1)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE4_year1', value: (parseInt(row.TYPE4_year1)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'sum_year1', value: (parseInt(row.TYPE1_year1) + parseInt(row.TYPE2_year1) + parseInt(row.TYPE3_year1) + parseInt(row.TYPE4_year1)).toLocaleString() },
                                { key: 'actual_year1', value: "0" },
                                { key: 'TYPE1_year2', value: (parseInt(row.TYPE1_year2)).toLocaleString() },
                                { key: 'Actual_type1', value: (parseInt(row.Actual_type1)).toLocaleString() },
                                { key: 'TYPE2_year2', value: (parseInt(row.TYPE2_year2)).toLocaleString() },
                                { key: 'Actual_type2', value: (parseInt(row.Actual_type2)).toLocaleString() },
                                { key: 'TYPE3_year2', value: (parseInt(row.TYPE3_year2)).toLocaleString() },
                                { key: 'Actual_type3', value: (parseInt(row.Actual_type3)).toLocaleString() },
                                { key: 'TYPE4_year2', value: (parseInt(row.TYPE4_year2)).toLocaleString() },
                                { key: 'Actual_type4', value: (parseInt(row.Actual_type4)).toLocaleString() },
                                { key: 'sum_year2', value: (parseInt(row.TYPE1_year2) + parseInt(row.TYPE2_year2) + parseInt(row.TYPE3_year2) + parseInt(row.TYPE4_year2)).toLocaleString() },
                                { key: 'actual_year2', value: (parseInt(row.Actual_type1) + parseInt(row.Actual_type2) + parseInt(row.Actual_type3) + parseInt(row.Actual_type4)).toLocaleString() },
                                { key: 'TYPE1_year3', value: (parseInt(row.TYPE1_year3)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE2_year3', value: (parseInt(row.TYPE2_year3)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE3_year3', value: (parseInt(row.TYPE3_year3)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE4_year3', value: (parseInt(row.TYPE4_year3)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'sum_year3', value: (parseInt(row.TYPE1_year3) + parseInt(row.TYPE2_year3) + parseInt(row.TYPE3_year3) + parseInt(row.TYPE4_year3)).toLocaleString() },
                                { key: 'actual_year3', value: "0" },
                                { key: 'TYPE1_year4', value: (parseInt(row.TYPE1_year4)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE2_year4', value: (parseInt(row.TYPE2_year4)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE3_year4', value: (parseInt(row.TYPE3_year4)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'TYPE4_year4', value: (parseInt(row.TYPE4_year4)).toLocaleString() },
                                { key: '0', value: "0" },
                                { key: 'sum_year4', value: (parseInt(row.TYPE1_year4) + parseInt(row.TYPE2_year4) + parseInt(row.TYPE3_year4) + parseInt(row.TYPE4_year4)).toLocaleString() },
                                { key: 'actual_year4', value: "0" }
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
        footerRow.innerHTML = '<td>รวมทั้งหมด</td>';

        // เริ่มต้นผลรวมแต่ละคอลัมน์
        let sums = new Array(columns - 1).fill(0); 

        // คำนวณผลรวม
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
            if (index >= 1) { // "ส่วนงาน/หน่วยงาน"             
                const value = cell.textContent.replace(/,/g, '');
                sums[index - 1] += parseFloat(value) || 0;
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