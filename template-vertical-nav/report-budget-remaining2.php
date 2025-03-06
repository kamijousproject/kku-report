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
                                                <th colspan="4">ยอดรวมงบประมาณ</th>
                                                <th colspan="8">เงินประจำงวด</th>
                                                <th colspan="2">ผูกพัน</th>
                                                <th colspan="2" nowrap>ผูกพันงบประมาณ<br/>ตามข้อตกลง/สัญญา</th>
                                                <th rowspan="2" nowrap>จำนวนงบประมาณเบิกจ่าย</th>
                                                <th rowspan="2" nowrap>เบิกงบประมาณเกินส่งคืน</th>
                                            </tr>
                                            <tr>
                                                <th>จำนวนงบประมาณ</th>
                                                <th nowrap>จำนวนงบประมาณ<br/>โอนเข้า</th>
                                                <th nowrap>จำนวนงบประมาณ<br/>โอนออก</th>
                                                <th nowrap>คงเหลือไม่อนุมัติ<br/>งวดเงิน</th>
                                                <th nowrap>ผูกพันงบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th nowrap>คงเหลือหลังผูกพัน<br/>งบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th nowrap>เบิกจ่ายงบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th nowrap>คงเหลือหลังเบิกจ่าย<br/>งบประมาณ</th>
                                                <th>ร้อยละ</th>
                                                <th nowrap>จำนวนงบประมาณ</th>
                                                <th nowrap>คงเหลือหลังเบิกจ่าย<br/>งบประมาณ</th>
                                                <th nowrap>จำนวนงบประมาณ</th>
                                                <th nowrap>คงเหลือหลังเบิกจ่าย<br/>งบประมาณ</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLSX()" class="btn btn-success m-t-15">Export XLS</button>
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

                        const smain = [...new Set(response.bgp.map(item => item.smain))];
                        /* const f2 = [...new Set(response.bgp.map(item => item.f2))];
                        const plan_name = [...new Set(response.bgp.map(item => item.plan_name))];
                        const sub_plan_name = [...new Set(response.bgp.map(item => item.sub_plan_name))];
                        const project_name = [...new Set(response.bgp.map(item => item.project_name))];
                        const account = [...new Set(response.bgp.map(item => item.TYPE))];
                        const sub_account = [...new Set(response.bgp.map(item => item.sub_type))]; */
                        //console.log(smain);
                        smain.forEach((row1, index) => {
                            const sm = response.bgp.filter(item => item.smain === row1 && item.fiscal_year === fyear && item.fund === fund && item.fname === fac && item.plan_name === plan && item.sub_plan_name === subplan && item.project_name === project && item.scenario === scenario && item.BUDGET_PERIOD === bgyear);
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
                            if (/^\d+\.\d+/.test(row1)) {
                                c1 = '&nbsp;'.repeat(8) + row1;
                                console.log(c1);
                            }
                            else {
                                c1 = row1;
                            }

                            //var diff=(sums.c2+sums.o2);
                            /* str1='<tr><td style="text-align:left;" nowrap>'+row1+'</td>';
                            str2='<td>'+sums.INITIAL_BUDGET.toLocaleString()+'</td>';
                            str3='<td>'+sums.adj_in.toLocaleString()+'</td>';
                            str4='<td>'+sums.adj_out.toLocaleString()+'</td>';
                            str5='<td>'+diff.toLocaleString()+'</td>';
                            str6='<td>'+sum_co.toLocaleString()+'</td>';
                            str7='<td>'+p1.toLocaleString()+'</td>';
                            str8='<td>'+diff2.toLocaleString()+'</td>';
                            str9='<td>'+total_p1.toLocaleString()+'</td>';
                            str10='<td>'+sums.EXPENDITURES.toLocaleString()+'</td>';
                            str11='<td>'+p2.toLocaleString()+'</td>';
                            str12='<td>'+diff3.toLocaleString()+'</td>';
                            str13='<td>'+total_p2.toLocaleString()+'</td>';
                            str14='<td>'+sums.COMMITMENTS.toLocaleString()+'</td>';
                            str15='<td>'+sums.FUNDS_AVAILABLE_AMOUNT.toLocaleString()+'</td>';
                            str16='<td>'+sums.OBLIGATIONS.toLocaleString()+'</td>';
                            str17='<td>'+sums.FUNDS_AVAILABLE_AMOUNT.toLocaleString()+'</td>';
                            str18='<td>'+sums.EXPENDITURES.toLocaleString()+'</td>';
                            str19='<td>'+sums.FUNDS_AVAILABLE_AMOUNT.toLocaleString()+'</td></tr>';
                            var html=str1+str2+str3+str4+str5+str6+str7+str8+str9+str10+str11+str12+str13+str14+str15+str16+str17+str18+str19;
                            tableBody.appendChild(html); */
                            var str = '<tr><td style="text-align:left;" nowrap>' + c1 + '</td>'
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
                            sm.forEach((row2, index) => {
                                if (row2.kku_item_name != "") {
                                    var sum_co = (parseInt(row2.COMMITMENTS) + parseInt(row2.OBLIGATIONS));
                                    var diff = (parseInt(row2.INITIAL_BUDGET) - parseInt(row2.adj_in));
                                    var diff2 = (parseInt(row2.INITIAL_BUDGET) - sum_co);
                                    var p1 = Math.round((((sum_co) * 100) / (parseInt(row2.INITIAL_BUDGET))) * 100) / 100 || 0;
                                    var total_p1 = (100 - p1);
                                    var p2 = Math.round((((parseInt(row2.EXPENDITURES)) * 100) / (parseInt(row2.INITIAL_BUDGET))) * 100) / 100 || 0;
                                    var diff3 = (parseInt(row2.INITIAL_BUDGET) - parseInt(row2.EXPENDITURES));
                                    var total_p2 = (100 - p2);
                                    var str = '<tr><td style="text-align:left;" nowrap>' + '&nbsp;'.repeat(16) + row2.kku_item_name + '</td>'
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
                                }
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
            const csvContent = "\uFEFF" + csvRows.join("\n");
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
            doc.text("รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณจำแนกตามโครงสร้างองค์กร ตาม แหล่งเงิน ตามแผนงาน/โครงการ โดยสามารถแสดงได้ทุกระดับย่อยของหน่วยงบประมาณ", 10, 500);

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
            doc.save('รายงานสรุปยอดงบประมาณคงเหลือ.pdf');
        }

        function exportXLSX() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            // จะสร้าง aoa ของ thead + merges array
            const { theadRows, theadMerges } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br/>, ไม่ merge) ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            // รวม rows ทั้งหมด: thead + tbody
            const allRows = [...theadRows, ...tbodyRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet (ถ้ามี)
            // สังเกตว่า thead อยู่แถวบนสุดของ allRows (index เริ่มจาก 0 ตาม parseThead)
            ws['!merges'] = theadMerges;

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์เป็น .xls (BIFF8)
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xls',
                type: 'array'
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