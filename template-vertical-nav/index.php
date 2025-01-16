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
                                    <form>
                                        <h4>เลือกประเภทรายงาน</h4>
                                        <!-- <p>Select2 can take a regular select box like this...</p> -->
                                        <p>
                                            <select class="form-control">
                                                <optgroup label="แผนงาน">
                                                    <option value="report-plan-levels">รายงานแผนงานระดับต่าง ๆ ของหน่วยงาน (มหาวิทยาลัย)</option>
                                                    <option value="report-plan-changes">รายงานการปรับเปลี่ยนแผนงาน</option>
                                                    <option value="report-plan-status">รายงานสถานะของแผนงานแต่ละแผน</option>
                                                    <option value="report-indicator-comparison">รายงานเปรียบเทียบตัวชี้วัดของแต่ละแผนงาน</option>
                                                    <option value="report-strategy-overview">รายงานภาพรวมของยุทธศาสตร์</option>
                                                    <option value="report-budget-expenses">รายงานการใช้จ่ายงบประมาณตามแผนงาน</option>
                                                    <option value="report-annual-action-summary">รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ (ระดับมหาวิทยาลัย)</option>
                                                    <option value="report-strategic-issues">รายงานผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ (จำแนกตามประเด็นยุทธศาสตร์-ระดับ มหาวิทยาลัย)</option>
                                                    <option value="report-strategic-indicators">รายงานจำนวนผลลัพธ์/ตัวชี้วัดที่สอดคล้องกับแผนยุทธศาสตร์มหาวิทยาลัย</option>
                                                    <option value="report-department-indicators">รายงานจำนวนผลลัพธ์/ตัวชี้วัดในแผนปฏิบัติการ ประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</option>
                                                    <option value="report-department-strategy-overview">รายงานภาพรวมยุทธศาสตร์ ส่วนงาน/หน่วยงาน</option>
                                                    <option value="report-department-action-summary">รายงานสรุปผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ ส่วนงาน/หน่วยงาน</option>
                                                    <option value="report-department-strategic-issues">รายงานผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ (จำแนกตามประเด็นยุทธศาสตร์-ระดับ ส่วนงาน/หน่วยงาน)</option>
                                                    <option value="report-project-summary">รายงานสรุปรายโครงการ</option>
                                                    <option value="report-budget-requests">รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</option>
                                                    <option value="report-reserve-funds">รายงานสรุปบัญชีทุนสำรองสะสม</option>

                                                </optgroup>
                                                <optgroup label="แผนอัตรากำลัง">
                                                    <option value="report-4year-framework">รายงานกรอบอัตรากำลังระยะเวลา 4 ปี</option>
                                                    <option value="report-budget-framework">รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</option>
                                                    <option value="report-unit-personnel">รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</option>
                                                    <option value="report-vacant-personnel">รายงานอัตรากำลังว่าง</option>
                                                    <option value="report-retirement">รายงานอัตรากำลังที่เกษียณอายุในแต่ละช่วงเวลา</option>
                                                    <option value="report-approval-requests">รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</option>
                                                    <option value="report-overview-framework">รายงานสรุปผลการจัดสรรกรอบอตัรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย</option>
                                                    <option value="report-annual-allocation">รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ</option>
                                                    <option value="report-staff-requests">รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร</option>
                                                    <option value="report-4year-workload">รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน</option>
                                                    <option value="report-framework-summary">รายงานสรุปแผนกรอบอตัรากำลัง 4 ปีแยกตามประเภท</option>
                                                    <option value="report-position-changes">รายงานผลการตัดโอน – เปลี่ยนตำแหน่ง</option>
                                                    <option value="report-current-vs-ideal">รายงานแสดงกรอบอัตรากำลังปัจจุบัน กับกรอบอัตรากำลังพึงมีรายตำแหน่ง</option>
                                                    <option value="report-new-vs-old-positions">รายงานการสรุป คำขออัตราใหม่และอัตราเดิม (หลังจากส่วนงาน/หน่วยงาน กรอกคำขออัตราเดิมและอัตราใหม่)</option>
                                                    <option value="report-new-positions-summary">รายงานสรุปคำขออนุมัติกรอบอัตรากำลังพนักงานมหาวิทยาลัยและลูกจ้างของมหาวทิยาลัย (อัตราใหม่) รายตำแหน่ง</option>
                                                </optgroup>
                                                <optgroup label="แผนงบประมาณ">
                                                    <option value="report-budget-annual-summary">รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</option>
                                                    <option value="report-budget-comparison">รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</option>
                                                    <option value="report-budget-structure-comparison">รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณจำแนกตามโครงสร้างองค์กร ตามแหล่งเงิน ตามแผนงาน/โครงการ โดยสามารถแสดงได้ทุกระดับย่อยของหน่วยงบประมาณ</option>
                                                    <option value="report-budget-spending-status">รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</option>
                                                    <option value="report-budget-adjustments">รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ</option>
                                                    <option value="report-budget-carryover">รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมกีารสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้</option>
                                                    <option value="report-budget-revenue-summary">รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ</option>
                                                    <option value="report-project-activities">รายงานโครงการ/กิจกรรม</option>
                                                    <option value="report-budget-request-summary">รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี(สรุปประมาณการรายรับและประมาณการรายจ่าย)</option>
                                                    <option value="report-project-requests">รายงานสรุปคำขอรายโครงการ</option>
                                                    <option value="report-budget-remaining">รายงานสรุปยอดงบประมาณคงเหลือ</option>
                                                    <option value="report-hcm-framework">รายงานข้อมูลกรอบอัตรากำลัง(จากระบบHCM) เพื่อนำไปตั้งงบประมาณขอไฟล์ database จากระบบ HCM</option>
                                                    <option value="report-budget-allocation">รายงานการจัดสรรเงินรายงวด</option>
                                                    <option value="report-revenue-estimation-comparison">รายงานแสดงการเปรียบเทียบการประมาณการรายได้กับรายได้จริง</option>
                                                    <option value="report-expense-estimation-comparison">รายงานแสดงการเปรียบเทียบการประมาณการรายจ่ายกับจ่ายจริง</option>
                                                    <option value="report-indicator-summary">รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</option>
                                                </optgroup>
                                            </select>
                                        </p>
                                    </form>
                                </div>
                                <hr>
                                <h4 class="card-title">เลือกไฟล์</h4>
                                <h6 class="card-subtitle">คลิ๊กเพื่อเลือกไฟล์ที่ต้องการ หรือลากไฟล์มาวาง</h6>
                                <form action="#" class="dropzone m-t-15">
                                    <div class="fallback">
                                        <input name="file" type="file" multiple="multiple">
                                    </div>
                                </form>
                                <button type="button" class="btn btn-primary m-t-15" id="uploadButton">Upload</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #/ container -->
        </div>
        <script>
            document.getElementById('uploadButton').addEventListener('click', function() {
                alert('อัพโหลดข้อมูลแล้ว');
                location.reload();
            });
        </script>
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