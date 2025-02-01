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
                        <h4>รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ (ระดับมหาวิทยาลัย)</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ (ระดับมหาวิทยาลัย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ (ระดับมหาวิทยาลัย)</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr class="text-nowrap">
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
                                            <tr>
                                                <td>001</td>
                                                <td>เสาหลักที่ 1</td>
                                                <td>S001</td>
                                                <td>ยุทธศาสตร์ที่ 1</td>
                                                <td>G001</td>
                                                <td>กลยุทธ์ที่ 1</td>
                                                <td>R001</td>
                                                <td>เพิ่มผลผลิต</td>
                                                <td>100%</td>
                                                <td>เปอร์เซ็นต์</td>
                                                <td>80%</td>
                                                <td>85%</td>
                                                <td>90%</td>
                                                <td>95%</td>
                                                <td>350%</td>
                                                <td>87.5%</td>
                                                <td>ดำเนินงานเสร็จสมบูรณ์</td>
                                                <td>1,500,000</td>
                                                <td>1,450,000</td>
                                                <td>ทีม A</td>
                                            </tr>
                                            <tr>
                                                <td>002</td>
                                                <td>เสาหลักที่ 2</td>
                                                <td>S002</td>
                                                <td>ยุทธศาสตร์ที่ 2</td>
                                                <td>G002</td>
                                                <td>กลยุทธ์ที่ 2</td>
                                                <td>R002</td>
                                                <td>พัฒนาคุณภาพ</td>
                                                <td>80%</td>
                                                <td>เปอร์เซ็นต์</td>
                                                <td>70%</td>
                                                <td>75%</td>
                                                <td>80%</td>
                                                <td>85%</td>
                                                <td>310%</td>
                                                <td>77.5%</td>
                                                <td>ดำเนินงานบางส่วน</td>
                                                <td>2,000,000</td>
                                                <td>1,800,000</td>
                                                <td>ทีม B</td>
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
                url: "../server/api.php",
                data: {
                    'command': 'get_kku_annual_action_summary'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.plan);
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
                        td1.textContent = row.pilar_code === previousPilarCode ? '' : row.pilar_code;;
                        tr.appendChild(td1);

                        // สำหรับ so_name, ถ้ามันเหมือนกับแถวก่อนหน้านี้จะเป็นช่องว่าง
                        const td2 = document.createElement('td');
                        td2.textContent = row.pilar_name === previousPilarName ? '' : row.pilar_name;
                        tr.appendChild(td2);

                        const td3 = document.createElement('td');
                        td3.textContent = row.si_code === previousSICode ? '' : row.si_code;
                        tr.appendChild(td3);

                        const td4 = document.createElement('td');
                        td4.textContent = row.si_name === previousSIName ? '' : row.si_name;
                        tr.appendChild(td4);

                        const td5 = document.createElement('td');
                        td5.textContent = row.Strategic_Object === previousSOCode ? '' : row.Strategic_Object;
                        tr.appendChild(td5);

                        const td6 = document.createElement('td');
                        td6.textContent = row.so_name === previousSOName ? '' : row.so_name;
                        tr.appendChild(td6);

                        const td7 = document.createElement('td');
                        td7.textContent = row.OKR === previousOKRCode ? '' : row.OKR;
                        tr.appendChild(td7);

                        const td8 = document.createElement('td');
                        td8.textContent = row.okr_name === previousOKRName ? '' : row.okr_name;
                        tr.appendChild(td8);

                        const td9 = document.createElement('td');
                        td9.textContent = row.Target_OKR_Objective_and_Key_Result;
                        tr.appendChild(td9);

                        const td10 = document.createElement('td');
                        td10.textContent = row.UOM;
                        tr.appendChild(td10);

                        const td11 = document.createElement('td');
                        td11.textContent = null;
                        tr.appendChild(td11);

                        const td12 = document.createElement('td');
                        td12.textContent = null;
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
                        td16.textContent = null;
                        tr.appendChild(td16);

                        const td17 = document.createElement('td');
                        td17.textContent = row.OKR_Progress_Details;
                        tr.appendChild(td17);

                        const td18 = document.createElement('td');
                        td18.textContent = row.Budget_Amount;
                        tr.appendChild(td18);

                        const td19 = document.createElement('td');
                        td19.textContent = row.Allocated_budget;
                        tr.appendChild(td19);

                        const td20 = document.createElement('td');
                        td20.textContent = row.Actual_Spend_Amount;
                        tr.appendChild(td20);


                        tableBody.appendChild(tr);

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