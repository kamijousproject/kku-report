<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>     
#main-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
}



.container {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}


.table-responsive {
    flex-grow: 1;
    overflow-y: auto; /* Scrollable content only inside table */
    max-height: 60vh; /* Set a fixed height */
    border: 1px solid #ccc;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

thead tr:nth-child(1) th {
    position: sticky;
    top: 0;
    background: #f4f4f4;
    z-index: 1000;
}

thead tr:nth-child(2) th {
    position: sticky;
    top: 65px; /* Adjust height based on previous row */
    background: #f4f4f4;
    z-index: 999;
}

thead tr:nth-child(3) th {
    position: sticky;
    top: 105px; /* Adjust height based on previous rows */
    background: #f4f4f4;
    z-index: 998;
}
.nowrap {
    white-space: nowrap;
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
        <?php include('../component/left-nev.php'); ?>
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานสรุปยอดงบประมาณคงเหลือ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">รายงานสรุปยอดงบประมาณคงเหลือ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- ส่วนหัวของหน้ารายงาน -->
                                <div class="card-title">
                                    <h4>รายงานสรุปยอดงบประมาณคงเหลือ</h4>
                                </div>


                                <!-- ส่วนฟอร์มค้นหา -->
                                <div class="info-section">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="fiscal_year" class="form-label">เลือกปีงบประมาณ:</label>
                                            <select name="fiscal_year" id="dropdown1">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="plan_name" class="form-label">เลือกแผนงาน:</label>
                                            <select name="plan_name" id="dropdown5">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="project_name" class="form-label">เลือกปีบริหารงบประมาณ:</label>
                                            <select name="budget_year" id="dropdown2">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="sub_plan_name" class="form-label">เลือกแผนงานย่อย:</label>
                                            <select name="sub_plan_name" id="dropdown6">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="faculty_alias" class="form-label">เลือกส่วนงาน/หน่วยงาน:</label>
                                            <select name="faculty_alias" id="dropdown3">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="project_name" class="form-label">เลือกโครงการ:</label>
                                            <select name="project_name" id="dropdown7">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="fund" class="form-label">เลือกแหล่งเงิน:</label>
                                            <select name="fund" id="dropdown4">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="scenario" class="form-label">เลือกประเภทงบประมาณ:</label>
                                            <select name="scenario" id="dropdown8">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div>
                                        
                                        <!-- <div class="col-md-6">
                                            <label for="project_name" class="form-label">เลือกประเภทรายการ:</label>
                                            <select name="project_name" id="dropdown8">
                                                <option value="">--- เลือก ---</option>

                                            </select>
                                        </div> -->


                                        <div class="col-md-2">
                                            <button id="submitBtn" disabled>Submit</button>
                                        </div>

                                    </div>
                                </div>
                                <br />
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">

                                        <thead>
                                            <tr>
                                                <th rowspan="2">ค่าใช้จ่าย</th>
                                                <th colspan="5">แผนงบประมาณ</th>
                                                <th colspan="3">ผูกพันงบประมาณ</th>
                                                <th colspan="2">จำนวนงบประมาณเบิกจ่าย</th>                                               
                                            </tr>
                                            <tr>
                                                <th nowrap>งบประมาณที่ได้รับ<br/>อนุมัติจัดสรร</th>
                                                <th nowrap>เงินงวดงบประมาณ</th>
                                                <th nowrap>งบประมาณโอนเข้า</th>
                                                <th nowrap>งบประมาณโอนออก</th>
                                                <th nowrap>งบประมาณสุทธิ</th>
                                                <th nowrap>จำนวนงบประมาณ PR</th>
                                                <th nowrap>จำนวนงบประมาณ PO</th>
                                                <th nowrap>คงเหลือหลังผูกพัน</th>
                                                <th nowrap>จำนวนงบประมาณ</th>
                                                <th nowrap>คงเหลือหลังเบิกจ่าย</th>
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
    </div>

<!--     <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; <a href="#">KKU</a> 2025</p>
        </div>
    </div> -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
        $(document).ready(function () {
            let alldata;
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'get_budget-remaining'
                },
                dataType: "json",
                success: function (response) {
                    const year = [...new Set(response.bgp.map(item => item.fiscal_year).filter(fiscal_year => fiscal_year !== null))];
                    alldata = response.bgp;
                    //console.log(year);
                    $('#dropdown2').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown3').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown4').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#submitBtn').prop('disabled', true);
                    year.forEach((row) => {
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="' + row + '">' + row + '</option>');

                    });
                }
            });


            $('#dropdown1').change(function () {
                let fyear = $(this).val();
                //console.log(alldata);
                $('#dropdown2').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown3').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const bgy = [...new Set(alldata.filter(item => item.fiscal_year === fyear).map(item => item.BUDGET_PERIOD))];
                //console.log(fac);
                bgy.forEach((row) => {
                    $('#dropdown2').append('<option value="' + row + '">' + row + '</option>').prop('disabled', false);
                });
            });


            $('#dropdown2').change(function () {
                let fyear = $('#dropdown1').val();
                let bgyear = $('#dropdown2').val();
                //console.log(alldata);
                $('#dropdown3').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const fac = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.BUDGET_PERIOD === bgyear).map(item => item.fname))];
                fac.sort();
                //console.log(fac);
                fac.forEach((row) => {
                    $('#dropdown3').append('<option value="' + row + '">' + row + '</option>').prop('disabled', false);
                });
            });
            $('#dropdown3').change(function () {
                let fyear = $('#dropdown1').val();
                let bgyear = $('#dropdown2').val();
                let fac = $('#dropdown3').val();
                //console.log(alldata);
                $('#dropdown4').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const fund = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.BUDGET_PERIOD === bgyear && item.fname===fac).map(item => item.fund))];
                //plan = Array.from(planMap, ([plan, plan_name]) => ({ plan, plan_name }));
                //console.log(plan);
                fund.sort();
                fund.forEach((row) => {
                    $('#dropdown4').append('<option value="' + row + '">' + row + '</option>').prop('disabled', false);
                });
            });
            $('#dropdown4').change(function () {
                let fyear = $('#dropdown1').val();
                let bgyear = $('#dropdown2').val();
                let fac = $('#dropdown3').val();
                let fund = $('#dropdown4').val();
                $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                //const splan = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac&& item.fund === fund&& item.plan_name === plan).map(item => item.sub_plan_name))];
                const planMap = new Map(
                    alldata
                        .filter(item => item.fiscal_year === fyear && item.fname === fac && item.fund === fund && item.BUDGET_PERIOD === bgyear)
                        .map(item => [item.plan, item.plan_name]) // [key, value] = [plan, plan_name]
                );
                var plan = Array.from(planMap, ([plan, plan_name]) => ({ plan, plan_name }));
                plan.sort((a, b) => a.plan - b.plan);
                plan.forEach((row) => {
                    $('#dropdown5').append('<option value="' + row.plan_name + '">' + row.plan_name + '</option>').prop('disabled', false);
                });
            });
            $('#dropdown5').change(function () {
                let fyear = $('#dropdown1').val();
                let bgyear = $('#dropdown2').val();
                let fac = $('#dropdown3').val();
                let fund = $('#dropdown4').val();
                let plan = $('#dropdown5').val();
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                //
                const splanMap = new Map(
                    alldata
                        .filter(item => item.fiscal_year === fyear && item.fname === fac && item.fund === fund && item.plan_name === plan && item.BUDGET_PERIOD === bgyear)
                        .map(item => [item.subplan, item.sub_plan_name]) // [key, value] = [plan, plan_name]
                );
                var subplan = Array.from(splanMap, ([subplan, sub_plan_name]) => ({ subplan, sub_plan_name }));
                subplan.sort((a, b) => a.subplan - b.subplan);
                subplan.forEach((row) => {
                    $('#dropdown6').append('<option value="' + row.sub_plan_name + '">' + row.subplan + " : " + row.sub_plan_name + '</option>').prop('disabled', false);
                });
            });
            $('#dropdown6').change(function () {
                let fyear = $('#dropdown1').val();
                let bgyear = $('#dropdown2').val();
                let fac = $('#dropdown3').val();
                let fund = $('#dropdown4').val();
                let plan = $('#dropdown5').val();
                let subplan = $('#dropdown6').val();
                //console.log(alldata);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const pro = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac && item.fund === fund && item.plan_name === plan && item.sub_plan_name === subplan&& item.BUDGET_PERIOD === bgyear).map(item => item.project_name))];
                //const sc = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac && item.fund === fund && item.plan_name === plan && item.sub_plan_name === subplan && item.project_name === project).map(item => item.scenario))];
                //console.log(fac);
                pro.sort();
                pro.forEach((row) => {
                    $('#dropdown7').append('<option value="' + row + '">' + row + '</option>').prop('disabled', false);
                });
            });
            $('#dropdown7').change(function () {
                let fyear = $('#dropdown1').val();
                let bgyear = $('#dropdown2').val();
                let fac = $('#dropdown3').val();
                let fund = $('#dropdown4').val();
                let plan = $('#dropdown5').val();
                let subplan = $('#dropdown6').val();
                let project = $('#dropdown7').val();
                //console.log(alldata);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const sc = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac && item.fund === fund && item.plan_name === plan && item.sub_plan_name === subplan && item.project_name === project&& item.BUDGET_PERIOD === bgyear).map(item => item.scenario))];
                //console.log(fac);
                sc.forEach((row) => {
                    $('#dropdown8').append('<option value="' + row + '">' + row + '</option>').prop('disabled', false);
                });
            });


            $('#dropdown8').change(function () {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);


                    /* let fyear = $('#dropdown1').val();
                    function convertFYtoBuddhistYear(fyear) {
                        if (fyear.startsWith('FY')) {

                            const fiscalYear = parseInt(fyear.slice(2), 10);

                            return fiscalYear + 2543;
                        }
                        return fyear;
                    }
 */

                    /* let fyear = $('#dropdown1').val();
                    let bgyear = $('#dropdown2').val();
                    let fac = $('#dropdown3').val();
                    let fund = $('#dropdown4 option:selected').text();
                    let plan = $('#dropdown5 option:selected').text();
                    let subplan = $('#dropdown6').val();
                    let project = $('#dropdown7').val();
                    let scenario = $('#dropdown8').val();
 */
                    /* 
                    $('#reportTable thead').empty();


                    $('#reportTable thead').append(`
               <tr>
                    <th style="text-align: left;" colspan="19">รายงานสรุปยอดงบประมาณคงเหลือ</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="19">ปีงบประมาณ : ${fyear}</th>
                </tr>
                
                <tr>
                    <th style="text-align: left;" colspan="19">ปีบริหารงบประมาณ  : ${buddhistYear}</th>
                </tr>

                <tr>
                    <th style="text-align: left;" colspan="19">ประเภทงบประมาณ : ${scenario}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="19">แหล่งเงิน : ${fund}</th>
                </tr>
                            <tr>
                    <th style="text-align: left;" colspan="19">ส่วนงาน/หน่วยงาน : ${fac}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="19">แผนงาน(ผลผลิต) [Plan] : ${plan}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="19">แผนงานย่อย(ผลผลิตย่อย/กิจกรรม) [Sub plan] : ${subplan}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="19">โครงการ [Project] : ${project}</th>
                </tr>
           

                                                        <tr>
                                                <th rowspan="2">ค่าใช้จ่าย</th>
                                                <th colspan="4">ยอดรวมงบประมาณ</th>
                                                <th colspan="8">เงินประจำงวด</th>
                                                <th colspan="2">ผูกพัน</th>
                                                <th colspan="2">ผูกพันงบประมาณตามข้อตกลง/สัญญา</th>
                                                <th rowspan="2">จำนวนงบประมาณเบิกจ่าย</th>
                                                <th rowspan="2">เบิกงบประมาณเกินส่งคืน</th>
                                            </tr>
                                            <tr>
                                                <th>จำนวนงบประมาณ</th>
                                                <th>จำนวนงบประมาณโอนเข้า</th>
                                                <th>จำนวนงบประมาณโอนออก</th>
                                                <th>คงเหลือไม่อนุมัติงวดเงิน</th>
                                                <th>ผูกพันงบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th>คงเหลือหลังผูกพันงบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th>เบิกจ่ายงบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th>คงเหลือหลังเบิกจ่ายงบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th>จำนวนงบประมาณ</th>
                                                <th>คงเหลือหลังเบิกจ่ายงบประมาณ</th>
                                                <th>จำนวนงบประมาณ</th>
                                                <th>คงเหลือหลังเบิกจ่ายงบประมาณ</th>
                                            </tr>`); */
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });





            $('#submitBtn').click(function () {
                let fyear = $('#dropdown1').val();
                let bgyear = $('#dropdown2').val();
                let fac = $('#dropdown3').val();
                let fund = $('#dropdown4').val();
                let plan = $('#dropdown5').val();
                let subplan = $('#dropdown6').val();
                let project = $('#dropdown7').val();
                let scenario = $('#dropdown8').val();
                $.ajax({
                    type: "POST",
                    url: "../server/budget_planing_api.php",
                    data: {
                        'command': 'report_budget-remaining',
                        'fiscal_year': fyear,
                        'fund': fund,
                        'faculty': fac,
                        'plan': plan,
                        'subplan': subplan,
                        'project': project,
                        'scenario': scenario,
                        'bgyear': bgyear
                    },
                    dataType: "json",
                    success: function (response) {
                        const tableBody = document.querySelector('#reportTable tbody');
                        tableBody.innerHTML = ''; // ล้างข้อมูลเก่า
                        const data = response.bgp.filter(item =>item.fiscal_year === fyear && item.fund === fund && item.fname === fac && item.plan_name === plan && item.sub_plan_name === subplan && item.project_name === project && item.scenario === scenario && item.BUDGET_PERIOD === bgyear);
                        const smain = [...new Set(data.map(item => item.level5))];
                        const lv4 = [...new Set(data.map(item => item.level4))];
                        const lv3 = [...new Set(data.map(item => item.level3))];
                        const lv2 = [...new Set(data.map(item => item.level2))];
                        const lv1 = [...new Set(data.map(item => item.level1))];
                        /* const f2 = [...new Set(response.bgp.map(item => item.f2))];
                        const plan_name = [...new Set(response.bgp.map(item => item.plan_name))];
                        const sub_plan_name = [...new Set(response.bgp.map(item => item.sub_plan_name))];
                        const project_name = [...new Set(response.bgp.map(item => item.project_name))];
                        const account = [...new Set(response.bgp.map(item => item.TYPE))];
                        const sub_account = [...new Set(response.bgp.map(item => item.sub_type))]; */
                        console.log(lv2);
                        //console.log(smain);
                        smain.forEach((row1, index) => {
                            const sm = data.filter(item => item.level5 === row1);
                            //console.log(sm);
                            const parseValue = (value) => {
                                if (value === null || value === undefined) {
                                    return 0;
                                }
                                const number = parseFloat(value.replace(/,/g, ''));
                                return isNaN(number) ? 0 : number;
                            };
                            const sums = sm.reduce((acc, item) => {
                                //console.log(item.INITIAL_BUDGET );
                                //console.log(item.adj_in );
                                return {
                                    INITIAL_BUDGET: acc.INITIAL_BUDGET + parseValue(item.INITIAL_BUDGET ?? '0'),
                                    adj_in: acc.adj_in + parseValue(item.adj_in ?? '0'),
                                    adj_out: acc.adj_out + parseValue(item.adj_out ?? '0'),
                                    COMMITMENTS: acc.COMMITMENTS + parseValue(item.COMMITMENTS ?? '0'),
                                    OBLIGATIONS: acc.OBLIGATIONS + parseValue(item.OBLIGATIONS ?? '0'),
                                    EXPENDITURES: acc.EXPENDITURES + parseValue(item.EXPENDITURES ?? '0'),
                                    FUNDS_AVAILABLE_AMOUNT: acc.FUNDS_AVAILABLE_AMOUNT + parseValue(item.FUNDS_AVAILABLE_AMOUNT ?? '0'),
                                };
                            }, {
                                INITIAL_BUDGET: 0, adj_in: 0, COMMITMENTS: 0, OBLIGATIONS: 0, EXPENDITURES: 0, FUNDS_AVAILABLE_AMOUNT: 0, adj_out: 0
                            });

                            var sum_co = (sums.COMMITMENTS + sums.OBLIGATIONS);
                            var diff = (sums.INITIAL_BUDGET - sums.adj_in);
                            var diff2 = (sums.INITIAL_BUDGET - sum_co);
                            var p1 = Math.round((((sum_co) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                            var total_p1 = (p1 === 0) ? 0 : (100 - p1);
                            var p2 = Math.round((((sums.EXPENDITURES) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                            var diff3 = (sums.INITIAL_BUDGET - sums.EXPENDITURES);
                            var total_p2 = (p1 === 0) ? 0 : (100 - p2);
                            var c1 = "";
                            
                            var str = '<tr><td style="text-align:left;" nowrap>' + row1 + '</td>'
                                + '<td>' + (sums.INITIAL_BUDGET).toLocaleString() + '</td>'
                                + '<td>' + (sums.adj_in).toLocaleString() + '</td>'
                                + '<td>' + (sums.adj_out).toLocaleString() + '</td>'
                                + '<td>' + diff.toLocaleString() + '</td>'
                                + '<td>' + sum_co.toLocaleString() + '</td>'
                                + '<td>' + p1.toLocaleString() + '</td>'
                                + '<td>' + diff2.toLocaleString() + '</td>'
                                + '<td>' + total_p1.toLocaleString() + '</td>'
                                + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                + '<td>' + p2.toLocaleString() + '</td>'
                                + '<td>' + diff3.toLocaleString() + '</td>'
                                + '<td>' + total_p2.toLocaleString() + '</td>'
                                + '<td>' + (sums.COMMITMENTS).toLocaleString() + '</td>'
                                + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                + '<td>' + (sums.OBLIGATIONS).toLocaleString() + '</td>'
                                + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td></tr>';

                            tableBody.insertAdjacentHTML('beforeend', str);
                            lv4.forEach((row2, index) => {
                                const l4 = sm.filter(item => item.level4 === row2 && item.level5 === row1);
                                //console.log(l4);
                                const parseValue = (value) => {
                                    if (value === null || value === undefined) {
                                        return 0;
                                    }
                                    const number = parseFloat(value.replace(/,/g, ''));
                                    return isNaN(number) ? 0 : number;
                                };
                                const sums = l4.reduce((acc, item) => {
                                    //console.log(item.INITIAL_BUDGET );
                                    //console.log(item.adj_in );
                                    return {
                                        INITIAL_BUDGET: acc.INITIAL_BUDGET + parseValue(item.INITIAL_BUDGET ?? '0'),
                                        adj_in: acc.adj_in + parseValue(item.adj_in ?? '0'),
                                        adj_out: acc.adj_out + parseValue(item.adj_out ?? '0'),
                                        COMMITMENTS: acc.COMMITMENTS + parseValue(item.COMMITMENTS ?? '0'),
                                        OBLIGATIONS: acc.OBLIGATIONS + parseValue(item.OBLIGATIONS ?? '0'),
                                        EXPENDITURES: acc.EXPENDITURES + parseValue(item.EXPENDITURES ?? '0'),
                                        FUNDS_AVAILABLE_AMOUNT: acc.FUNDS_AVAILABLE_AMOUNT + parseValue(item.FUNDS_AVAILABLE_AMOUNT ?? '0'),
                                    };
                                }, {
                                    INITIAL_BUDGET: 0, adj_in: 0, COMMITMENTS: 0, OBLIGATIONS: 0, EXPENDITURES: 0, FUNDS_AVAILABLE_AMOUNT: 0, adj_out: 0
                                });

                                var sum_co = (sums.COMMITMENTS + sums.OBLIGATIONS);
                                var diff = (sums.INITIAL_BUDGET - sums.adj_in);
                                var diff2 = (sums.INITIAL_BUDGET - sum_co);
                                var p1 = Math.round((((sum_co) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                var total_p1 = (p1 === 0) ? 0 : (100 - p1);
                                var p2 = Math.round((((sums.EXPENDITURES) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                var diff3 = (sums.INITIAL_BUDGET - sums.EXPENDITURES);
                                var total_p2 = (p1 === 0) ? 0 : (100 - p2);

                                var str = '<tr><td style="text-align:left;" nowrap>'+ '&nbsp;'.repeat(8) + row2 +  '</td>'
                                    + '<td>' + (sums.INITIAL_BUDGET).toLocaleString() + '</td>'
                                    + '<td>' + (sums.adj_in).toLocaleString() + '</td>'
                                    + '<td>' + (sums.adj_out).toLocaleString() + '</td>'
                                    + '<td>' + diff.toLocaleString() + '</td>'
                                    + '<td>' + sum_co.toLocaleString() + '</td>'
                                    + '<td>' + p1.toLocaleString() + '</td>'
                                    + '<td>' + diff2.toLocaleString() + '</td>'
                                    + '<td>' + total_p1.toLocaleString() + '</td>'
                                    + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                    + '<td>' + p2.toLocaleString() + '</td>'
                                    + '<td>' + diff3.toLocaleString() + '</td>'
                                    + '<td>' + total_p2.toLocaleString() + '</td>'
                                    + '<td>' + (sums.COMMITMENTS).toLocaleString() + '</td>'
                                    + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                    + '<td>' + (sums.OBLIGATIONS).toLocaleString() + '</td>'
                                    + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                    + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                    + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td></tr>';

                                tableBody.insertAdjacentHTML('beforeend', str);
                                console.log(lv3);
                                lv3.forEach((row3, index) => {
                                    const l3 = l4.filter(item => item.level3 === row3 &&item.level4 === row2 && item.level5 === row1);
                                    //console.log(l3);
                                    
                                    const parseValue = (value) => {
                                        if (value === null || value === undefined) {
                                            return 0;
                                        }
                                        const number = parseFloat(value.replace(/,/g, ''));
                                        return isNaN(number) ? 0 : number;
                                    };
                                    const sums = l3.reduce((acc, item) => {
                                        //console.log(item.INITIAL_BUDGET );
                                        //console.log(item.adj_in );
                                        return {
                                            INITIAL_BUDGET: acc.INITIAL_BUDGET + parseValue(item.INITIAL_BUDGET ?? '0'),
                                            adj_in: acc.adj_in + parseValue(item.adj_in ?? '0'),
                                            adj_out: acc.adj_out + parseValue(item.adj_out ?? '0'),
                                            COMMITMENTS: acc.COMMITMENTS + parseValue(item.COMMITMENTS ?? '0'),
                                            OBLIGATIONS: acc.OBLIGATIONS + parseValue(item.OBLIGATIONS ?? '0'),
                                            EXPENDITURES: acc.EXPENDITURES + parseValue(item.EXPENDITURES ?? '0'),
                                            FUNDS_AVAILABLE_AMOUNT: acc.FUNDS_AVAILABLE_AMOUNT + parseValue(item.FUNDS_AVAILABLE_AMOUNT ?? '0'),
                                        };
                                    }, {
                                        INITIAL_BUDGET: 0, adj_in: 0, COMMITMENTS: 0, OBLIGATIONS: 0, EXPENDITURES: 0, FUNDS_AVAILABLE_AMOUNT: 0, adj_out: 0
                                    });

                                    var sum_co = (sums.COMMITMENTS + sums.OBLIGATIONS);
                                    var diff = (sums.INITIAL_BUDGET - sums.adj_in);
                                    var diff2 = (sums.INITIAL_BUDGET - sum_co);
                                    var p1 = Math.round((((sum_co) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                    var total_p1 = (p1 === 0) ? 0 : (100 - p1);
                                    var p2 = Math.round((((sums.EXPENDITURES) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                    var diff3 = (sums.INITIAL_BUDGET - sums.EXPENDITURES);
                                    var total_p2 = (p1 === 0) ? 0 : (100 - p2);
                                    if(l3.length>0)
                                    {
                                        var str = '<tr><td style="text-align:left;" nowrap>'+ '&nbsp;'.repeat(16) + row3 +  '</td>'
                                            + '<td>' + (sums.INITIAL_BUDGET).toLocaleString() + '</td>'
                                            + '<td>' + (sums.adj_in).toLocaleString() + '</td>'
                                            + '<td>' + (sums.adj_out).toLocaleString() + '</td>'
                                            + '<td>' + diff.toLocaleString() + '</td>'
                                            + '<td>' + sum_co.toLocaleString() + '</td>'
                                            + '<td>' + p1.toLocaleString() + '</td>'
                                            + '<td>' + diff2.toLocaleString() + '</td>'
                                            + '<td>' + total_p1.toLocaleString() + '</td>'
                                            + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                            + '<td>' + p2.toLocaleString() + '</td>'
                                            + '<td>' + diff3.toLocaleString() + '</td>'
                                            + '<td>' + total_p2.toLocaleString() + '</td>'
                                            + '<td>' + (sums.COMMITMENTS).toLocaleString() + '</td>'
                                            + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                            + '<td>' + (sums.OBLIGATIONS).toLocaleString() + '</td>'
                                            + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                            + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                            + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td></tr>';

                                        tableBody.insertAdjacentHTML('beforeend', str);
                                    }
                                    lv2.forEach((row4, index) => {
                                        const l2 = l3.filter(item => item.level2 === row4 &&item.level3 === row3 &&item.level4 === row2 && item.level5 === row1);
                                        //console.log(l2);
                                        
                                        const parseValue = (value) => {
                                            if (value === null || value === undefined) {
                                                return 0;
                                            }
                                            const number = parseFloat(value.replace(/,/g, ''));
                                            return isNaN(number) ? 0 : number;
                                        };
                                        const sums = l2.reduce((acc, item) => {
                                            //console.log(item.INITIAL_BUDGET );
                                            //console.log(item.adj_in );
                                            return {
                                                INITIAL_BUDGET: acc.INITIAL_BUDGET + parseValue(item.INITIAL_BUDGET ?? '0'),
                                                adj_in: acc.adj_in + parseValue(item.adj_in ?? '0'),
                                                adj_out: acc.adj_out + parseValue(item.adj_out ?? '0'),
                                                COMMITMENTS: acc.COMMITMENTS + parseValue(item.COMMITMENTS ?? '0'),
                                                OBLIGATIONS: acc.OBLIGATIONS + parseValue(item.OBLIGATIONS ?? '0'),
                                                EXPENDITURES: acc.EXPENDITURES + parseValue(item.EXPENDITURES ?? '0'),
                                                FUNDS_AVAILABLE_AMOUNT: acc.FUNDS_AVAILABLE_AMOUNT + parseValue(item.FUNDS_AVAILABLE_AMOUNT ?? '0'),
                                            };
                                        }, {
                                            INITIAL_BUDGET: 0, adj_in: 0, COMMITMENTS: 0, OBLIGATIONS: 0, EXPENDITURES: 0, FUNDS_AVAILABLE_AMOUNT: 0, adj_out: 0
                                        });

                                        var sum_co = (sums.COMMITMENTS + sums.OBLIGATIONS);
                                        var diff = (sums.INITIAL_BUDGET - sums.adj_in);
                                        var diff2 = (sums.INITIAL_BUDGET - sum_co);
                                        var p1 = Math.round((((sum_co) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                        var total_p1 = (p1 === 0) ? 0 : (100 - p1);
                                        var p2 = Math.round((((sums.EXPENDITURES) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                        var diff3 = (sums.INITIAL_BUDGET - sums.EXPENDITURES);
                                        var total_p2 = (p1 === 0) ? 0 : (100 - p2);
                                        if(l2.length>0 && row4!=null)
                                        {
                                            var str = '<tr><td style="text-align:left;" nowrap>'+ '&nbsp;'.repeat(24) + row4 +  '</td>'
                                                + '<td>' + (sums.INITIAL_BUDGET).toLocaleString() + '</td>'
                                                + '<td>' + (sums.adj_in).toLocaleString() + '</td>'
                                                + '<td>' + (sums.adj_out).toLocaleString() + '</td>'
                                                + '<td>' + diff.toLocaleString() + '</td>'
                                                + '<td>' + sum_co.toLocaleString() + '</td>'
                                                + '<td>' + p1.toLocaleString() + '</td>'
                                                + '<td>' + diff2.toLocaleString() + '</td>'
                                                + '<td>' + total_p1.toLocaleString() + '</td>'
                                                + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                                + '<td>' + p2.toLocaleString() + '</td>'
                                                + '<td>' + diff3.toLocaleString() + '</td>'
                                                + '<td>' + total_p2.toLocaleString() + '</td>'
                                                + '<td>' + (sums.COMMITMENTS).toLocaleString() + '</td>'
                                                + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                                + '<td>' + (sums.OBLIGATIONS).toLocaleString() + '</td>'
                                                + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                                + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                                + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td></tr>';

                                            tableBody.insertAdjacentHTML('beforeend', str);
                                        }
                                        if(l2.length>0 && row4==null){
                                            l2.forEach((row4_null) => {
                                                var sum_co = (parseInt(row4_null.COMMITMENTS) + parseInt(row4_null.OBLIGATIONS));
                                                var diff = (parseInt(row4_null.INITIAL_BUDGET) - parseInt(row4_null.adj_in));
                                                var diff2 = (parseInt(row4_null.INITIAL_BUDGET) - sum_co);
                                                var p1 = Math.round((((sum_co) * 100) / (parseInt(row4_null.INITIAL_BUDGET))) * 100) / 100 || 0;
                                                var total_p1 = (100 - p1);
                                                var p2 = Math.round((((parseInt(row4_null.EXPENDITURES)) * 100) / (parseInt(row4_null.INITIAL_BUDGET))) * 100) / 100 || 0;
                                                var diff3 = (parseInt(row4_null.INITIAL_BUDGET) - parseInt(row4_null.EXPENDITURES));
                                                var total_p2 = (100 - p2);
                                                var str = '<tr><td style="text-align:left;" nowrap>' + '&nbsp;'.repeat(24) + row4_null.kku_item_name + '</td>'
                                                    + '<td>' + (parseInt(row4_null.INITIAL_BUDGET)).toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.adj_in)).toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.adj_out)).toLocaleString() + '</td>'
                                                    + '<td>' + diff.toLocaleString() + '</td>'
                                                    + '<td>' + sum_co.toLocaleString() + '</td>'
                                                    + '<td>' + p1.toLocaleString() + '</td>'
                                                    + '<td>' + diff2.toLocaleString() + '</td>'
                                                    + '<td>' + total_p1.toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.EXPENDITURES)).toLocaleString() + '</td>'
                                                    + '<td>' + p2.toLocaleString() + '</td>'
                                                    + '<td>' + diff3.toLocaleString() + '</td>'
                                                    + '<td>' + total_p2.toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.COMMITMENTS)).toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.OBLIGATIONS)).toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.EXPENDITURES)).toLocaleString() + '</td>'
                                                    + '<td>' + (parseInt(row4_null.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td></tr>';

                                                tableBody.insertAdjacentHTML('beforeend', str);
                                            });
                                        }
                                        lv1.forEach((row5, index) => {
                                            const l1 = l2.filter(item => item.level1 === row5 &&item.level2 === row4 &&item.level3 === row3 &&item.level4 === row2 && item.level5 === row1);
                                            //console.log(l2);
                                            
                                            const parseValue = (value) => {
                                                if (value === null || value === undefined) {
                                                    return 0;
                                                }
                                                const number = parseFloat(value.replace(/,/g, ''));
                                                return isNaN(number) ? 0 : number;
                                            };
                                            const sums = l1.reduce((acc, item) => {
                                                //console.log(item.INITIAL_BUDGET );
                                                //console.log(item.adj_in );
                                                return {
                                                    INITIAL_BUDGET: acc.INITIAL_BUDGET + parseValue(item.INITIAL_BUDGET ?? '0'),
                                                    adj_in: acc.adj_in + parseValue(item.adj_in ?? '0'),
                                                    adj_out: acc.adj_out + parseValue(item.adj_out ?? '0'),
                                                    COMMITMENTS: acc.COMMITMENTS + parseValue(item.COMMITMENTS ?? '0'),
                                                    OBLIGATIONS: acc.OBLIGATIONS + parseValue(item.OBLIGATIONS ?? '0'),
                                                    EXPENDITURES: acc.EXPENDITURES + parseValue(item.EXPENDITURES ?? '0'),
                                                    FUNDS_AVAILABLE_AMOUNT: acc.FUNDS_AVAILABLE_AMOUNT + parseValue(item.FUNDS_AVAILABLE_AMOUNT ?? '0'),
                                                };
                                            }, {
                                                INITIAL_BUDGET: 0, adj_in: 0, COMMITMENTS: 0, OBLIGATIONS: 0, EXPENDITURES: 0, FUNDS_AVAILABLE_AMOUNT: 0, adj_out: 0
                                            });

                                            var sum_co = (sums.COMMITMENTS + sums.OBLIGATIONS);
                                            var diff = (sums.INITIAL_BUDGET - sums.adj_in);
                                            var diff2 = (sums.INITIAL_BUDGET - sum_co);
                                            var p1 = Math.round((((sum_co) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                            var total_p1 = (p1 === 0) ? 0 : (100 - p1);
                                            var p2 = Math.round((((sums.EXPENDITURES) * 100) / (sums.INITIAL_BUDGET)) * 100) / 100 || 0;
                                            var diff3 = (sums.INITIAL_BUDGET - sums.EXPENDITURES);
                                            var total_p2 = (p1 === 0) ? 0 : (100 - p2);
                                            if(l1.length>0 && row5!=null)
                                            {
                                                var str = '<tr><td style="text-align:left;" nowrap>'+ '&nbsp;'.repeat(32) + row5 +  '</td>'
                                                    + '<td>' + (sums.INITIAL_BUDGET).toLocaleString() + '</td>'
                                                    + '<td>' + (sums.adj_in).toLocaleString() + '</td>'
                                                    + '<td>' + (sums.adj_out).toLocaleString() + '</td>'
                                                    + '<td>' + diff.toLocaleString() + '</td>'
                                                    + '<td>' + sum_co.toLocaleString() + '</td>'
                                                    + '<td>' + p1.toLocaleString() + '</td>'
                                                    + '<td>' + diff2.toLocaleString() + '</td>'
                                                    + '<td>' + total_p1.toLocaleString() + '</td>'
                                                    + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                                    + '<td>' + p2.toLocaleString() + '</td>'
                                                    + '<td>' + diff3.toLocaleString() + '</td>'
                                                    + '<td>' + total_p2.toLocaleString() + '</td>'
                                                    + '<td>' + (sums.COMMITMENTS).toLocaleString() + '</td>'
                                                    + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                                    + '<td>' + (sums.OBLIGATIONS).toLocaleString() + '</td>'
                                                    + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td>'
                                                    + '<td>' + (sums.EXPENDITURES).toLocaleString() + '</td>'
                                                    + '<td>' + (sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString() + '</td></tr>';

                                                tableBody.insertAdjacentHTML('beforeend', str);
                                            }
                                            if(l1.length>0 && row5==null && row4!=null ){
                                                l1.forEach((row5_null) => {
                                                    var sum_co = (parseInt(row5_null.COMMITMENTS) + parseInt(row5_null.OBLIGATIONS));
                                                    var diff = (parseInt(row5_null.INITIAL_BUDGET) - parseInt(row5_null.adj_in));
                                                    var diff2 = (parseInt(row5_null.INITIAL_BUDGET) - sum_co);
                                                    var p1 = Math.round((((sum_co) * 100) / (parseInt(row5_null.INITIAL_BUDGET))) * 100) / 100 || 0;
                                                    var total_p1 = (100 - p1);
                                                    var p2 = Math.round((((parseInt(row5_null.EXPENDITURES)) * 100) / (parseInt(row5_null.INITIAL_BUDGET))) * 100) / 100 || 0;
                                                    var diff3 = (parseInt(row5_null.INITIAL_BUDGET) - parseInt(row5_null.EXPENDITURES));
                                                    var total_p2 = (100 - p2);
                                                    var str = '<tr><td style="text-align:left;" nowrap>' + '&nbsp;'.repeat(32) + row5_null.kku_item_name + '</td>'
                                                        + '<td>' + (parseInt(row5_null.INITIAL_BUDGET)).toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.adj_in)).toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.adj_out)).toLocaleString() + '</td>'
                                                        + '<td>' + diff.toLocaleString() + '</td>'
                                                        + '<td>' + sum_co.toLocaleString() + '</td>'
                                                        + '<td>' + p1.toLocaleString() + '</td>'
                                                        + '<td>' + diff2.toLocaleString() + '</td>'
                                                        + '<td>' + total_p1.toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.EXPENDITURES)).toLocaleString() + '</td>'
                                                        + '<td>' + p2.toLocaleString() + '</td>'
                                                        + '<td>' + diff3.toLocaleString() + '</td>'
                                                        + '<td>' + total_p2.toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.COMMITMENTS)).toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.OBLIGATIONS)).toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.EXPENDITURES)).toLocaleString() + '</td>'
                                                        + '<td>' + (parseInt(row5_null.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td></tr>';

                                                    tableBody.insertAdjacentHTML('beforeend', str);
                                                });
                                            }
                                            
                                        });
                                    });
                                });
                                /* if (row2.kku_item_name != "") {
                                    var sum_co = (parseInt(row2.COMMITMENTS) + parseInt(row2.OBLIGATIONS));
                                    var diff = (parseInt(row2.INITIAL_BUDGET) - parseInt(row2.adj_in));
                                    var diff2 = (parseInt(row2.INITIAL_BUDGET) - sum_co);
                                    var p1 = Math.round((((sum_co) * 100) / (parseInt(row2.INITIAL_BUDGET))) * 100) / 100 || 0;
                                    var total_p1 = (100 - p1);
                                    var p2 = Math.round((((parseInt(row2.EXPENDITURES)) * 100) / (parseInt(row2.INITIAL_BUDGET))) * 100) / 100 || 0;
                                    var diff3 = (parseInt(row2.INITIAL_BUDGET) - parseInt(row2.EXPENDITURES));
                                    var total_p2 = (100 - p2);
                                    var str = '<tr><td style="text-align:left;" nowrap>' + '&nbsp;'.repeat(10) + row2.kku_item_name + '</td>'
                                        + '<td>' + (parseInt(row2.INITIAL_BUDGET)).toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.adj_in)).toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.adj_out)).toLocaleString() + '</td>'
                                        + '<td>' + diff.toLocaleString() + '</td>'
                                        + '<td>' + sum_co.toLocaleString() + '</td>'
                                        + '<td>' + p1.toLocaleString() + '</td>'
                                        + '<td>' + diff2.toLocaleString() + '</td>'
                                        + '<td>' + total_p1.toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.EXPENDITURES)).toLocaleString() + '</td>'
                                        + '<td>' + p2.toLocaleString() + '</td>'
                                        + '<td>' + diff3.toLocaleString() + '</td>'
                                        + '<td>' + total_p2.toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.COMMITMENTS)).toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.OBLIGATIONS)).toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.EXPENDITURES)).toLocaleString() + '</td>'
                                        + '<td>' + (parseInt(row2.FUNDS_AVAILABLE_AMOUNT)).toLocaleString() + '</td></tr>';

                                    tableBody.insertAdjacentHTML('beforeend', str);
                                } */
                            });
                        });
                    },
                    error: function (jqXHR, exception) {
                        console.error("Error: " + exception);
                        responseError(jqXHR, exception);
                    }
                });
            });
        });
        function exportCSV() {
            const table = document.getElementById('reportTable');
            const csvRows = [];
            const filters = getFilterValues();
            const reportHeader = [
                `"รายงานสรุปยอดงบประมาณคงเหลือ"`,
                `"ปีงบประมาณ: ${filters.fyear}"`,
                `"ปีบริหารงบประมาณ: ${filters.bgyear}"`,
                `"ส่วนงาน/หน่วยงาน: ${filters.fac}"`,
                `"แหล่งเงิน: ${filters.fund}"`,
                `"แผนงาน: ${filters.plan}"`,
                `"แผนงานย่อย: ${filters.sp}"`,
                `"โครงการ: ${filters.proj}"`,               
                `"ประเภทงบประมาณ: ${filters.se}"`
            ];
            
            // วนลูปทีละ <tr>
            for (const row of table.rows) {
                // เก็บบรรทัดย่อยของแต่ละเซลล์
                const cellLines = [];
                let maxSubLine = 1;

                // วนลูปทีละเซลล์ <td>/<th>
                for (const cell of row.cells) {
                    let html = cell.innerHTML;

                    // 1) แปลง &nbsp; ติดกันให้เป็น non-breaking space (\u00A0) ตามจำนวน
                    html = html.replace(/(&nbsp;)+/g, (match) => {
                        const count = match.match(/&nbsp;/g).length;
                        return '\u00A0'.repeat(count); // ex. 3 &nbsp; → "\u00A0\u00A0\u00A0"
                    });

                    // 2) แปลง <br/> เป็น \n เพื่อแตกเป็นแถวใหม่ใน CSV
                    html = html.replace(/<br\s*\/?>/gi, '\n');

                    // 3) (ถ้าต้องการ) ลบ tag HTML อื่นออก
                    // html = html.replace(/<\/?[^>]+>/g, '');

                    // 4) แยกเป็น array บรรทัดย่อย
                    const lines = html.split('\n').map(x => x.trimEnd());
                    // ใช้ trimEnd() เฉพาะท้าย ไม่ trim ต้นเผื่อบางคนอยากเห็นช่องว่างนำหน้า

                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }

                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];

                    // วนลูปแต่ละเซลล์
                    for (const lines of cellLines) {
                        let text = lines[i] || ''; // ถ้าไม่มีบรรทัดที่ i ก็ว่าง
                        // Escape double quotes
                        text = text.replace(/"/g, '""');
                        // ครอบด้วย ""
                        text = `"${text}"`;
                        rowData.push(text);
                    }

                    csvRows.push(rowData.join(','));
                }
            }

            // รวมเป็น CSV + BOM
            const csvContent = "\uFEFF" + 
                reportHeader.join('\n') + '\n' + // เพิ่มส่วนหัวจาก dropdowns
                '\n' + // บรรทัดว่างแยกส่วนหัวกับข้อมูล
                csvRows.join('\n'); // ตรงนี้คือส่วนที่แก้ไข

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปยอดงบประมาณคงเหลือ.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        function getFilterValues() {
            return {
                fyear: document.getElementById('dropdown1').options[document.getElementById('dropdown1').selectedIndex].text,
                bgyear: document.getElementById('dropdown2').options[document.getElementById('dropdown2').selectedIndex].text,
                fac: document.getElementById('dropdown3').options[document.getElementById('dropdown3').selectedIndex].text,
                fund: document.getElementById('dropdown4').options[document.getElementById('dropdown4').selectedIndex].text,
                plan: document.getElementById('dropdown5').options[document.getElementById('dropdown5').selectedIndex].text,
                sp: document.getElementById('dropdown6').options[document.getElementById('dropdown6').selectedIndex].text,
                proj: document.getElementById('dropdown7').options[document.getElementById('dropdown7').selectedIndex].text,
                se: document.getElementById('dropdown8').options[document.getElementById('dropdown8').selectedIndex].text,
            };
        }

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            
            // ตั้งค่ามาร์จินและขนาดกระดาษ
            const marginLeft = 5;
            const marginRight = 5;
            const marginTop = 40;
            const marginBottom = 10;
            const pageWidth = doc.internal.pageSize.width;
            const usableWidth = pageWidth - marginLeft - marginRight;
            
            // ตั้งค่าฟอนต์
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");
            const filterValues = getFilterValues();
                    doc.setFontSize(12);
                    doc.text("รายงานสรุปยอดงบประมาณคงเหลือ", 150, 10,{ align: 'center' });
                    doc.setFontSize(10);
                    doc.text(`ปีงบประมาณ: ${filterValues.fyear}`, 15, 20);
                    doc.text(`แผนงาน: ${filterValues.plan}`, 150, 20);
                    doc.text(`ปีบริหารงบประมาณ: ${filterValues.bgyear}`, 15, 25);
                    doc.text(`แผนงานย่อย: ${filterValues.sp}`, 150, 25);
                    doc.text(`ส่วนงาน/หน่วยงาน: ${filterValues.fac}`, 15, 30);
                    doc.text(`โครงการ: ${filterValues.proj}`, 150, 30);
                    doc.text(`แหล่งเงิน: ${filterValues.fund}`, 15, 35);
                    doc.text(`ประเภทงบประมาณ: ${filterValues.se}`, 150, 35);
            
            // ฟังก์ชันตรวจสอบว่าเป็นตัวเลขหรือไม่
            function isNumeric(str) {
                if (typeof str != "string") return false;
                return !isNaN(str) && !isNaN(parseFloat(str));
            }
            
            // นับจำนวนคอลัมน์ในตาราง
            const table = document.getElementById('reportTable');
            if (!table) {
                console.error('ไม่พบตาราง reportTable');
                return;
            }
            
            // ตรวจสอบจำนวนคอลัมน์จากส่วนหัวตาราง
            const headerRows = table.querySelectorAll('thead tr');
            let maxColumns = 0;
            headerRows.forEach(row => {
                let colCount = 0;
                const cells = row.querySelectorAll('th');
                cells.forEach(cell => {
                    const colspan = parseInt(cell.getAttribute('colspan') || 1);
                    colCount += colspan;
                });
                maxColumns = Math.max(maxColumns, colCount);
            });
            
            console.log(`จำนวนคอลัมน์ที่พบในตาราง: ${maxColumns}`);
            
            // ลดขนาดตัวอักษรตามจำนวนคอลัมน์
            let fontSize = 8;
            if (maxColumns > 10) fontSize = 6;
            if (maxColumns > 15) fontSize = 5;
            
            // สร้างตารางใหม่สำหรับจัดการ <br/> ในข้อมูล
            const tempTable = document.createElement('table');
            tempTable.style.display = 'none';
            document.body.appendChild(tempTable);
            
            try {
                // คัดลอกส่วนหัวตารางอย่างถูกต้อง
                const thead = document.createElement('thead');
                tempTable.appendChild(thead);
                
                // คัดลอกแถวหัวตารางทั้งหมด
                headerRows.forEach(originalHeaderRow => {
                    const newHeaderRow = document.createElement('tr');
                    const cells = originalHeaderRow.querySelectorAll('th');
                    
                    cells.forEach(cell => {
                        const newCell = document.createElement('th');
                        
                        // คัดลอก attributes สำคัญ (rowspan, colspan)
                        const rowspan = cell.getAttribute('rowspan');
                        if (rowspan) newCell.setAttribute('rowspan', rowspan);
                        
                        const colspan = cell.getAttribute('colspan');
                        if (colspan) newCell.setAttribute('colspan', colspan);
                        
                        const style = cell.getAttribute('style');
                        if (style) newCell.setAttribute('style', style);
                        
                        newCell.textContent = cell.textContent.trim();
                        newHeaderRow.appendChild(newCell);
                    });
                    
                    thead.appendChild(newHeaderRow);
                });
                
                // สร้าง tbody
                const tbody = document.createElement('tbody');
                tempTable.appendChild(tbody);
                
                // ประมวลผลข้อมูลแต่ละแถว
                const dataRows = table.querySelectorAll('tbody tr');
                
                dataRows.forEach(originalRow => {
                    const cells = originalRow.querySelectorAll('td');
                    
                    // เก็บข้อมูลจากทุกเซลล์ในแถว
                    const rowData = [];
                    cells.forEach(cell => {
                        rowData.push({
                            html: cell.innerHTML,
                            text: cell.textContent
                        });
                    });
                    
                    // ตรวจสอบว่าเซลล์แรกมี <br/> หรือไม่
                    if (rowData.length > 0 && rowData[0].html.includes('<br')) {
                        // แยกข้อความในเซลล์แรกด้วย <br>
                        const parts = rowData[0].html.split(/<br\s*\/?>/i);
                        
                        // ตรวจสอบว่าเซลล์อื่นๆ มีข้อมูลแยกด้วย <br> ตรงกันหรือไม่
                        const otherCellsParts = [];
                        for (let i = 1; i < rowData.length; i++) {
                            if (rowData[i].html.includes('<br')) {
                                otherCellsParts[i] = rowData[i].html.split(/<br\s*\/?>/i);
                            } else {
                                // ถ้าเซลล์นี้ไม่มี <br> ต้องเติมอาร์เรย์ว่างให้ครบจำนวน parts
                                otherCellsParts[i] = Array(parts.length).fill('');
                            }
                        }
                        
                        // สร้างแถวใหม่สำหรับแต่ละส่วนที่แยกได้
                        for (let partIndex = 0; partIndex < parts.length; partIndex++) {
                            const part = parts[partIndex].trim();
                            if (!part) continue; // ข้ามข้อมูลที่ว่างเปล่า
                            
                            const newRow = document.createElement('tr');
                            
                            // สร้างเซลล์แรก
                            const firstCell = document.createElement('td');
                            
                            // คัดลอกข้อมูลต้นฉบับ
                            const htmlPart = part;
                            
                            // นับจำนวน &nbsp; เพื่อคำนวณการเยื้อง
                            const nbspCount = (htmlPart.match(/&nbsp;/g) || []).length;
                            const indentLevel = Math.floor(nbspCount / 8); // ทุก 8 &nbsp; = 1 ระดับ
                            
                            // แทนที่จะแทนที่ &nbsp; ด้วยช่องว่างธรรมดา
                            // ใช้วิธีการจัดการเยื้องโดยใช้ padding ใน PDF แทน
                            
                            // แปลง HTML entities เป็นข้อความ (แต่ยังคงรักษาโครงสร้างเดิม)
                            const div = document.createElement('div');
                            div.innerHTML = htmlPart;
                            let textContent = div.textContent;
                            
                            // แทนที่การใช้ textContent ที่จะลบ &nbsp; ให้ใช้การจัดการช่องว่างด้วย CSS
                            firstCell.style.whiteSpace = 'pre';
                            firstCell.textContent = textContent.trim();
                            
                            // ใช้ padding เพื่อจัดการการเยื้อง
                            if (indentLevel > 0) {
                                firstCell.style.paddingLeft = (indentLevel * 10) + 'mm'; // เพิ่มค่าเยื้องให้ชัดเจนขึ้น
                            }
                            
                            newRow.appendChild(firstCell);
                            
                            // สร้างเซลล์อื่นๆ
                            for (let i = 1; i < rowData.length; i++) {
                                const cell = document.createElement('td');
                                
                                // ถ้าเซลล์นี้มีข้อมูลที่แยกด้วย <br> และมีข้อมูลในส่วนที่ตรงกับเซลล์แรก
                                if (otherCellsParts[i] && partIndex < otherCellsParts[i].length) {
                                    const cellContent = otherCellsParts[i][partIndex];
                                    // แปลง HTML entities
                                    const tempDiv = document.createElement('div');
                                    tempDiv.innerHTML = cellContent;
                                    cell.textContent = tempDiv.textContent.trim();
                                    
                                    // จัดให้ตัวเลขอยู่ตรงกลาง
                                    if (isNumeric(cell.textContent)) {
                                        cell.style.textAlign = 'center';
                                    }
                                } else {
                                    cell.textContent = '';
                                }
                                
                                newRow.appendChild(cell);
                            }
                            
                            tbody.appendChild(newRow);
                        }
                    } else {
                        // ถ้าไม่มี <br/> ก็คัดลอกแถวปกติ
                        const newRow = document.createElement('tr');
                        
                        rowData.forEach((cellData, index) => {
                            const cell = document.createElement('td');
                            
                            // สำหรับคอลัมน์แรก ตรวจสอบ &nbsp;
                            if (index === 0 && cellData.html.includes('&nbsp;')) {
                                // นับจำนวน &nbsp;
                                const nbspCount = (cellData.html.match(/&nbsp;/g) || []).length;
                                const indentLevel = Math.floor(nbspCount / 8);
                                
                                // ใช้ padding แทนการแทนที่ &nbsp;
                                if (indentLevel > 0) {
                                    cell.style.paddingLeft = (indentLevel * 10) + 'mm';
                                }
                            }
                            
                            cell.textContent = cellData.text.trim();
                            newRow.appendChild(cell);
                        });
                        
                        tbody.appendChild(newRow);
                    }
                });
                
                // กำหนดความกว้างคอลัมน์
                const columnStyles = {};
                
                // คอลัมน์แรก (รายการ) ให้กว้างกว่า
                columnStyles[0] = {
                    cellWidth: usableWidth * 0.30,
                    halign: 'left'
                };
                
                // คอลัมน์ที่เหลือแบ่งพื้นที่ที่เหลือ
                const otherColWidth = (usableWidth * 0.70) / (maxColumns - 1);
                for (let i = 1; i < maxColumns; i++) {
                    columnStyles[i] = {
                        cellWidth: otherColWidth,
                        halign: 'center'
                    };
                }
                
                // สร้าง PDF
                doc.autoTable({
                    html: tempTable,
                    startY: marginTop,
                    theme: 'plain', // เปลี่ยนจาก 'grid' เป็น 'plain' เพื่อลบเส้นขอบ
                    styles: {
                        font: "THSarabun",
                        fontSize: fontSize,
                        cellPadding: 1,
                        lineWidth: 0, // กำหนดเป็น 0 เพื่อลบเส้นขอบ
                        minCellHeight: 4,
                        overflow: 'linebreak',
                        textColor: [0, 0, 0]
                    },
                    headStyles: {
                        fillColor: [220, 230, 241],
                        textColor: [0, 0, 0],
                        fontSize: fontSize,
                        fontStyle: 'bold',
                        halign: 'center',
                        valign: 'middle',
                        lineWidth: 0 // ลบเส้นขอบส่วนหัวตาราง
                    },
                    columnStyles: columnStyles,
                    margin: { top: marginTop, right: marginRight, bottom: marginBottom, left: marginLeft },
                    tableWidth: usableWidth,
                    showHead: 'everyPage',
                    tableLineWidth: 0, // กำหนดเป็น 0 เพื่อลบเส้นขอบตาราง
                    // ลบการกำหนดสีเส้นขอบเนื่องจากไม่จำเป็น
                    didDrawCell: function(data) {
                        // ปรับความสูงของเซลล์หัวตาราง
                        if (data.section === 'head') {
                            const cell = data.cell;
                            if (cell.raw.getAttribute('rowspan') || cell.raw.getAttribute('colspan')) {
                                // เพิ่มความสูงสำหรับเซลล์ที่มี rowspan หรือ colspan
                                cell.styles.minCellHeight = 8;
                            }
                        }
                    },
                    didParseCell: function(data) {
                        // จัดการกับการตั้งค่า align ของเซลล์
                        if (data.section === 'head') {
                            const style = data.cell.raw.getAttribute('style');
                            if (style && style.includes('text-align: left')) {
                                data.cell.styles.halign = 'left';
                            }
                        }
                        
                        // สำหรับเซลล์ข้อมูล คอลัมน์แรกใช้ text-align: left
                        if (data.section === 'body' && data.column.index === 0) {
                            data.cell.styles.halign = 'left';
                            
                            // ตรวจสอบหาการเยื้อง
                            const paddingLeft = data.cell.raw.style.paddingLeft;
                            if (paddingLeft) {
                                // แปลง paddingLeft จาก mm เป็นจำนวนช่องว่าง
                                // และใส่ช่องว่างเพิ่มที่ข้อความ
                                const mmValue = parseFloat(paddingLeft);
                                if (!isNaN(mmValue)) {
                                    // แปลงค่า mm เป็นจำนวนช่องว่าง (ประมาณการ)
                                    const spacesPerMm = 0.5; // ประมาณการจำนวนช่องว่างต่อ 1 mm
                                    const spaces = ' '.repeat(Math.round(mmValue * spacesPerMm));
                                    
                                    // เพิ่มช่องว่างนำหน้าข้อความที่มีอยู่
                                    const originalText = data.cell.text || '';
                                    data.cell.text = spaces + originalText;
                                }
                            }
                        }
                    }
                });
            } finally {
                // ลบตารางชั่วคราว
                if (tempTable.parentNode) {
                    tempTable.parentNode.removeChild(tempTable);
                }
            }
            
            doc.save('รายงานสรุปยอดงบประมาณคงเหลือ.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');
            const filterValues = getFilterValues();

            // สร้าง Workbook
            const wb = XLSX.utils.book_new();
            
            // สร้างส่วนหัวรายงาน (แบบที่แก้ไขแล้ว)
            const headerRows = [
                ["รายงานสรุปยอดงบประมาณคงเหลือ"],
                ["ปีงบประมาณ:", filterValues.fyear, "แผนงาน:", filterValues.plan],
                ["ปีบริหารงบประมาณ:", filterValues.bgyear, "แผนงานย่อย:", filterValues.sp],
                ["ส่วนงาน/หน่วยงาน:", filterValues.fac, "โครงการ:", filterValues.proj],
                ["แหล่งเงิน:", filterValues.fund, "ประเภทงบประมาณ:", filterValues.se],
                [""] // แถวว่าง
            ];

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const { theadRows, theadMerges } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br/>, ไม่ merge) ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            // รวมทุกแถวเข้าด้วยกัน: headerRows + theadRows + tbodyRows
            const allRows = [...headerRows, ...theadRows, ...tbodyRows];

            // สร้าง worksheet จากข้อมูลทั้งหมด
            const ws = XLSX.utils.aoa_to_sheet(allRows);
            
            // จัดการ styles สำหรับส่วนหัว (รูปแบบใหม่)
            ws['A1'].s = { font: { bold: true, sz: 15 }, alignment: { horizontal: "center" } };
            
            // ใส่ style ให้กับส่วนหัวที่เป็นฟิลด์ชื่อ (เช่น "ปีงบประมาณ:", "แผนงาน:")
            const fieldCells = [
                'A2', 'C2', // ปีงบประมาณ, แผนงาน
                'A3', 'C3', // ปีบริหารงบประมาณ, แผนงานย่อย
                'A4', 'C4', // ส่วนงาน/หน่วยงาน, โครงการ
                'A5', 'C5'  // แหล่งเงิน, ประเภทงบประมาณ
            ];
            
            fieldCells.forEach(cell => {
                if (!ws[cell]) ws[cell] = { v: '', t: 's' };
                if (!ws[cell].s) ws[cell].s = {};
                ws[cell].s.font = { bold: true };
            });

            // ปรับ merges สำหรับหัวข้อแรก
            const headerMerges = [];
            
            // merge หัวข้อแรกให้กินพื้นที่ทั้งแถว
            const maxCols = Math.max(...allRows.map(row => row.length));
            if (maxCols > 1) {
                headerMerges.push({
                    s: { r: 0, c: 0 },
                    e: { r: 0, c: maxCols - 1 }
                });
            }

            // ปรับ theadMerges (บวก offset จาก headerRows)
            const headerRowCount = headerRows.length;
            const updatedTheadMerges = theadMerges.map(merge => ({
                s: { r: merge.s.r + headerRowCount, c: merge.s.c },
                e: { r: merge.e.r + headerRowCount, c: merge.e.c }
            }));

            // รวม merges ทั้งหมด
            ws['!merges'] = [...headerMerges, ...updatedTheadMerges];

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // ปรับความกว้างคอลัมน์ (เพิ่มเติม)
            ws['!cols'] = [
                { wch: 20 }, { wch: 25 }, { wch: 20 }, { wch: 25 }
            ];

            // เขียนไฟล์เป็น .xls (BIFF8)
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xls',
                type: 'array',
                cellStyles: true
            });

            // สร้าง Blob + ดาวน์โหลด
            const blob = new Blob([excelBuffer], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปยอดงบประมาณคงเหลือ.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        /**
         * -----------------------
         * 1) parseThead: รองรับ merge
         * -----------------------
         * - ใช้ skipMap จัดการ colSpan/rowSpan
         * - ไม่แยก <br/> เป็นแถวใหม่ (โดยทั่วไป header ไม่ต้องแตกแถว)
         * - ถ้า thead มีหลาย <tr> ก็จะได้หลาย row
         * - return: { theadRows: [][] , theadMerges: [] }
         */
        function parseThead(thead) {
            const theadRows = [];
            const theadMerges = [];

            if (!thead) {
                return { theadRows, theadMerges };
            }

            // Map กันการเขียนทับ merge
            const skipMap = {};

            for (let rowIndex = 0; rowIndex < thead.rows.length; rowIndex++) {
                const tr = thead.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    // ข้ามเซลล์ที่ถูก merge ครอบไว้
                    while (skipMap[`${rowIndex},${colIndex}`]) {
                        rowData[colIndex] = "";
                        colIndex++;
                    }

                    const cell = tr.cells[cellIndex];
                    // ไม่แยก <br/> → แค่แทน &nbsp; เป็น space
                    let text = cell.innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length)) // &nbsp; => spaces
                        .replace(/<br\s*\/?>/gi, ' ') // ถ้ามี <br/> ใน thead ก็เปลี่ยนเป็นช่องว่าง (ไม่แตกแถว)
                        .replace(/<\/?[^>]+>/g, '')   // ลบ tag อื่น ถ้าเหลือ
                        .trim();

                    rowData[colIndex] = text;

                    // ดู rowSpan/colSpan
                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        // Push merges object
                        theadMerges.push({
                            s: { r: rowIndex, c: colIndex },
                            e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 }
                        });

                        // Mark skipMap
                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r === 0 && c === 0) continue;
                                skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                            }
                        }
                    }
                    colIndex++;
                }
                theadRows.push(rowData);
            }

            return { theadRows, theadMerges };
        }

        /**
         * -----------------------
         * 2) parseTbody: แตก <br/> เป็นหลาย sub-row
         * -----------------------
         * - ไม่ทำ merge (ตัวอย่าง) เพื่อความง่าย
         * - ถ้าใน tbody มี colSpan/rowSpan ต้องประยุกต์ skipMap ต่อเอง
         */
        function parseTbody(tbody) {
            const rows = [];

            if (!tbody) return rows;

            for (const tr of tbody.rows) {
                // เก็บ sub-lines ของแต่ละเซลล์
                const cellLines = [];
                let maxSubLine = 1;

                for (const cell of tr.cells) {
                    // (a) แปลง &nbsp; → space ตามจำนวน
                    // (b) แปลง <br/> → \n เพื่อนำไป split เป็นหลายบรรทัด
                    let html = cell.innerHTML.replace(/(&nbsp;)+/g, match => {
                        const count = match.match(/&nbsp;/g).length;
                        return ' '.repeat(count);
                    });
                    html = html.replace(/<br\s*\/?>/gi, '\n');

                    // (c) ลบแท็กอื่น ๆ (ถ้าต้องการ)
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // (d) split ด้วย \n → ได้หลาย sub-lines
                    const lines = html.split('\n').map(x => x.trimEnd());
                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }
                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];
                    for (const lines of cellLines) {
                        rowData.push(lines[i] || ''); // ถ้าไม่มีบรรทัด => ใส่ว่าง
                    }
                    rows.push(rowData);
                }
            }

            return rows;
        }


    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
    <!-- โหลดไลบรารี xlsx จาก CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


</body>

</html>