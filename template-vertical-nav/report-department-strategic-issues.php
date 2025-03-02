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
                        <h4>รายงานผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>(จำแนกตามประเด็นยุทธศาสตร์-ระดับ ส่วนงาน/หน่วยงาน)</h4>
                                </div>
                                <label for="selectcategory">เลือกส่วนงาน:</label>
                                <select name="selectcategory" id="selectcategory" onchange="selectFilter()">
                                    <option value="">-- ทั้งหมด --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th class="align-middle" rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                                <th class="align-middle" rowspan="2">เสาหลัก</th>
                                                <th class="align-middle" rowspan="2">ยุทธศาสตร์</th>
                                                <th colspan="3">จำนวน</th>
                                                <th class="align-middle" rowspan="2">ร้อยละ ความสำเร็จ</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th>กลยุทธ์</th>
                                                <th>ผลลัพธ์ตามวัตถุประสงค์</th>
                                                <th>แผนงาน/โครงการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot id="reportTableFooter">
                                            <!-- แถวรวมจะถูกเพิ่มที่นี่ -->
                                        </tfoot>
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
                    'command': 'get_department_strategic_issues'
                },
                dataType: "json",
                success: function(response) {
                    report_plan_status = response.plan;
                    // console.log(response.plan);
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
            let totalOKRProgress;
            let alltotalOKRProgress = 0;
            let totalSO = 0;
            let totalOKR = 0;
            let totalKSP = 0;
            const siStats = {}; // เก็บข้อมูล SO, OKR และ KSP ที่ไม่ซ้ำภายในแต่ละ SI


            data.forEach(row => {
                if (!siStats[row.si_name]) {
                    siStats[row.si_name] = {
                        soSet: new Set(), // เก็บ SO ที่ไม่ซ้ำ
                        okrSet: new Set(), // เก็บ OKR ที่ไม่ซ้ำ
                        kspSet: new Set(), // เก็บ KSP ที่ไม่ซ้ำ
                        okrProgress: {}
                    };
                }

                siStats[row.si_name].soSet.add(row.so_name);
                siStats[row.si_name].okrSet.add(row.okr_name);
                siStats[row.si_name].kspSet.add(row.ksp_name);

                // ถ้า OKR ยังไม่มีใน okrProgress ให้เริ่มเก็บค่า
                
                if (!siStats[row.si_name].okrProgress[row.okr_name]) {
                    siStats[row.si_name].okrProgress[row.okr_name] = Math.min(
                                parseFloat((row.Quarter_Progress_Value / row.Target_OKR_Objective_and_Key_Result) * 100) || 0,
                                100
                            );
                   
                }
            });

            // แสดงจำนวน SO, OKR, KSP ที่ไม่ซ้ำ และผลรวมของ Quarter_Progress_Value ของ OKR ที่ไม่ซ้ำ
            Object.keys(siStats).forEach(si => {
                totalOKRProgress = Object.values(siStats[si].okrProgress).reduce((sum, value) => sum + value, 0);
                alltotalOKRProgress += Object.values(siStats[si].okrProgress).reduce((sum, value) => sum + value, 0);
                siStats[si].totalOKRProgress = (totalOKRProgress / siStats[si].okrSet.size);
                totalSO += siStats[si].soSet.size;
                totalOKR += siStats[si].okrSet.size;
                totalKSP += siStats[si].kspSet.size;
                // console.log(`SI: ${si}, Unique SO Count: ${siStats[si].soSet.size}, Unique OKR Count: ${siStats[si].okrSet.size}, Unique KSP Count: ${siStats[si].kspSet.size}, Total OKR Progress: ${totalOKRProgress}`);
            });

            data.forEach(row => {
                if (previousSIName !== row.si_name) {
                    const tr = document.createElement('tr');

                    // สำหรับ si_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                    const td1 = document.createElement('td');
                    td1.textContent = row.fa_name === previousFacultyName ? '' : row.fa_name;
                    tr.appendChild(td1);

                    // สำหรับ so_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                    const td2 = document.createElement('td');
                    td2.textContent = row.pilar_name === previousPilarName ? '' : row.pilar_name;
                    tr.appendChild(td2);

                    const td3 = document.createElement('td');
                    td3.textContent = row.si_name === previousSIName ? '' : row.si_name;
                    tr.appendChild(td3);

                    const td4 = document.createElement('td');
                    td4.textContent = siStats[row.si_name].soSet.size;
                    tr.appendChild(td4);

                    const td5 = document.createElement('td');
                    td5.textContent = siStats[row.si_name].okrSet.size;
                    tr.appendChild(td5);

                    const td6 = document.createElement('td');
                    td6.textContent = siStats[row.si_name].kspSet.size;
                    tr.appendChild(td6);

                    const td7 = document.createElement('td');
                    td7.textContent = (siStats[row.si_name].totalOKRProgress).toFixed(2) + ' %';
                    tr.appendChild(td7);

                    tableBody.appendChild(tr);
                    // เก็บค่า si_name และ so_name ของแถวนี้ไว้ใช้ในการเปรียบเทียบในแถวถัดไป
                }
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
            // เพิ่มแถวใน footer สำหรับผลรวม
            const tableFooter = document.querySelector('#reportTableFooter');
            tableFooter.innerHTML = '';
            const footerRow = document.createElement('tr');
            
            const footerTd1 = document.createElement('td');
            footerTd1.textContent = 'รวม';
            footerTd1.colSpan = 3;
            footerRow.appendChild(footerTd1);

            const footerTd2 = document.createElement('td');
            footerTd2.textContent = totalSO;
            footerRow.appendChild(footerTd2);

            const footerTd3 = document.createElement('td');
            footerTd3.textContent = totalOKR;
            footerRow.appendChild(footerTd3);

            const footerTd4 = document.createElement('td');
            footerTd4.textContent = totalKSP;
            footerRow.appendChild(footerTd4);

            const footerTd5 = document.createElement('td');
            footerTd5.textContent = (alltotalOKRProgress / totalOKR).toFixed(2) + ' %';
            footerRow.appendChild(footerTd5);

            // เพิ่มแถวผลรวมไปยัง <tfoot>
            
            tableFooter.appendChild(footerRow);

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