<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th,
    #reportTable td {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

    .wide-column {
        min-width: 250px;
        /* ปรับขนาด column ให้กว้างขึ้น */
        word-break: break-word;
        /* ทำให้ข้อความขึ้นบรรทัดใหม่ได้ */
        white-space: pre-line;
        /* รักษารูปแบบการขึ้นบรรทัด */
        vertical-align: top;
        /* ทำให้ข้อความอยู่ด้านบนของเซลล์ */
        padding: 10px;
        /* เพิ่มช่องว่างด้านใน */
    }

    .wide-column div {
        margin-bottom: 5px;
        /* เพิ่มระยะห่างระหว่างแต่ละรายการ */
    }

    /* กำหนดให้ตารางขยายขนาดเต็มหน้าจอ */
    table {
        width: 100%;
        border-collapse: collapse;
        /* ลบช่องว่างระหว่างเซลล์ */
    }

    /* ทำให้หัวตารางติดอยู่กับด้านบน */
    th {
        position: sticky;
        /* ทำให้ header ติดอยู่กับด้านบน */
        top: 0;
        /* กำหนดให้หัวตารางอยู่ที่ตำแหน่งด้านบน */
        background-color: #fff;
        /* กำหนดพื้นหลังให้กับหัวตาราง */
        z-index: 2;
        /* กำหนด z-index ให้สูงกว่าแถวอื่น ๆ */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงาให้หัวตาราง */
        padding: 8px;
    }

    /* เพิ่มเงาให้กับแถวหัวตาราง */
    th,
    td {

        border: 2px solid #000;
        /* เพิ่มความหนาของเส้นขอบเซลล์ */
        padding: 8px;
        /* เพิ่มช่องว่างภายในเซลล์ */
    }

    /* ทำให้ข้อมูลในตารางเลื่อนได้ */
    .table-responsive {
        max-height: 60vh;
        /* กำหนดความสูงของตาราง */
        overflow-y: auto;
        /* ทำให้สามารถเลื่อนข้อมูลในตารางได้ */
    }

    .container-custom {
        max-width: 1200px;
        /* กำหนดค่าความกว้างสูงสุด */
        width: 120%;
        /* ใช้ 90% ของหน้าจอเพื่อให้ขนาดพอดี */
        margin: 0 auto;
        /* จัดให้อยู่ตรงกลาง */
    }

    table {
        font-size: 12px;

        /* ลดขนาดตัวอักษรของตารางในหน้าจอเล็ก */
        border: 3px solid #000;
        /* กำหนดเส้นขอบของตารางให้หนาขึ้น */
    }

    thead th {
        border-bottom: 3px solid #000;
        /* ทำให้เส้นขอบของหัวตารางหนากว่า */


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
                                            <label for="fiscal_year"
                                                class="form-label">เลือกปีบริหารงบประมาณ:</label>
                                            <select name="fiscal_year" id="dropdown1">
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="sub_plan_name" class="form-label">เลือกแผนงานย่อย:</label>
                                            <select name="sub_plan_name" id="dropdown5">
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="faculty_alias"
                                                class="form-label">เลือกส่วนงาน/หน่วยงาน:</label>
                                            <select name="faculty_alias" id="dropdown2">
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="project_name" class="form-label">เลือกโครงการ:</label>
                                            <select name="project_name" id="dropdown6">
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="fund" class="form-label">เลือกแหล่งเงิน:</label>
                                            <select name="fund" id="dropdown3">
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="scenario" class="form-label">เลือกประเภทงบประมาณ:</label>
                                            <select name="scenario" id="dropdown7"
                                                >
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="plan_name" class="form-label">เลือกแผนงาน:</label>
                                            <select name="plan_name" id="dropdown4"
                                                >
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="project_name" class="form-label">เลือกประเภทรายการ:</label>
                                            <select name="project_name" id="dropdown8"
                                                >
                                                <option value="">--- เลือก ---</option>
                                                
                                            </select>
                                        </div>

                                        
                                        <div class="col-md-2">
                                            <button id="submitBtn" disabled>Submit</button>                                               
                                        </div>
                                        
                                    </div>
                                </div>
                                <br/>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="22" style="text-align: center;">
                                                    รายงานสรุปยอดงบประมาณคงเหลือ</th>
                                            </tr>
                                        
                                        </thead>
                                        <thead>
                                            <tr>
                                                <th rowspan="2">คำใช้จ่าย</th>
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

    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; <a href="#">KKU</a> 2025</p>
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            let alldata;
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'get_budget-remaining'
                },
                dataType: "json",
                success: function(response) {
                    const year = [...new Set(response.bgp.map(item => item.fiscal_year).filter(fiscal_year => fiscal_year !== null))];
                    alldata=response.bgp;
                    //console.log(year);
                    $('#dropdown2').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown3').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown4').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                    $('#submitBtn').prop('disabled', true);
                    year.forEach((row) =>{
                        //console.log(row.y);
                        $('#dropdown1').append('<option value="'+row+'">'+row+'</option>');
                        
                    });   
                }
            });


            $('#dropdown1').change(function() {
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
                const fac = [...new Set(alldata.filter(item => item.fiscal_year === fyear).map(item => item.fname))];
                //console.log(fac);
                fac.forEach((row) =>{
                    $('#dropdown2').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);
                });   
            });


            $('#dropdown2').change(function() {
                let fyear = $('#dropdown1').val();
                let fac = $('#dropdown2').val();
                //console.log(alldata);
                $('#dropdown3').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown4').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const fund = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac).map(item => item.fund))];
                //console.log(fac);
                fund.forEach((row) =>{
                    $('#dropdown3').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);
                });   
            });
            $('#dropdown3').change(function() {
                let fyear = $('#dropdown1').val();
                let fac = $('#dropdown2').val();
                let fund = $('#dropdown3').val();
                //console.log(alldata);
                $('#dropdown4').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const plan = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac&& item.fund === fund).map(item => item.plan_name))];
                //console.log(fac);
                plan.forEach((row) =>{
                    $('#dropdown4').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);
                });   
            });
            $('#dropdown4').change(function() {
                let fyear = $('#dropdown1').val();
                let fac = $('#dropdown2').val();
                let fund = $('#dropdown3').val();
                let plan = $('#dropdown4').val();
                $('#dropdown5').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const splan = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac&& item.fund === fund&& item.plan_name === plan).map(item => item.sub_plan_name))];
                //console.log(fac);
                splan.forEach((row) =>{
                    $('#dropdown5').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);
                });   
            });
            $('#dropdown5').change(function() {
                let fyear = $('#dropdown1').val();
                let fac = $('#dropdown2').val();
                let fund = $('#dropdown3').val();
                let plan = $('#dropdown4').val();
                let subplan = $('#dropdown5').val();
                $('#dropdown6').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const pro = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac&& item.fund === fund&& item.plan_name === plan&& item.sub_plan_name === subplan).map(item => item.project_name))];
                //console.log(pro);
                pro.forEach((row) =>{
                    $('#dropdown6').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);
                });   
            });
            $('#dropdown6').change(function() {
                let fyear = $('#dropdown1').val();
                let fac = $('#dropdown2').val();
                let fund = $('#dropdown3').val();
                let plan = $('#dropdown4').val();
                let subplan = $('#dropdown5').val();
                let project = $('#dropdown6').val();
                //console.log(alldata);
                $('#dropdown7').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const sc = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac&& item.fund === fund&& item.plan_name === plan&& item.sub_plan_name === subplan&& item.project_name === project).map(item => item.scenario))];
                //console.log(fac);
                sc.forEach((row) =>{
                    $('#dropdown7').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);
                });   
            });
            $('#dropdown7').change(function() {
                let fyear = $('#dropdown1').val();
                let fac = $('#dropdown2').val();
                let fund = $('#dropdown3').val();
                let plan = $('#dropdown4').val();
                let subplan = $('#dropdown5').val();
                let project = $('#dropdown6').val();
                let scenario = $('#dropdown7').val();
                //console.log(alldata);
                $('#dropdown8').html('<option value="">--- เลือก ---</option>').prop('disabled', true);
                $('#submitBtn').prop('disabled', true);
                const etype = [...new Set(alldata.filter(item => item.fiscal_year === fyear && item.fname === fac&& item.fund === fund&& item.plan_name === plan&& item.sub_plan_name === subplan&& item.project_name === project&& item.scenario === scenario).map(item => item.ftype))];
                //console.log(fac);
                etype.forEach((row) =>{
                    $('#dropdown8').append('<option value="'+row+'">'+row+'</option>').prop('disabled', false);
                });   
            });

            $('#dropdown8').change(function() {
                if ($(this).val()) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            });


            $('#submitBtn').click(function() {
                let fyear = $('#dropdown1').val();
                let fac = $('#dropdown2').val();
                let fund = $('#dropdown3').val();
                let plan = $('#dropdown4').val();
                let subplan = $('#dropdown5').val();
                let project = $('#dropdown6').val();
                let scenario = $('#dropdown7').val();
                let etype = $('#dropdown8').val();
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
                    'etype': etype
                },
                dataType: "json",
                success: function(response) {
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
                        const sm = response.bgp.filter(item =>item.smain === row1 && item.fiscal_year === fyear && item.fund === fund && item.fname === fac && item.plan_name === plan && item.sub_plan_name === subplan && item.project_name === project && item.scenario === scenario && item.ftype === etype);
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
                                INITIAL_BUDGET: acc.INITIAL_BUDGET + parseValue(item.INITIAL_BUDGET?? '0'),
                                adj_in: acc.adj_in + parseValue(item.adj_in?? '0'),
                                adj_out: acc.adj_out + parseValue(item.adj_out?? '0'),
                                COMMITMENTS: acc.COMMITMENTS + parseValue(item.COMMITMENTS ?? '0'),
                                OBLIGATIONS: acc.OBLIGATIONS + parseValue(item.OBLIGATIONS ?? '0'),
                                EXPENDITURES: acc.EXPENDITURES + parseValue(item.EXPENDITURES ?? '0'),
                                FUNDS_AVAILABLE_AMOUNT: acc.FUNDS_AVAILABLE_AMOUNT + parseValue(item.FUNDS_AVAILABLE_AMOUNT ?? '0'),
                            };
                        }, {
                            INITIAL_BUDGET: 0,adj_in: 0, COMMITMENTS: 0, OBLIGATIONS: 0, EXPENDITURES: 0,FUNDS_AVAILABLE_AMOUNT:0,adj_out:0
                        });
                        
                        var sum_co=(sums.COMMITMENTS+sums.OBLIGATIONS);
                        var diff=(sums.INITIAL_BUDGET-sums.adj_in);
                        var diff2=(sums.INITIAL_BUDGET-sum_co);
                        var p1=Math.round((((sum_co)*100)/(sums.INITIAL_BUDGET))* 100) / 100 || 0;
                        var total_p1 = (p1 === 0) ? 0 : (100 - p1);
                        var p2=Math.round((((sums.EXPENDITURES)*100)/(sums.INITIAL_BUDGET))* 100) / 100 || 0;
                        var diff3=(sums.INITIAL_BUDGET-sums.EXPENDITURES);
                        var total_p2 = (p1 === 0) ? 0 : (100 - p2);
                        var c1="";
                        if(/^\d+\.\d+/.test(row1))
                        {
                            c1='&nbsp;'.repeat(8)+row1;
                            console.log(c1);
                        }
                        else{
                            c1=row1;
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
                        var str = '<tr><td style="text-align:left;" nowrap>'+c1+'</td>'
                        + '<td>'+(sums.INITIAL_BUDGET).toLocaleString()+'</td>'
                        + '<td>'+(sums.adj_in).toLocaleString()+'</td>'
                        + '<td>'+(sums.adj_out).toLocaleString()+'</td>'
                        + '<td>'+diff.toLocaleString()+'</td>'
                        + '<td>'+sum_co.toLocaleString()+'</td>'
                        + '<td>'+p1.toLocaleString()+'</td>'
                        + '<td>'+diff2.toLocaleString()+'</td>'
                        + '<td>'+total_p1.toLocaleString()+'</td>'
                        + '<td>'+(sums.EXPENDITURES).toLocaleString()+'</td>'
                        + '<td>'+p2.toLocaleString()+'</td>'
                        + '<td>'+diff3.toLocaleString()+'</td>'
                        + '<td>'+total_p2.toLocaleString()+'</td>'
                        + '<td>'+(sums.COMMITMENTS).toLocaleString()+'</td>'
                        + '<td>'+(sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString()+'</td>'
                        + '<td>'+(sums.OBLIGATIONS).toLocaleString()+'</td>'
                        + '<td>'+(sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString()+'</td>'
                        + '<td>'+(sums.EXPENDITURES).toLocaleString()+'</td>'
                        + '<td>'+(sums.FUNDS_AVAILABLE_AMOUNT).toLocaleString()+'</td></tr>';

                        tableBody.insertAdjacentHTML('beforeend', str);
                        sm.forEach((row2, index) => {
                            if(row2.kku_item_name!=""){
                                var sum_co=(parseInt(row2.COMMITMENTS)+parseInt(row2.OBLIGATIONS));
                                var diff=(parseInt(row2.INITIAL_BUDGET)-parseInt(row2.adj_in));
                                var diff2=(parseInt(row2.INITIAL_BUDGET)-sum_co);
                                var p1=Math.round((((sum_co)*100)/(parseInt(row2.INITIAL_BUDGET)))* 100) / 100 || 0;
                                var total_p1=(100-p1);
                                var p2=Math.round((((parseInt(row2.EXPENDITURES))*100)/(parseInt(row2.INITIAL_BUDGET)))* 100) / 100 || 0;
                                var diff3=(parseInt(row2.INITIAL_BUDGET)-parseInt(row2.EXPENDITURES));
                                var total_p2=(100-p2);
                                var str = '<tr><td style="text-align:left;" nowrap>'+'&nbsp;'.repeat(16)+row2.kku_item_name+'</td>'
                                + '<td>'+(parseInt(row2.INITIAL_BUDGET)).toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.adj_in)).toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.adj_out)).toLocaleString()+'</td>'
                                + '<td>'+diff.toLocaleString()+'</td>'
                                + '<td>'+sum_co.toLocaleString()+'</td>'
                                + '<td>'+p1.toLocaleString()+'</td>'
                                + '<td>'+diff2.toLocaleString()+'</td>'
                                + '<td>'+total_p1.toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.EXPENDITURES)).toLocaleString()+'</td>'
                                + '<td>'+p2.toLocaleString()+'</td>'
                                + '<td>'+diff3.toLocaleString()+'</td>'
                                + '<td>'+total_p2.toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.COMMITMENTS)).toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.FUNDS_AVAILABLE_AMOUNT)).toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.OBLIGATIONS)).toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.FUNDS_AVAILABLE_AMOUNT)).toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.EXPENDITURES)).toLocaleString()+'</td>'
                                + '<td>'+(parseInt(row2.FUNDS_AVAILABLE_AMOUNT)).toLocaleString()+'</td></tr>';

                                tableBody.insertAdjacentHTML('beforeend', str);
                            }
                        });
                    });                    
                },
                error: function(jqXHR, exception) {
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
        link.download = 'report.csv';
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
        doc.text("รายงานสรุปยอดงบประมาณคงเหลือ", 10, 10);

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

    function exportXLSX() {
    const table = document.getElementById('reportTable');

    const rows = [];
    const merges = {};
    const skipMap = {};

    for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
        const tr = table.rows[rowIndex];
        const rowData = [];
        let colIndex = 0;

        for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
            while (skipMap[`${rowIndex},${colIndex}`]) {
                rowData.push("");
                colIndex++;
            }

            const cell = tr.cells[cellIndex];
            let cellText = cell.innerText.trim();
            rowData[colIndex] = cellText;

            const rowspan = cell.rowSpan || 1;
            const colspan = cell.colSpan || 1;

            if (rowspan > 1 || colspan > 1) {
                const mergeRef = {
                    s: { r: rowIndex, c: colIndex },
                    e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 }
                };

                const mergeKey = `merge_${rowIndex}_${colIndex}`;
                merges[mergeKey] = mergeRef;

                for (let r = 0; r < rowspan; r++) {
                    for (let c = 0; c < colspan; c++) {
                        if (!(r === 0 && c === 0)) {
                            skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                        }
                    }
                }
            }

            colIndex++;
        }
        rows.push(rowData);
    }

    // Create Workbook
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);

    // Apply merges
    ws['!merges'] = Object.values(merges);

    // Apply header styles to the first few rows (all header rows)
    const totalHeaderRows = table.tHead.rows.length; // Get the number of header rows
    for (let R = 0; R < totalHeaderRows; R++) {
        for (let C = 0; C < rows[R].length; C++) {
            const cellRef = XLSX.utils.encode_cell({ r: R, c: C });

            if (ws[cellRef]) {
                ws[cellRef].s = {
                    alignment: { horizontal: "center", vertical: "center" }, // Center text
                    font: { bold: true, name: "Arial", sz: 12 }, // Bold + Font
                    fill: { fgColor: { rgb: "FFFFCC" } }, // Light yellow background
                    border: {
                        top: { style: "thin", color: { rgb: "000000" } },
                        bottom: { style: "thin", color: { rgb: "000000" } },
                        left: { style: "thin", color: { rgb: "000000" } },
                        right: { style: "thin", color: { rgb: "000000" } }
                    }
                };
            }
        }
    }

    // Append sheet and write file
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    const excelBuffer = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

    // Download file
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'report.xlsx'; // Change to .xlsx for proper styling support
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
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