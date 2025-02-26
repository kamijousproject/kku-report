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
                        <h4>รายงานสถานะของแผนงานแต่ละแผน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสถานะของแผนงานแต่ละแผน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสถานะของแผนงานแต่ละแผน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>รหัส</th>
                                                <th>ยุทธศาสตร์</th>
                                                <th>รหัส</th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th colspan="4">สถานะ (Status)</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>ยังไม่ดำเนินการ</th>
                                                <th>อยู่ระหว่างดำเนินการ</th>
                                                <th>ดำเนินการแล้ว</th>
                                                <th>ยกเลิก</th>
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
                    'command': 'get_kku_planing_status'
                },
                dataType: "json",
                success: function(response) {
                    // console.log(response.plan);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    let previousFacultyName = '';
                    let previousSIName = '';
                    let previousSICode = '';
                    let previousKSPName = '';

                    response.plan.forEach(row => {
                        const tr = document.createElement('tr');

                        // สำหรับ si_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                        const td1 = document.createElement('td');
                        td1.textContent = row.fa_name === previousFacultyName ? '' : row.fa_name;
                        tr.appendChild(td1);

                        // สำหรับ so_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                        const td2 = document.createElement('td');
                        td2.textContent = row.si_code === previousSICode ? '' : row.si_code;
                        tr.appendChild(td2);

                        const td3 = document.createElement('td');
                        td3.textContent = row.si_name === previousSIName ? '' : row.si_name;
                        tr.appendChild(td3);

                        const td4 = document.createElement('td');
                        td4.textContent = row.Strategic_Project;
                        tr.appendChild(td4);

                        const td5 = document.createElement('td');
                        td5.textContent = row.ksp_name;
                        tr.appendChild(td5);

                        if (row.Progress_Status === "Not Started") {
                            const td6 = document.createElement('td');
                            td6.innerHTML = `<span class="badge badge-secondary">X</span><br>`+row.Strategic_Project_Progress_Details;
                            tr.appendChild(td6);

                            const td7 = document.createElement('td');
                            td7.innerHTML = ``;
                            tr.appendChild(td7);

                            const td8 = document.createElement('td');
                            td8.innerHTML = ``;
                            tr.appendChild(td8);

                            const td9 = document.createElement('td');
                            td9.innerHTML = ``;
                            tr.appendChild(td9);
                        }

                        if (row.Progress_Status === "In Progress") {
                            const td6 = document.createElement('td');
                            td6.innerHTML = ``;
                            tr.appendChild(td6);

                            const td7 = document.createElement('td');
                            td7.innerHTML = `<span class="badge badge-primary">X</span><br>`+row.Strategic_Project_Progress_Details;
                            tr.appendChild(td7);

                            const td8 = document.createElement('td');
                            td8.innerHTML = ``;
                            tr.appendChild(td8);

                            const td9 = document.createElement('td');
                            td9.innerHTML = ``;
                            tr.appendChild(td9);
                        }

                        if (row.Progress_Status === "Completed") {
                            const td6 = document.createElement('td');
                            td6.innerHTML = ``;
                            tr.appendChild(td6);

                            const td7 = document.createElement('td');
                            td7.innerHTML = ``;
                            tr.appendChild(td7);

                            const td8 = document.createElement('td');
                            td8.innerHTML = `<span class="badge badge-success">X</span><br>`+row.Strategic_Project_Progress_Details;
                            tr.appendChild(td8);

                            const td9 = document.createElement('td');
                            td9.innerHTML = ``;
                            tr.appendChild(td9);
                        }

                        if (row.Progress_Status === "Cancelled") {
                            const td6 = document.createElement('td');
                            td6.innerHTML = ``;
                            tr.appendChild(td6);

                            const td7 = document.createElement('td');
                            td7.innerHTML = ``;
                            tr.appendChild(td7);

                            const td8 = document.createElement('td');
                            td8.innerHTML = ``;
                            tr.appendChild(td8);

                            const td9 = document.createElement('td');
                            td9.innerHTML = `<span class="badge badge-danger">X</span><br>`+row.Strategic_Project_Progress_Details;
                            tr.appendChild(td9);
                        }

                        if (!row.Progress_Status) {
                            const td6 = document.createElement('td');
                            td6.innerHTML = ``;
                            tr.appendChild(td6);

                            const td7 = document.createElement('td');
                            td7.innerHTML = ``;
                            tr.appendChild(td7);

                            const td8 = document.createElement('td');
                            td8.innerHTML = ``;
                            tr.appendChild(td8);

                            const td9 = document.createElement('td');
                            td9.innerHTML = ``;
                            tr.appendChild(td9);
                        }

                        





                        tableBody.appendChild(tr);

                        // เก็บค่า fa_name และ so_name ของแถวนี้ไว้ใช้ในการเปรียบเทียบในแถวถัดไป
                        previousFacultyName = row.fa_name;
                        previousSICode = row.si_code;
                        previousSIName = row.si_name;
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
                useCss: true,
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