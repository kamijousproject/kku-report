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
                    <h4>รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/</br>ผลการใช้งบประมาณในภาพรวม</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">
                                รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                <h4>รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</h4>
                                </div>
                                </br>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3" >รายการ</th>
                                                <th rowspan="3" value="UOM">หน่วยนับของตัวชี้วัด (UOM)</th>
                                                <th colspan="5">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2" rowspan="2">เพิ่ม/ลด</th>
                                                <th rowspan="3" value="explain">คำชี้แจง</th>
                                            </tr>
                                            <tr>

                                                <th rowspan="2">ปริมาณของตัวชี้วัด</th>
                                                <th rowspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th rowspan="2">เงินนอกงบประมาณ</th>
                                                <th rowspan="2">เงินรายได้</th>
                                                <th rowspan="2">รวม</th>
                                                <th rowspan="2" value="indicators">ปริมาณของตัวชี้วัด</th>
                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                <th colspan="2">เงินรายได้</th>
                                                <th rowspan="2" value="sumfn">รวม</th>
                                            </tr>
                                            <tr>

                                                <th value="fn06-1">คำขอ</th>
                                                <th value="fn06-2">จัดสรร</th>
                                                <th value="fn08-1">คำขอ</th>
                                                <th value="fn08-2">จัดสรร</th>
                                                <th value="fn02-1">คำขอ</th>
                                                <th value="fn02-2">จัดสรร</th>
                                                <th value="quantity">จำนวน</th>
                                                <th value="percentage">ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </table>

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
            loadData();
        });

        function loadData() {
        $.ajax({
        type: "POST",
        url: "../server/api.php",
        data: {
            'command': 'report-budget-comparison'
        },
        dataType: "json",
        success: function(response) {
            console.log("API Response:", response.budget); // ตรวจสอบข้อมูลที่ได้รับ
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = '';

            if (!response.budget || response.budget.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="17" class="text-center">No data found</td></tr>';
                return;
            }

            // ใช้ Map เพื่อจัดกลุ่ม Plan และ Sub_Plan
            const groupedData = new Map();

            response.budget.forEach(item => {
                const key = `${item.Plan_Name} > ${item.Sub_Plan_Name}`;

                if (!groupedData.has(key)) {
                    groupedData.set(key, {
                        plan: item.Plan_Name,
                        subPlan: item.Sub_Plan_Name,
                        subPlanKPI: item.Sub_plan_KPI_Name,
                        projKPI: item.Proj_KPI_Name,
                        accountAlias: item.Account_Alias_Default,
                        type: item.type,
                        itemName: item.KKU_Item_Name,
                        indicators: item.UoM_for_Sub_plan_KPI, 
                        fn06_1: 0, fn06_2: 0,
                        fn08_1: 0, fn08_2: 0,
                        fn02_1: 0, fn02_2: 0,
                        reason: [] // เพิ่ม Array เก็บ Reason
                    });
                }

                let group = groupedData.get(key);

                // ตรวจสอบ Fund และเพิ่มค่าที่เหมาะสม
                if (item.Fund === "FN06") {
                    group.fn06_1 += parseFloat(item.Total_Amount_Quantity || 0);
                    group.fn06_2 += parseFloat(item.Allocated_Total_Amount_Quantity || 0);
                } else if (item.Fund === "FN08") {
                    group.fn08_1 += parseFloat(item.Total_Amount_Quantity || 0);
                    group.fn08_2 += parseFloat(item.Allocated_Total_Amount_Quantity || 0);
                } else if (item.Fund === "FN02") {
                    group.fn02_1 += parseFloat(item.Total_Amount_Quantity || 0);
                    group.fn02_2 += parseFloat(item.Allocated_Total_Amount_Quantity || 0);
                }

                // เก็บเหตุผล (Reason)
                if (item.Reason && !group.reason.includes(item.Reason)) {
                    group.reason.push(item.Reason);
                }
            });

            // Loop ใส่ข้อมูลลงในตาราง
            groupedData.forEach((group, key) => {
                let sumfn = group.fn06_2 + group.fn08_2 + group.fn02_2;
                let quantity = sumfn; // sumfn - 0
                let percentage = (sumfn === 0) ? "100%" : ((quantity / sumfn) * 100).toFixed(2) + "%"; // ป้องกันหาร 0
                let explain = group.reason.join(", "); // รวมเหตุผลทั้งหมดเป็น String

                let row = `<tr>
                    <td>
                        <strong>${group.plan}</strong><br>
                        ${group.subPlan}<br>
                        ${group.subPlanKPI}<br>
                        ${group.projKPI}<br>
                        ${group.accountAlias}<br>
                        ${group.type}<br>
                        ${group.itemName}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>${group.indicators}</td>
                    <td>${group.fn06_1}</td>
                    <td>${group.fn06_2}</td>
                    <td>${group.fn08_1}</td>
                    <td>${group.fn08_2}</td>
                    <td>${group.fn02_1}</td>
                    <td>${group.fn02_2}</td>
                    <td>${sumfn}</td> 
                    <td>${quantity}</td> 
                    <td>${percentage}</td>
                    <td>${explain}</td>
                </tr>`;

                tableBody.innerHTML += row;
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