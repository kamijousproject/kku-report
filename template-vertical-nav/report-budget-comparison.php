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
                                <?php
                                include '../server/connectdb.php';

                                $db = new Database();
                                $conn = $db->connect();

                                // ดึงข้อมูล Faculty
                                $query_faculty = "SELECT DISTINCT
                                                    abp.Faculty, 
                                                    Faculty.Alias_Default
                                                FROM
                                                    budget_planning_allocated_annual_budget_plan abp
                                                LEFT JOIN Faculty 
                                                    ON abp.Faculty = Faculty.Faculty";
                                $stmt = $conn->prepare($query_faculty);
                                $stmt->execute();
                                $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // รับค่าที่เลือกจากฟอร์ม
                                $selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';
                                $selected_fund = isset($_GET['fund']) ? $_GET['fund'] : '';

                                // WHERE Clause แบบ Dynamic
                                $where_clause = "WHERE 1=1 AND acc.type LIKE '%ค่าใช้จ่าย%'";
                                if ($selected_faculty !== '') {
                                    $where_clause .= " AND bpanbp.Faculty = :faculty AND acc.type LIKE '%ค่าใช้จ่าย%'";
                                }
                                if ($selected_fund !== '') {
                                    $where_clause .= " AND bpanbp.Fund = :fund AND acc.type LIKE '%ค่าใช้จ่าย%'";
                                }

                                // ฟังก์ชันดึงข้อมูล
                                function fetchBudgetData($conn, $where_clause, $selected_faculty, $selected_fund)
                                {
                                    $query = "SELECT
                                                DISTINCT acc.alias_default AS Account_Alias_Default,
                                                acc.type,
                                                acc.sub_type,
                                                bpanbp.Service,
                                                bpanbp.Account,
                                                bpanbp.Faculty,
                                                bpanbp.Plan,
                                                bpanbp.Sub_Plan,
                                                bpanbp.Project,
                                                bpanbp.KKU_Item_Name,
                                                bpanbp.Allocated_Total_Amount_Quantity,
                                                bpanbp.Reason,
                                                sp_kpi.UoM_for_Sub_plan_KPI,
                                                sp_kpi.Sub_plan_KPI_Name,
                                                pj_kpi.UoM_for_Proj_KPI,
                                                pj_kpi.Proj_KPI_Name,
                                                bpanbp.Fund,
                                                bpabp.Total_Amount_Quantity,
                                                bpabp.Fund,
                                                f.Alias_Default AS Faculty_Name,
                                                (
                                                    SELECT
                                                        Faculty_Parent.Alias_Default
                                                    FROM
                                                        Faculty Faculty_Parent
                                                    WHERE
                                                        Faculty_Parent.Faculty = CONCAT(
                                                            LEFT(f.Faculty, 2),
                                                            '000'
                                                        )
                                                    LIMIT
                                                        1
                                                ) AS Alias_Default_Parent,
                                                p.plan_name AS Plan_Name,
                                                sp.sub_plan_name AS Sub_Plan_Name,
                                                pr.project_name AS Project_Name
                                            FROM
                                                budget_planning_allocated_annual_budget_plan bpanbp
                                                LEFT JOIN (
                                                    SELECT
                                                        DISTINCT Account,
                                                        Plan,
                                                        Sub_Plan,
                                                        Project,
                                                        Total_Amount_Quantity,
                                                        Fund
                                                    FROM
                                                        budget_planning_annual_budget_plan
                                                ) bpabp ON bpanbp.Account = bpabp.Account
                                                AND bpanbp.Plan = bpabp.Plan
                                                AND bpanbp.Sub_Plan = bpabp.Sub_Plan
                                                AND bpanbp.Project = bpabp.Project
                                                LEFT JOIN (
                                                    SELECT
                                                        DISTINCT Plan,
                                                        Sub_Plan,
                                                        Faculty,
                                                        UoM_for_Sub_plan_KPI,
                                                        Sub_plan_KPI_Name
                                                    FROM
                                                        budget_planning_subplan_kpi
                                                ) sp_kpi ON bpanbp.Plan = sp_kpi.Plan
                                                AND bpanbp.Sub_Plan = sp_kpi.Sub_Plan
                                                AND bpanbp.Faculty = sp_kpi.Faculty
                                                LEFT JOIN (
                                                    SELECT
                                                        DISTINCT Faculty,
                                                        Project,
                                                        UoM_for_Proj_KPI,
                                                        Proj_KPI_Name
                                                    FROM
                                                        budget_planning_project_kpi
                                                ) pj_kpi ON bpanbp.Faculty = pj_kpi.Faculty
                                                AND bpanbp.Project = pj_kpi.Project
                                                LEFT JOIN account acc ON bpanbp.Account = acc.account
                                                LEFT JOIN Faculty f ON bpanbp.Faculty = f.Faculty
                                                LEFT JOIN plan p ON bpanbp.Plan = p.plan_id
                                                LEFT JOIN sub_plan sp ON bpanbp.Sub_Plan = sp.sub_plan_id
                                                LEFT JOIN project pr ON bpanbp.Project = pr.project_id
                                                $where_clause";

                                    try {
                                        $stmt = $conn->prepare($query);

                                        // ผูกค่า Parameter ป้องกัน SQL Injection
                                        if ($selected_faculty !== '') {
                                            $stmt->bindParam(':faculty', $selected_faculty, PDO::PARAM_STR);
                                        }
                                        if ($selected_fund !== '') {
                                            $stmt->bindParam(':fund', $selected_fund, PDO::PARAM_STR);
                                        }

                                        $stmt->execute();
                                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        echo "Error: " . $e->getMessage();
                                        return [];
                                    }
                                }

                                $resultsFN = fetchBudgetData($conn, $where_clause, $selected_faculty, $selected_fund);
                                ?>

                                <form method="GET" class="d-flex align-items-center gap-2">
                                    <label for="faculty" class="me-2">เลือกส่วนงาน/หน่วยงาน:</label>
                                    <select name="faculty" id="faculty" class="form-control me-2">
                                        <option value="">เลือกส่วนงาน/หน่วยงาน ทั้งหมด</option>
                                        <?php foreach ($faculties as $faculty): ?>
                                            <option value="<?= $faculty['Faculty'] ?>" <?= ($selected_faculty == $faculty['Faculty']) ? 'selected' : '' ?>>
                                                <?= $faculty['Alias_Default'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <label for="fund" class="me-2">เลือกแหล่งงบประมาณ:</label>
                                    <select name="fund" id="fund" class="form-control me-2">
                                        <option value="">ทุกแหล่งงบประมาณ</option>
                                        <option value="FN02" <?= ($selected_fund == "FN02") ? 'selected' : '' ?>>FN02</option>
                                        <option value="FN08" <?= ($selected_fund == "FN08") ? 'selected' : '' ?>>FN08</option>
                                        <option value="FN06" <?= ($selected_fund == "FN06") ? 'selected' : '' ?>>FN06</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </form>

                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
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
                                            <?php
                                            $current_plan = "";
                                            $current_sub_plan = "";
                                            $current_project = "";
                                            $current_expense = "";

                                            $subplan_totals = [];
                                            $expense_totals = [];

                                            foreach ($resultsFN as $row):
                                                // คำนวณค่าผลรวมโดยใช้ Fund เป็นเงื่อนไข
                                                $fn02_request = ($row['Fund'] === 'FN02') ? $row['Total_Amount_Quantity'] : 0;
                                                $fn02_allocated = ($row['Fund'] === 'FN02') ? $row['Allocated_Total_Amount_Quantity'] : 0;

                                                $fn08_request = ($row['Fund'] === 'FN08') ? $row['Total_Amount_Quantity'] : 0;
                                                $fn08_allocated = ($row['Fund'] === 'FN08') ? $row['Allocated_Total_Amount_Quantity'] : 0;

                                                $fn06_request = ($row['Fund'] === 'FN06') ? $row['Total_Amount_Quantity'] : 0;
                                                $fn06_allocated = ($row['Fund'] === 'FN06') ? $row['Allocated_Total_Amount_Quantity'] : 0;

                                                $total_allocated = $fn02_allocated + $fn08_allocated + $fn06_allocated;

                                                // สร้าง key สำหรับรวมค่า Sub Plan KPI
                                                $subplan_key = $row['Plan_Name'] . '|' . $row['Sub_Plan_Name'];

                                                if (!isset($subplan_totals[$subplan_key])) {
                                                    $subplan_totals[$subplan_key] = [
                                                        'FN02_Request' => 0,
                                                        'FN02_Allocated' => 0,
                                                        'FN08_Request' => 0,
                                                        'FN08_Allocated' => 0,
                                                        'FN06_Request' => 0,
                                                        'FN06_Allocated' => 0,
                                                        'Total_Allocated' => 0
                                                    ];
                                                }

                                                // บวกค่าไปที่ระดับ Sub Plan
                                                $subplan_totals[$subplan_key]['FN02_Request'] += $fn02_request;
                                                $subplan_totals[$subplan_key]['FN02_Allocated'] += $fn02_allocated;
                                                $subplan_totals[$subplan_key]['FN08_Request'] += $fn08_request;
                                                $subplan_totals[$subplan_key]['FN08_Allocated'] += $fn08_allocated;
                                                $subplan_totals[$subplan_key]['FN06_Request'] += $fn06_request;
                                                $subplan_totals[$subplan_key]['FN06_Allocated'] += $fn06_allocated;
                                                $subplan_totals[$subplan_key]['Total_Allocated'] += $total_allocated;

                                                // สร้าง key สำหรับรวมค่า Expense
                                                $expense_key = $row['Plan_Name'] . '|' . $row['Sub_Plan_Name'] . '|' . $row['Project_Name'] . '|' . $row['Account_Alias_Default'];

                                                if (!isset($expense_totals[$expense_key])) {
                                                    $expense_totals[$expense_key] = [
                                                        'FN02_Request' => 0,
                                                        'FN02_Allocated' => 0,
                                                        'FN08_Request' => 0,
                                                        'FN08_Allocated' => 0,
                                                        'FN06_Request' => 0,
                                                        'FN06_Allocated' => 0,
                                                        'Total_Allocated' => 0
                                                    ];
                                                }

                                                // บวกค่าไปที่ระดับ Expense
                                                $expense_totals[$expense_key]['FN02_Request'] += $fn02_request;
                                                $expense_totals[$expense_key]['FN02_Allocated'] += $fn02_allocated;
                                                $expense_totals[$expense_key]['FN08_Request'] += $fn08_request;
                                                $expense_totals[$expense_key]['FN08_Allocated'] += $fn08_allocated;
                                                $expense_totals[$expense_key]['FN06_Request'] += $fn06_request;
                                                $expense_totals[$expense_key]['FN06_Allocated'] += $fn06_allocated;
                                                $expense_totals[$expense_key]['Total_Allocated'] += $total_allocated;
                                            ?>

                                                <?php if ($current_plan !== $row['Plan_Name']): ?>
                                                    <tr>
                                                        <td><?= $row['Plan_Name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                    <?php $current_plan = $row['Plan_Name']; ?>
                                                <?php endif; ?>

                                                <?php if ($current_sub_plan !== $row['Sub_Plan_Name']): ?>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;<?= $row['Sub_Plan_Name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                        <td>-</td>
                                                        <td><?= $subplan_totals[$subplan_key]['FN02_Request'] ?></td>
                                                        <td><?= $subplan_totals[$subplan_key]['FN02_Allocated'] ?></td>
                                                        <td><?= $subplan_totals[$subplan_key]['FN08_Request'] ?></td>
                                                        <td><?= $subplan_totals[$subplan_key]['FN08_Allocated'] ?></td>
                                                        <td><?= $subplan_totals[$subplan_key]['FN06_Request'] ?></td>
                                                        <td><?= $subplan_totals[$subplan_key]['FN06_Allocated'] ?></td>
                                                        <td><?= $subplan_totals[$subplan_key]['Total_Allocated'] ?></td>
                                                        <td><?= $subplan_totals[$subplan_key]['Total_Allocated'] - 0 ?></td>
                                                        <td>100</td>
                                                        <td>-</td>
                                                    </tr>
                                                    <?php $current_sub_plan = $row['Sub_Plan_Name']; ?>
                                                <?php endif; ?>

                                                <?php if ($current_expense !== $row['Account_Alias_Default']): ?>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $row['Account_Alias_Default'] . " (" . $row['type'] . " " . $row['sub_type'] . ")" ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                        <td><?= $row['UoM_for_Sub_plan_KPI'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['FN02_Request'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['FN02_Allocated'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['FN08_Request'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['FN08_Allocated'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['FN06_Request'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['FN06_Allocated'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['Total_Allocated'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['Total_Allocated'] - 0 ?></td>
                                                        <td>100</td>
                                                        <td><?= $row['Reason'] ?></td>
                                                    </tr>
                                                    <?php $current_expense = $row['Account_Alias_Default']; ?>
                                                <?php endif; ?>

                                            <?php endforeach; ?>
                                        </tbody>

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
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        function exportCSV() {
            const table = document.getElementById('reportTable');
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน",
                raw: true
            });
            XLSX.writeFile(wb, 'รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม.csv', {
                bookType: 'csv',
                type: 'array'
            });
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
            const table = document.getElementById('reportTable');
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน"
            });
            XLSX.writeFile(wb, 'รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>