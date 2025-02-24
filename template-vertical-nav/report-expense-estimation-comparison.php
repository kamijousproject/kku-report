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
                        <h4>รายงานแสดงการเปรียบเทียบการประมาณการรายจ่ายกับจ่ายจริง</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานแสดงการเปรียบเทียบการประมาณการรายจ่ายกับจ่ายจริง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>
                                        รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ
                                    </h4>
                                </div>
                                <?php
                                include '../server/connectdb.php';

                                $db = new Database();
                                $conn = $db->connect();

                                // ดึงข้อมูล Faculty
                                $query_faculty = "SELECT Faculty, Alias_Default FROM Faculty";
                                $stmt = $conn->prepare($query_faculty);
                                $stmt->execute();
                                $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // รับค่าที่เลือกจากฟอร์ม
                                $selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';
                                $selected_fund = isset($_GET['fund']) ? $_GET['fund'] : '';

                                // WHERE Clause แบบ Dynamic
                                $where_clause = "WHERE 1=1";
                                if ($selected_faculty !== '') {
                                    $where_clause .= " AND Faculty.Faculty = :faculty";
                                }
                                if ($selected_fund !== '') {
                                    $where_clause .= " AND abp.Fund = :fund";
                                }

                                // ฟังก์ชันดึงข้อมูล
                                function fetchBudgetData($conn, $where_clause, $selected_faculty, $selected_fund)
                                {
                                    $query = "SELECT
                                                p.plan_name,
                                                sp.sub_plan_name,
                                                pj.project_name,
                                                acc.type,
                                                acc.sub_type,
                                                abp.KKU_Item_Name,
                                                SUM(abp.Total_Amount_Quantity) AS Total_Amount_Quantity,
                                                SUM(
                                                    CASE WHEN MONTH(pa.created_at) IN (10, 11, 12) THEN pa.EXPENDITURES ELSE 0 END
                                                ) AS Q1_EXP,
                                                SUM(
                                                    CASE WHEN MONTH(pa.created_at) IN (1, 2, 3) THEN pa.EXPENDITURES ELSE 0 END
                                                ) AS Q2_EXP,
                                                SUM(
                                                    CASE WHEN MONTH(pa.created_at) IN (4, 5, 6) THEN pa.EXPENDITURES ELSE 0 END
                                                ) AS Q3_EXP,
                                                SUM(
                                                    CASE WHEN MONTH(pa.created_at) IN (7, 8, 9) THEN pa.EXPENDITURES ELSE 0 END
                                                ) AS Q4_EXP
                                            FROM
                                                budget_planning_annual_budget_plan abp
                                                LEFT JOIN budget_planning_actual pa ON abp.Faculty = pa.FACULTY
                                                AND abp.Plan = pa.PLAN
                                                AND REPLACE(abp.Sub_Plan, 'SP_', '') = pa.SUBPLAN
                                                AND abp.Project = pa.PROJECT
                                                LEFT JOIN `account` acc ON abp.`Account` = acc.`account`
                                                LEFT JOIN plan p ON abp.Plan = p.plan_id
                                                LEFT JOIN sub_plan sp ON abp.Sub_Plan = sp.sub_plan_id
                                                LEFT JOIN project pj ON abp.Project = pj.project_id
                                                LEFT JOIN Faculty ON abp.Faculty = Faculty.Faculty
                                            $where_clause
                                            GROUP BY
                                                p.plan_name,
                                                sp.sub_plan_name,
                                                pj.project_name,
                                                acc.type,
                                                acc.sub_type,
                                                abp.KKU_Item_Name
                                            LIMIT 20";

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
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="6">ปี 2564 </th>
                                                <th colspan="6">ปี 2565</th>
                                                <th colspan="6">ปี 2566</th>
                                                <th colspan="6">ปี 2567</th>
                                                <th colspan="6">ปี 2568</th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2">ประมาณการรายจ่าย</th>
                                                <th colspan="4">รายจ่ายจริง</th>
                                                <th rowspan="2">รวม</th>
                                                <th rowspan="2">ประมาณการรายจ่าย</th>
                                                <th colspan="4">รายจ่ายจริง</th>
                                                <th rowspan="2">รวม</th>
                                                <th rowspan="2">ประมาณการรายจ่าย</th>
                                                <th colspan="4">รายจ่ายจริง</th>
                                                <th rowspan="2">รวม</th>
                                                <th rowspan="2">ประมาณการรายจ่าย</th>
                                                <th colspan="4">รายจ่ายจริง</th>
                                                <th rowspan="2">รวม</th>
                                                <th rowspan="2">ประมาณการรายจ่าย</th>
                                                <th colspan="4">รายจ่ายจริง</th>
                                                <th rowspan="2">รวม</th>
                                            </tr>
                                            <tr>
                                                <th>ไตรมาส 1</th>
                                                <th>ไตรมาส 2</th>
                                                <th>ไตรมาส 3</th>
                                                <th>ไตรมาส 4</th>
                                                <th>ไตรมาส 1</th>
                                                <th>ไตรมาส 2</th>
                                                <th>ไตรมาส 3</th>
                                                <th>ไตรมาส 4</th>
                                                <th>ไตรมาส 1</th>
                                                <th>ไตรมาส 2</th>
                                                <th>ไตรมาส 3</th>
                                                <th>ไตรมาส 4</th>
                                                <th>ไตรมาส 1</th>
                                                <th>ไตรมาส 2</th>
                                                <th>ไตรมาส 3</th>
                                                <th>ไตรมาส 4</th>
                                                <th>ไตรมาส 1</th>
                                                <th>ไตรมาส 2</th>
                                                <th>ไตรมาส 3</th>
                                                <th>ไตรมาส 4</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $current_plan = "";
                                            $current_sub_plan = "";
                                            $current_project = "";
                                            $current_expense = "";
                                            $expense_totals = [];

                                            foreach ($resultsFN as $row):
                                                // คำนวณค่ารวมของ Expense Code
                                                $expense_key = $row['plan_name'] . '|' . $row['sub_plan_name'] . '|' . $row['project_name'] . '|' . $row['type'] . ' ' . $row['sub_type'];

                                                if (!isset($expense_totals[$expense_key])) {
                                                    $expense_totals[$expense_key] = [
                                                        'Total_Amount_Quantity' => 0,
                                                        'Q1_EXP' => 0,
                                                        'Q2_EXP' => 0,
                                                        'Q3_EXP' => 0,
                                                        'Q4_EXP' => 0
                                                    ];
                                                }

                                                // เพิ่มค่าเข้าไป
                                                $expense_totals[$expense_key]['Total_Amount_Quantity'] += $row['Total_Amount_Quantity'];
                                                $expense_totals[$expense_key]['Q1_EXP'] += $row['Q1_EXP'];
                                                $expense_totals[$expense_key]['Q2_EXP'] += $row['Q2_EXP'];
                                                $expense_totals[$expense_key]['Q3_EXP'] += $row['Q3_EXP'];
                                                $expense_totals[$expense_key]['Q4_EXP'] += $row['Q4_EXP'];
                                            ?>

                                                <?php if ($current_plan !== $row['plan_name']): ?>
                                                    <tr>
                                                        <td><?= $row['plan_name'] ?></td>
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
                                                    <?php $current_plan = $row['plan_name']; ?>
                                                <?php endif; ?>

                                                <?php if ($current_sub_plan !== $row['sub_plan_name']): ?>
                                                    <tr>
                                                        <td><?= $row['sub_plan_name'] ?></td>
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
                                                    <?php $current_sub_plan = $row['sub_plan_name']; ?>
                                                <?php endif; ?>

                                                <?php if ($current_project !== $row['project_name']): ?>
                                                    <tr>
                                                        <td><?= $row['project_name'] ?></td>
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
                                                    <?php $current_project = $row['project_name']; ?>
                                                <?php endif; ?>

                                                <?php if ($current_expense !== ($row['type'] . " " . $row['sub_type'])): ?>
                                                    <tr>
                                                        <td><?= $row['type'] . " " . $row['sub_type'] ?></td>
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
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td><?= $expense_totals[$expense_key]['Total_Amount_Quantity'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['Q1_EXP'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['Q2_EXP'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['Q3_EXP'] ?></td>
                                                        <td><?= $expense_totals[$expense_key]['Q4_EXP'] ?></td>
                                                        <td><?= array_sum($expense_totals[$expense_key]) ?></td>
                                                    </tr>
                                                    <?php $current_expense = $row['type'] . " " . $row['sub_type']; ?>
                                                <?php endif; ?>

                                                <tr>
                                                    <td><?= $row['KKU_Item_Name'] ?></td>
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
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td><?= $row['Total_Amount_Quantity'] ?></td>
                                                    <td><?= $row['Q1_EXP'] ?></td>
                                                    <td><?= $row['Q2_EXP'] ?></td>
                                                    <td><?= $row['Q3_EXP'] ?></td>
                                                    <td><?= $row['Q4_EXP'] ?></td>
                                                    <td><?= $row['Q1_EXP'] + $row['Q2_EXP'] + $row['Q3_EXP'] + $row['Q4_EXP'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
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