<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    #reportTable td {
        text-align: left;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: top;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
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
                        <h4>รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี(สรุปประมาณการรายรับและประมาณการรายจ่าย)</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี(สรุปประมาณการรายรับและประมาณการรายจ่าย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายรับรายจ่าย</h4>
                                </div>
                                <div class="table-responsive">
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
                                    <label for="dropdown4">ส่วนงาน/หน่วยงาน:</label>
                                    <select id="dropdown4" disabled>
                                        <option value="">เลือกส่วนงาน/หน่วยงาน</option>
                                    </select>
                                    <br/>
                                    <!-- Submit Button -->
                                    <button id="submitBtn" disabled>Submit</button>
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            
                                            <tr>
                                                <th>รายการ</th>
                                                <th>งบประมาณ</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="revenue">
                                            
                                        </tbody>
                                        <tbody id="expense">
                                            
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
                //$('#dropdown3').html('<option value="">เลือกแหล่งเงิน</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">เลือกส่วนงาน/หน่วยงาน</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'get_faculty_2',
                        'fiscal_year': year
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

            $('#dropdown4').change(function() {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });


            $('#submitBtn').click(function() {
                let year = $('#dropdown1').val();
                let faculty = $('#dropdown4').val();
                $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-request-summary-revenue',
                    'fiscal_year': year,
                    'faculty': faculty
                },
                dataType: "json",
                success: function(response) {
                    const revenue = document.querySelector('#revenue');
                    revenue.innerHTML = ''; // ล้างข้อมูลเก่า
                    var total=0;
                    if(response.bgp.length > 0)
                    {
                        const parseValue = (value) => {
                            const number = parseFloat(value.replace(/,/g, ''));
                            return isNaN(number) ? 0 : number;
                        };
                        const sums = response.bgp.reduce((acc, item) => {
                            return {
                                Total_Amount_Quantity: acc.Total_Amount_Quantity + parseValue(item.Total_Amount_Quantity),
                            };
                        }, {
                            Total_Amount_Quantity: 0
                        });
                        total=sums.Total_Amount_Quantity;                       
                    }
                    
                    var tr =document.createElement('tr');
                    var td = document.createElement('td');
                    td.setAttribute('colspan', '3');
                    td.textContent = "รายรับ"; // เพิ่มข้อความภายในเซลล์
                    td.style.textAlign = "center";
                    tr.appendChild(td);
                    revenue.appendChild(tr);
                    //console.log(response.bgp);
                    response.bgp.forEach((row, index) => {                   
                        const tr = document.createElement('tr');

                        const columns = [
                            { key: 'Type', value: row.type },  // ประเภทบัญชี
                            { key: 'Total_Amount_Quantity', value: parseInt(row.Total_Amount_Quantity).toLocaleString() }, // ยอดรวมค่าใช้จ่าย
                            { key: 'Total', value: (((parseInt(row.Total_Amount_Quantity)*100)/total)|| 0).toLocaleString()}
                        ];

                        columns.forEach(col => {

                            const td = document.createElement('td');
                            td.textContent = col.value;
                            tr.appendChild(td); 
                        });


                        revenue.appendChild(tr);
                        
                    });
                    
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-request-summary-expense',
                    'fiscal_year': year,
                    'faculty': faculty
                },
                dataType: "json",
                success: function(response) {
                    const revenue = document.querySelector('#expense');
                    revenue.innerHTML = ''; // ล้างข้อมูลเก่า
                    var total=0;
                    if(response.bgp.length > 0)
                    {
                        const parseValue = (value) => {
                            const number = parseFloat(value.replace(/,/g, ''));
                            return isNaN(number) ? 0 : number;
                        };
                        const sums = response.bgp.reduce((acc, item) => {
                            return {
                                Total_Amount_Quantity: acc.Total_Amount_Quantity + parseValue(item.Total_Amount_Quantity),
                            };
                        }, {
                            Total_Amount_Quantity: 0
                        });
                        total=sums.Total_Amount_Quantity;                       
                    }
                    
                    var tr =document.createElement('tr');
                    var td = document.createElement('td');
                    td.setAttribute('colspan', '3');
                    td.textContent = "รายจ่าย"; // เพิ่มข้อความภายในเซลล์
                    td.style.textAlign = "center";
                    tr.appendChild(td);
                    revenue.appendChild(tr);
                    //console.log(response.bgp);
                    response.bgp.forEach((row, index) => {                   
                        const tr = document.createElement('tr');

                        const columns = [
                            { key: 'Type', value: row.type },  // ประเภทบัญชี
                            { key: 'Total_Amount_Quantity', value: parseInt(row.Total_Amount_Quantity).toLocaleString() }, // ยอดรวมค่าใช้จ่าย
                            { key: 'Total', value: (((parseInt(row.Total_Amount_Quantity)*100)/total)|| 0).toLocaleString()}
                        ];

                        columns.forEach(col => {

                            const td = document.createElement('td');
                            td.textContent = col.value;
                            tr.appendChild(td); 
                        });


                        revenue.appendChild(tr);
                        
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