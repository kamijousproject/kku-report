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

        <!-- content body -->
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>นำเข้าข้อมูลเพื่อสร้างรายงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">หน้าหลัก</a>
                            </li>
                            <li class="breadcrumb-item active">นำเข้าข้อมูล</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                    <?php
                                    if (isset($_GET['status'])) {
                                        if ($_GET['status'] == 'success') {
                                            echo '<div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px;">';
                                            echo 'บันทึกข้อมูลสำเร็จ.';
                                            echo '</div>';
                                        } elseif ($_GET['status'] == 'error') {
                                            $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'An unknown error occurred.';
                                            echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; margin-bottom: 15px;">';
                                            echo 'Error: ' . $message;
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                    <form action="../server/upload.php" method="post" enctype="multipart/form-data">
                                        <h4>เลือกประเภทไฟล์</h4>
                                        <select name="file_type" class="form-control" required>
                                            <option value="">-- กรุณาเลือกประเภทไฟล์ --</option>
                                            <optgroup label="Budget Planning">
                                                <option value="budget_planning_actual">Budget Planning Actual</option>
                                                <option value="budget_planning_allocated_annual_budget_plan">Allocated annual budget plan</option>
                                                <option value="budget_planning_annual_budget_plan">Annual budget plan</option>
                                                <option value="budget_planning_disbursement_budget_plan_anl_release">Disbursement budget plan annual release</option>
                                                <option value="budget_planning_project_kpi_progress">Budget planning project KPI progress</option>
                                                <option value="budget_planning_project_kpi">Budget planning project kpi</option>
                                                <option value="budget_planning_sub_plan_kpi_progress">Budget planning sub plan KPI progress</option>
                                                <option value="budget_planning_subplan_kpi">Budget planning subplan KPI</option>
                                                <!-- สามารถเพิ่มหมวดหมู่และไฟล์เพิ่มเติมได้ -->
                                            </optgroup>
                                            <!-- เพิ่มหมวดหมู่อื่น ๆ ตามต้องการ -->
                                        </select>
                                        <br>

                                        <h4>เลือกไฟล์</h4>
                                        <hr>
                                        <h6 class="card-subtitle">คลิ๊กเพื่อเลือกไฟล์ที่ต้องการ หรือลากไฟล์มาวาง</h6>
                                        <div class="fallback">
                                            <input name="file" type="file" accept=".csv" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary m-t-15">Upload</button>
                                    </form>

                                    <!-- <script>
                                        setTimeout(function() {
                                            var alert = document.querySelector('div[style*="background-color"]');
                                            if (alert) {
                                                alert.style.display = 'none';
                                            }
                                        }, 5000); // ซ่อนข้อความหลังจาก 5 วินาที
                                    </script> -->
                                </div>
                                <!-- <hr> -->
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #/ container -->
        </div>

        <!-- #/ content body -->
        <!-- footer -->
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy; <a href="#">Ameen</a> 2018</p>
            </div>
        </div>
        <!-- #/ footer -->
    </div>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
    <!-- Dropzone -->
    <script src="../assets/plugins/dropzone-master/dist/dropzone.js"></script>
</body>

</html>