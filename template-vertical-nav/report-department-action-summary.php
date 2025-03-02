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
    <script>
        let report_plan_status = [];
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
                    console.log(response.plan);
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
            console.log('filter');

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

            data.forEach(row => {

                if (previousOKRName !== row.okr_name) {
                    const tr = document.createElement('tr');

                    const td1 = document.createElement('td');
                    td1.textContent = row.fa_name === previousFacultyName ? '' : row.fa_name;;
                    tr.appendChild(td1);
                    // สำหรับ si_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                    const td2 = document.createElement('td');
                    td2.textContent = row.pilar_code === previousPilarCode ? '' : row.pilar_code;;
                    tr.appendChild(td2);

                    // สำหรับ so_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                    const td3 = document.createElement('td');
                    td3.textContent = row.pilar_name === previousPilarName ? '' : row.pilar_name;
                    tr.appendChild(td3);

                    const td4 = document.createElement('td');
                    td4.textContent = row.si_code === previousSICode ? '' : row.si_code;
                    tr.appendChild(td4);

                    const td5 = document.createElement('td');
                    td5.textContent = row.si_name === previousSIName ? '' : row.si_name;
                    tr.appendChild(td5);

                    const td6 = document.createElement('td');
                    td6.textContent = row.Strategic_Object === previousSOCode ? '' : row.Strategic_Object;
                    tr.appendChild(td6);

                    const td7 = document.createElement('td');
                    td7.textContent = row.so_name === previousSOName ? '' : row.so_name;
                    tr.appendChild(td7);

                    const td8 = document.createElement('td');
                    td8.textContent = row.OKR === previousOKRCode ? '' : row.OKR;
                    tr.appendChild(td8);

                    const td9 = document.createElement('td');
                    td9.textContent = row.okr_name === previousOKRName ? '' : row.okr_name;
                    tr.appendChild(td9);

                    const td10 = document.createElement('td');
                    td10.textContent = row.Target_OKR_Objective_and_Key_Result;
                    tr.appendChild(td10);

                    const td11 = document.createElement('td');
                    td11.textContent = row.UOM;
                    tr.appendChild(td11);

                    const td12 = document.createElement('td');
                    td12.textContent = row.Quarter_Progress_Value;
                    tr.appendChild(td12);

                    const td13 = document.createElement('td');
                    td13.textContent = null;
                    tr.appendChild(td13);

                    const td14 = document.createElement('td');
                    td14.textContent = null;
                    tr.appendChild(td14);

                    const td15 = document.createElement('td');
                    td15.textContent = null;
                    tr.appendChild(td15);

                    const td16 = document.createElement('td');
                    td16.textContent = siStats[row.okr_name].kspSet.size;
                    tr.appendChild(td16);

                    const td17 = document.createElement('td');
                    td17.textContent = siStats[row.okr_name].totalOKR + ' %';
                    tr.appendChild(td17);

                    const td18 = document.createElement('td');
                    td18.textContent = row.OKR_Progress_Details;
                    tr.appendChild(td18);

                    const td19 = document.createElement('td');
                    td19.textContent = Number(siStats[row.okr_name].totalBudget).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });;
                    tr.appendChild(td19);

                    const td20 = document.createElement('td');
                    td20.textContent = Number(siStats[row.okr_name].totalActual_spend).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    tr.appendChild(td20);

                    const td21 = document.createElement('td');
                    td21.textContent = row.Responsible_person;
                    tr.appendChild(td21);


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