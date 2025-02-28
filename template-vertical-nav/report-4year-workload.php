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
                        <h4>รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">ที่</th>
                                            <th rowspan="2">ส่วนงาน</th>
                                            <th colspan="2">ประเภทบริหาร</th>
                                            <th colspan="6">ประเภทวิชาการ</th>
                                            <th colspan="2">ประเภทวิจัย</th>
                                            <th colspan="14">ประเภทสนับสนุน</th>
                                            <th rowspan="2" nowrap>รวมกรอบอัตรา<br/>พึงมีทั้งหมด</th>
                                        </tr>
                                        <tr>
                                            <!-- ประเภทบริหาร -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมี</th>
                                            <!-- ประเภทวิชาการ -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th nowrap>กรอบพึงมีวิชาการ<br/>ตามแผน 2563-2566</th>
                                            <th>เกณฑ์ FTES</th>
                                            <th nowrap>เกณฑ์ภาระ<br/>งานวิจัย</th>
                                            <th nowrap>เกณฑ์ภาระงาน<br/>บริการวิชาการ</th>
                                            <th>รวมวิชาการ</th>
                                            <!-- ประเภทวิจัย -->
                                            <th nowrap>เกณฑ์ภาระ<br/>งานวิจัย</th>
                                            <th>รวมวิจัย</th>
                                            <!-- ประเภทสนับสนุน -->
                                            <th>Healthcare Services</th>
                                            <th>Student and Faculty Services</th>
                                            <th>Technical and Research services</th>
                                            <th>Internationalization</th>
                                            <th>Human Resources</th>
                                            <th>Administration</th>
                                            <th>Legal, Compliance and Protection</th>
                                            <th>Strategic Management</th>
                                            <th>Information Technology</th>
                                            <th>Infrastructure and Facility Services</th>
                                            <th>Communication and Relation Management</th>
                                            <th>Cultural Affair</th>
                                            <th>Financial Services</th>
                                            <th>รวมประเภทสนับสนุน</th>
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
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_staff-requests_current'
                },
                dataType: "json",
                success: function(response) {
                    data_current=response.wf;
                    //console.log(data_current);
                    $.ajax({
                        type: "POST",
                        url: "../server/workforce_api.php",
                        data: {
                            'command': 'kku_wf_4year-workload'
                        },
                        dataType: "json",
                        success: function(response) {
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
            let data;
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
            data.forEach((row, index) => {                   
                const tr = document.createElement('tr');

                const columns = [
                        { key: 'No', value: index+1 },
                        { key: 'Alias_Default', value: row.Alias_Default  },
                        
                        { key: 'Actual_type1', value: row.Actual_type1||0 },
                        { key: 'wf_type1', value: row.wf_type1 ||0},
                        
                        { key: 'Actual_type2', value: row.Actual_type2 ||0},
                        { key: 'wf_plan', value: "" ||0},
                        { key: 'sum_FTES', value: row.sum_FTES ||0},
                        
                        { key: 'sum_RWC', value: row.sum_RWC ||0},
                        { key: 'sum_WCAS', value: row.sum_WCAS ||0},
                        
                        { key: 'total_type2', value:  parseInt(row.Actual_type2||0) +parseInt(row.sum_FTES||0) +parseInt(row.sum_RWC||0) +parseInt(row.sum_WCAS||0) },
                        { key: 'sum_RWC2', value: row.sum_RWC2 ||0},
                        
                        { key: 'sum_RWC2', value: row.sum_RWC2 ||0},

                        { key: 'j1', value: (row.j1 ||0).toLocaleString()},        
                        { key: 'j2', value: (row.j2 ||0).toLocaleString()},
                        { key: 'j3', value: (row.j3 ||0).toLocaleString()},
                        { key: 'j4', value: (row.j4||0).toLocaleString()},
                        { key: 'j5', value: (row.j5 ||0).toLocaleString()},
                        { key: 'j6', value: (row.j6 ||0).toLocaleString()},
                        { key: 'j7', value: (row.j7 ||0).toLocaleString()},
                        { key: 'j8', value: (row.j8||0).toLocaleString()},
                        { key: 'j9', value: (row.j9 ||0).toLocaleString()},
                        { key: 'j10', value: (row.j10 ||0).toLocaleString()},
                        { key: 'j11', value: (row.j11 ||0).toLocaleString()},
                        { key: 'j12', value: (row.j12 ||0).toLocaleString()},        
                        { key: 'j13', value: (row.j13 ||0).toLocaleString()},
                        { key: 'j14', value: (parseInt(row.j1 ||0) + 
                                                parseInt(row.j2 ||0) + 
                                                parseInt(row.j3 ||0) + 
                                                parseInt(row.j4 ||0) + 
                                                parseInt(row.j5 ||0) + 
                                                parseInt(row.j6 ||0) + 
                                                parseInt(row.j7 ||0) + 
                                                parseInt(row.j8 ||0) + 
                                                parseInt(row.j9 ||0) + 
                                                parseInt(row.j10 ||0) + 
                                                parseInt(row.j11 ||0) + 
                                                parseInt(row.j12 ||0) + 
                                                parseInt(row.j13 ||0)).toLocaleString() },
                        { key: 'j15', value: "0"},
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
        footerRow.innerHTML = '<td colspan="2">รวม</td>';

        // เริ่มต้นผลรวมแต่ละคอลัมน์
        let sums = new Array(columns - 2).fill(0); 

        // คำนวณผลรวม
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
            const value = parseFloat(cell.textContent.replace(/,/g, '')) || 0;
            if (index >= 2) { // "ส่วนงาน/หน่วยงาน"               
                sums[index - 2] += parseFloat(value) || 0;
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
            link.download = 'รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน.csv';
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
        startY: 10,
        theme: 'grid',
        styles: {
            font: "THSarabun",
            fontSize: 7, 
            cellPadding: 1, 
            lineWidth: 0.1,
            lineColor: [0, 0, 0],
            minCellHeight: 6
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
            0: { cellWidth: 8, halign: 'center' }, // คอลัมน์ "ที่"
            1: { cellWidth: 30, halign: 'left' }, // ส่วนงาน
            2: { cellWidth: 10, halign: 'center' }, // ประเภทบริหาร (อัตราปัจจุบัน)
            3: { cellWidth: 10, halign: 'center' }, // ประเภทบริหาร (กรอบที่พึงมี)
            4: { cellWidth: 10, halign: 'center' }, // วิชาการ (อัตราปัจจุบัน)
            5: { cellWidth: 10, halign: 'center' }, // วิชาการ (แผน 2563-2566)
            6: { cellWidth: 10, halign: 'center' }, // FTES
            7: { cellWidth: 10, halign: 'center' }, // ภาระงานวิจัย
            8: { cellWidth: 10, halign: 'center' }, // ภาระงานบริการวิชาการ
            9: { cellWidth: 10, halign: 'center' }, // รวมวิชาการ
            10: { cellWidth: 10, halign: 'center' }, // เกณฑ์ภาระงานวิจัย
            11: { cellWidth: 10, halign: 'center' }, // รวมวิจัย
            12: { cellWidth: 10, halign: 'center' }, // Healthcare Services
            13: { cellWidth: 10, halign: 'center' }, // Student and Faculty Services
            14: { cellWidth: 10, halign: 'center' }, // Technical and Research services
            15: { cellWidth: 10, halign: 'center' }, // Internationalization
            16: { cellWidth: 10, halign: 'center' }, // Human Resources
            17: { cellWidth: 10, halign: 'center' }, // Administration
            18: { cellWidth: 10, halign: 'center' }, // Legal, Compliance and Protection
            19: { cellWidth: 10, halign: 'center' }, // Strategic Management
            20: { cellWidth: 10, halign: 'center' }, // Information Technology
            21: { cellWidth: 10, halign: 'center' }, // Infrastructure and Facility Services
            22: { cellWidth: 10, halign: 'center' }, // Communication and Relation Management
            23: { cellWidth: 10, halign: 'center' }, // Cultural Affair
            24: { cellWidth: 10, halign: 'center' }, // Financial Services
            25: { cellWidth: 10, halign: 'center' }, // รวมประเภทสนับสนุน
            26: { cellWidth: 10, halign: 'center' }, // รวมทั้งหมด
        },
        didParseCell: function(data) {
            if (data.section === 'body') {
                if (data.column.index === 1) {
                    data.cell.styles.halign = 'left'; // จัดข้อความของ "ส่วนงาน" ให้อยู่ชิดซ้าย
                } else {
                    data.cell.styles.halign = 'center'; // ข้อความอื่นให้อยู่กึ่งกลาง
                }
            }
        },
        margin: { top: 15, right: 10, bottom: 10, left: 5 },
        tableWidth: 'auto' // ปรับตารางให้อยู่ในขนาดของ A4
    });

    doc.save('รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน.pdf');
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
            XLSX.writeFile(wb, 'รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน.xlsx');
        }
    </script>
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>