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
                        <h4>รายงานประมาณการค่าใช้จ่ายบุคลากรตามกรอบอัตรากำลัง</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานประมาณการค่าใช้จ่ายบุคลากรตามกรอบอัตรากำลัง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานประมาณการค่าใช้จ่ายบุคลากรตามกรอบอัตรากำลัง</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th >ส่วนงาน</th>
                                            <th >หน่วยงาน</th>
                                            <th >ตำแหน่ง</th>
                                            <th >จำนวนตำแหน่ง</th>
                                            <th >เงินเดือน</th>
                                            <th >ค่าตอบแทนเงินประจำตำแหน่งทางวิชาการ/สนับสนุน</th>
                                            <th >ค่าตอบแทนเงินเดือนเต็มขั้น</th>
                                            <th >ค่าตอบแทนตำแหน่งบริหาร</th>
                                            <th >ค่ารถประจำตำแหน่ง</th>
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
            laodData();
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'expense-prediction-wf'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response.wf;
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
        }
        function fetchData() {
            let category = document.getElementById("category").value;
            //let resultDiv = document.getElementById("result");
            console.log(category);
                             
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // Clear old data

            let prevAlias = null;
            let prevName = null;
            let aliasRowSpan = {};
            let nameRowSpan = {};
            let data;
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
            // **Step 1: Calculate Rowspan Counts Before Rendering**
            data.forEach(row => {
                let aliasKey = row.pname;
                let nameKey = row.Alias_Default;

                // Count occurrences for Alias_Default (Column 2)
                if (!aliasRowSpan[aliasKey]) {
                    aliasRowSpan[aliasKey] = all_data.filter(r => r.pname === aliasKey).length;
                }

                // Count occurrences for Name (Column 3), but only within the same Alias_Default
                if (!nameRowSpan[nameKey]) {
                    nameRowSpan[nameKey] = all_data.filter(r => r.pname === aliasKey && r.Alias_Default === nameKey).length;
                    
                }
            });
            //console.log(Object.keys(nameRowSpan).length);
            // **Step 2: Generate Table Rows**
            data.forEach((row, index) => {                   
                let tr = document.createElement('tr');
                

                let currentAlias = row.pname;
                let currentName = row.Alias_Default;
                //console.log(nameRowSpan[currentName]);
                // **Step 3: Always Add "No" Column (Index)**
                const tdNo = document.createElement('td');

                // **Step 4: Create Table Cells with Rowspan Handling**
                if (currentAlias !== prevAlias) {
                    const tdAlias = document.createElement('td');
                    tdAlias.textContent = currentAlias;
                    tdAlias.rowSpan = aliasRowSpan[currentAlias]+Object.keys(nameRowSpan).length+1; // Apply Rowspan
                    tr.appendChild(tdAlias);
                        // Update previous alias

                    var sub_total=all_data.filter(r => r.pname === currentAlias);
                    const parseValue = (value) => {
                        const number = parseFloat(String(value).replace(/,/g, ''));
                        return isNaN(number) ? 0 : number;
                    };
                    const sums = sub_total.reduce((acc, item) => {
                        return {
                            position_count: acc.position_count + parseValue(item.position_count),
                            SALARY_RATE: acc.SALARY_RATE + parseValue(item.SALARY_RATE),
                            pc: acc.pc + parseValue(item.pc),
                            fa: acc.fa + parseValue(item.fa),
                            ec: acc.ec + parseValue(item.ec),
                            pca: acc.pca + parseValue(item.pca)
                        };
                    }, {
                        position_count: 0, SALARY_RATE: 0, pc: 0, fa: 0,
                        ec: 0, pca: 0
                    });
                    const fac = document.createElement('td');
                    fac.textContent = "";
                    fac.style.textAlign = 'left';
                    fac.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(fac);

                    const tdPosition = document.createElement('td');
                    tdPosition.textContent = "";
                    tdPosition.style.textAlign = 'left';
                    tdPosition.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdPosition);

                    const tdC1 = document.createElement('td');
                    tdC1.textContent = sums.position_count.toLocaleString();
                    tdC1.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdC1);

                    const tdC2 = document.createElement('td');
                    tdC2.textContent = (parseFloat(sums.SALARY_RATE).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdC2.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdC2);

                    const tdC3 = document.createElement('td');
                    tdC3.textContent = (parseFloat(sums.pc).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdC3.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdC3);
                    const tdfa = document.createElement('td');
                    tdfa.textContent = (parseFloat(sums.fa).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdfa.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdfa);
                    const tdec = document.createElement('td');
                    tdec.textContent = (parseFloat(sums.ec).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdec.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdec);
                    const tdpca = document.createElement('td');
                    tdpca.textContent = (parseFloat(sums.pca).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdpca.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdpca);

                    tableBody.appendChild(tr);
                }
                if (currentAlias !== prevAlias) {
                    tr = document.createElement('tr');
                    prevAlias = currentAlias;
                }
                if (currentName !== prevName) {
                    const tdName = document.createElement('td');
                    tdName.textContent = row.Alias_Default;
                    tdName.rowSpan = nameRowSpan[currentName]+1; // Apply Rowspan
                    tr.appendChild(tdName);
                    //console.log(nameRowSpan[currentName]+1);
                        // Update previous name
                    var sub_total=all_data.filter(r => r.pname === currentAlias && r.Alias_Default === currentName);
                    const parseValue = (value) => {
                        const number = parseFloat(String(value).replace(/,/g, ''));
                        return isNaN(number) ? 0 : number;
                    };
                    const sums = sub_total.reduce((acc, item) => {
                        return {
                            position_count: acc.position_count + parseValue(item.position_count),
                            SALARY_RATE: acc.SALARY_RATE + parseValue(item.SALARY_RATE),
                            pc: acc.pc + parseValue(item.pc),
                            fa: acc.fa + parseValue(item.fa),
                            ec: acc.ec + parseValue(item.ec),
                            pca: acc.pca + parseValue(item.pca)
                        };
                    }, {
                        position_count: 0, SALARY_RATE: 0, pc: 0, fa: 0,
                        ec: 0, pca: 0
                    });
                    const tdPosition = document.createElement('td');
                    tdPosition.textContent = "";
                    tdPosition.style.textAlign = 'left';
                    tdPosition.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdPosition);

                    const tdC1 = document.createElement('td');
                    tdC1.textContent = sums.position_count;
                    tdC1.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdC1);

                    const tdC2 = document.createElement('td');
                    tdC2.textContent = (parseFloat(sums.SALARY_RATE).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdC2.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdC2);

                    const tdC3 = document.createElement('td');
                    tdC3.textContent = (parseFloat(sums.pc).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdC3.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdC3);
                    const tdfa = document.createElement('td');
                    tdfa.textContent = (parseFloat(sums.fa).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdfa.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdfa);
                    const tdec = document.createElement('td');
                    tdec.textContent = (parseFloat(sums.ec).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdec.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdec);
                    const tdpca = document.createElement('td');
                    tdpca.textContent = (parseFloat(sums.pca).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdpca.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdpca);

                    tableBody.appendChild(tr);
                }
                if(currentName !== prevName)
                {
                    tr = document.createElement('tr');
                    prevName = currentName;
                }
                // **Step 5: Ensure Proper Column Order**
                const tdPosition = document.createElement('td');
                tdPosition.textContent = row.POSITION;
                tdPosition.style.textAlign = 'left';
                tr.appendChild(tdPosition);

                const tdC1 = document.createElement('td');
                tdC1.textContent = row.position_count;
                tr.appendChild(tdC1);

                const tdC2 = document.createElement('td');
                tdC2.textContent = (parseFloat(row.SALARY_RATE).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdC2);

                const tdC3 = document.createElement('td');
                tdC3.textContent = (parseFloat(row.pc).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdC3);
                const tdfa = document.createElement('td');
                tdfa.textContent = (parseFloat(row.fa).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdfa);
                const tdec = document.createElement('td');
                tdec.textContent = (parseFloat(row.ec).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdec);
                const tdpca = document.createElement('td');
                tdpca.textContent = (parseFloat(row.pca).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdpca);

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

    // Configure autoTable
    doc.autoTable({
        html: '#reportTable',
        startY: 25,
        styles: {
            font: "THSarabun",
            fontSize: 10,
            cellPadding: 2,
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [220, 230, 241],
            textColor: [0, 0, 0],
            fontSize: 10,
            fontStyle: 'bold',
            halign: 'center',
            valign: 'middle'
        },
        columnStyles: {
            0: { cellWidth: 40 }, // ส่วนงาน
            1: { cellWidth: 40 }, // หน่วยงาน
            2: { cellWidth: 40 }, // ตำแหน่ง
            3: { cellWidth: 20 }, // จำนวนตำแหน่ง
            4: { cellWidth: 25 }, // เงินเดือน
            5: { cellWidth: 30 }, // ค่าตอบแทน
            6: { cellWidth: 30 }, // เงินเดือนเต็มขั้น
            7: { cellWidth: 25 }, // ตำแหน่งบริหาร
            8: { cellWidth: 25 }  // ค่ารถ
        },
        didDrawPage: function(data) {
            // Add header
            doc.setFontSize(16);
            doc.text('รายงานประมาณการค่าใช้จ่ายบุคลากรตามกรอบอัตรากำลัง', 14, 15);
            
            // Add footer with page number
            doc.setFontSize(10);
            doc.text(
                'หน้า ' + doc.internal.getCurrentPageInfo().pageNumber + ' จาก ' + doc.internal.getNumberOfPages(),
                doc.internal.pageSize.width - 20, 
                doc.internal.pageSize.height - 10,
                { align: 'right' }
            );
        },
        didParseCell: function(data) {
            // Apply background color for summary rows
            const element = data.cell.raw;
            if (element && element.style && element.style.backgroundColor === 'rgb(247, 247, 247)') {
                data.cell.styles.fillColor = [247, 247, 247];
            }

            // Apply text alignment
            if (element && element.style && element.style.textAlign === 'left') {
                data.cell.styles.halign = 'left';
            }
        },
        margin: { top: 20, right: 14, bottom: 20, left: 14 }
    });

    // Save the PDF
    doc.save('รายงานประมาณการค่าใช้จ่ายบุคลากร.pdf');
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
                rowData[colIndex] = {
                    v: cellText,
                    s: {
                        alignment: {
                            vertical: "top",
                            horizontal: "left"
                        }
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
        XLSX.writeFile(wb, 'report.xlsx');
    }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>