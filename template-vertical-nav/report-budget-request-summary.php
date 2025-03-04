<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    #reportTable td {
        text-align: left;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: top;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
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
                        <h4>รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี (สรุปประมาณการรายรับและประมาณการรายจ่าย)</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี (สรุปประมาณการรายรับและประมาณการรายจ่าย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายรับรายจ่าย</h4>
                                </div>
                                <div class="table-responsive">
                                    <label for="dropdown1">ปีงบประมาณ:</label>
                                    <select id="dropdown1">
                                        <option value="">เลือกปีงบประมาณ</option>
                                    </select>
                                    <br />
                                    <!-- Dropdown 2 (Changes based on Dropdown 1) -->
                                    <label for="dropdown2">ประเภทงบประมาณ:</label>
                                    <select id="dropdown2" disabled>
                                        <option value="">เลือกประเภทงบประมาณ</option>
                                    </select>
                                    <br />
                                    <!-- Dropdown 3 (Changes based on Dropdown 2) -->
                                    <label for="dropdown4">ส่วนงาน/หน่วยงาน:</label>
                                    <select id="dropdown4" disabled>
                                        <option value="">เลือกส่วนงาน/หน่วยงาน</option>
                                    </select>
                                    <br />
                                    <!-- Submit Button -->
                                    <button id="submitBtn" class="btn btn-primary" disabled>Submit</button>
                                    <table id="reportTable" class="table table-bordered">
                                        <br>
                                        <br>
                                        <div class="card-title">
                                            <h6>รายงานสรุปรายรับรายจ่าย</h6>
                                        </div>
                                        <thead>

                                            <tr>
                                                <th>รายการ</th>
                                                <th>งบประมาณ</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="revenue">

                                        </tbody>
                                        <tbody id="expense">

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
        let y = "";
        let f = "";
        $(document).ready(function() {

            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'get_fiscal_year'
                },
                dataType: "json",
                success: function(response) {

                    response.bgp.forEach((row) => {
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="' + row.y + '">' + row.y + '</option>');
                    });
                }
            });


            $('#dropdown1').change(function() {
                let year = $(this).val();
                //console.log(year);
                $('#dropdown2').html('<option value="">เลือกประเภทงบประมาณ</option>').prop('disabled', true);
                //$('#dropdown3').html('<option value="">เลือกแหล่งเงิน</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'get_scenario',
                        'fiscal_year': year
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        response.fac.forEach((row) => {
                            $('#dropdown2').append('<option value="' + row.scenario + '">' + row.scenario + '</option>').prop('disabled', false);

                        });
                    },
                    error: function(jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });


            $('#dropdown2').change(function() {
                let year = $('#dropdown1').val();
                let scenario = $(this).val();
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);

                if (scenario) {
                    $.ajax({
                        type: "POST",
                        url: "../server/budget_planing_api.php",
                        data: {
                            'command': 'get_faculty_2',
                            'fiscal_year': year,
                            'scenario': scenario
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
                            response.fac.forEach((row) => {
                                $('#dropdown4').append('<option value="' + row.faculty + '">' + row.faculty + '</option>').prop('disabled', false);

                            });
                        },
                        error: function(jqXHR, exception) {
                            console.error("Error: " + exception);
                            responseError(jqXHR, exception);
                        }
                    });
                }
            });

            $('#dropdown4').change(function() {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });


            $('#submitBtn').click(function() {
                let year = $('#dropdown1').val();
                let scenario = $('#dropdown2').val();
                let faculty = $('#dropdown4').val();
                y = $('#dropdown1').find('option:selected').text();;
                f = $('#dropdown4').find('option:selected').text();;
                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'kku_bgp_budget-request-summary-revenue',
                        'fiscal_year': year,
                        'scenario': scenario,
                        'faculty': faculty
                    },
                    dataType: "json",
                    success: function(response) {
                        const revenue = document.querySelector('#revenue');
                        revenue.innerHTML = ''; // ล้างข้อมูลเก่า
                        var total = 0;
                        if (response.bgp.length > 0) {
                            const parseValue = (value) => {
                                const number = parseFloat(value.replace(/,/g, ''));
                                return isNaN(number) ? 0 : number;
                            };
                            const sums = response.bgp.reduce((acc, item) => {
                                return {
                                    Total_Amount_Quantity: acc.Total_Amount_Quantity + parseValue(item.Total_Amount_Quantity),
                                };
                            }, {
                                Total_Amount_Quantity: 0
                            });
                            total = sums.Total_Amount_Quantity;
                        }

                        var tr = document.createElement('tr');
                        var td = document.createElement('td');
                        td.textContent = "รายรับ"; // เพิ่มข้อความภายในเซลล์
                        tr.appendChild(td);
                        revenue.appendChild(tr);

                        var td = document.createElement('td');
                        td.textContent = total.toLocaleString(); // เพิ่มข้อความภายในเซลล์
                        tr.appendChild(td);
                        revenue.appendChild(tr);

                        var td = document.createElement('td');
                        td.textContent = "100"; // เพิ่มข้อความภายในเซลล์
                        tr.appendChild(td);
                        revenue.appendChild(tr);
                        //console.log(response.bgp);

                        response.bgp.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            const columns = [{
                                    key: 'Type',
                                    value: row.type
                                }, // ประเภทบัญชี
                                {
                                    key: 'Total_Amount_Quantity',
                                    value: parseInt(row.Total_Amount_Quantity).toLocaleString()
                                }, // ยอดรวมค่าใช้จ่าย
                                {
                                    key: 'Total',
                                    value: (((parseInt(row.Total_Amount_Quantity) * 100) / total) || 0).toLocaleString()
                                }
                            ];

                            columns.forEach(col => {

                                const td = document.createElement('td');
                                td.textContent = col.value;
                                tr.appendChild(td);
                            });


                            revenue.appendChild(tr);

                        });

                    },
                    error: function(jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'kku_bgp_budget-request-summary-expense',
                        'fiscal_year': year,
                        'scenario': scenario,
                        'faculty': faculty
                    },
                    dataType: "json",
                    success: function(response) {
                        const revenue = document.querySelector('#expense');
                        revenue.innerHTML = ''; // ล้างข้อมูลเก่า
                        var total = 0;
                        if (response.bgp.length > 0) {
                            const parseValue = (value) => {
                                const number = parseFloat(value.replace(/,/g, ''));
                                return isNaN(number) ? 0 : number;
                            };
                            const sums = response.bgp.reduce((acc, item) => {
                                return {
                                    Total_Amount_Quantity: acc.Total_Amount_Quantity + parseValue(item.Total_Amount_Quantity),
                                };
                            }, {
                                Total_Amount_Quantity: 0
                            });
                            total = sums.Total_Amount_Quantity;
                        }

                        var tr = document.createElement('tr');
                        var td = document.createElement('td');
                        td.textContent = "รายจ่าย"; // เพิ่มข้อความภายในเซลล์
                        tr.appendChild(td);
                        revenue.appendChild(tr);

                        var td = document.createElement('td');
                        td.textContent = total.toLocaleString();; // เพิ่มข้อความภายในเซลล์
                        tr.appendChild(td);
                        revenue.appendChild(tr);

                        var td = document.createElement('td');
                        td.textContent = "100"; // เพิ่มข้อความภายในเซลล์
                        tr.appendChild(td);
                        revenue.appendChild(tr);
                        //console.log(response.bgp);
                        response.bgp.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            const columns = [{
                                    key: 'Type',
                                    value: row.type
                                }, // ประเภทบัญชี
                                {
                                    key: 'Total_Amount_Quantity',
                                    value: parseInt(row.Total_Amount_Quantity).toLocaleString()
                                }, // ยอดรวมค่าใช้จ่าย
                                {
                                    key: 'Total',
                                    value: (((parseInt(row.Total_Amount_Quantity) * 100) / total) || 0).toLocaleString()
                                }
                            ];

                            columns.forEach(col => {

                                const td = document.createElement('td');
                                td.textContent = col.value;
                                tr.appendChild(td);
                            });


                            revenue.appendChild(tr);

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
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี (สรุปประมาณการรายรับและประมาณการรายจ่าย).csv';
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

            let yPos = 10;
            // ตั้งค่าฟอนต์และข้อความ
            doc.setFontSize(14);
            let lines = doc.splitTextToSize("รายงานสรุปรายรับรายจ่าย", 300); // กำหนดความกว้างของแต่ละบรรทัด
            doc.text(lines, 10, yPos);
            yPos += 10;

            doc.setFontSize(10);
            // กลับไปใช้ตัวอักษรปกติ
            doc.setFont("THSarabun", "normal");
            lines = doc.splitTextToSize("ปีงบประมาณ: " + y, 100); // กำหนดความกว้างของแต่ละบรรทัด
            doc.text(lines, 10, yPos);
            yPos += 10;
            lines = doc.splitTextToSize("ประเภทงบประมาณ: ", 100); // กำหนดความกว้างของแต่ละบรรทัด
            doc.text(lines, 10, yPos);
            yPos += 10;
            lines = doc.splitTextToSize("ส่วนงาน/หน่วยงาน: " + f, 400); // กำหนดความกว้างของแต่ละบรรทัด
            doc.text(lines, 10, yPos);
            yPos += 10;


            // ใช้ autoTable สำหรับสร้างตาราง
            doc.autoTable({
                html: '#reportTable',
                startY: yPos,
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
            doc.save('รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี (สรุปประมาณการรายรับและประมาณการรายจ่าย).pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');

            // เพิ่ม header เป็นแถวแรก
            const headers = [
                ["รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี (สรุปประมาณการรายรับและประมาณการรายจ่าย)"], // แถวชื่อรายงาน
                [""], // แถวเว้นว่างเพื่อความสวยงาม
                ["ปีงบประมาณ: " + y, "", "", ""],
                ["ประเภทงบประมาณ:", "", "", ""],
                ["ส่วนงาน/หน่วยงาน: " + f, "", "", ""],
                [] // แถวว่างเป็นตัวแบ่ง
            ];

            // เก็บข้อมูลแต่ละแถวเป็น Array ของ Array
            const rows = headers; // เริ่มต้นด้วย headers
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
                    while (skipMap[`${rowIndex + headers.length},${colIndex}`]) {
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
                        // โดยเพิ่ม offset ของ headers
                        const mergeRef = {
                            s: {
                                r: rowIndex + headers.length,
                                c: colIndex
                            }, // จุดเริ่ม (start)
                            e: {
                                r: rowIndex + headers.length + rowspan - 1,
                                c: colIndex + colspan - 1
                            } // จุดจบ (end)
                        };

                        // เก็บลง merges (รูปแบบเก่าคือ ws['!merges'] = [])
                        // แต่ต้องรอใส่หลังสร้าง Worksheet ด้วย SheetJS
                        // จึงบันทึกชั่วคราวใน merges พร้อม index
                        const mergeKey = `merge_${rowIndex + headers.length}_${colIndex}`;
                        merges[mergeKey] = mergeRef;

                        // Mark skipMap กันซ้ำ โดยเพิ่ม offset ของ headers
                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (!(r === 0 && c === 0)) {
                                    skipMap[`${rowIndex + headers.length + r},${colIndex + c}`] = true;
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
            link.download = 'รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี (สรุปประมาณการรายรับและประมาณการรายจ่าย).xls'; // ชื่อไฟล์ .xls
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