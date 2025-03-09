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
    top: 85px; /* Adjust height based on previous row */
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
                        <h4>รายงานการสรุป คำขออัตราใหม่และอัตราเดิม </span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการสรุป คำขออัตราใหม่และอัตราเดิม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการสรุป คำขออัตราใหม่และอัตราเดิม</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <br/>
                                <label for="req_type">ประเภทคำขอ:</label>
                                <select name="req_type" id="req_type" disabled>
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
                                            <th rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                            <th rowspan="2">ประเภทคำขอ</th>
                                            <th rowspan="2">ชื่อ - นามสกุล</th>
                                            <th rowspan="2">ประเภทบุคลากร</th>
                                            <th rowspan="2">ประเภทการจ้าง</th>
                                            <th rowspan="2">เลขประจำตำแหน่ง</th>
                                            <th rowspan="2">ชื่อตำแหน่ง</th>
                                            <th rowspan="2">จำนวนที่ขอ</th>
                                            <th rowspan="2">Job Family</th>
                                            <th rowspan="2">ประเภทตำแหน่ง</th>
                                            <th rowspan="2">กลุ่มบุคลากร</th>
                                            <th rowspan="2">ประเภทสัญญา</th>
                                            <th rowspan="2">ระยะเวลาสัญญา</th>
                                            <th rowspan="2">คุณวุฒิของตำแหน่ง</th>
                                            <th rowspan="2">สถานะอัตรา</th>
                                            <th rowspan="2">เงินเดือน</th>
                                            <th rowspan="2">แหล่งงบประมาณ</th>
                                            <th rowspan="2">งบประมาณแผ่นดิน</th>
                                            <th rowspan="2">งบประมาณเงินรายได้</th>
                                            <th rowspan="2">งบประมาณเงินรายได้ สนอ.</th>
                                            <th rowspan="2">สาขาวิชา/สถานที่ปฏิบัติงาน</th>
                                            <th rowspan="2">สถานะการปฏิบัติงาน</th>
                                            <th rowspan="2">วันที่เกษียณ</th>
                                            <th colspan="2">ผลการประเมิน</th>                                           
                                            <th>ประสงค์จ้างต่อเนื่อง</th>
                                            <th rowspan="2">ระยะเวลาการจ้าง</th>
                                        </tr>
                                        <tr>
                                            <th>ผลประเมิน</th>
                                            <th>ร้อยละ</th>
                                            <th>ใช่/ไม่ใช่</th>
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
                    'command': 'kku_wf_new-vs-old-positions'
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
        $('#category').change(function () {
            let fac = $('#category').val();
            $('#req_type').html('<option value="">เลือกประเภทคำขอ</option>').prop('disabled', true);
            let req;
            if(fac!="all")
            {
                req=all_data.filter(item=>item.pname===fac);
            }
            else{
                req=all_data;
            }
            const reqtype = [...new Set(req.map(item => item.TYPE))];
            let dropdown = document.getElementById("req_type");
            dropdown.innerHTML = '<option value="">-- เลือกประเภทคำขอ --</option><option value="all">เลือกทั้งหมด</option>';
            reqtype.forEach(category => {
                let option = document.createElement("option");
                option.value = category;
                option.textContent = category;
                dropdown.appendChild(option);
            });
            $('#req_type').prop('disabled', false);
        });
        $('#req_type').change(function() {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });
        $('#submitBtn').click(function() {
            let category = document.getElementById("category").value;
            let req = document.getElementById("req_type").value;
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
            let data;
            let data2;
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
            if(req=="all"){
                data2=data;
            }
            else{
                data2=data.filter(item=>item.TYPE===req);
            }

            data2.forEach((row, index) => {                   
                const tr = document.createElement('tr');

                const columns = [
                    { key: 'No', value: index + 1 },
                    { key: 'Alias_Default', value: row.Alias_Default },
                    { key: 'TYPE', value: row.TYPE },
                    { key: 'Workers_Name_Surname', value: row.Workers_Name_Surname },
                    { key: 'Personnel_Type', value: row.Personnel_Type },
                    { key: 'Employment_Type', value: row.Employment_Type },
                    { key: 'Position_Number', value: row.Position_Number },
                    { key: 'Position', value: row.Position },
                    { key: 'Requested_HC_unit', value: row.Requested_HC_unit },
                    { key: 'Job_Family', value: row.Job_Family },
                    { key: 'All_PositionTypes', value: row.All_PositionTypes },
                    { key: 'Personnel_Group', value: row.Personnel_Group },
                    { key: 'Contract_Type', value: row.Contract_Type },
                    { key: 'Contract_Period_Short_Term', value: row.Contract_Period_Short_Term },
                    { key: 'Position_Qualifications', value: row.Position_Qualifications },
                    { key: 'rate_status', value: row.rate_status },
                    { key: 'salary_rate', value: (parseFloat(row.salary_rate|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')  },
                    { key: 'fund_ft', value: row.fund_ft },
                    { key: 'govt_fund', value: (parseFloat(row.govt_fund|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')  },
                    { key: 'division_revenue', value: (parseFloat(row.division_revenue|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')  },
                    { key: 'oop_central_revenue', value: (parseFloat(row.oop_central_revenue|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,')  },
                    { key: 'Location_Code', value: row.Location_Code },
                    { key: 'WORKING_STATUS', value: row.WORKING_STATUS },
                    { key: 'RETIREMENT_DATE', value: row.RETIREMENT_DATE },
                    { key: 'Performance_Evaluation', value: row.Performance_Evaluation },
                    { key: 'Performance_Evaluation_Percentage', value: row.Performance_Evaluation_Percentage },                            
                    { key: 'Wish_to_Continue_Employement', value: row.Wish_to_Continue_Employement },
                    { key: 'Hiring_Start_End_Date', value: row.Hiring_Start_End_Date },

                ];

                columns.forEach(col => {
                    const td = document.createElement('td');
                    td.textContent = col.value;
                    tr.appendChild(td);
                });
                tableBody.appendChild(tr);     
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
    const doc = new jsPDF('l', 'mm', 'a4');

    // Add Thai font
    doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
    doc.addFont("THSarabun.ttf", "THSarabun", "normal");
    doc.setFont("THSarabun");
    
    // กำหนดค่าความกว้างของคอลัมน์ลดลงเพื่อให้พอดีกับกระดาษ A4 แนวนอน (ความกว้าง 297mm)
    const columnWidths = {
        0: 6,    // ที่
        1: 20,   // ส่วนงาน/หน่วยงาน
        2: 10,   // ประเภทคำขอ
        3: 14,   // ชื่อ-นามสกุล
        4: 10,   // ประเภทบุคลากร
        5: 10,   // ประเภทการจ้าง
        6: 10,   // เลขประจำตำแหน่ง
        7: 12,   // ชื่อตำแหน่ง
        8: 9,    // จำนวนที่ขอ
        9: 10,   // Job Family
        10: 10,   // ประเภทตำแหน่ง
        11: 9,   // กลุ่มบุคลากร
        12: 9,   // ประเภทสัญญา
        13: 15,   // ระยะเวลาสัญญา
        14: 9,   // คุณวุฒิของตำแหน่ง
        15: 9,   // สถานะอัตรา
        16: 10,   // เงินเดือน
        17: 9,   // แหล่งงบประมาณ
        18: 9,   // งบประมาณแผ่นดิน
        19: 9,   // งบประมาณเงินรายได้
        20: 9,   // งบประมาณเงินรายได้ สนอ.
        21: 9,   // สาขาวิชา/สถานที่ปฏิบัติงาน
        22: 9,   // สถานะการปฏิบัติงาน
        23: 9,   // วันที่เกษียณ
        24: 9,   // ผลประเมิน
        25: 9,   // ร้อยละ
        26: 9,   // ใช่/ไม่ใช่
        27: 9    // ระยะเวลาการจ้าง
    };

    doc.autoTable({
        html: '#reportTable',
        startY: 15,
        theme: 'grid',
        styles: {
            font: "THSarabun", // ใช้ font ปกติ ไม่ใช่ bold
            fontSize: 6,  // ลดขนาดฟอนต์ลงอีก
            cellPadding: 0.8, // ลดระยะห่างภายในเซลล์
            lineWidth: 0.1,
            lineColor: [0, 0, 0],
            minCellHeight: 3.5, // ลดความสูงขั้นต่ำของเซลล์
            overflow: 'linebreak',
            cellWidth: 'wrap'
        },
        headStyles: {
            fillColor: [220, 230, 241],
            textColor: [0, 0, 0],
            fontSize: 6, // ลดขนาดฟอนต์ลงอีก
            fontStyle: 'normal', // ไม่ใช้ bold
            halign: 'center',
            valign: 'middle'
        },
        columnStyles: {
            0: { halign: 'center', cellWidth: columnWidths[0] },  // ที่ - ตัวเลขให้อยู่กลาง
            1: { halign: 'left', cellWidth: columnWidths[1] },    // ส่วนงาน/หน่วยงาน - ชิดซ้าย
            2: { halign: 'left', cellWidth: columnWidths[2] },    // ประเภทคำขอ
            3: { halign: 'left', cellWidth: columnWidths[3] },    // ชื่อ - นามสกุล
            4: { halign: 'left', cellWidth: columnWidths[4] },    // ประเภทบุคลากร
            5: { halign: 'left', cellWidth: columnWidths[5] },    // ประเภทการจ้าง
            6: { halign: 'center', cellWidth: columnWidths[6] },  // เลขประจำตำแหน่ง
            7: { halign: 'left', cellWidth: columnWidths[7] },    // ชื่อตำแหน่ง
            8: { halign: 'center', cellWidth: columnWidths[8] },  // จำนวนที่ขอ
            9: { halign: 'left', cellWidth: columnWidths[9] },    // Job Family
            10: { halign: 'left', cellWidth: columnWidths[10] },  // ประเภทตำแหน่ง
            11: { halign: 'left', cellWidth: columnWidths[11] },  // กลุ่มบุคลากร
            12: { halign: 'left', cellWidth: columnWidths[12] },  // ประเภทสัญญา
            13: { halign: 'center', cellWidth: columnWidths[13] }, // ระยะเวลาสัญญา
            14: { halign: 'left', cellWidth: columnWidths[14] },  // คุณวุฒิของตำแหน่ง
            15: { halign: 'center', cellWidth: columnWidths[15] }, // สถานะอัตรา
            16: { halign: 'right', cellWidth: columnWidths[16] }, // เงินเดือน - ตัวเลขให้ชิดขวา
            17: { halign: 'left', cellWidth: columnWidths[17] },  // แหล่งงบประมาณ
            18: { halign: 'right', cellWidth: columnWidths[18] }, // งบประมาณแผ่นดิน - ตัวเลขให้ชิดขวา
            19: { halign: 'right', cellWidth: columnWidths[19] }, // งบประมาณเงินรายได้ - ตัวเลขให้ชิดขวา
            20: { halign: 'right', cellWidth: columnWidths[20] }, // งบประมาณเงินรายได้ สนอ. - ตัวเลขให้ชิดขวา
            21: { halign: 'left', cellWidth: columnWidths[21] },  // สาขาวิชา/สถานที่ปฏิบัติงาน
            22: { halign: 'center', cellWidth: columnWidths[22] }, // สถานะการปฏิบัติงาน
            23: { halign: 'center', cellWidth: columnWidths[23] }, // วันที่เกษียณ
            24: { halign: 'center', cellWidth: columnWidths[24] }, // ผลประเมิน
            25: { halign: 'center', cellWidth: columnWidths[25] }, // ร้อยละ
            26: { halign: 'center', cellWidth: columnWidths[26] }, // ใช่/ไม่ใช่
            27: { halign: 'center', cellWidth: columnWidths[27] }, // ระยะเวลาการจ้าง
        },
        didParseCell: function(data) {
            // จัดการ rowspan ในหัวตาราง
            const cellElem = data.cell.raw;
            if (cellElem && cellElem.hasAttribute && cellElem.hasAttribute('rowspan')) {
                const rowspan = parseInt(cellElem.getAttribute('rowspan'));
                if (rowspan > 1) {
                    data.cell.rowSpan = rowspan;
                }
            }
            
            // จัดการกับเงินเดือนและงบประมาณให้แสดงเป็นตัวเลข
            if (data.section === 'body') {
                if (data.column.index === 16 || data.column.index === 18 || 
                    data.column.index === 19 || data.column.index === 20) {
                    const value = data.cell.text;
                    if (value && !isNaN(value)) {
                        data.cell.text = parseInt(value).toLocaleString('th-TH');
                    }
                }
            }
        },
        // ตั้งค่าขอบกระดาษให้เหมาะสมกับการพิมพ์ - เพิ่มขอบด้านข้าง
        margin: { top: 10, right: 7, bottom: 10, left: 7 },
        // กำหนดความกว้างตารางให้แน่นอน
        tableWidth: 283, // 297mm (A4 แนวนอน) - ขอบซ้าย 7mm - ขอบขวา 7mm = 283mm
        // หัวกระดาษ - เพิ่มชื่อรายงานด้านบน
        didDrawPage: function(data) {
            // เพิ่มหัวกระดาษ
            doc.setFont("THSarabun", "normal"); // ใช้ฟอนต์ธรรมดา ไม่ใช่ bold
            doc.setFontSize(10);
            doc.text('รายงานการสรุป คำขออัตราใหม่และอัตราเดิม', data.settings.margin.left, 10);
            
            // เพิ่มเลขหน้า
            doc.setFontSize(8);
            doc.text('หน้า ' + data.pageNumber + ' จาก ' + data.pageCount, data.settings.margin.left + doc.internal.pageSize.width - 40, 10);
        }
    });
    
    doc.save('รายงานการสรุป คำขออัตราใหม่และอัตราเดิม.pdf');
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
            XLSX.writeFile(wb, 'รายงานการสรุป คำขออัตราใหม่และอัตราเดิม.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>