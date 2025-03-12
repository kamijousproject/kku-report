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
                                            <th >กองทุนสำรองเลี้ยงชีพ</th>
                                            <th >กองทุนประกันสังคม</th>
                                            <th > กบข.</th>
                                            <th >กสจ.</th>
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
            let num_fac = [...new Set(data.map(item => item.pname))];
            // **Step 1: Calculate Rowspan Counts Before Rendering**
            data.forEach(row => {
                let aliasKey = row.pname;
                let nameKey = row.Alias_Default;
                let fac = row.FACULTY;
                // Count occurrences for Alias_Default (Column 2)
                if (!aliasRowSpan[aliasKey]) {
                    aliasRowSpan[aliasKey] = all_data.filter(r => r.pname === aliasKey).length;
                }
                if (!nameRowSpan[aliasKey]) {
                    nameRowSpan[aliasKey] = {}; // สร้าง object ว่างสำหรับ fac
                }
                // Count occurrences for Name (Column 3), but only within the same Alias_Default
                if (!nameRowSpan[nameKey]) {
                    nameRowSpan[aliasKey][nameKey] = all_data.filter(r => r.pname === aliasKey && r.Alias_Default === nameKey).length;
                    
                }
            });
            //console.log(nameRowSpan);
            // **Step 2: Generate Table Rows**
            let row_num=1;
            data.forEach((row, index) => {                   
                let tr = document.createElement('tr');
                //row_num+=1;

                let currentAlias = row.pname;
                let currentName = row.Alias_Default;
                let currentfac = row.FACULTY;
                //console.log(nameRowSpan[currentName]);
                // **Step 3: Always Add "No" Column (Index)**
                const tdNo = document.createElement('td');
                
                // **Step 4: Create Table Cells with Rowspan Handling**
                if (currentAlias !== prevAlias) {
                    const tdAlias = document.createElement('td');
                    tdAlias.textContent = currentAlias;
                    tdAlias.rowSpan = aliasRowSpan[currentAlias]+Object.keys(nameRowSpan[currentAlias]).length+1; // Apply Rowspan
                    tr.appendChild(tdAlias);
                        // Update previous alias
                    /* console.log(currentAlias);
                    console.log(aliasRowSpan[currentAlias]);
                    console.log(Object.keys(nameRowSpan[currentAlias]));
                    console.log(aliasRowSpan[currentAlias]+Object.keys(nameRowSpan[currentAlias]).length+1); */
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
                            pca: acc.pca + parseValue(item.pca),
                            pf: acc.pf + parseValue(item.pf),
                            ssf: acc.ssf + parseValue(item.ssf),
                            gpf: acc.gpf + parseValue(item.gpf),
                            gsif: acc.gsif + parseValue(item.gsif),
                        };
                    }, {
                        position_count: 0, SALARY_RATE: 0, pc: 0, fa: 0,
                        ec: 0, pca: 0, pf: 0, ssf: 0, gpf: 0, gsif: 0
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
                    //tdC3.textContent = row_num;
                    //row_num+=1;
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
                    
                    const tdpf = document.createElement('td');
                    tdpf.textContent = (parseFloat(sums.pf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdpf.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdpf);
                    const tdssf = document.createElement('td');
                    tdssf.textContent = (parseFloat(sums.ssf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdssf.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdssf);
                    const tdgpf = document.createElement('td');
                    tdgpf.textContent = (parseFloat(sums.gpf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdgpf.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdgpf);
                    const tdgsif = document.createElement('td');
                    tdgsif.textContent = (parseFloat(sums.gsif).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdgsif.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdgsif);

                    tableBody.appendChild(tr);
                }
                if (currentAlias !== prevAlias) {
                    tr = document.createElement('tr');
                    prevAlias = currentAlias;
                }
                if (currentName !== prevName) {
                    const tdName = document.createElement('td');
                    tdName.textContent = row.Alias_Default;
                    tdName.rowSpan = nameRowSpan[currentAlias][currentName]+1; // Apply Rowspan
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
                            pca: acc.pca + parseValue(item.pca),
                            pf: acc.pf + parseValue(item.pf),
                            ssf: acc.ssf + parseValue(item.ssf),
                            gpf: acc.gpf + parseValue(item.gpf),
                            gsif: acc.gsif + parseValue(item.gsif),
                        };
                    }, {
                        position_count: 0, SALARY_RATE: 0, pc: 0, fa: 0,
                        ec: 0, pca: 0, pf: 0, ssf: 0, gpf: 0, gsif: 0
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
                    //tdC3.textContent = row_num;
                    //row_num+=1;
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

                    const tdpf = document.createElement('td');
                    tdpf.textContent = (parseFloat(sums.pf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdpf.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdpf);
                    const tdssf = document.createElement('td');
                    tdssf.textContent = (parseFloat(sums.ssf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdssf.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdssf);
                    const tdgpf = document.createElement('td');
                    tdgpf.textContent = (parseFloat(sums.gpf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdgpf.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdgpf);
                    const tdgsif = document.createElement('td');
                    tdgsif.textContent = (parseFloat(sums.gsif).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    tdgsif.style.backgroundColor = '#f7f7f7';
                    tr.appendChild(tdgsif);

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
                //tdC3.textContent = row_num;
                //row_num+=1;
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

                const tdpf = document.createElement('td');
                tdpf.textContent = (parseFloat(row.pf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdpf);
                const tdssf = document.createElement('td');
                tdssf.textContent = (parseFloat(row.ssf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdssf);
                const tdgpf = document.createElement('td');
                tdgpf.textContent = (parseFloat(row.gpf).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdgpf);
                const tdgsif = document.createElement('td');
                tdgsif.textContent = (parseFloat(row.gsif).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                tr.appendChild(tdgsif);

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
    doc.setFontSize(12);
    doc.text('รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง', 14, 10);
    // ก่อนสร้างตาราง เราต้องแปลงข้อมูลจากตาราง HTML
    const tableElement = document.getElementById('reportTable');
    const tableRows = Array.from(tableElement.querySelectorAll('tr'));
    
    // สร้างข้อมูลสำหรับ autoTable โดยรวมค่า rowspan ด้วย
    const tableData = [];
    const tableHeaders = [];
    
    // ดึงข้อมูลหัวตาราง
    const headerCells = tableRows[0].querySelectorAll('th');
    headerCells.forEach(cell => {
        tableHeaders.push(cell.textContent.trim());
    });
    
    // สร้างอาร์เรย์เก็บข้อมูลการรวมแถว (rowspan)
    let spanningCells = {}; // เก็บข้อมูล cell ที่มีการรวมแถว
    
    // แปลงข้อมูลแถวให้เป็นรูปแบบที่ autoTable รองรับ
    for (let i = 1; i < tableRows.length; i++) { // เริ่มที่ 1 เพื่อข้ามหัวตาราง
        const row = tableRows[i];
        const cells = row.querySelectorAll('td');
        const rowData = {};
        
        let cellIndex = 0;
        for (let j = 0; j < headerCells.length; j++) {
            // ตรวจสอบว่ามี cell ที่ถูก span มาจากแถวก่อนหน้าหรือไม่
            if (spanningCells[j] && spanningCells[j].rowsLeft > 0) {
                rowData[tableHeaders[j]] = spanningCells[j].content;
                spanningCells[j].rowsLeft--;
                continue;
            }
            
            // ถ้าไม่มี cell ที่ถูก span มา ให้ใช้ cell ปัจจุบัน
            const cell = cells[cellIndex++];
            if (!cell) continue; // กรณีไม่มี cell นี้ให้ข้าม
            
            const content = cell.textContent.trim();
            rowData[tableHeaders[j]] = content;
            
            // บันทึกข้อมูล rowspan หากมี
            if (cell.hasAttribute('rowspan')) {
                const rowSpan = parseInt(cell.getAttribute('rowspan'));
                if (rowSpan > 1) {
                    spanningCells[j] = {
                        content: content,
                        rowsLeft: rowSpan - 1
                    };
                }
            }
        }
        
        tableData.push(rowData);
    }

    doc.autoTable({
        head: [tableHeaders],
        body: tableData.map(row => tableHeaders.map(header => row[header] || '')),
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
            0: { cellWidth: 30, halign: 'center' }, // คอลัมน์ "ที่"
            1: { cellWidth: 30, halign: 'center' }, // ส่วนงาน
            2: { cellWidth: 35, halign: 'left' }, // ประเภทบริหาร (อัตราปัจจุบัน)
            3: { cellWidth: 20, halign: 'center' }, // ประเภทบริหาร (กรอบที่พึงมี)
            4: { cellWidth: 20, halign: 'center' }, // วิชาการ (อัตราปัจจุบัน)
            5: { cellWidth: 20, halign: 'center' }, // วิชาการ (แผน 2563-2566)
            6: { cellWidth: 25, halign: 'center' }, // FTES
            7: { cellWidth: 18, halign: 'center' }, // ภาระงานวิจัย
            8: { cellWidth: 18, halign: 'center' }, // ภาระงานบริการวิชาการ
            9: { cellWidth: 18, halign: 'center' }, // รวมวิชาการ
            10: { cellWidth: 18, halign: 'center' }, // เกณฑ์ภาระงานวิจัย
            11: { cellWidth: 15, halign: 'center' }, // รวมวิจัย
            12: { cellWidth: 15, halign: 'center' }, // Healthcare Services
        },
        didParseCell: function(data) {
            if (data.section === 'body' && data.column.index === 0) {
                data.cell.styles.halign = 'left'; // จัด text-align left สำหรับคอลัมน์แรก
            }
        },
        willDrawCell: function(data) {
            // ตรวจสอบว่าเป็นเซลล์ที่ต้องซ่อนเส้นขอบด้านล่างหรือไม่ (เนื่องจากมี rowspan)
            if (data.section === 'body') {
                const rowIndex = data.row.index;
                const colIndex = data.column.index;
                
                // ถ้าเซลล์นี้เป็นส่วนหนึ่งของ rowspan ให้ซ่อนเส้นขอบด้านล่าง
                if (spanningCells[colIndex] && spanningCells[colIndex].rowsLeft > 0 && 
                    rowIndex < tableData.length - 1) {
                    // ซ่อนเส้นขอบด้านล่าง
                    data.cell.styles.lineWidth = [0.1, 0.1, 0, 0.1]; // [top, right, bottom, left]
                }
            }
        },
        margin: { top: 15, right: 5, bottom: 10, left:7 },
        tableWidth: 'auto'
    });
    doc.save('รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่งรายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง.pdf');
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
        XLSX.writeFile(wb, 'รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง.xlsx');
    }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>