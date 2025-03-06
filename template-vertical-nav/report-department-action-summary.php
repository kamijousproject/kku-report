<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    .table-responsive {
        max-height: 30rem;
        /* กำหนดความสูงให้ตารางมี Scroll */
        overflow-y: auto;
    }

    .table thead th {
        position: sticky;
        background-color: #F2F2F2;
        top: 0;
        z-index: 10;
    }

    #reportTable th {
        background-color: #F2F2F2;
    }

    .table thead tr th {
        z-index: 11;
    }

    .table thead tr:first-child th {
        /* ให้แถวแรก (th ที่ colspan) ตรึงที่ด้านบน */
        position: sticky;
        top: 0;
        background: #F2F2F2;
        z-index: 10;
        border-bottom: 1px solid #ffffff;
        /* เพิ่มเส้นขอบใต้ */
    }

    .table thead tr:nth-child(2) th {
        /* ให้แถวที่สอง (th ที่มี day column) ตรึงอยู่ที่ด้านบน */
        position: sticky;
        top: 45.4px;
        background: #F2F2F2;
        z-index: 9;
        border-bottom: 1px solid #ffffff;
        /* เพิ่มเส้นขอบใต้ */
    }

    /* ให้แถวที่สองไม่ถูกบดบังด้วยแถวแรก */
    .table thead tr:nth-child(2) th {
        z-index: 9;
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
                        <h4>รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</h4>
                                </div>
                                <label for="selectcategory">เลือกส่วนงาน:</label>
                                <select name="selectcategory" id="selectcategory" onchange="selectFilter()">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>รหัส</th>
                                                <th>เสาหลัก</th>
                                                <th>รหัส</th>
                                                <th>ยุทธศาสตร์</th>
                                                <th>รหัส</th>
                                                <th>กลยุทธ์</th>
                                                <th>รหัส</th>
                                                <th>ผลลัพธ์ตามวัตถุประสงค์</th>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>หน่วยนับ</th>
                                                <th>ผลงาน ไตรมาส 1</th>
                                                <th>ผลงาน ไตรมาส 2</th>
                                                <th>ผลงาน ไตรมาส 3</th>
                                                <th>ผลงาน ไตรมาส 4</th>
                                                <th>ผลงาน รวม</th>
                                                <th>ร้อยละ ความสำเร็จ</th>
                                                <th>รายละเอียดผลการดำเนินงาน</th>
                                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                                <th>งบประมาณที่ใช้</th>
                                                <th>ผู้รับผิดชอบหลัก</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let report_plan_status = [];
        let quarter = [];
        let filterdata = []
        let categories = new Set();
        $(document).ready(function() {
            laodData();
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/api.php",
                data: {
                    'command': 'get_department_action_summary'
                },
                dataType: "json",
                success: function(response) {
                    report_plan_status = response.plan;
                    quarter = response.quarter;
                    response.plan.forEach(data => {
                        categories.add(data.fa_name);

                    })
                    const categorySelect = document.getElementById("selectcategory");

                    // เพิ่มตัวเลือกทั้งหมด
                    categorySelect.innerHTML = '<option value="">-- ทั้งหมด --</option>';

                    // เพิ่มตัวเลือกสำหรับแต่ละ fa_name ที่ไม่ซ้ำ
                    categories.forEach(category => {
                        const option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        categorySelect.appendChild(option);
                    });
                    writeBody(response.plan);

                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
        }

        function selectFilter() {
            const selectedCategory = document.getElementById('selectcategory').value;
            if (selectedCategory === "") {
                filterdata = report_plan_status;
                writeBody(filterdata);
            } else {
                // filter ข้อมูลที่ fa_name ตรงกับค่าที่เลือก
                filterdata = report_plan_status.filter(item => item.fa_name === selectedCategory);
                writeBody(filterdata);
            }
            document.querySelector('.table-responsive').scrollTop = 0;
        }

        function writeBody(data) {
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

            let previousFacultyCode = '';
            let previousFacultyName = '';
            let previousPilarCode = '';
            let previousPilarName = '';
            let previousSICode = '';
            let previousSIName = '';
            let previousSOCode = '';
            let previousSOName = '';
            let previousOKRCode = '';
            let previousOKRName = '';

            let totalOKR;
            let alltotalOKR;
            let totalKSP = 0;
            const siStats = {}; // เก็บข้อมูล SO, OKR และ KSP ที่ไม่ซ้ำภายในแต่ละ SI


            data.forEach(row => {
                if (!siStats[row.okr_name]) {
                    siStats[row.okr_name] = {
                        kspSet: new Set(), // เก็บ KSP ที่ไม่ซ้ำ
                        okrProgress: {},
                        kspBudget: {},
                        kspActual_spend: {}
                    };
                }
                siStats[row.okr_name].kspSet.add(row.ksp_name);

                // ถ้า OKR ยังไม่มีใน okrProgress ให้เริ่มเก็บค่า
                if (!siStats[row.okr_name].okrProgress[row.ksp_name]) {
                    siStats[row.okr_name].okrProgress[row.ksp_name] = parseFloat((row.Quarter_Progress_Value / row.Target_OKR_Objective_and_Key_Result) * 100) || 0;
                    siStats[row.okr_name].kspBudget[row.ksp_name] = parseFloat(row.Allocated_budget) || 0;
                    siStats[row.okr_name].kspActual_spend[row.ksp_name] = parseFloat(row.Actual_Spend_Amount) || 0;
                    // console.log(siStats[row.okr_name].okrProgress[row.ksp_name]);
                }

            });

            // แสดงจำนวน SO, OKR, KSP ที่ไม่ซ้ำ และผลรวมของ Quarter_Progress_Value ของ OKR ที่ไม่ซ้ำ
            Object.keys(siStats).forEach(si => {
                totalOKR = Object.values(siStats[si].okrProgress).reduce((sum, value) => sum + value, 0);
                totalBudget = Object.values(siStats[si].kspBudget).reduce((sum, value) => sum + value, 0);
                totalActual_spend = Object.values(siStats[si].kspActual_spend).reduce((sum, value) => sum + value, 0);
                siStats[si].totalOKR = (totalOKR / siStats[si].kspSet.size);
                siStats[si].totalBudget = totalBudget;
                siStats[si].totalActual_spend = totalActual_spend;
                // totalKSP += siStats[si].kspSet.size;
                //  console.log(`SI: ${si},  Unique KSP Count: ${siStats[si].kspSet.size}, totalOKR ${totalOKR}, real percent ${siStats[si].totalOKR},sumBudget ${totalBudget}`);
            });

            okrResults = {};
            // เก็บข้อมูลใน okrResults
            quarter.forEach(row => {
                if (!okrResults[row.OKR]) {
                    okrResults[row.OKR] = {
                        Q1: "",
                        Q2: "",
                        Q3: "",
                        Q4: ""
                    };
                }

                if (row.Version === "Q1_Prog") {
                    okrResults[row.OKR].Q1 = row.Quarter_Progress_Value;
                } else if (row.Version === "Q2_Prog") {
                    okrResults[row.OKR].Q2 = row.Quarter_Progress_Value;
                } else if (row.Version === "Q3_Prog") {
                    okrResults[row.OKR].Q3 = row.Quarter_Progress_Value;
                } else if (row.Version === "Q4_Prog") {
                    okrResults[row.OKR].Q4 = row.Quarter_Progress_Value;
                }
            });


            data.forEach(row => {

                if (previousOKRName !== row.okr_name) {
                    const tr = document.createElement('tr');

                    const createCell = (text, align = "left") => {
                        const td = document.createElement('td');
                        td.textContent = text;
                        td.style.textAlign = align;
                        return td;
                    };

                    const td1 = createCell(row.fa_name === previousFacultyName ? '' : row.fa_name);
                    tr.appendChild(td1);

                    const td2 = createCell(row.pilar_code === previousPilarCode ? '' : row.pilar_code);
                    tr.appendChild(td2);

                    const td3 = createCell(row.pilar_name === previousPilarName ? '' : row.pilar_name);
                    tr.appendChild(td3);

                    const td4 = createCell(row.si_code === previousSICode ? '' : row.si_code);
                    tr.appendChild(td4);

                    const td5 = createCell(row.si_name === previousSIName ? '' : row.si_name);
                    tr.appendChild(td5);

                    const td6 = createCell(row.Strategic_Object === previousSOCode ? '' : row.Strategic_Object);
                    tr.appendChild(td6);

                    const td7 = createCell(row.so_name === previousSOName ? '' : row.so_name);
                    tr.appendChild(td7);

                    const td8 = createCell(row.OKR === previousOKRCode ? '' : row.OKR);
                    tr.appendChild(td8);

                    const td9 = createCell(row.okr_name === previousOKRName ? '' : row.okr_name);
                    tr.appendChild(td9);

                    const td10 = createCell(row.Target_OKR_Objective_and_Key_Result);
                    tr.appendChild(td10);

                    const td11 = createCell(row.UOM);
                    tr.appendChild(td11);

                    // การจัดการกับ okrProgress
                    if (okrResults[row.OKR]) {
                        const okrProgress = okrResults[row.OKR];

                        const formatNumber = (value) => {
                            return value ? parseFloat(value).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) : '';
                        };

                        const tdQ1 = createCell(formatNumber(okrProgress.Q1), "right");
                        tr.appendChild(tdQ1);

                        const tdQ2 = createCell(formatNumber(okrProgress.Q2), "right");
                        tr.appendChild(tdQ2);

                        const tdQ3 = createCell(formatNumber(okrProgress.Q3), "right");
                        tr.appendChild(tdQ3);

                        const tdQ4 = createCell(formatNumber(okrProgress.Q4), "right");
                        tr.appendChild(tdQ4);
                    }

                    // คำนวณผลรวมของ Q1, Q2, Q3, Q4
                    let totalQuarterProgress = 0;

                    if (okrResults[row.OKR]) {
                        const okrProgress = okrResults[row.OKR];
                        totalQuarterProgress = (parseFloat(okrProgress.Q1) || 0) + (parseFloat(okrProgress.Q2) || 0) + (parseFloat(okrProgress.Q3) || 0) + (parseFloat(okrProgress.Q4) || 0);
                    }

                    const tdTotal = createCell(totalQuarterProgress.toFixed(2), "right");
                    tr.appendChild(tdTotal);

                    const tdProgressPercentage = createCell(((totalQuarterProgress / row.Target_OKR_Objective_and_Key_Result) * 100).toFixed(2) + ' %', "right");
                    tr.appendChild(tdProgressPercentage);

                    const td12 = createCell(row.OKR_Progress_Details);
                    tr.appendChild(td12);

                    const td13 = createCell(Number(siStats[row.okr_name].totalBudget).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }), "right");
                    tr.appendChild(td13);

                    const td14 = createCell(Number(siStats[row.okr_name].totalActual_spend).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }), "right");
                    tr.appendChild(td14);

                    const td15 = createCell(row.Responsible_person);
                    tr.appendChild(td15);

                    tableBody.appendChild(tr);
                }



                // เก็บค่า si_name และ so_name ของแถวนี้ไว้ใช้ในการเปรียบเทียบในแถวถัดไป
                previousFacultyCode = row.Faculty;
                previousFacultyName = row.fa_name;
                previousPilarCode = row.pilar_code;
                previousPilarName = row.pilar_name;
                previousSICode = row.si_code;
                previousSIName = row.si_name;
                previousSOCode = row.Strategic_Object;
                previousSOName = row.so_name;
                previousOKRCode = row.OKR;
                previousOKRName = row.okr_name;
            });
        }



        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('l', 'mm', [305, 215.9]); // Legal landscape size

            // Add Thai font
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");

            // Configure autoTable
            doc.autoTable({
                html: '#reportTable',
                startY: 25,
                theme: 'grid',
                styles: {
                    font: "THSarabun",
                    fontSize: 8,
                    cellPadding: 1,
                    lineWidth: 0.1,
                    lineColor: [0, 0, 0],
                    minCellHeight: 6
                },
                headStyles: {
                    fillColor: [220, 230, 241],
                    textColor: [0, 0, 0],
                    fontSize: 8,
                    fontStyle: 'bold',
                    halign: 'center',
                    valign: 'middle',
                    minCellHeight: 12
                },
                columnStyles: {
                    0: {
                        cellWidth: 15
                    },
                    1: {
                        cellWidth: 10
                    },
                    2: {
                        cellWidth: 25
                    },
                    3: {
                        cellWidth: 10
                    },
                    4: {
                        cellWidth: 25
                    },
                    5: {
                        cellWidth: 10
                    },
                    6: {
                        cellWidth: 25
                    },
                    7: {
                        cellWidth: 10
                    },
                    8: {
                        cellWidth: 25
                    },
                    9: {
                        cellWidth: 10
                    },
                    10: {
                        cellWidth: 10
                    },
                    11: {
                        cellWidth: 10
                    },
                    12: {
                        cellWidth: 10
                    },
                    13: {
                        cellWidth: 10
                    },
                    14: {
                        cellWidth: 10
                    },
                    15: {
                        cellWidth: 10
                    },
                    16: {
                        cellWidth: 10
                    },
                    17: {
                        cellWidth: 15
                    },
                    18: {
                        cellWidth: 15
                    },
                    19: {
                        cellWidth: 15
                    },
                    20: {
                        cellWidth: 10
                    },
                },
                didDrawPage: function(data) {
                    // Add header
                    doc.setFontSize(16);
                    doc.text('รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน', 14, 15);

                    // Add footer with page number
                    doc.setFontSize(10);
                    doc.text(
                        'หน้า ' + doc.internal.getCurrentPageInfo().pageNumber + ' จาก ' + doc.internal.getNumberOfPages(),
                        doc.internal.pageSize.width - 20,
                        doc.internal.pageSize.height - 10, {
                            align: 'right'
                        }
                    );
                },
                // Handle cell styles
                didParseCell: function(data) {
                    // Center align all header cells
                    if (data.section === 'head') {
                        data.cell.styles.halign = 'center';
                        data.cell.styles.valign = 'middle';
                        data.cell.styles.cellPadding = 1;
                    }

                    // Center align all body cells except the second column (ส่วนงาน/หน่วยงาน)
                    if (data.section === 'body') {
                        if (data.column.index === 9 || data.column.index === 11 || data.column.index === 12 || data.column.index === 13 || data.column.index === 14 || data.column.index === 15 || data.column.index === 16 || data.column.index === 18 || data.column.index === 19) {
                            data.cell.styles.halign = 'right';
                        } else {
                            data.cell.styles.halign = 'left';
                        }
                    }

                    // Style footer row
                    if (data.section === 'foot') {
                        data.cell.styles.fontStyle = 'bold';
                        data.cell.styles.fillColor = [240, 240, 240];
                        if (data.column.index !== 1) {
                            data.cell.styles.halign = 'center';
                        }
                    }
                },
                // Handle table width
                margin: {
                    top: 25,
                    right: 7,
                    bottom: 15,
                    left: 7
                },
                tableWidth: 'auto'
            });

            // Save the PDF
            doc.save('รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน.pdf');
        }

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

            // สร้างตาราง 2D สำหรับเก็บข้อมูล CSV (+1 เพื่อเพิ่มแถวใหม่ด้านบน)
            let csvMatrix = Array.from({
                length: numRows + 1
            }, () => Array(maxCols).fill('""'));

            // ✅ เพิ่ม "text" ใน cell แรกของ CSV
            csvMatrix[0][0] = `"รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน"`;

            // ใช้ตัวแปรตรวจสอบว่า cell ไหนถูก merge ไปแล้ว
            let cellMap = Array.from({
                length: numRows
            }, () => Array(maxCols).fill(false));

            for (let rowIndex = 0; rowIndex < numRows; rowIndex++) {
                const row = table.rows[rowIndex];
                let colIndex = 0;

                for (const cell of row.cells) {
                    while (cellMap[rowIndex][colIndex]) {
                        colIndex++;
                    }

                    let text = cell.innerText.trim().replace(/"/g, '""'); // Escape double quotes

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    // ✅ ขยับ index ข้อมูลลง 1 แถว เพื่อรองรับแถว "text"
                    csvMatrix[rowIndex + 1][colIndex] = `"${text}"`;

                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            cellMap[rowIndex + r][colIndex + c] = true;

                            if (r !== 0 || c !== 0) {
                                csvMatrix[rowIndex + r + 1][colIndex + c] = '""';
                            }
                        }
                    }

                    colIndex += colspan;
                }
            }

            // แปลงข้อมูลเป็น CSV
            const csvContent = "\uFEFF" + csvMatrix.map(row => row.join(',')).join('\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }



        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const {
                theadRows,
                theadMerges
            } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            // ============ ส่วนที่ 3: ข้อความพิเศษในแถวแรก (row0) ============
            const row0 = ['รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน']; // เพิ่มข้อความพิเศษที่แถวแรก

            // สร้าง allRows โดยให้ row0 เป็นแถวแรก
            const allRows = [row0, ...theadRows, ...tbodyRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet
            // ทำการย้ายการ merge เพื่อไม่ให้กระทบกับ row0
            ws['!merges'] = theadMerges.map(merge => ({
                s: {
                    r: merge.s.r + 1,
                    c: merge.s.c
                }, // เลื่อนแถวที่ merge ลงไป 1
                e: {
                    r: merge.e.r + 1,
                    c: merge.e.c
                } // เลื่อนแถวที่ merge ลงไป 1
            }));

            // กำหนดให้ Header (thead) อยู่กึ่งกลาง
            theadRows.forEach((row, rowIndex) => {
                row.forEach((_, colIndex) => {
                    const cellAddress = XLSX.utils.encode_cell({
                        r: rowIndex + 1, // เริ่มจากแถวที่ 1 เพื่อไม่ให้ซ้ำกับ row0
                        c: colIndex
                    });
                    if (!ws[cellAddress]) return;
                    ws[cellAddress].s = {
                        alignment: {
                            horizontal: "center",
                            vertical: "center"
                        }, // จัดให้อยู่กึ่งกลาง
                        font: {
                            bold: true
                        } // ทำให้ header ตัวหนา
                    };
                });
            });

            // ตั้งค่าความกว้างของคอลัมน์ให้พอดีกับเนื้อหา
            ws['!cols'] = new Array(theadRows[0].length).fill({
                wch: 15
            });

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์เป็น .xls
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xls',
                type: 'array'
            });

            // สร้าง Blob + ดาวน์โหลดไฟล์
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function parseThead(thead) {
            const theadRows = [];
            const theadMerges = [];

            if (!thead) {
                return {
                    theadRows,
                    theadMerges
                };
            }

            // Map กันการเขียนทับ merge
            const skipMap = {};

            for (let rowIndex = 0; rowIndex < thead.rows.length; rowIndex++) {
                const tr = thead.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    // ข้ามเซลล์ที่ถูก merge ครอบไว้
                    while (skipMap[`${rowIndex},${colIndex}`]) {
                        rowData[colIndex] = "";
                        colIndex++;
                    }

                    const cell = tr.cells[cellIndex];
                    // ไม่แยก <br/> → แค่แทน &nbsp; เป็น space
                    let text = cell.innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length)) // &nbsp; => spaces
                        .replace(/<br\s*\/?>/gi, ' ') // ถ้ามี <br/> ใน thead ก็เปลี่ยนเป็นช่องว่าง (ไม่แตกแถว)
                        .replace(/<\/?[^>]+>/g, '') // ลบ tag อื่น ถ้าเหลือ
                        .trim();

                    rowData[colIndex] = text;

                    // ดู rowSpan/colSpan
                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        // Push merges object
                        theadMerges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            },
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            }
                        });

                        // Mark skipMap
                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r === 0 && c === 0) continue;
                                skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                            }
                        }
                    }
                    colIndex++;
                }
                theadRows.push(rowData);
            }

            return {
                theadRows,
                theadMerges
            };
        }

        /**
         * -----------------------
         * 2) parseTbody: แตก <br/> เป็นหลาย sub-row
         * -----------------------
         * - ไม่ทำ merge (ตัวอย่าง) เพื่อความง่าย
         * - ถ้าใน tbody มี colSpan/rowSpan ต้องประยุกต์ skipMap ต่อเอง
         */
        function parseTbody(tbody) {
            const rows = [];

            if (!tbody) return rows;

            for (const tr of tbody.rows) {
                // เก็บ sub-lines ของแต่ละเซลล์
                const cellLines = [];
                let maxSubLine = 1;

                for (const cell of tr.cells) {
                    // (a) แปลง &nbsp; → space ตามจำนวน
                    // (b) แปลง <br/> → \n เพื่อนำไป split เป็นหลายบรรทัด
                    let html = cell.innerHTML.replace(/(&nbsp;)+/g, match => {
                        const count = match.match(/&nbsp;/g).length;
                        return ' '.repeat(count);
                    });
                    html = html.replace(/<br\s*\/?>/gi, '\n');

                    // (c) ลบแท็กอื่น ๆ (ถ้าต้องการ)
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // (d) split ด้วย \n → ได้หลาย sub-lines
                    const lines = html.split('\n').map(x => x.trimEnd());
                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }
                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];
                    for (const lines of cellLines) {
                        rowData.push(lines[i] || ''); // ถ้าไม่มีบรรทัด => ใส่ว่าง
                    }
                    rows.push(rowData);
                }
            }

            return rows;
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>