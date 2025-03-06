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
                        <h4>รายงานโครงการ/กิจกรรม</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานโครงการ/กิจกรรม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานโครงการ/กิจกรรม</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                                <th rowspan="2">แผนงาน (ผลผลิต)</th>
                                                <th rowspan="2">แผนงานย่อย (ผลผลิตย่อย/กิจกรรม)</th>
                                                <th rowspan="2">โครงการ/กิจกรรม</th>
                                                <th rowspan="2">แหล่งงบประมาณ</th>
                                                <th rowspan="2">งบประมาณ</th>
                                                <th colspan="3">KPI For Project</th>
                                                <th rowspan="2">วัตถุประสงค์</th>
                                                <th rowspan="2">ผลผลิต</th>
                                                <th rowspan="2">ผลลัพธ์</th>
                                                <th rowspan="2">ผลกระทบ</th>
                                                <th colspan="4">ความเชื่อมโยงกับระบบแผนงาน</th>
                                            </tr>
                                            <tr>
                                                <th>KPI Name For Project</th>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>KPI Unit For Project</th>
                                                <th>ประเด็นยุทธศาสตร์ / พันธกิจ Mission </th>
                                                <th>OKRs</th>
                                                <th>Good Governance</th>
                                                <th>SDGs</th>
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
        $(document).ready(function () {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_project-activities'
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

        function fetchData() {
            let category = document.getElementById("category").value;
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
            data.forEach((row, index) => {
                const tr = document.createElement('tr');

                const columns = [
                    { key: 'Alias_Default', value: row.Alias_Default },
                    { key: 'Plan_Name', value: row.plan_name ,nowrap: true},
                    { key: 'Sub_Plan_Name', value: row.sub_plan_name,nowrap: true} ,
                    { key: 'Project_Name', value: row.project_name },
                    { key: 'Fund', value: row.Fund },
                    { key: 'Total_Amount_Quantity', value: row.Total_Amount_Quantity },
                    { key: 'Proj_KPI_Name', value: row.Proj_KPI_Name },
                    { key: 'Proj_KPI_Target', value: row.Proj_KPI_Target },
                    { key: 'UoM_for_Proj_KPI', value: row.UoM_for_Proj_KPI },
                    { key: 'Objective', value: row.Objective },
                    { key: 'Project_Output', value: row.Project_Output },
                    { key: 'Project_Outcome', value: row.Project_Outcome },
                    { key: 'Project_Impact', value: row.Project_Impact },
                    { key: 'Pillar_Name', value: row.pillar_name ,nowrap: true},
                    { key: 'OKR_Name', value: row.okr_name },
                    { key: 'Principles_of_Good_Governance', value: row.Principles_of_good_governance },
                    { key: 'SDGs', value: row.SDGs }
                ];


                columns.forEach(col => {
                    const td = document.createElement('td');
                    td.textContent = col.value;
                    if (col.nowrap) {
                        td.classList.add("nowrap");
                    }
                    tr.appendChild(td);
                });


                tableBody.appendChild(tr);

            });
                
        }
        function exportCSV() {
            const table = document.getElementById('reportTable');
            const numRows = table.rows.length;
            const filters = getFilterValues();
            const reportHeader = [
                `"รายงานโครงการ/กิจกรรม"`,
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
            link.download = 'รายงานโครงการ/กิจกรรม.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        function getFilterValues() {
            return {
                
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
            doc.text("รายงานโครงการ/กิจกรรม", 150, 10,{ align: 'center' });
            doc.setFontSize(10);
            doc.text(`ส่วนงาน/หน่วยงาน: ${filterValues.department}`, 15, 20);
            doc.autoTable({
                html: '#reportTable',
                startY: 25,
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
                
                didParseCell: function(data) {
                    data.cell.styles.halign = 'center';
                    
                    /* if (data.section === 'body' && data.column.index === 0) {
                        data.cell.styles.halign = 'left'; // จัด text-align left สำหรับคอลัมน์แรก
                    } */
                },
                margin: { top: 15, right: 5, bottom: 10, left: 5 },
                tableWidth: 'auto'
            });
            doc.save('รายงานโครงการ/กิจกรรม.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ดึงค่าจาก dropdown
            const filterValues = getFilterValues();

            // สร้างข้อมูลสำหรับหัวรายงาน (4 แถวแรก)
            const headerRows = [
                [{ v: "รายงานโครงการ/กิจกรรม", s: { font: { bold: true, sz: 14 }, alignment: { horizontal: "center" } } }],
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
            merges.push({ s: { r: 0, c: 0 }, e: { r: 0, c: 17 } }); // รวมเซลล์หัวรายงาน

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
            XLSX.writeFile(wb, `รายงานโครงการ/กิจกรรม.xlsx`);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>