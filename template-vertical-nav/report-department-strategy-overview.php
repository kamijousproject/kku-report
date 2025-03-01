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
                        <h4>รายงานภาพรวมยุทธศาสตร์ ส่วนงาน/หน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานภาพรวมยุทธศาสตร์ ส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานภาพรวมยุทธศาสตร์ ส่วนงาน/หน่วยงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">เสาหลัก</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">ยุทธศาสตร์</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">กลยุทธ์</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">แผนงาน/โครงการ</th>
                                                <th class="align-middle" rowspan="2">รหัส</th>
                                                <th class="align-middle" rowspan="2">ผลลัพธ์สำคัญ</th>
                                                <th class="align-middle" rowspan="2">หน่วยนับ</th>
                                                <th colspan="4">ผลการดำเนินงาน</th>
                                                <th colspan="4">ค่าเป้าหมาย (ปี)</th>
                                                <th class="align-middle" rowspan="2">กรอบวงเงิน (บาท)</th>
                                                <th class="align-middle" rowspan="2">ผู้รับผิดชอบ</th>
                                            </tr>
                                            <tr>
                                                <th>2564</th>
                                                <th>2565</th>
                                                <th>2566</th>
                                                <th>ค่าเฉลี่ย</th>
                                                <th>2567</th>
                                                <th>2568</th>
                                                <th>2569</th>
                                                <th>2570</th>
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
        $(document).ready(function() {
            laodData();
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/api.php",
                data: {
                    'command': 'get_department_strategy_overview'
                },
                dataType: "json",
                success: function(response) {
                    // console.log(response.plan);
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

                    response.plan.forEach(row => {
                        const tr = document.createElement('tr');

                        // สำหรับ si_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                        const td1 = document.createElement('td');
                        td1.textContent = row.Faculty === previousFacultyCode ? '' : row.Faculty;;
                        tr.appendChild(td1);

                        // สำหรับ so_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                        const td2 = document.createElement('td');
                        td2.textContent = row.fa_name === previousFacultyName ? '' : row.fa_name;
                        tr.appendChild(td2);

                        const td3 = document.createElement('td');
                        td3.textContent = row.pilar_code === previousPilarCode ? '' : row.pilar_code;
                        tr.appendChild(td3);

                        const td4 = document.createElement('td');
                        td4.textContent = row.pilar_name === previousPilarName ? '' : row.pilar_name;
                        tr.appendChild(td4);

                        const td5 = document.createElement('td');
                        td5.textContent = row.si_code === previousSICode ? '' : row.si_code;
                        tr.appendChild(td5);

                        const td6 = document.createElement('td');
                        td6.textContent = row.si_name === previousSIName ? '' : row.si_name;
                        tr.appendChild(td6);

                        const td7 = document.createElement('td');
                        td7.textContent = row.Strategic_Object === previousSOCode ? '' : row.Strategic_Object;
                        tr.appendChild(td7);

                        const td8 = document.createElement('td');
                        td8.textContent = row.so_name === previousSOCode ? '' : row.so_name;
                        tr.appendChild(td8);

                        const td9 = document.createElement('td');
                        td9.textContent = row.Strategic_Project;
                        tr.appendChild(td9);

                        const td10 = document.createElement('td');
                        td10.textContent = row.ksp_name;
                        tr.appendChild(td10);

                        const td11 = document.createElement('td');
                        td11.textContent = row.OKR;
                        tr.appendChild(td11);

                        const td12 = document.createElement('td');
                        td12.textContent = row.okr_name;
                        tr.appendChild(td12);

                        const td13 = document.createElement('td');
                        td13.textContent = row.UOM;
                        tr.appendChild(td13);

                        const td14 = document.createElement('td');
                        td14.textContent = null;
                        tr.appendChild(td14);

                        const td15 = document.createElement('td');
                        td15.textContent = null;
                        tr.appendChild(td15);

                        const td16 = document.createElement('td');
                        td16.textContent = null;
                        tr.appendChild(td16);

                        const td17 = document.createElement('td');
                        td17.textContent = null;
                        tr.appendChild(td17);

                        const td18 = document.createElement('td');
                        td18.textContent = row.Y1;
                        tr.appendChild(td18);

                        const td19 = document.createElement('td');
                        td19.textContent = row.Y2;
                        tr.appendChild(td19);

                        const td20 = document.createElement('td');
                        td20.textContent = row.Y3;
                        tr.appendChild(td20);

                        const td21 = document.createElement('td');
                        td21.textContent = row.Y4;
                        tr.appendChild(td21);

                        const td22 = document.createElement('td');
                        td22.textContent = Number(row.Budget_Amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        tr.appendChild(td22);

                        const td23 = document.createElement('td');
                        td23.textContent = row.Responsible_person;
                        tr.appendChild(td23);


                        tableBody.appendChild(tr);

                        // เก็บค่า si_name และ so_name ของแถวนี้ไว้ใช้ในการเปรียบเทียบในแถวถัดไป
                        previousFacultyCode = row.Faculty;
                        previousFacultyName = row.fa_name;
                        previousPilarCode = row.pilar_code;
                        previousPilarName = row.pilar_name;
                        previousSICode = row.si_code;
                        previousSIName = row.si_name;
                        previousSOName = row.so_name;
                        previousSOName = row.so_name;
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