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
                        <h4>รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h4>รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย</h4>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="reportTable" class="table table-hover">
                                            <thead>
                                                <tr class="text-nowrap">
                                                    <th rowspan="4">ลำดับ</th>
                                                    <th rowspan="4">รหัส</th>
                                                    <th rowspan="4">ส่วนงาน/หน่วยงาน</th>
                                                    <th rowspan="4">จำนวนผลลัพธ์/ตัวชี้วัดทั้งหมด</th>
                                                    <th colspan="19">ความสอดคล้องของแผน</th>
                                                </tr>
                                                <tr class="text-nowrap">
                                                    <th colspan="13">แผนยุทธศาสตร์การบริหารมหาวิทยาลัยขอนแก่น</th>
                                                    <th colspan="2">แผนพัธกิจ</th>
                                                    <th colspan="2">แผนสรรหา</th>
                                                    <th colspan="2">แผนสร้างความโดดเด่น</th>
                                                </tr>
                                                <tr class="text-nowrap">
                                                    <th rowspan="2">จำนวน</th>
                                                    <th rowspan="2">ร้อยละ</th>
                                                    <th colspan="11">ยุทธศาสตร์</th>
                                                    <th rowspan="2">จำนวน</th>
                                                    <th rowspan="2">ร้อยละ</th>
                                                    <th rowspan="2">จำนวน</th>
                                                    <th rowspan="2">ร้อยละ</th>
                                                    <th rowspan="2">จำนวน</th>
                                                    <th rowspan="2">ร้อยละ</th>
                                                </tr>
                                                <tr class="text-nowrap">
                                                    <th>1</th>
                                                    <th>2</th>
                                                    <th>3</th>
                                                    <th>4</th>
                                                    <th>5</th>
                                                    <th>6</th>
                                                    <th>7</th>
                                                    <th>8</th>
                                                    <th>9</th>
                                                    <th>10</th>
                                                    <th>11</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>001</td>
                                                    <td>หน่วยงาน A</td>
                                                    <td>10</td>
                                                    <td>2</td>
                                                    <td>3</td>
                                                    <td>1</td>
                                                    <td>1</td>
                                                    <td>1</td>
                                                    <td>1</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>1</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>5</td>
                                                    <td>50%</td>
                                                    <td>3</td>
                                                    <td>30%</td>
                                                    <td>2</td>
                                                    <td>20%</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>002</td>
                                                    <td>หน่วยงาน B</td>
                                                    <td>15</td>
                                                    <td>5</td>
                                                    <td>4</td>
                                                    <td>2</td>
                                                    <td>1</td>
                                                    <td>2</td>
                                                    <td>1</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td>7</td>
                                                    <td>46.67%</td>
                                                    <td>4</td>
                                                    <td>26.67%</td>
                                                    <td>3</td>
                                                    <td>20%</td>
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
                url: "../server/api.php",
                data: {
                    'command': 'get_strategic-indicators'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.plan);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    response.plan.forEach((row, index) => {                   
                        const tr = document.createElement('tr');
                        var sum1= parseInt(row.s1)+parseInt(row.s2)+parseInt(row.s3)+parseInt(row.s4)
                        +parseInt(row.s5)+parseInt(row.s6)+parseInt(row.s7)+parseInt(row.s8)+parseInt(row.s9)
                        +parseInt(row.s10)+parseInt(row.s11);
                        const columns = [
                            { key: 'No', value: index+1 },
                            { key: 'fac_code', value: (row.Alias_Default).substring(0, 2) },
                            { key: 'fac', value: row.Alias_Default },
                            { key: 'count_okr', value: parseInt(row.count_okr).toLocaleString() },
                            { key: 'sum1', value: parseInt(sum1).toLocaleString() },
                            { key: 'avg1', value: ((parseInt(sum1)*100)/parseInt(row.count_okr)).toLocaleString()+"%" },
                            { key: 's1', value: parseInt(row.s1).toLocaleString() },
                            { key: 's2', value: parseInt(row.s2).toLocaleString() },
                            { key: 's3', value: parseInt(row.s3).toLocaleString() },
                            { key: 's4', value: parseInt(row.s4).toLocaleString() },
                            { key: 's5', value: parseInt(row.s5).toLocaleString() },
                            { key: 's6', value: parseInt(row.s6).toLocaleString() },
                            { key: 's7', value: parseInt(row.s7).toLocaleString() },
                            { key: 's8', value: parseInt(row.s8).toLocaleString() },
                            { key: 's9', value: parseInt(row.s9).toLocaleString() },
                            { key: 's10', value: parseInt(row.s10).toLocaleString() },
                            { key: 's11', value: parseInt(row.s11).toLocaleString() },
                            { key: 'p1', value: "" },    
                            { key: 'p1', value: "" },  
                            { key: 'dev_plan', value: parseInt(row.dev_plan).toLocaleString() },  
                            { key: 'avg2', value: ((parseInt(row.dev_plan)*100)/parseInt(row.count_okr)).toLocaleString()+"%" },  
                            { key: 'divis', value: parseInt(row.divis).toLocaleString() },  
                            { key: 'avg3', value: ((parseInt(row.divis)*100)/parseInt(row.count_okr)).toLocaleString()+"%" },                                                                  
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