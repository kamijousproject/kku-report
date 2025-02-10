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
                        <h4>รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย)</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานอัตรากำลังที่เกษียณอายุราชการในแต่ละปีงบประมาณ (ภาพรวมมหาวิทยาลัย)</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="3">ที่</th>
                                            <th rowspan="3">ส่วนงาน/หน่วยงาน<br>(คณะ L.1 / สนอ L.3)</th>
                                            <th rowspan="3">ชื่อตำแหน่ง</th>
                                            <th rowspan="3">ประเภทตำแหน่ง</th>
                                            <th rowspan="3">Job Family</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2567</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2568</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2569</th>
                                            <th colspan="6">ปีงบประมาณ พ.ศ. 2570</th>
                                            <th rowspan="3">รวมจำนวนอัตราเกษียณอายุราชการ 4 ปี</th>
                                        </tr>
                                        <tr>
                                            <!-- ปีงบประมาณ พ.ศ. 2567 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2568 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2569 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2570 -->
                                            <th rowspan="2">ข้าราชการ</th>
                                            <th colspan="2">พนักงานมหาวิทยาลัย</th>
                                            <th rowspan="2">ลูกจ้างประจำ</th>
                                            <th rowspan="2">ลูกจ้างของมหาวิทยาลัย</th>
                                            <th rowspan="2">รวม</th>
                                        </tr>
                                        <tr>
                                            <!-- ปีงบประมาณ พ.ศ. 2567 -->
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2568 -->
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>
                                            <!-- ปีงบประมาณ พ.ศ. 2569 -->
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>                                           
                                            <!-- ปีงบประมาณ พ.ศ. 2570 -->
                                            <th>แผ่นดิน</th>
                                            <th>รายได้</th>                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>ตัวอย่างส่วนงาน</td>
                                            <td>ตัวอย่างตำแหน่ง</td>
                                            <td>ตัวอย่างประเภท</td>
                                            <td>ตัวอย่าง Job Family</td>
                                            <!-- ปีงบประมาณ พ.ศ. 2567 -->
                                            <td>1</td>
                                            <td>2</td>
                                            <td>3</td>
                                            <td>4</td>
                                            <td>5</td>
                                            <td>6</td>
                                            <!-- ปีงบประมาณ พ.ศ. 2568 -->
                                            <td>17</td>
                                            <td>18</td>
                                            <td>19</td>
                                            <td>20</td>
                                            <td>21</td>
                                            <td>22</td>    
                                            <!-- ปีงบประมาณ พ.ศ. 2569 -->
                                            <td>33</td>
                                            <td>34</td>
                                            <td>35</td>
                                            <td>36</td>
                                            <td>37</td>
                                            <td>38</td>
                                            <!-- ปีงบประมาณ พ.ศ. 2570 -->
                                            <td>49</td>
                                            <td>50</td>
                                            <td>51</td>
                                            <td>52</td>
                                            <td>53</td>
                                            <td>54</td>
                                            <!-- รวมจำนวนอัตราเกษียณอายุราชการ 4 ปี -->
                                            <td>260</td>
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
    <script>
        $(document).ready(function() {
            laodData();
            
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_retirement-fiscal-year'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.wf);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    response.wf.forEach((row, index) => {                   
                        const tr = document.createElement('tr');
                        var sum1=parseInt(row.p1 ?? 0)+parseInt(row.p2?? 0)+parseInt(row.p3?? 0)+parseInt(row.p4?? 0);
                        var sum2=parseInt(row.p1_y2?? 0)+parseInt(row.p2_y2?? 0)+parseInt(row.p3_y2?? 0)+parseInt(row.p4_y2?? 0);
                        var sum3=parseInt(row.p1_y3?? 0)+parseInt(row.p2_y3?? 0)+parseInt(row.p3_y3?? 0)+parseInt(row.p4_y3?? 0);
                        var sum4=parseInt(row.p1_y4?? 0)+parseInt(row.p2_y4?? 0)+parseInt(row.p3_y4?? 0)+parseInt(row.p4_y4?? 0);
                        var sum5=sum1+sum2+sum3+sum4;                      
                        const columns = [
                            { key: 'No', value: index+1 },
                            { key: 'Faculty', value: row.Alias_Default??row.Alias_Default_y2??row.Alias_Default_y3??row.Alias_Default_y4 },                           
                            { key: 'Position', value: row.POSITION??row.POSITION_y2??row.POSITION_y3??row.POSITION_y4 },
                            { key: 'personnel_type', value: row.All_PositionTypes??row.All_PositionTypes_y2??row.All_PositionTypes_y3??row.All_PositionTypes_y4 },  
                            { key: 'Job_Family', value: row.Job_Family??row.Job_Family_y2??row.Job_Family_y3??row.Job_Family_y4 },
                            { key: 'p1', value: row.p1?? 0 },                            
                            { key: 'p2', value: row.p2 ?? 0},
                            { key: 'p3', value: row.p3 ?? 0},
                            { key: 'p4', value: row.p4 ?? 0},     
                            { key: 'p5', value: row.p5 ?? 0},      
                            { key: 'sum1', value: sum1 },
                            { key: 'p1_y2', value: row.p1_y2 ?? 0},                            
                            { key: 'p2_y2', value: row.p2_y2 ?? 0},
                            { key: 'p3_y2', value: row.p3_y2 ?? 0},
                            { key: 'p4_y2', value: row.p4_y2 ?? 0},     
                            { key: 'p5_y2', value: row.p5_y2 ?? 0},      
                            { key: 'sum2', value: sum2 },
                            { key: 'p1_y3', value: row.p1_y3 ?? 0},                            
                            { key: 'p2_y3', value: row.p2_y3 ?? 0},
                            { key: 'p3_y3', value: row.p3_y3 ?? 0},
                            { key: 'p4_y3', value: row.p4_y3 ?? 0},     
                            { key: 'p5_y3', value: row.p5_y3 ?? 0},      
                            { key: 'sum3', value: sum3 },
                            { key: 'p1_y4', value: row.p1_y4 ?? 0},                            
                            { key: 'p2_y4', value: row.p2_y4 ?? 0},
                            { key: 'p3_y4', value: row.p3_y4 ?? 0},
                            { key: 'p4_y4', value: row.p4_y4 ?? 0},     
                            { key: 'p5_y4', value: row.p5_y4 ?? 0},      
                            { key: 'sum4', value: sum4 },    
                            { key: 'sum5', value: sum5 },                                                                         
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
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells.join(","));
            }
            const csvContent = "\uFEFF" + rows.join("\n"); // Add BOM
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
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
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells);
            }
            let xlsContent = "<table>";
            rows.forEach(row => {
                xlsContent += "<tr>" + row.map(cell => `<td>${cell}</td>`).join('') + "</tr>";
            });
            xlsContent += "</table>";

            const blob = new Blob([xlsContent], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.xls');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>