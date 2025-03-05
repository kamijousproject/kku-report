<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    .table-responsive {
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
                        <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <br/>
                                <label for="dropdown2">ปีงบประมาณ:</label>
                                <select name="dropdown2" id="dropdown2" disabled>
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <br/>
                                <button id="submitBtn" disabled>Submit</button>
                                <br/><br/>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <!-- แถวที่ 1 -->
                                            <tr>
                                                <th>ปีงบประมาณ</th>
                                                <th colspan="3" style="background-color: white;" id="fiscal_year"></th>
                                            </tr>
                                            <!-- แถวที่ 2 -->
                                            <tr>
                                                <th>ส่วนงาน / หน่วยงาน</th>
                                                <th colspan="3" style="background-color: white;" id="faculty"></th>
                                            </tr>
                                            <!-- แถวที่ 3 -->
                                            <tr>
                                                <th >ผ่านมติที่ประชุมส่วนงาน / หน่วยงาน ครั้งที่</th>
                                                <th style="background-color: white;" ></th>
                                                <th >ณ วันที่</th>
                                                <th style="background-color: white;"></th>
                                            </tr>
                                            <!-- แถวที่ 4 -->
                                            <tr>
                                                <th>ประเภทบุคลากร</th>
                                                <th>อัตราเดิม</th>
                                                <th>อัตราใหม่</th>
                                                <th>รวม (อัตรา)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- ข้อมูลกลุ่ม 1 -->
                                            <tr>
                                                <td style="text-align: left;">1. พนักงานมหาวิทยาลัยงบประมาณเงินรายได้</td>
                                                <td id="type1"></td>
                                                <td id="type1_new"></td>
                                                <td id="type1_sum"></td>
                                            </tr>
                                            <tr>
                                                <td class="sub-row" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;- ประเภทวิชาการ</td>
                                                <td id="academic1"></td>
                                                <td id="academic1_new"></td>
                                                <td id="academic1_sum"></td>
                                            </tr>
                                            <tr>
                                                <td class="sub-row" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;- ประเภทวิจัย</td>
                                                <td id="research1"></td>
                                                <td id="research1_new"></td>
                                                <td id="research1_sum"></td>
                                            </tr>
                                            <tr>
                                                <td class="sub-row" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;- ประเภทสนับสนุน</td>
                                                <td id="support1"></td>
                                                <td id="support1_new"></td>
                                                <td id="support1_sum"></td>
                                            </tr>
                                            <tr>
                                                <td class="sub-row" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;- ระยะสั้น</td>
                                                <td id="period"></td>
                                                <td id="period_new"></td>
                                                <td id="period_sum"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;ประเภทการจ้าง ชาวต่างประเทศ</td>
                                                <td id="emp1"></td>
                                                <td id="emp1_new"></td>
                                                <td id="emp1_sum"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;ประเภทการจ้าง ผู้เกษียณอายุราชการ</td>
                                                <td id="emp2"></td>
                                                <td id="emp2_new"></td>
                                                <td id="emp2_sum"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;ประเภทการจ้าง ผู้ปฏิบัติงานในมหาวิทยาลัย</td>
                                                <td id="emp3"></td>
                                                <td id="emp3_new"></td>
                                                <td id="emp3_sum"></td>
                                            </tr>

                                            <!-- ข้อมูลกลุ่ม 2 -->
                                            <tr>
                                                <td style="text-align: left;">2. ลูกจ้างของมหาวิทยาลัย</td>
                                                <td id="type2"></td>
                                                <td id="type2_new"></td>
                                                <td id="type2_sum"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;" class="sub-row">&nbsp;&nbsp;&nbsp;&nbsp;- ประเภทวิจัย</td>
                                                <td id="research2"></td>
                                                <td id="research2_new"></td>
                                                <td id="research2_sum"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;" class="sub-row">&nbsp;&nbsp;&nbsp;&nbsp;- ประเภทสนับสนุน</td>
                                                <td id="support2"></td>
                                                <td id="support2_new"></td>
                                                <td id="support2_sum"></td>
                                            </tr>
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
        let data_current;
        let data_new;
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
                            'command': 'kku_wf_staff-requests_new'
                        },
                        dataType: "json",
                        success: function(response) {
                            data_new=response.wf;
                            //console.log(data_current);
                            //console.log(data_new);
                            let union=[...new Set([...data_current, ...data_new])];
                            const type = [...new Set(union.map(item => item.pname))];
                            type.sort();
                            let dropdown = document.getElementById("category");
                            dropdown.innerHTML = '<option value="">-- Select --</option>';
                            type.forEach(category => {
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
        $('#category').change(function() {
            $('#dropdown2').html('<option value="">เลือกปีงบประมาณ</option>').prop('disabled', true);
            $('#submitBtn').prop('disabled', true);

            $('#dropdown2').append('<option value="">-- Select --</option><option value="all">2568</option>').prop('disabled', false);
        });
        $('#dropdown2').change(function() {
            if ($(this).val()) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        });
        $('#submitBtn').click(function() {
            let category = document.getElementById("category").value;
            let resultDiv = document.getElementById("result");
            var categoryDropdown = document.getElementById("category");
            var categoryText = categoryDropdown.options[categoryDropdown.selectedIndex].text;
            document.getElementById("faculty").textContent=categoryText;

            var fyear = document.getElementById("dropdown2");
            var categoryText = fyear.options[fyear.selectedIndex].text;
            document.getElementById("fiscal_year").textContent=categoryText;
            var Sumresearch=0;
            var Sumresearch2=0;
            var Sumacademic=0;
            var Sumsupport=0;
            var Sumsupport2=0;
            var SumshortTerm=0;
            var Sumemp1=0;
            var Sumemp2=0;
            var Sumemp3=0;
            var Sumtype1=0;
            var Sumtype2=0;
            
                    var research=0;
                    var research2=0;
                    var academic=0;
                    var support=0;
                    var support2=0;
                    var shortTerm=0;
                    var emp1=0;
                    var emp2=0;
                    var emp3=0;
                    var type1=0;
                    var type2=0;
                    var cur=data_current.filter(item=>item.pname===category);
                    cur.forEach((row, index) => {
                        if(row.Personnel_Type=="พนักงานมหาวิทยาลัยงบประมาณเงินรายได้")
                        {
                            type1+=1;
                            Sumtype1+=1;
                            if(row.All_PositionTypes=="วิชาการ")
                            {
                                academic+=1;
                                Sumacademic+=1;
                            }
                            if(row.All_PositionTypes=="วิจัย")
                            {
                                research+=1;
                                Sumresearch+=1;
                            }
                            if(row.All_PositionTypes=="สนับสนุน")
                            {
                                support+=1;
                                Sumsupport+=1;
                            }
                            if(row.Contract_Type=="สัญญาระยะสั้น")
                            {
                                shortTerm+=1;
                                SumshortTerm+=1;
                            }
                            if(row.Employment_Type=="ชาวต่างประเทศ")
                            {
                                emp1+=1;
                                Sumemp1+=1;
                            }
                            if(row.Employment_Type=="ผู้เกษียณอายุราชการ")
                            {
                                emp2+=1;
                                Sumemp2+=1;
                            }
                            if(row.Employment_Type=="ผู้ปฏิบัติงานในมหาวิทยาลัย")
                            {
                                emp3+=1;
                                Sumemp3+=1;
                            }
                        }
                        else if(row.Personnel_Type=="ลูกจ้างของมหาวิทยาลัย")
                        {
                            type2+=1;
                            Sumtype2+=1;
                            if(row.All_PositionTypes=="วิจัย")
                            {
                                research2+=1;
                                Sumresearch2+=1;
                            }
                            if(row.All_PositionTypes=="สนับสนุน")
                            {
                                support2+=1;
                                Sumsupport2+=1;
                            }
                        }
                        else{}
                    });
                    document.getElementById("research1").innerText=research;
                    document.getElementById("research2").innerText=research2;
                    document.getElementById("academic1").innerText=academic;
                    document.getElementById("support1").innerText=support;
                    document.getElementById("support2").innerText=support2;
                    document.getElementById("period").innerText=shortTerm;
                    document.getElementById("emp1").innerText=emp1;
                    document.getElementById("emp2").innerText=emp2;
                    document.getElementById("emp3").innerText=emp3;
                    document.getElementById("type1").innerText=type1;
                    document.getElementById("type2").innerText=type2;
                
            
            
                    var research=0;
                    var research2=0;
                    var academic=0;
                    var support=0;
                    var support2=0;
                    var shortTerm=0;
                    var emp1=0;
                    var emp2=0;
                    var emp3=0;
                    var type1=0;
                    var type2=0;
                    var d_new=data_new.filter(item=>item.pname===category);
                    d_new.forEach((row, index) => {
                        if(row.Personnel_Type=="พนักงานมหาวิทยาลัยงบประมาณเงินรายได้")
                        {
                            type1+=1;
                            Sumtype1+=1;
                            if(row.All_PositionTypes=="วิชาการ")
                            {
                                academic+=1;
                                Sumacademic+=1;
                            }
                            if(row.All_PositionTypes=="วิจัย")
                            {
                                research+=1;
                                Sumresearch+=1;
                            }
                            if(row.All_PositionTypes=="สนับสนุน")
                            {
                                support+=1;
                                Sumsupport+=1;
                            }
                            if(row.Contract_Type=="สัญญาระยะสั้น")
                            {
                                shortTerm+=1;
                                SumshortTerm+=1;
                            }
                            if(row.Employment_Type=="ชาวต่างประเทศ")
                            {
                                emp1+=1;
                                Sumemp1+=1;
                            }
                            if(row.Employment_Type=="ผู้เกษียณอายุราชการ")
                            {
                                emp2+=1;
                                Sumemp2+=1;
                            }
                            if(row.Employment_Type=="ผู้ปฏิบัติงานในมหาวิทยาลัย")
                            {
                                emp3+=1;
                                Sumemp3+=1;
                            }
                        }
                        else if(row.Personnel_Type=="ลูกจ้างของมหาวิทยาลัย")
                        {
                            type2+=1;
                            Sumtype2+=1;
                            if(row.All_PositionTypes=="วิจัย")
                            {
                                research2+=1;
                                Sumresearch2+=1;
                            }
                            if(row.All_PositionTypes=="สนับสนุน")
                            {
                                support2+=1;
                                Sumsupport2+=1;
                            }
                        }
                        else{}
                    });
                    document.getElementById("research1_new").innerText=research;
                    document.getElementById("research2_new").innerText=research2;
                    document.getElementById("academic1_new").innerText=academic;
                    document.getElementById("support1_new").innerText=support;
                    document.getElementById("support2_new").innerText=support2;
                    document.getElementById("period_new").innerText=shortTerm;
                    document.getElementById("emp1_new").innerText=emp1;
                    document.getElementById("emp2_new").innerText=emp2;
                    document.getElementById("emp3_new").innerText=emp3;
                    document.getElementById("type1_new").innerText=type1;
                    document.getElementById("type2_new").innerText=type2;
                
            document.getElementById("research1_sum").innerText=Sumresearch;
            document.getElementById("research2_sum").innerText=Sumresearch2;
            document.getElementById("academic1_sum").innerText=Sumacademic;
            document.getElementById("support1_sum").innerText=Sumsupport;
            document.getElementById("support2_sum").innerText=Sumsupport2;
            document.getElementById("period_sum").innerText=SumshortTerm;
            document.getElementById("emp1_sum").innerText=Sumemp1;
            document.getElementById("emp2_sum").innerText=Sumemp2;
            document.getElementById("emp3_sum").innerText=Sumemp3;
            document.getElementById("type1_sum").innerText=Sumtype1;
            document.getElementById("type2_sum").innerText=Sumtype2;
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
            link.download = 'รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร.csv';
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
            0: { halign: 'left' },  // คอลัมน์แรกให้ชิดซ้าย
        },
        didParseCell: function(data) {
            if (data.section === 'body' && data.column.index === 0) {
                data.cell.styles.halign = 'left'; // จัด text-align left สำหรับคอลัมน์แรก
            }
        },
        margin: { top: 15, right: 5, bottom: 10, left: 5 },
        tableWidth: 'auto'
    });
    doc.save('รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร.pdf');
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
            XLSX.writeFile(wb, 'รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>