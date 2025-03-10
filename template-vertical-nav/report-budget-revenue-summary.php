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
th {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
    vertical-align: top;
}
 td {
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
    top: 44px; /* Adjust height based on previous row */
    background: #f4f4f4;
    z-index: 999;
}

thead tr:nth-child(3) th {
    position: sticky;
    top: 89px; /* Adjust height based on previous rows */
    background: #f4f4f4;
    z-index: 998;
}
.nowrap {
    white-space: nowrap;
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
                        <h4>รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ</h4>
                                </div>
                                <label for="fyear">ปีงบประมาณ:</label>
                                <select name="fyear" id="fyear" >
                                    <option value="">-- Select --</option>
                                    <option value="">2568</option>
                                </select>
                                <br/>
                                <label for="scenario">ประเภทงบประมาณ:</label>
                                <select name="scenario" id="scenario" disabled>
                                    <option value="">-- Select --</option>
                                </select>
                                <br/>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()" disabled>
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            
                                            <tr>
                                                <th style="vertical-align: top;">ที่</th>
                                                <th style="vertical-align: top;">ส่วนงาน/หน่วยงาน</th>
                                                <th style="vertical-align: top;"nowrap>1.เงินอุดหนุนจากรัฐ</th>
                                                <th style="vertical-align: top;"nowrap>2.เงินและทรัพย์สิน<br/>ซึ่งมีผู้บริจาค<br/>ให้แก่มหาวิทยาลัย</th>
                                                <th style="vertical-align: top;"nowrap>3.เงินกองทุนที่รัฐบาล<br/>หรือมหาวิทยาลัยจัดตั้งขึ้นและรายได้<br/>หรือผลประโยชน์จากกองทุน</th>
                                                <th style="vertical-align: top;"nowrap>4.ค่าธรรมเนียม ค่าบำรุง<br/>ค่าตอบแทน เบี้ยปรับ <br/>และค่าบริการต่างๆของมหาวิทยาลัย</th>
                                                <th style="vertical-align: top;"nowrap>5.รายได้หรือผลประโยชน์<br/>ที่ได้จากการลงทุนหรือการร่วมลงทุน<br/>จากทรัพย์สินของมหาวิทยาลัย</th>
                                                <th style="vertical-align: top;"nowrap>6.รายได้หรือผลประโยชน์<br/>ที่ได้จากการใช้ทรัพย์สินหรือจัดทำ<br/>เพื่อเป็นที่ราชพัสดุหรือทรัพย์สิน<br/>ของมหาวิทยาลัยปกครองดูแล<br/>ใช้หรือจัดทำประโยชน์</th>
                                                <th style="vertical-align: top;"nowrap>7.เงินอุดหนุนจากหน่วยงานภายนอก<br/>เงินทุนอุดหนุนการวิจัยหรือ<br/>การบริการวิชาการที่ได้รับจาก<br/>หน่วยงานของรัฐ</th>
                                                <th style="vertical-align: top;"nowrap>8.เงินและผลประโยชน์ที่ได้รับ<br/>จากการบริการวิชาการ การวิจัย<br/>และนำทรัพย์สินทางปัญญาไปทำประโยชน์</th>
                                                <th style="vertical-align: top;"nowrap>9.รายได้ผลประโยชน์อื่นๆ</th>
                                                <th style="vertical-align: top;"nowrap>รวมทั้งหมด</th>
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
        $(document).ready(function () {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-revenue-summary'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response.bgp;
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
        $('#fyear').change(function () {
            const scenario = [...new Set(all_data.map(item => item.scenario))];
            let facDropdown = document.getElementById("scenario");
                    facDropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
                    scenario.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        facDropdown.appendChild(option);
                    });   
            $('#scenario').prop('disabled', false);
        });
        $('#scenario').change(function () {
            let scenario = document.getElementById("scenario").value;
            let all_data2 = all_data.filter(item=>item.scenario===scenario);
            console.log(all_data2);
            const fac = [...new Set(all_data2.map(item => item.pname))];
                    let facDropdown = document.getElementById("category");
                    facDropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
                    fac.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        facDropdown.appendChild(option);
                    });
            $('#category').prop('disabled', false);
        });
        function fetchData() {
            let category = document.getElementById("category").value;
            let scenario = document.getElementById("scenario").value;
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า      
            let data;
            if (scenario == "all") {
                data = all_data;
            } else {
                data = all_data.filter(item => item.Scenario === scenario);
            }         
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
            data.forEach((row, index) => {
                const tr = document.createElement('tr');
                var total = parseInt(row.a1) + parseInt(row.a2) + parseInt(row.a3) + parseInt(row.a4)
                    + parseInt(row.a5) + parseInt(row.a6) + parseInt(row.a7) + parseInt(row.a8) +
                    parseInt(row.a9);
                const columns = [
                    { key: 'No', value: index + 1 },
                    { key: 'fac', value: row.Alias_Default },
                    { key: 'a1', value: parseInt(row.a1).toLocaleString() },
                    { key: 'a2', value: parseInt(row.a2).toLocaleString() },
                    { key: 'a3', value: parseInt(row.a3).toLocaleString() },
                    { key: 'a4', value: parseInt(row.a4).toLocaleString() },
                    { key: 'a5', value: parseInt(row.a5).toLocaleString() },
                    { key: 'a6', value: parseInt(row.a6).toLocaleString() },
                    { key: 'a7', value: parseInt(row.a7).toLocaleString() },
                    { key: 'a8', value: parseInt(row.a8).toLocaleString() },
                    { key: 'a9', value: parseInt(row.a9).toLocaleString() },
                    { key: 'total', value: total.toLocaleString() },
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

            // Create footer row
            let footerRow = document.createElement('tr');
            footerRow.innerHTML = '<td colspan="2">รวม</td>';

            // Initialize sums for each column
            let sums = new Array(columns - 2).fill(0);

            // Calculate sums
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (index >= 2) { // Skip the first two columns (e.g., "ส่วนงาน/หน่วยงาน")
                        // Remove commas and convert to a number
                        const value = parseFloat(cell.textContent.replace(/,/g, '')) || 0;
                        sums[index - 2] += value;
                    }
                });
            });

            // Add sums to the footer row
            sums.forEach(sum => {
                // Format the sum with commas
                footerRow.innerHTML += `<td>${sum.toLocaleString()}</td>`;
            });

            footer.innerHTML='';
            footer.append(footerRow);
        }
        function exportCSV() {
            const table = document.getElementById('reportTable');
            const numRows = table.rows.length;
            const filters = getFilterValues();
            const reportHeader = [
                `"รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ"`,
                `"ปีงบประมาณ: ${filters.fyear}"`,
                `"ประเภทงบประมาณ: ${filters.scenario}"`,
                `"ส่วนงาน/หน่วยงาน: ${filters.department}"`
                
            ];
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
            const csvContent = "\uFEFF" + 
        reportHeader.join('\n') + '\n' + // เพิ่มส่วนหัวจาก dropdowns
        '\n' + // บรรทัดว่างแยกส่วนหัวกับข้อมูล
        csvMatrix.map(row => row.join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        function getFilterValues() {
            return {
                fyear: document.getElementById('fyear').options[document.getElementById('fyear').selectedIndex].text,
                scenario: document.getElementById('scenario').options[document.getElementById('scenario').selectedIndex].text,
                department: document.getElementById('category').options[document.getElementById('category').selectedIndex].text
            };
        }
        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            // Add Thai font
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");
            const filterValues = getFilterValues();
            doc.setFontSize(12);
            doc.text("รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ", 150, 10,{ align: 'center' });
            doc.setFontSize(10);
            doc.text(`ปีงบประมาณ: ${filterValues.fyear}`, 15, 20);
            doc.text(`ประเภทงบประมาณ: ${filterValues.scenario}`, 150, 20);
            doc.text(`ส่วนงาน/หน่วยงาน: ${filterValues.department}`, 15, 25);
            doc.autoTable({
                html: '#reportTable',
                startY: 30,
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
                    0: { cellWidth: 8 },  // ที่
                    1: { cellWidth: 21 }, // ส่วนงาน/หน่วยงาน
                    // ข้าราชการ
                    2: { cellWidth: 20 },  // วิชาการ
                    3: { cellWidth: 25 },  // สนับสนุน
                    4: { cellWidth: 25 },  // รวม
                    // ลูกจ้างประจำ
                    5: { cellWidth: 25 },  // สนับสนุน
                    // พนักงานมหาวิทยาลัยงบประมาณแผ่นดิน
                    6: { cellWidth: 36 },  // บริหาร
                    7: { cellWidth: 36},  // วิชาการ-คนครอง
                    8: { cellWidth: 30 },  // วิชาการ-อัตราว่าง
                    9: { cellWidth: 25 },  // วิชาการ-อัตราว่าง
                    10: { cellWidth: 20 },  // วิชาการ-อัตราว่าง
                    11: { cellWidth: 15 },  // วิชาการ-อัตราว่าง
                    
                },
                didParseCell: function(data) {
                    data.cell.styles.halign = 'center';
                    
                    /* if (data.section === 'body' && data.column.index === 0) {
                        data.cell.styles.halign = 'left'; // จัด text-align left สำหรับคอลัมน์แรก
                    } */
                },
                margin: { top: 15, right: 5, bottom: 10, left: 5 },
                tableWidth: 'auto'
            });
            doc.save('รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ดึงค่าจาก dropdown
            const filterValues = getFilterValues();

            // สร้างข้อมูลสำหรับหัวรายงาน (4 แถวแรก)
            const headerRows = [
                [{ v: "รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ", s: { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } } }],
                [
                    { v: "ปีงบประมาณ:", s: { font: { bold: true } } },
                    { v: filterValues.fyear }
                ],
                [
                    { v: "ประเภทงบประมาณ:", s: { font: { bold: true } } },
                    { v: filterValues.scenario }
                ],
                [
                    { v: "ส่วนงาน/หน่วยงาน:", s: { font: { bold: true } } },
                    { v: filterValues.department }
                ],
                [] // ว่างไว้ 1 แถว
            ];

            // สร้างข้อมูลจากตาราง
            const rows = [];
            const merges = [];
            const skipMap = {};

            // จัดการกับการรวมเซลล์ในส่วนหัวรายงาน
            merges.push({ s: { r: 0, c: 0 }, e: { r: 0, c: 11 } }); // รวมเซลล์หัวรายงาน

            // ปรับ offset สำหรับตาราง (เพิ่มจำนวนแถวหัวรายงาน)
            const rowOffset = headerRows.length;

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const tr = table.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    while (skipMap[`${rowIndex + rowOffset},${colIndex}`]) {
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
                            s: { r: rowIndex + rowOffset, c: colIndex },
                            e: { r: rowIndex + rowOffset + rowspan - 1, c: colIndex + colspan - 1 }
                        });

                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (!(r === 0 && c === 0)) {
                                    skipMap[`${rowIndex + rowOffset + r},${colIndex + c}`] = true;
                                }
                            }
                        }
                    }

                    colIndex++;
                }
                rows.push(rowData);
            }

            // รวมข้อมูลหัวรายงานและข้อมูลตาราง
            const allRows = [...headerRows, ...rows];

            // สร้าง Workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // นำ merges ไปใช้
            ws['!merges'] = merges;

            // กำหนดความกว้างของคอลัมน์
            const cols = [];
            // กำหนดความกว้างตามต้องการ
            cols.push({ wch: 10 }); // ที่
            cols.push({ wch: 30 }); // โครงการ/กิจกรรม
            cols.push({ wch: 45 }); // ประเด็นยุทธศาสตร์
            cols.push({ wch: 20 }); // OKR
            cols.push({ wch: 35 }); // แผนงาน
            cols.push({ wch: 35 }); // แผนงานย่อย
            // คอลัมน์ที่เหลือความกว้าง 15
            for (let i = 0; i < 10; i++) {
                cols.push({ wch: 15 });
            }
            ws['!cols'] = cols;

            // เพิ่ม Worksheet ลงใน Workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์ Excel
            XLSX.writeFile(wb, `รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ.xlsx`);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>