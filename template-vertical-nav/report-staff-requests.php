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
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <!-- แถวที่ 1 -->
                                            <tr>
                                                <th colspan="4">ส่วนงาน/หน่วยงาน</th>
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
    <script>
        $(document).ready(function() {
            laodData();
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'list-faculty'
                },
                dataType: "json",
                success: function(response) {
                    let dropdown = document.getElementById("category");
                    dropdown.innerHTML = '<option value="">-- Select --</option>';
                    response.wf.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category.faculty;
                        option.textContent = category.Alias_Default;
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
            let resultDiv = document.getElementById("result");
            var categoryDropdown = document.getElementById("category");
            var categoryText = categoryDropdown.options[categoryDropdown.selectedIndex].text;
            document.getElementById("faculty").textContent=categoryText;
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
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_staff-requests_current',
                    'slt':category
                },
                dataType: "json",
                success: function(response) {
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
                    response.wf.forEach((row, index) => {
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
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_staff-requests_new',
                    'slt':category
                },
                dataType: "json",
                success: function(response) {
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
                    response.wf.forEach((row, index) => {
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
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
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