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
                        <h4>รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h4>รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย</h4>
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
                                                    <th class="align-middle" rowspan="4">ลำดับ</th>
                                                    <th class="align-middle" rowspan="4">รหัส</th>
                                                    <th class="align-middle" rowspan="4">ส่วนงาน/หน่วยงาน</th>
                                                    <th class="align-middle" rowspan="4">จำนวนผลลัพธ์/ตัวชี้วัดทั้งหมด</th>
                                                    <th class="align-middle" colspan="19">ความสอดคล้องของแผน</th>
                                                </tr>
                                                <tr class="text-nowrap">
                                                    <th class="align-middle" colspan="13">แผนยุทธศาสตร์การบริหารมหาวิทยาลัยขอนแก่น</th>
                                                    <th class="align-middle" colspan="2">แผนพันธกิจ</th>
                                                    <th class="align-middle" colspan="2">แผนสรรหา</th>
                                                    <th class="align-middle" colspan="2">แผนสร้างความโดดเด่น</th>
                                                </tr>
                                                <tr class="text-nowrap">
                                                    <th class="align-middle" rowspan="2">จำนวน</th>
                                                    <th class="align-middle" rowspan="2">ร้อยละ</th>
                                                    <th class="align-middle" colspan="11">ยุทธศาสตร์</th>
                                                    <th class="align-middle" rowspan="2">จำนวน</th>
                                                    <th class="align-middle" rowspan="2">ร้อยละ</th>
                                                    <th class="align-middle" rowspan="2">จำนวน</th>
                                                    <th class="align-middle" rowspan="2">ร้อยละ</th>
                                                    <th class="align-middle" rowspan="2">จำนวน</th>
                                                    <th class="align-middle" rowspan="2">ร้อยละ</th>
                                                </tr>
                                                <tr class="text-nowrap">
                                                    <th>1</th>
                                                    <th>2</th>
                                                    <th>3</th>
                                                    <th>4</th>
                                                    <th>5</th>
                                                    <th>6</th>
                                                    <th>7</th>
                                                    <th>8</th>
                                                    <th>9</th>
                                                    <th>10</th>
                                                    <th>11</th>
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
                    'command': 'get_faculty_get_strategic_indicators'
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
                $.ajax({
                type: "POST",
                url: "../server/api.php",
                data: {
                    'command': 'get_strategic-indicators',
                    'faculty':faculty
                },
                dataType: "json",
                success: function(response) {
                    // console.log(response.plan);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    response.plan.forEach((row, index) => {                   
                        const tr = document.createElement('tr');
                        var sum1= parseInt(row.s1)+parseInt(row.s2)+parseInt(row.s3)+parseInt(row.s4)
                        +parseInt(row.s5)+parseInt(row.s6)+parseInt(row.s7)+parseInt(row.s8)+parseInt(row.s9)
                        +parseInt(row.s10)+parseInt(row.s11);
                        const columns = [
                            { key: 'No', value: index+1 },
                            { key: 'fac_code', value: (row.Alias_Default).substring(0, 2) },
                            { key: 'fac', value: row.Alias_Default.replace(/^(\d{5}) - /, '') },
                            { key: 'count_okr', value: parseInt(row.count_okr).toLocaleString() },
                            { key: 'sum1', value: parseInt(sum1).toLocaleString() },
                            { key: 'avg1', value: (parseFloat((parseFloat(sum1)*100)/parseFloat(row.count_okr)|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"%" },
                            { key: 's1', value: parseInt(row.s1).toLocaleString() },
                            { key: 's2', value: parseInt(row.s2).toLocaleString() },
                            { key: 's3', value: parseInt(row.s3).toLocaleString() },
                            { key: 's4', value: parseInt(row.s4).toLocaleString() },
                            { key: 's5', value: parseInt(row.s5).toLocaleString() },
                            { key: 's6', value: parseInt(row.s6).toLocaleString() },
                            { key: 's7', value: parseInt(row.s7).toLocaleString() },
                            { key: 's8', value: parseInt(row.s8).toLocaleString() },
                            { key: 's9', value: parseInt(row.s9).toLocaleString() },
                            { key: 's10', value: parseInt(row.s10).toLocaleString() },
                            { key: 's11', value: parseInt(row.s11).toLocaleString() },
                            { key: 'p1', value: (parseInt(row.count_okr)-sum1).toLocaleString() },    
                            { key: 'p1', value: (((parseInt(row.count_okr)-sum1)*100)/parseInt(row.count_okr)).toLocaleString()+"%" },  
                            { key: 'dev_plan', value: parseInt(row.dev_plan || 0).toLocaleString() },  
                            { key: 'avg2', value: (parseFloat((parseFloat(row.dev_plan || 0)*100)/parseFloat(row.count_okr)|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"%" },  
                            { key: 'divis', value: parseInt(row.divis || 0).toLocaleString() },  
                            { key: 'avg3', value: (parseFloat((parseFloat(row.divis || 0)*100)/parseFloat(row.count_okr)|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')+"%" },                                                                  
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
            doc.text("รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย", 10, 10);

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