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
                        <h4>รายงานแผนงานระดับต่าง ๆ ของหน่วยงาน (มหาวิทยาลัย)</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานแผนงานระดับต่าง ๆ ของหน่วยงาน (มหาวิทยาลัย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายละเอียดแผนงาน/โครงการ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
                                                <th>รหัส</th>
                                                <th>ยุทธศาสตร์</th>
                                                <th>รหัส</th>
                                                <th>กลยุทธ์</th>
                                                <th>รหัส</th>
                                                <th>ผลลัพธ์สำคัญ</th>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>หน่วยนับ</th>
                                                <th>รหัส</th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th>กรอบวงเงินงบประมาณ</th>
                                                <th colspan="2">ระยะเวลาที่ดำเนินการ</th>
                                                <th>ระดับและการปรับใช้</th>
                                                <th>ผู้รับผิดชอบ</th>
                                            </tr>
                                            <tr class="text-nowrap">
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>วันเริ่มต้น</th>
                                                <th>วันสิ้นสุด</th>
                                                <th></th>
                                                <th></th>
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
            loadData();
        });

        function loadData() {
            $.ajax({
                type: "POST",
                url: "../server/api.php",
                data: {
                    'command': 'get_kku_planing_level'
                },
                dataType: "json",
                success: function(response) {
                    // console.log(response.plan);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า

                    let previousSICode = '';
                    let previousSIName = '';
                    let previousSOCode = '';
                    let previousSOName = '';
                    let previousOKRCode = '';
                    let previousOKRName = '';
                    let previousTarget = '';
                    let previousUOM = '';

                    response.plan.forEach(row => {
                        const tr = document.createElement('tr');

                        // ถ้าค่าปัจจุบันไม่เท่ากับค่าก่อนหน้า ให้แสดงค่า และรีเซ็ตค่าของคอลัมน์ถัดไป
                        const td1 = document.createElement('td');
                        if (row.si_code !== previousSICode) {
                            td1.textContent = row.si_code;
                            previousSIName = '';
                            previousSOCode = '';
                            previousSOName = '';
                            previousOKRCode = '';
                            previousOKRName = '';
                            previousTarget = '';
                            previousUOM = '';
                        }
                        tr.appendChild(td1);

                        const td2 = document.createElement('td');
                        if (row.si_name !== previousSIName) {
                            td2.textContent = row.si_name;
                            previousSOCode = '';
                            previousSOName = '';
                            previousOKRCode = '';
                            previousOKRName = '';
                            previousTarget = '';
                            previousUOM = '';
                        }
                        tr.appendChild(td2);

                        const td3 = document.createElement('td');
                        if (row.Strategic_Object !== previousSOCode) {
                            td3.textContent = row.Strategic_Object;
                            previousSOName = '';
                            previousOKRCode = '';
                            previousOKRName = '';
                            previousTarget = '';
                            previousUOM = '';
                        }
                        tr.appendChild(td3);

                        const td4 = document.createElement('td');
                        if (row.so_name !== previousSOName) {
                            td4.textContent = row.so_name;
                            previousOKRCode = '';
                            previousOKRName = '';
                            previousTarget = '';
                            previousUOM = '';
                        }
                        tr.appendChild(td4);

                        const td5 = document.createElement('td');
                        if (row.OKR !== previousOKRCode) {
                            td5.textContent = row.OKR;
                            previousOKRName = '';
                            previousTarget = '';
                            previousUOM = '';
                        }
                        tr.appendChild(td5);

                        const td6 = document.createElement('td');
                        if (row.okr_name !== previousOKRName) {
                            td6.textContent = row.okr_name;
                            previousTarget = '';
                            previousUOM = '';
                        }
                        tr.appendChild(td6);

                        const td7 = document.createElement('td');
                        if (row.Target_OKR_Objective_and_Key_Result !== previousTarget) {
                            td7.textContent = row.Target_OKR_Objective_and_Key_Result;
                            previousUOM = ''; // รีเซ็ต UOM เมื่อเป้าหมายเปลี่ยนไป
                        }
                        tr.appendChild(td7);

                        const td8 = document.createElement('td');
                        if (row.UOM !== previousUOM) {
                            td8.textContent = row.UOM;
                        }
                        tr.appendChild(td8);

                        // คอลัมน์ที่ไม่ต้องเช็คค่าก่อนหน้า
                        const td9 = document.createElement('td');
                        td9.textContent = row.Strategic_Project;
                        tr.appendChild(td9);

                        const td10 = document.createElement('td');
                        td10.textContent = row.ksp_name;
                        tr.appendChild(td10);

                        const td11 = document.createElement('td');
                        td11.textContent = Number(row.Budget_Amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        tr.appendChild(td11);

                        const td12 = document.createElement('td');
                        td12.textContent = row.Start_Date;
                        tr.appendChild(td12);

                        const td13 = document.createElement('td');
                        td13.textContent = row.End_Date;
                        tr.appendChild(td13);

                        const td14 = document.createElement('td');
                        td14.textContent = row.Tiers_Deploy;
                        tr.appendChild(td14);

                        const td15 = document.createElement('td');
                        td15.textContent = row.Responsible_person;
                        tr.appendChild(td15);

                        tableBody.appendChild(tr);

                        // อัปเดตค่าก่อนหน้า
                        previousSICode = row.si_code;
                        previousSIName = row.si_name;
                        previousSOCode = row.Strategic_Object;
                        previousSOName = row.so_name;
                        previousOKRCode = row.OKR;
                        previousOKRName = row.okr_name;
                        previousTarget = row.Target_OKR_Objective_and_Key_Result;
                        previousUOM = row.UOM;
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

        function responseError(jqXHR, exception) {
            let errorMessage = '';
            if (jqXHR.status === 0) {
                errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้.';
            } else if (jqXHR.status === 404) {
                errorMessage = 'ไม่พบไฟล์หรือ URL ที่ต้องการ.';
            } else if (jqXHR.status === 500) {
                errorMessage = 'เซิร์ฟเวอร์เกิดข้อผิดพลาด.';
            } else if (exception === 'parsererror') {
                errorMessage = 'ไม่สามารถแปลงข้อมูลจาก JSON ได้.';
            } else if (exception === 'timeout') {
                errorMessage = 'การเชื่อมต่อล้มเหลวเนื่องจากหมดเวลา.';
            } else if (exception === 'abort') {
                errorMessage = 'การเชื่อมต่อถูกยกเลิก.';
            } else {
                errorMessage = 'เกิดข้อผิดพลาดที่ไม่สามารถระบุได้.';
            }
            console.error("ข้อผิดพลาด: " + errorMessage);
            alert("ข้อผิดพลาด: " + errorMessage);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>