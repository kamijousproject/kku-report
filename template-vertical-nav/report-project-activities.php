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
                                    <h4>ตารางข้อมูลแผนงานและโครงการ</h4>
                                </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {
            laodData();
            
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_project-activities'
                },
                dataType: "json",
                success: function(response) {
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า


                        response.bgp.forEach((row, index) => {                   
                            const tr = document.createElement('tr');

                            const columns = [
                                { key: 'Alias_Default', value: row.Alias_Default },
                                { key: 'Plan_Name', value: row.plan_name },
                                { key: 'Sub_Plan_Name', value: row.sub_plan_name },
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
                                { key: 'Pillar_Name', value: row.pillar_name },
                                { key: 'OKR_Name', value: row.okr_name },
                                { key: 'Principles_of_Good_Governance', value: row.Principles_of_good_governance },
                                { key: 'SDGs', value: row.SDGs }
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
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

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

        // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
        // จะสร้าง aoa ของ thead + merges array
        const { theadRows, theadMerges } = parseThead(table.tHead);

        // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br/>, ไม่ merge) ============
        const tbodyRows = parseTbody(table.tBodies[0]);

        // รวม rows ทั้งหมด: thead + tbody
        const allRows = [...theadRows, ...tbodyRows];

        // สร้าง Workbook + Worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(allRows);

        // ใส่ merges ของ thead ลงใน sheet (ถ้ามี)
        // สังเกตว่า thead อยู่แถวบนสุดของ allRows (index เริ่มจาก 0 ตาม parseThead)
        ws['!merges'] = theadMerges;

        // เพิ่ม worksheet ลงใน workbook
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // เขียนไฟล์เป็น .xls (BIFF8)
        const excelBuffer = XLSX.write(wb, {
            bookType: 'xls',
            type: 'array'
        });

        // สร้าง Blob + ดาวน์โหลด
        const blob = new Blob([excelBuffer], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'report.xls';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        }

        /**
         * -----------------------
         * 1) parseThead: รองรับ merge
         * -----------------------
         * - ใช้ skipMap จัดการ colSpan/rowSpan
         * - ไม่แยก <br/> เป็นแถวใหม่ (โดยทั่วไป header ไม่ต้องแตกแถว)
         * - ถ้า thead มีหลาย <tr> ก็จะได้หลาย row
         * - return: { theadRows: [][] , theadMerges: [] }
         */
        function parseThead(thead) {
        const theadRows = [];
        const theadMerges = [];

        if (!thead) {
            return { theadRows, theadMerges };
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
                .replace(/<\/?[^>]+>/g, '')   // ลบ tag อื่น ถ้าเหลือ
                .trim();

            rowData[colIndex] = text;

            // ดู rowSpan/colSpan
            const rowspan = cell.rowSpan || 1;
            const colspan = cell.colSpan || 1;

            if (rowspan > 1 || colspan > 1) {
                // Push merges object
                theadMerges.push({
                s: { r: rowIndex, c: colIndex },
                e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 }
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

        return { theadRows, theadMerges };
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