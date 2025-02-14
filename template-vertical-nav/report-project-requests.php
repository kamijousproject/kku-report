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
                        <h4>รายงานสรุปคำขอรายโครงการ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอรายโครงการ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอรายโครงการ</h4>
                                </div>
                                <div class="info-section">
                                    <label for="dropdown1">ปีบริหารงบประมาณ:</label>
                                    <select id="dropdown1">
                                        <option value="">เลือกปีบริหารงบประมาณ</option>
                                    </select>
                                    <br/>
                                    <!-- Dropdown 2 (Changes based on Dropdown 1) -->
                                    <label for="dropdown2">ประเภทงบประมาณ:</label>
                                    <select id="dropdown2" disabled>
                                        <option value="">เลือกประเภทงบประมาณ</option>
                                    </select>
                                    <br/>
                                    <!-- Dropdown 3 (Changes based on Dropdown 2) -->
                                    <label for="dropdown3">แหล่งเงิน:</label>
                                    <select id="dropdown3" disabled>
                                        <option value="">เลือกแหล่งเงิน</option>
                                    </select>
                                    <br/>
                                    <!-- Dropdown 3 (Changes based on Dropdown 2) -->
                                    <label for="dropdown4">ส่วนงาน/หน่วยงาน:</label>
                                    <select id="dropdown4" disabled>
                                        <option value="">เลือกส่วนงาน/หน่วยงาน</option>
                                    </select>
                                    <br/>
                                    <!-- Submit Button -->
                                    <button id="submitBtn" disabled>Submit</button>
                                </div>
                                <br/>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">ที่</th>
                                                <th rowspan="2">โครงการ/กิจกรรม</th>
                                                <th rowspan="2">ประเด็นยุทธศาสตร์</th>
                                                <th rowspan="2">OKR</th>
                                                <th rowspan="2">แผนงาน (ผลผลิต)</th>
                                                <th rowspan="2">แผนงานย่อย (ผลผลิตย่อย/กิจกรรม)</th>
                                                <th colspan="5">งบประมาณ</th>
                                                <th rowspan="2">รวมงบประมาณ</th>
                                                <th colspan="4">แผนการใช้ง่ายงบประมาณ</th>
                                            </tr>
                                            <tr>
                                                <th>1. ค่าใช้จ่าย</th>
                                                <th>2. ค่าใช้จ่าย</th>
                                                <th>3. ค่าใช้จ่าย</th>
                                                <th>4. ค่าใช้จ่าย</th>
                                                <th>5. ค่าใช้จ่ายอื่น</th>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>โครงการ A</td>
                                                <td>ยุทธศาสตร์ 1</td>
                                                <td>OKR 1</td>
                                                <td>ผลผลิต A</td>
                                                <td>กิจกรรมย่อย A1</td>
                                                <td>1,000,000</td>
                                                <td>500,000</td>
                                                <td>300,000</td>
                                                <td>200,000</td>
                                                <td>100,000</td>
                                                <td>2,100,000</td>
                                                <td>500,000</td>
                                                <td>500,000</td>
                                                <td>600,000</td>
                                                <td>500,000</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>โครงการ B</td>
                                                <td>ยุทธศาสตร์ 2</td>
                                                <td>OKR 2</td>
                                                <td>ผลผลิต B</td>
                                                <td>กิจกรรมย่อย B1</td>
                                                <td>800,000</td>
                                                <td>400,000</td>
                                                <td>200,000</td>
                                                <td>300,000</td>
                                                <td>100,000</td>
                                                <td>1,800,000</td>
                                                <td>400,000</td>
                                                <td>500,000</td>
                                                <td>500,000</td>
                                                <td>400,000</td>
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

            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'get_fiscal_year'
                },
                dataType: "json",
                success: function(response) {
                    
                    response.bgp.forEach((row) =>{
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="'+row.y+'">'+row.y+'</option>');
                    });   
                }
            });


            $('#dropdown1').change(function() {
                let year = $(this).val();
                //console.log(year);
                $('#dropdown2').html('<option value="">เลือกประเภทงบประมาณ</option>').prop('disabled', true);
                $('#dropdown3').html('<option value="">เลือกแหล่งเงิน</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'get_fund',
                        'fiscal_year': year
                    },
                    dataType: "json",
                    success: function(response) {
                        //console.log(response);
                        response.fund.forEach((row) =>{
                            $('#dropdown3').append('<option value="'+row.f+'">'+row.f+'</option>').prop('disabled', false);
                        });   
                    }
                    ,
                    error: function(jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });


            $('#dropdown2').change(function() {
                let subcategoryId = $(this).val();
                $('#dropdown3').html('<option value="">Select Item</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);

                if (subcategoryId) {
                    $.ajax({
                        url: 'query.php',
                        type: 'POST',
                        data: { action: 'getItems', subcategory_id: subcategoryId },
                        success: function(response) {
                            $('#dropdown3').html(response).prop('disabled', false);
                        }
                    });
                }
            });
            $('#dropdown3').change(function() {
                let fund = $('#dropdown3').val();
                var year = $('#dropdown1').val();
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                //console.log(year);
                //console.log(fund);
                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'get_faculty',
                        'fiscal_year': year,
                        'fund': fund
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        response.fac.forEach((row) =>{
                            $('#dropdown4').append('<option value="'+row.faculty+'">'+row.faculty+'</option>').prop('disabled', false);
                            
                        });   
                    }
                    ,
                    error: function(jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });

            $('#dropdown4').change(function() {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });


            $('#submitBtn').click(function() {
                let year = $('#dropdown1').val();
                let fund = $('#dropdown2').val();
                let faculty = $('#dropdown3').val();

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
            });
        });
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