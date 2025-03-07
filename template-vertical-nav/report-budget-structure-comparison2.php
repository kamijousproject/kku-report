<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: top;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

    #reportTable td {
        text-align: left;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: top;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

    #main-wrapper {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    .content-body {
        flex-grow: 1;
        overflow: hidden;
        /* Prevent body scrolling */
        display: flex;
        flex-direction: column;
    }

    .container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }


    .table-responsive {
        flex-grow: 1;
        overflow-y: auto;
        /* Scrollable content only inside table */
        max-height: 60vh;
        /* Set a fixed height */
        border: 1px solid #ccc;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead tr:nth-child(1) th {
        position: sticky;
        top: 0;
        background: #f4f4f4;
        z-index: 1000;
    }

    thead tr:nth-child(2) th {
        position: sticky;
        top: 45px;
        /* Adjust height based on previous row */
        background: #f4f4f4;
        z-index: 999;
    }

    thead tr:nth-child(3) th {
        position: sticky;
        top: 90px;
        /* Adjust height based on previous rows */
        background: #f4f4f4;
        z-index: 998;
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
        <?php include('../component/left-nev.php') ?>
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณจำแนกตามโครงสร้างองค์กร ตาม
                            แหล่งเงิน ตามแผนงาน/โครงการ โดยสามารถแสดงได้ทุกระดับย่อยของหน่วยงบประมาณ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานเปรียบเทียบงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                <h4>รายงานเปรียบเทียบงบประมาณ</h4>

                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>
                                            
                                            <!-- แถวแรก: หัวข้อหลัก -->
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="10">ปีงบประมาณ Budget Year</th>
                                                <th rowspan="2" colspan="5">รวม</th>
                                            </tr>
                                            <!-- แถวที่สอง: หัวข้อย่อย -->
                                            <tr>
                                                <th colspan="5">เงินอุดหนุนจากรัฐ (FN06)</th>
                                                <th colspan="5">เงินรายได้ (FN02)</th>

                                            </tr>
                                            <!-- แถวที่สาม: รายละเอียด -->
                                            <tr>
                                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                                <th>ผลการก่อหนี้ผูกพัน</th>
                                                <th>ร้อยละผลการก่อหนี้ผูกพัน</th>
                                                <th>ผลการใช้จ่าย</th>
                                                <th>ร้อยละผลการใช้จ่าย</th>

                                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                                <th>ผลการก่อหนี้ผูกพัน</th>
                                                <th>ร้อยละผลการก่อหนี้ผูกพัน</th>
                                                <th>ผลการใช้จ่าย</th>
                                                <th>ร้อยละผลการใช้จ่าย</th>

                                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                                <th>ผลการก่อหนี้ผูกพัน</th>
                                                <th>ร้อยละผลการก่อหนี้ผูกพัน</th>
                                                <th>ผลการใช้จ่าย</th>
                                                <th>ร้อยละผลการใช้จ่าย</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>



                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLSX</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let all_data;
        $(document).ready(function () {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-structure-comparison2'
                },
                dataType: "json",
                success: function (response) {
                    all_data = response.bgp;
                    const fac = [...new Set(all_data.map(item => item.f1))];
                    let dropdown = document.getElementById("category");
                    dropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
                    fac.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        dropdown.appendChild(option);
                    });
                },
                error: function (jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });

        });

        function fetchData() {
            let category = document.getElementById("category").value;

            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า               
            if (category == "all") {
                data = all_data;
            }
            else {
                data = all_data.filter(item => item.f1 === category);
            }
            //console.log(all_data);
            const f1 = [...new Set(data.map(item => item.f1))];
            const f2 = [...new Set(data.map(item => item.f2))];
            const plan_name = [...new Set(data.map(item => item.plan_name))];
            const sub_plan_name = [...new Set(data.map(item => item.sub_plan_name))];
            const project_name = [...new Set(data.map(item => item.project_name))];
            const account = [...new Set(data.map(item => item.level5))];
            const sub_account = [...new Set(data.map(item => item.level4))];
            const accname = [...new Set(data.map(item => item.level3))];
            const lv2 = [...new Set(data.map(item => item.level2))];
            const lv1 = [...new Set(data.map(item => item.level1))];
            console.log(account);
            console.log(sub_account);
            console.log(accname);
            console.log(lv2);
            console.log(lv1);
            /* console.log(f1);
            console.log(f2);
            console.log(plan_name);
            console.log(sub_plan_name);
            console.log(project_name);
            console.log(account);  */
            //console.log(all_data);
            
            /* var str1=''; 
            var str2='';
            var str3='';
            var str4=''; 
            var str5='';
            var str6='';
            var str7='';
            var str8='';
            var str9='';
            var str10='';
            var str11='';
            var str12='';
            var str13='';
            var str14='';
            var str15='';
            var str16=''; */
            var html = '';
            f1.forEach((row1) => {
                const fac = data.filter(item => item.f1 === row1);
                //console.log(pro);
                const parseValue = (value) => {
                    const number = parseFloat(value.replace(/,/g, ''));
                    return isNaN(number) ? 0 : number;
                };
                const sums = fac.reduce((acc, item) => {
                    return {
                        a2: acc.a2 + parseValue(item.a2),
                        c2: acc.c2 + parseValue(item.c2),
                        o2: acc.o2 + parseValue(item.o2),
                        e2: acc.e2 + parseValue(item.e2),
                        a6: acc.a6 + parseValue(item.a6),
                        c6: acc.c6 + parseValue(item.c6),
                        o6: acc.o6 + parseValue(item.o6),
                        e6: acc.e6 + parseValue(item.e6)
                    };
                }, {
                    a2: 0, c2: 0, o2: 0, e2: 0,
                    a6: 0, c6: 0, o6: 0, e6: 0
                });
                var s4 = Math.round((((sums.c6 + sums.o6) * 100) / (sums.a6)) * 100) / 100 || 0;
                var s9 = Math.round((((sums.c2 + sums.o2) * 100) / (sums.a2)) * 100) / 100 || 0;
                var s6 = Math.round(((sums.e6 * 100) / (sums.a6)) * 100) / 100 || 0;
                var s10 = Math.round(((sums.e2 * 100) / (sums.a2)) * 100) / 100 || 0;
                var s3 = (sums.c6 + sums.o6);
                var s8 = (sums.c2 + sums.o2);
                str1 = '<tr><td>' + row1;
                str2 = '<td>' + sums.a6.toLocaleString();
                str3 = '<td>' + s3.toLocaleString();
                str4 = '<td>' + s4.toLocaleString();
                str5 = '<td>' + sums.e6.toLocaleString();
                str6 = '<td>' + s6.toLocaleString();
                str7 = '<td>' + sums.a2.toLocaleString();
                str8 = '<td>' + s8.toLocaleString();
                str9 = '<td>' + s9.toLocaleString();
                str10 = '<td>' + sums.e2.toLocaleString();
                str11 = '<td>' + s10.toLocaleString();
                str12 = '<td>' + (sums.a6 + sums.a2).toLocaleString();
                str13 = '<td>' + (s3 + s8).toLocaleString();
                str14 = '<td>' + (s4 + s9).toLocaleString();
                str15 = '<td>' + (sums.e6 + sums.e2).toLocaleString();
                str16 = '<td>' + (Math.round((s6 + s10) * 100) / 100).toLocaleString();

                f2.forEach((row2) => {
                    const fac2 = fac.filter(item => item.f2 === row2 && item.f1 === row1);
                    //console.log(pro);
                    const parseValue = (value) => {
                        const number = parseFloat(value.replace(/,/g, ''));
                        return isNaN(number) ? 0 : number;
                    };
                    const sums = fac2.reduce((acc, item) => {
                        return {
                            a2: acc.a2 + parseValue(item.a2),
                            c2: acc.c2 + parseValue(item.c2),
                            o2: acc.o2 + parseValue(item.o2),
                            e2: acc.e2 + parseValue(item.e2),
                            a6: acc.a6 + parseValue(item.a6),
                            c6: acc.c6 + parseValue(item.c6),
                            o6: acc.o6 + parseValue(item.o6),
                            e6: acc.e6 + parseValue(item.e6)
                        };
                    }, {
                        a2: 0, c2: 0, o2: 0, e2: 0,
                        a6: 0, c6: 0, o6: 0, e6: 0
                    });
                    var s4 = Math.round((((sums.c6 + sums.o6) * 100) / (sums.a6)) * 100) / 100 || 0;
                    var s9 = Math.round((((sums.c2 + sums.o2) * 100) / (sums.a2)) * 100) / 100 || 0;
                    var s6 = Math.round(((sums.e6 * 100) / (sums.a6)) * 100) / 100 || 0;
                    var s10 = Math.round(((sums.e2 * 100) / (sums.a2)) * 100) / 100 || 0;
                    var s3 = (sums.c6 + sums.o6);
                    var s8 = (sums.c2 + sums.o2);
                    str1 += '<br/>' + '&nbsp;'.repeat(5) + row2;
                    str2 += '<br/>' + sums.a6.toLocaleString();
                    str3 += '<br/>' + s3.toLocaleString();
                    str4 += '<br/>' + s4.toLocaleString();
                    str5 += '<br/>' + sums.e6.toLocaleString();
                    str6 += '<br/>' + s6.toLocaleString();
                    str7 += '<br/>' + sums.a2.toLocaleString();
                    str8 += '<br/>' + s8.toLocaleString();
                    str9 += '<br/>' + s9.toLocaleString();
                    str10 += '<br/>' + sums.e2.toLocaleString();
                    str11 += '<br/>' + s10.toLocaleString();
                    str12 += '<br/>' + (sums.a6 + sums.a2).toLocaleString();
                    str13 += '<br/>' + (s3 + s8).toLocaleString();
                    str14 += '<br/>' + (s4 + s9).toLocaleString();
                    str15 += '<br/>' + (sums.e6 + sums.e2).toLocaleString();
                    str16 += '<br/>' + (Math.round((s6 + s10) * 100) / 100).toLocaleString();
                    plan_name.forEach((row3) => {
                        var p = data.filter(item => item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                        const parseValue = (value) => {
                            const number = parseFloat(value.replace(/,/g, ''));
                            return isNaN(number) ? 0 : number;
                        };
                        const sums = p.reduce((acc, item) => {
                            return {
                                a2: acc.a2 + parseValue(item.a2),
                                c2: acc.c2 + parseValue(item.c2),
                                o2: acc.o2 + parseValue(item.o2),
                                e2: acc.e2 + parseValue(item.e2),
                                a6: acc.a6 + parseValue(item.a6),
                                c6: acc.c6 + parseValue(item.c6),
                                o6: acc.o6 + parseValue(item.o6),
                                e6: acc.e6 + parseValue(item.e6)
                            };
                        }, {
                            a2: 0, c2: 0, o2: 0, e2: 0,
                            a6: 0, c6: 0, o6: 0, e6: 0
                        });

                        if (p.length > 0) {
                            var s4 = Math.round((((sums.c6 + sums.o6) * 100) / (sums.a6)) * 100) / 100 || 0;
                            var s9 = Math.round((((sums.c2 + sums.o2) * 100) / (sums.a2)) * 100) / 100 || 0;
                            var s6 = Math.round(((sums.e6 * 100) / (sums.a6)) * 100) / 100 || 0;
                            var s10 = Math.round(((sums.e2 * 100) / (sums.a2)) * 100) / 100 || 0;
                            var s3 = (sums.c6 + sums.o6);
                            var s8 = (sums.c2 + sums.o2);
                            str1 += '<br/>' + '&nbsp;'.repeat(10) + row3;
                            str2 += '<br/>' + sums.a6.toLocaleString();
                            str3 += '<br/>' + s3.toLocaleString();
                            str4 += '<br/>' + s4.toLocaleString();
                            str5 += '<br/>' + sums.e6.toLocaleString();
                            str6 += '<br/>' + s6.toLocaleString();
                            str7 += '<br/>' + sums.a2.toLocaleString();
                            str8 += '<br/>' + s8.toLocaleString();
                            str9 += '<br/>' + s9.toLocaleString();
                            str10 += '<br/>' + sums.e2.toLocaleString();
                            str11 += '<br/>' + s10.toLocaleString();
                            str12 += '<br/>' + (sums.a6 + sums.a2).toLocaleString();
                            str13 += '<br/>' + (s3 + s8).toLocaleString();
                            str14 += '<br/>' + (s4 + s9).toLocaleString();
                            str15 += '<br/>' + (sums.e6 + sums.e2).toLocaleString();
                            str16 += '<br/>' + (Math.round((s6 + s10) * 100) / 100).toLocaleString();
                        }
                        sub_plan_name.forEach((row4) => {
                            var sp = p.filter(item => item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                            //console.log(sp);
                            const parseValue = (value) => {
                                const number = parseFloat(value.replace(/,/g, ''));
                                return isNaN(number) ? 0 : number;
                            };
                            const sums = sp.reduce((acc, item) => {
                                return {
                                    a2: acc.a2 + parseValue(item.a2),
                                    c2: acc.c2 + parseValue(item.c2),
                                    o2: acc.o2 + parseValue(item.o2),
                                    e2: acc.e2 + parseValue(item.e2),
                                    a6: acc.a6 + parseValue(item.a6),
                                    c6: acc.c6 + parseValue(item.c6),
                                    o6: acc.o6 + parseValue(item.o6),
                                    e6: acc.e6 + parseValue(item.e6)
                                };
                            }, {
                                a2: 0, c2: 0, o2: 0, e2: 0,
                                a6: 0, c6: 0, o6: 0, e6: 0
                            });
                            if (sp.length > 0) {
                                var s4 = Math.round((((sums.c6 + sums.o6) * 100) / (sums.a6)) * 100) / 100 || 0;
                                var s9 = Math.round((((sums.c2 + sums.o2) * 100) / (sums.a2)) * 100) / 100 || 0;
                                var s6 = Math.round(((sums.e6 * 100) / (sums.a6)) * 100) / 100 || 0;
                                var s10 = Math.round(((sums.e2 * 100) / (sums.a2)) * 100) / 100 || 0;
                                var s3 = (sums.c6 + sums.o6);
                                var s8 = (sums.c2 + sums.o2);
                                str1 += '<br/>' + '&nbsp;'.repeat(15) + row4;
                                str2 += '<br/>' + sums.a6.toLocaleString();
                                str3 += '<br/>' + s3.toLocaleString();
                                str4 += '<br/>' + s4.toLocaleString();
                                str5 += '<br/>' + sums.e6.toLocaleString();
                                str6 += '<br/>' + s6.toLocaleString();
                                str7 += '<br/>' + sums.a2.toLocaleString();
                                str8 += '<br/>' + s8.toLocaleString();
                                str9 += '<br/>' + s9.toLocaleString();
                                str10 += '<br/>' + sums.e2.toLocaleString();
                                str11 += '<br/>' + s10.toLocaleString();
                                str12 += '<br/>' + (sums.a6 + sums.a2).toLocaleString();
                                str13 += '<br/>' + (s3 + s8).toLocaleString();
                                str14 += '<br/>' + (s4 + s9).toLocaleString();
                                str15 += '<br/>' + (sums.e6 + sums.e2).toLocaleString();
                                str16 += '<br/>' + (Math.round((s6 + s10) * 100) / 100).toLocaleString();
                            }
                            project_name.forEach((row5) => {
                                const pro = sp.filter(item =>item.project_name === row5 &&item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                //console.log(pro);
                                const parseValue = (value) => {
                                    const number = parseFloat(value.replace(/,/g, ''));
                                    return isNaN(number) ? 0 : number;
                                };
                                const sums = pro.reduce((acc, item) => {
                                    return {
                                        a2: acc.a2 + parseValue(item.a2),
                                        c2: acc.c2 + parseValue(item.c2),
                                        o2: acc.o2 + parseValue(item.o2),
                                        e2: acc.e2 + parseValue(item.e2),
                                        a6: acc.a6 + parseValue(item.a6),
                                        c6: acc.c6 + parseValue(item.c6),
                                        o6: acc.o6 + parseValue(item.o6),
                                        e6: acc.e6 + parseValue(item.e6)
                                    };
                                }, {
                                    a2: 0, c2: 0, o2: 0, e2: 0,
                                    a6: 0, c6: 0, o6: 0, e6: 0
                                });
                                //console.log(sums);
                                if (pro.length > 0) {
                                    var s4 = Math.round((((sums.c6 + sums.o6) * 100) / (sums.a6)) * 100) / 100 || 0;
                                    var s9 = Math.round((((sums.c2 + sums.o2) * 100) / (sums.a2)) * 100) / 100 || 0;
                                    var s6 = Math.round(((sums.e6 * 100) / (sums.a6)) * 100) / 100 || 0;
                                    var s10 = Math.round(((sums.e2 * 100) / (sums.a2)) * 100) / 100 || 0;
                                    var s3 = (sums.c6 + sums.o6);
                                    var s8 = (sums.c2 + sums.o2);
                                    str1 += '<br/>' + '&nbsp;'.repeat(20) + row5;
                                    str2 += '<br/>' + sums.a6.toLocaleString();
                                    str3 += '<br/>' + s3.toLocaleString();
                                    str4 += '<br/>' + s4.toLocaleString();
                                    str5 += '<br/>' + sums.e6.toLocaleString();
                                    str6 += '<br/>' + s6.toLocaleString();
                                    str7 += '<br/>' + sums.a2.toLocaleString();
                                    str8 += '<br/>' + s8.toLocaleString();
                                    str9 += '<br/>' + s9.toLocaleString();
                                    str10 += '<br/>' + sums.e2.toLocaleString();
                                    str11 += '<br/>' + s10.toLocaleString();
                                    str12 += '<br/>' + (sums.a6 + sums.a2).toLocaleString();
                                    str13 += '<br/>' + (s3 + s8).toLocaleString();
                                    str14 += '<br/>' + (s4 + s9).toLocaleString();
                                    str15 += '<br/>' + (sums.e6 + sums.e2).toLocaleString();
                                    str16 += '<br/>' + (Math.round((s6 + s10) * 100) / 100).toLocaleString();
                                }
                                account.forEach((row6) => {
                                    const ac = pro.filter(item => item.level5 === row6 && item.project_name === row5 && item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                    const parseValue = (value) => {
                                        const number = parseFloat(value.replace(/,/g, ''));
                                        return isNaN(number) ? 0 : number;
                                    };
                                    const sums = ac.reduce((acc, item) => {
                                        return {
                                            a2: acc.a2 + parseValue(item.a2),
                                            c2: acc.c2 + parseValue(item.c2),
                                            o2: acc.o2 + parseValue(item.o2),
                                            e2: acc.e2 + parseValue(item.e2),
                                            a6: acc.a6 + parseValue(item.a6),
                                            c6: acc.c6 + parseValue(item.c6),
                                            o6: acc.o6 + parseValue(item.o6),
                                            e6: acc.e6 + parseValue(item.e6)
                                        };
                                    }, {
                                        a2: 0, c2: 0, o2: 0, e2: 0,
                                        a6: 0, c6: 0, o6: 0, e6: 0
                                    });
                                    if (ac.length > 0) {
                                        var s4 = Math.round((((sums.c6 + sums.o6) * 100) / (sums.a6)) * 100) / 100 || 0;
                                        var s9 = Math.round((((sums.c2 + sums.o2) * 100) / (sums.a2)) * 100) / 100 || 0;
                                        var s6 = Math.round(((sums.e6 * 100) / (sums.a6)) * 100) / 100 || 0;
                                        var s10 = Math.round(((sums.e2 * 100) / (sums.a2)) * 100) / 100 || 0;
                                        var s3 = (sums.c6 + sums.o6);
                                        var s8 = (sums.c2 + sums.o2);
                                        str1 += '<br/>' + '&nbsp;'.repeat(25) + row6;
                                        str2 += '<br/>' + sums.a6.toLocaleString();
                                        str3 += '<br/>' + s3.toLocaleString();
                                        str4 += '<br/>' + s4.toLocaleString();
                                        str5 += '<br/>' + sums.e6.toLocaleString();
                                        str6 += '<br/>' + s6.toLocaleString();
                                        str7 += '<br/>' + sums.a2.toLocaleString();
                                        str8 += '<br/>' + s8.toLocaleString();
                                        str9 += '<br/>' + s9.toLocaleString();
                                        str10 += '<br/>' + sums.e2.toLocaleString();
                                        str11 += '<br/>' + s10.toLocaleString();
                                        str12 += '<br/>' + (sums.a6 + sums.a2).toLocaleString();
                                        str13 += '<br/>' + (s3 + s8).toLocaleString();
                                        str14 += '<br/>' + (s4 + s9).toLocaleString();
                                        str15 += '<br/>' + (sums.e6 + sums.e2).toLocaleString();
                                        str16 += '<br/>' + (Math.round((s6 + s10) * 100) / 100).toLocaleString();
                                    }
                                    sub_account.forEach((row7) => {
                                        const sa = pro.filter(item =>item.level4 === row7 &&item.level5 === row6 &&item.project_name === row5 &&item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                        //console.log("sa");
                                        //console.log(sa);
                                        const parseValue = (value) => {
                                            const number = parseFloat(value.replace(/,/g, ''));
                                            return isNaN(number) ? 0 : number;
                                        };
                                        const sums = sa.reduce((acc, item) => {
                                            return {
                                                a2: acc.a2 + parseValue(item.a2),
                                                c2: acc.c2 + parseValue(item.c2),
                                                o2: acc.o2 + parseValue(item.o2),
                                                e2: acc.e2 + parseValue(item.e2),
                                                a6: acc.a6 + parseValue(item.a6),
                                                c6: acc.c6 + parseValue(item.c6),
                                                o6: acc.o6 + parseValue(item.o6),
                                                e6: acc.e6 + parseValue(item.e6)
                                            };
                                        }, {
                                            a2: 0, c2: 0, o2: 0, e2: 0,
                                            a6: 0, c6: 0, o6: 0, e6: 0
                                        });
                                        if (sa.length > 0) {
                                            var s4 = Math.round((((sums.c6 + sums.o6) * 100) / (sums.a6)) * 100) / 100 || 0;
                                            var s9 = Math.round((((sums.c2 + sums.o2) * 100) / (sums.a2)) * 100) / 100 || 0;
                                            var s6 = Math.round(((sums.e6 * 100) / (sums.a6)) * 100) / 100 || 0;
                                            var s10 = Math.round(((sums.e2 * 100) / (sums.a2)) * 100) / 100 || 0;
                                            var s3 = (sums.c6 + sums.o6);
                                            var s8 = (sums.c2 + sums.o2);
                                            str1 += '<br/>' + '&nbsp;'.repeat(30) + row7;
                                            str2 += '<br/>' + sums.a6.toLocaleString();
                                            str3 += '<br/>' + s3.toLocaleString();
                                            str4 += '<br/>' + s4.toLocaleString();
                                            str5 += '<br/>' + sums.e6.toLocaleString();
                                            str6 += '<br/>' + s6.toLocaleString();
                                            str7 += '<br/>' + sums.a2.toLocaleString();
                                            str8 += '<br/>' + s8.toLocaleString();
                                            str9 += '<br/>' + s9.toLocaleString();
                                            str10 += '<br/>' + sums.e2.toLocaleString();
                                            str11 += '<br/>' + s10.toLocaleString();
                                            str12 += '<br/>' + (sums.a6 + sums.a2).toLocaleString();
                                            str13 += '<br/>' + (s3 + s8).toLocaleString();
                                            str14 += '<br/>' + (s4 + s9).toLocaleString();
                                            str15 += '<br/>' + (sums.e6 + sums.e2).toLocaleString();
                                            str16 += '<br/>' + (Math.round((s6 + s10) * 100) / 100).toLocaleString();
                                        }
                                        accname.forEach((row8) => {
                                            const sa2 = sa.filter(item =>item.level3 === row8 &&item.level4 === row7 &&item.level5 === row6 &&item.project_name === row5 &&item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                            //console.log("aaaaaa");
                                            //console.log(sa2);
                                            const parseValue = (value) => {
                                                    const number = parseFloat(value.replace(/,/g, ''));
                                                    return isNaN(number) ? 0 : number;
                                                };
                                            const sums = sa2.reduce((acc, item) => {
                                                    return {
                                                        a2: acc.a2 + parseValue(item.a2),
                                                        c2: acc.c2 + parseValue(item.c2),
                                                        o2: acc.o2 + parseValue(item.o2),
                                                        e2: acc.e2 + parseValue(item.e2),
                                                        a6: acc.a6 + parseValue(item.a6),
                                                        c6: acc.c6 + parseValue(item.c6),
                                                        o6: acc.o6 + parseValue(item.o6),
                                                        e6: acc.e6 + parseValue(item.e6)
                                                    };
                                                }, {
                                                    a2: 0, c2: 0, o2: 0, e2: 0,
                                                    a6: 0, c6: 0, o6: 0, e6: 0
                                                });
                                            if(sa2.length>0 && row8!=null){
                                                var s4=Math.round((((sums.c6+sums.o6)*100)/(sums.a6))* 100) / 100 || 0;
                                                var s9=Math.round((((sums.c2+sums.o2)*100)/(sums.a2))* 100) / 100 || 0;
                                                var s6=Math.round(((sums.e6*100)/(sums.a6))* 100) / 100 || 0;
                                                var s10=Math.round(((sums.e2*100)/(sums.a2))* 100) / 100 || 0;
                                                var s3=(sums.c6+sums.o6);
                                                var s8=(sums.c2+sums.o2);
                                                str1+='<br/>'+'&nbsp;'.repeat(35)+row8;
                                                str2+='<br/>'+sums.a6.toLocaleString();
                                                str3+='<br/>'+s3.toLocaleString();
                                                str4+='<br/>'+s4.toLocaleString();
                                                str5+='<br/>'+sums.e6.toLocaleString();
                                                str6+='<br/>'+s6.toLocaleString();
                                                str7+='<br/>'+sums.a2.toLocaleString();
                                                str8+='<br/>'+s8.toLocaleString();
                                                str9+='<br/>'+s9.toLocaleString();
                                                str10+='<br/>'+sums.e2.toLocaleString();
                                                str11+='<br/>'+s10.toLocaleString();
                                                str12+='<br/>'+(sums.a6+sums.a2).toLocaleString();
                                                str13+='<br/>'+(s3+s8).toLocaleString();
                                                str14+='<br/>'+(s4+s9).toLocaleString();
                                                str15+='<br/>'+(sums.e6+sums.e2).toLocaleString();
                                                str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                            }
                                            if(sa2.length>0 && row8==null){
                                                sa2.forEach((row8_null) => {
                                                    const parseValue = (value) => {
                                                        const number = parseFloat(value.replace(/,/g, ''));
                                                        return isNaN(number) ? 0 : number;
                                                    };
                                                    
                                                    if(row8_null.KKU_Item_Name!=""){
                                                        var s4=Math.round((((parseInt(row8_null.c6)+parseInt(row8_null.o6))*100)/(parseInt(row8_null.a6)))* 100) / 100 || 0;
                                                        var s9=Math.round((((parseInt(row8_null.c2)+parseInt(row8_null.o2))*100)/(parseInt(row8_null.a2)))* 100) / 100 || 0;
                                                        var s6=Math.round(((parseInt(row8_null.e6)*100)/(parseInt(row8_null.a6)))* 100) / 100 || 0;
                                                        var s10=Math.round(((parseInt(row8_null.e2)*100)/(parseInt(row8_null.a2)))* 100) / 100 || 0;
                                                        var s3=(parseInt(row8_null.c6)+parseInt(row8_null.o6));
                                                        var s8=(parseInt(row8_null.c2)+parseInt(row8_null.o2));
                                                        str1+='<br/>'+'&nbsp;'.repeat(35)+row8_null.KKU_Item_Name2;
                                                        str2+='<br/>'+parseInt(row8_null.a6).toLocaleString();
                                                        str3+='<br/>'+s3.toLocaleString();
                                                        str4+='<br/>'+s4.toLocaleString();
                                                        str5+='<br/>'+parseInt(row8_null.e6).toLocaleString();
                                                        str6+='<br/>'+s6.toLocaleString();
                                                        str7+='<br/>'+parseInt(row8_null.a2).toLocaleString();
                                                        str8+='<br/>'+s8.toLocaleString();
                                                        str9+='<br/>'+s9.toLocaleString();
                                                        str10+='<br/>'+parseInt(row8_null.e2).toLocaleString();
                                                        str11+='<br/>'+s10.toLocaleString();
                                                        str12+='<br/>'+(parseInt(row8_null.a6)+parseInt(row8_null.a2)).toLocaleString();
                                                        str13+='<br/>'+(s3+s8).toLocaleString();
                                                        str14+='<br/>'+(s4+s9).toLocaleString();
                                                        str15+='<br/>'+(parseInt(row8_null.e6)+parseInt(row8_null.e2)).toLocaleString();
                                                        str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                    }
                                                });
                                            }
                                            lv2.forEach((row9) => {
                                                const l2 = sa2.filter(item =>item.level2 === row9 &&item.level3 === row8 &&item.level4 === row7 &&item.level5 === row6 &&item.project_name === row5 &&item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                                //console.log("aaaaaa");
                                                //console.log(l2);
                                                const parseValue = (value) => {
                                                        const number = parseFloat(value.replace(/,/g, ''));
                                                        return isNaN(number) ? 0 : number;
                                                    };
                                                const sums = l2.reduce((acc, item) => {
                                                        return {
                                                            a2: acc.a2 + parseValue(item.a2),
                                                            c2: acc.c2 + parseValue(item.c2),
                                                            o2: acc.o2 + parseValue(item.o2),
                                                            e2: acc.e2 + parseValue(item.e2),
                                                            a6: acc.a6 + parseValue(item.a6),
                                                            c6: acc.c6 + parseValue(item.c6),
                                                            o6: acc.o6 + parseValue(item.o6),
                                                            e6: acc.e6 + parseValue(item.e6)
                                                        };
                                                    }, {
                                                        a2: 0, c2: 0, o2: 0, e2: 0,
                                                        a6: 0, c6: 0, o6: 0, e6: 0
                                                    });
                                                if(l2.length>0 && row9!=null){
                                                    //console.log(l2)
                                                    var s4=Math.round((((sums.c6+sums.o6)*100)/(sums.a6))* 100) / 100 || 0;
                                                    var s9=Math.round((((sums.c2+sums.o2)*100)/(sums.a2))* 100) / 100 || 0;
                                                    var s6=Math.round(((sums.e6*100)/(sums.a6))* 100) / 100 || 0;
                                                    var s10=Math.round(((sums.e2*100)/(sums.a2))* 100) / 100 || 0;
                                                    var s3=(sums.c6+sums.o6);
                                                    var s8=(sums.c2+sums.o2);
                                                    str1+='<br/>'+'&nbsp;'.repeat(40)+row9;
                                                    str2+='<br/>'+sums.a6.toLocaleString();
                                                    str3+='<br/>'+s3.toLocaleString();
                                                    str4+='<br/>'+s4.toLocaleString();
                                                    str5+='<br/>'+sums.e6.toLocaleString();
                                                    str6+='<br/>'+s6.toLocaleString();
                                                    str7+='<br/>'+sums.a2.toLocaleString();
                                                    str8+='<br/>'+s8.toLocaleString();
                                                    str9+='<br/>'+s9.toLocaleString();
                                                    str10+='<br/>'+sums.e2.toLocaleString();
                                                    str11+='<br/>'+s10.toLocaleString();
                                                    str12+='<br/>'+(sums.a6+sums.a2).toLocaleString();
                                                    str13+='<br/>'+(s3+s8).toLocaleString();
                                                    str14+='<br/>'+(s4+s9).toLocaleString();
                                                    str15+='<br/>'+(sums.e6+sums.e2).toLocaleString();
                                                    str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                }
                                                if(l2.length>0 && row9==null&& row8!=null){
                                                    l2.forEach((row9_null) => {
                                                        const parseValue = (value) => {
                                                            const number = parseFloat(value.replace(/,/g, ''));
                                                            return isNaN(number) ? 0 : number;
                                                        };
                                                        
                                                        if(row9_null.KKU_Item_Name!=""){
                                                            var s4=Math.round((((parseInt(row9_null.c6)+parseInt(row9_null.o6))*100)/(parseInt(row9_null.a6)))* 100) / 100 || 0;
                                                            var s9=Math.round((((parseInt(row9_null.c2)+parseInt(row9_null.o2))*100)/(parseInt(row9_null.a2)))* 100) / 100 || 0;
                                                            var s6=Math.round(((parseInt(row9_null.e6)*100)/(parseInt(row9_null.a6)))* 100) / 100 || 0;
                                                            var s10=Math.round(((parseInt(row9_null.e2)*100)/(parseInt(row9_null.a2)))* 100) / 100 || 0;
                                                            var s3=(parseInt(row9_null.c6)+parseInt(row9_null.o6));
                                                            var s8=(parseInt(row9_null.c2)+parseInt(row9_null.o2));
                                                            str1+='<br/>'+'&nbsp;'.repeat(40)+row9_null.KKU_Item_Name2;
                                                            str2+='<br/>'+parseInt(row9_null.a6).toLocaleString();
                                                            str3+='<br/>'+s3.toLocaleString();
                                                            str4+='<br/>'+s4.toLocaleString();
                                                            str5+='<br/>'+parseInt(row9_null.e6).toLocaleString();
                                                            str6+='<br/>'+s6.toLocaleString();
                                                            str7+='<br/>'+parseInt(row9_null.a2).toLocaleString();
                                                            str8+='<br/>'+s8.toLocaleString();
                                                            str9+='<br/>'+s9.toLocaleString();
                                                            str10+='<br/>'+parseInt(row9_null.e2).toLocaleString();
                                                            str11+='<br/>'+s10.toLocaleString();
                                                            str12+='<br/>'+(parseInt(row9_null.a6)+parseInt(row9_null.a2)).toLocaleString();
                                                            str13+='<br/>'+(s3+s8).toLocaleString();
                                                            str14+='<br/>'+(s4+s9).toLocaleString();
                                                            str15+='<br/>'+(parseInt(row9_null.e6)+parseInt(row9_null.e2)).toLocaleString();
                                                            str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                        }
                                                    });
                                                }
                                                lv1.forEach((row10) => {
                                                    const l1 = l2.filter(item =>item.level1 === row10 &&item.level2 === row9 &&item.level3 === row8 &&item.level4 === row7 &&item.level5 === row6 &&item.project_name === row5 &&item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                                    //console.log("aaaaaa");
                                                    //console.log(sa2);
                                                    const parseValue = (value) => {
                                                            const number = parseFloat(value.replace(/,/g, ''));
                                                            return isNaN(number) ? 0 : number;
                                                        };
                                                    const sums = l1.reduce((acc, item) => {
                                                            return {
                                                                a2: acc.a2 + parseValue(item.a2),
                                                                c2: acc.c2 + parseValue(item.c2),
                                                                o2: acc.o2 + parseValue(item.o2),
                                                                e2: acc.e2 + parseValue(item.e2),
                                                                a6: acc.a6 + parseValue(item.a6),
                                                                c6: acc.c6 + parseValue(item.c6),
                                                                o6: acc.o6 + parseValue(item.o6),
                                                                e6: acc.e6 + parseValue(item.e6)
                                                            };
                                                        }, {
                                                            a2: 0, c2: 0, o2: 0, e2: 0,
                                                            a6: 0, c6: 0, o6: 0, e6: 0
                                                        });
                                                    if(l1.length>0 && row10!=null){
                                                        var s4=Math.round((((sums.c6+sums.o6)*100)/(sums.a6))* 100) / 100 || 0;
                                                        var s9=Math.round((((sums.c2+sums.o2)*100)/(sums.a2))* 100) / 100 || 0;
                                                        var s6=Math.round(((sums.e6*100)/(sums.a6))* 100) / 100 || 0;
                                                        var s10=Math.round(((sums.e2*100)/(sums.a2))* 100) / 100 || 0;
                                                        var s3=(sums.c6+sums.o6);
                                                        var s8=(sums.c2+sums.o2);
                                                        str1+='<br/>'+'&nbsp;'.repeat(45)+row10;
                                                        str2+='<br/>'+sums.a6.toLocaleString();
                                                        str3+='<br/>'+s3.toLocaleString();
                                                        str4+='<br/>'+s4.toLocaleString();
                                                        str5+='<br/>'+sums.e6.toLocaleString();
                                                        str6+='<br/>'+s6.toLocaleString();
                                                        str7+='<br/>'+sums.a2.toLocaleString();
                                                        str8+='<br/>'+s8.toLocaleString();
                                                        str9+='<br/>'+s9.toLocaleString();
                                                        str10+='<br/>'+sums.e2.toLocaleString();
                                                        str11+='<br/>'+s10.toLocaleString();
                                                        str12+='<br/>'+(sums.a6+sums.a2).toLocaleString();
                                                        str13+='<br/>'+(s3+s8).toLocaleString();
                                                        str14+='<br/>'+(s4+s9).toLocaleString();
                                                        str15+='<br/>'+(sums.e6+sums.e2).toLocaleString();
                                                        str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                        l1.forEach((row10_item) => {
                                                            const parseValue = (value) => {
                                                                const number = parseFloat(value.replace(/,/g, ''));
                                                                return isNaN(number) ? 0 : number;
                                                            };
                                                            
                                                            if(row10_item.KKU_Item_Name!=""){
                                                                var s4=Math.round((((parseInt(row10_item.c6)+parseInt(row10_item.o6))*100)/(parseInt(row10_item.a6)))* 100) / 100 || 0;
                                                                var s9=Math.round((((parseInt(row10_item.c2)+parseInt(row10_item.o2))*100)/(parseInt(row10_item.a2)))* 100) / 100 || 0;
                                                                var s6=Math.round(((parseInt(row10_item.e6)*100)/(parseInt(row10_item.a6)))* 100) / 100 || 0;
                                                                var s10=Math.round(((parseInt(row10_item.e2)*100)/(parseInt(row10_item.a2)))* 100) / 100 || 0;
                                                                var s3=(parseInt(row10_item.c6)+parseInt(row10_item.o6));
                                                                var s8=(parseInt(row10_item.c2)+parseInt(row10_item.o2));
                                                                str1+='<br/>'+'&nbsp;'.repeat(45)+row10_item.KKU_Item_Name2;
                                                                str2+='<br/>'+parseInt(row10_item.a6).toLocaleString();
                                                                str3+='<br/>'+s3.toLocaleString();
                                                                str4+='<br/>'+s4.toLocaleString();
                                                                str5+='<br/>'+parseInt(row10_item.e6).toLocaleString();
                                                                str6+='<br/>'+s6.toLocaleString();
                                                                str7+='<br/>'+parseInt(row10_item.a2).toLocaleString();
                                                                str8+='<br/>'+s8.toLocaleString();
                                                                str9+='<br/>'+s9.toLocaleString();
                                                                str10+='<br/>'+parseInt(row10_item.e2).toLocaleString();
                                                                str11+='<br/>'+s10.toLocaleString();
                                                                str12+='<br/>'+(parseInt(row10_item.a6)+parseInt(row10_item.a2)).toLocaleString();
                                                                str13+='<br/>'+(s3+s8).toLocaleString();
                                                                str14+='<br/>'+(s4+s9).toLocaleString();
                                                                str15+='<br/>'+(parseInt(row10_item.e6)+parseInt(row10_item.e2)).toLocaleString();
                                                                str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                            }
                                                        });
                                                    }
                                                    if(l1.length>0 && row9==null&& row8!=null&& row10!=null){
                                                        l1.forEach((row9_null) => {
                                                            const parseValue = (value) => {
                                                                const number = parseFloat(value.replace(/,/g, ''));
                                                                return isNaN(number) ? 0 : number;
                                                            };
                                                            
                                                            if(row10_null.KKU_Item_Name!=""){
                                                                var s4=Math.round((((parseInt(row10_null.c6)+parseInt(row10_null.o6))*100)/(parseInt(row10_null.a6)))* 100) / 100 || 0;
                                                                var s9=Math.round((((parseInt(row10_null.c2)+parseInt(row10_null.o2))*100)/(parseInt(row10_null.a2)))* 100) / 100 || 0;
                                                                var s6=Math.round(((parseInt(row10_null.e6)*100)/(parseInt(row10_null.a6)))* 100) / 100 || 0;
                                                                var s10=Math.round(((parseInt(row10_null.e2)*100)/(parseInt(row10_null.a2)))* 100) / 100 || 0;
                                                                var s3=(parseInt(row10_null.c6)+parseInt(row10_null.o6));
                                                                var s8=(parseInt(row10_null.c2)+parseInt(row10_null.o2));
                                                                str1+='<br/>'+'&nbsp;'.repeat(45)+row10_null.KKU_Item_Name2;
                                                                str2+='<br/>'+parseInt(row10_null.a6).toLocaleString();
                                                                str3+='<br/>'+s3.toLocaleString();
                                                                str4+='<br/>'+s4.toLocaleString();
                                                                str5+='<br/>'+parseInt(row10_null.e6).toLocaleString();
                                                                str6+='<br/>'+s6.toLocaleString();
                                                                str7+='<br/>'+parseInt(row10_null.a2).toLocaleString();
                                                                str8+='<br/>'+s8.toLocaleString();
                                                                str9+='<br/>'+s9.toLocaleString();
                                                                str10+='<br/>'+parseInt(row10_null.e2).toLocaleString();
                                                                str11+='<br/>'+s10.toLocaleString();
                                                                str12+='<br/>'+(parseInt(row10_null.a6)+parseInt(row10_null.a2)).toLocaleString();
                                                                str13+='<br/>'+(s3+s8).toLocaleString();
                                                                str14+='<br/>'+(s4+s9).toLocaleString();
                                                                str15+='<br/>'+(parseInt(row10_null.e6)+parseInt(row10_null.e2)).toLocaleString();
                                                                str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                            }
                                                        });
                                                    }
                                                    /* sa2.forEach((row9) => {
                                                        const parseValue = (value) => {
                                                            const number = parseFloat(value.replace(/,/g, ''));
                                                            return isNaN(number) ? 0 : number;
                                                        };
                                                        
                                                        if(row9.KKU_Item_Name!=""){
                                                            var s4=Math.round((((parseInt(row9.c6)+parseInt(row9.o6))*100)/(parseInt(row9.a6)))* 100) / 100 || 0;
                                                            var s9=Math.round((((parseInt(row9.c2)+parseInt(row9.o2))*100)/(parseInt(row9.a2)))* 100) / 100 || 0;
                                                            var s6=Math.round(((parseInt(row9.e6)*100)/(parseInt(row9.a6)))* 100) / 100 || 0;
                                                            var s10=Math.round(((parseInt(row9.e2)*100)/(parseInt(row9.a2)))* 100) / 100 || 0;
                                                            var s3=(parseInt(row9.c6)+parseInt(row9.o6));
                                                            var s8=(parseInt(row9.c2)+parseInt(row9.o2));
                                                            str1+='<br/>'+'&nbsp;'.repeat(64)+row9.KKU_Item_Name2;
                                                            str2+='<br/>'+parseInt(row9.a6).toLocaleString();
                                                            str3+='<br/>'+s3.toLocaleString();
                                                            str4+='<br/>'+s4.toLocaleString();
                                                            str5+='<br/>'+parseInt(row9.e6).toLocaleString();
                                                            str6+='<br/>'+s6.toLocaleString();
                                                            str7+='<br/>'+parseInt(row9.a2).toLocaleString();
                                                            str8+='<br/>'+s8.toLocaleString();
                                                            str9+='<br/>'+s9.toLocaleString();
                                                            str10+='<br/>'+parseInt(row9.e2).toLocaleString();
                                                            str11+='<br/>'+s10.toLocaleString();
                                                            str12+='<br/>'+(parseInt(row9.a6)+parseInt(row9.a2)).toLocaleString();
                                                            str13+='<br/>'+(s3+s8).toLocaleString();
                                                            str14+='<br/>'+(s4+s9).toLocaleString();
                                                            str15+='<br/>'+(parseInt(row9.e6)+parseInt(row9.e2)).toLocaleString();
                                                            str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                        }
                                                    }); */
                                                
                                                });
                                                
                                                /* sa2.forEach((row9) => {
                                                        const parseValue = (value) => {
                                                            const number = parseFloat(value.replace(/,/g, ''));
                                                            return isNaN(number) ? 0 : number;
                                                        };
                                                        
                                                        if(row9.KKU_Item_Name!=""){
                                                            var s4=Math.round((((parseInt(row9.c6)+parseInt(row9.o6))*100)/(parseInt(row9.a6)))* 100) / 100 || 0;
                                                            var s9=Math.round((((parseInt(row9.c2)+parseInt(row9.o2))*100)/(parseInt(row9.a2)))* 100) / 100 || 0;
                                                            var s6=Math.round(((parseInt(row9.e6)*100)/(parseInt(row9.a6)))* 100) / 100 || 0;
                                                            var s10=Math.round(((parseInt(row9.e2)*100)/(parseInt(row9.a2)))* 100) / 100 || 0;
                                                            var s3=(parseInt(row9.c6)+parseInt(row9.o6));
                                                            var s8=(parseInt(row9.c2)+parseInt(row9.o2));
                                                            str1+='<br/>'+'&nbsp;'.repeat(64)+row9.KKU_Item_Name2;
                                                            str2+='<br/>'+parseInt(row9.a6).toLocaleString();
                                                            str3+='<br/>'+s3.toLocaleString();
                                                            str4+='<br/>'+s4.toLocaleString();
                                                            str5+='<br/>'+parseInt(row9.e6).toLocaleString();
                                                            str6+='<br/>'+s6.toLocaleString();
                                                            str7+='<br/>'+parseInt(row9.a2).toLocaleString();
                                                            str8+='<br/>'+s8.toLocaleString();
                                                            str9+='<br/>'+s9.toLocaleString();
                                                            str10+='<br/>'+parseInt(row9.e2).toLocaleString();
                                                            str11+='<br/>'+s10.toLocaleString();
                                                            str12+='<br/>'+(parseInt(row9.a6)+parseInt(row9.a2)).toLocaleString();
                                                            str13+='<br/>'+(s3+s8).toLocaleString();
                                                            str14+='<br/>'+(s4+s9).toLocaleString();
                                                            str15+='<br/>'+(parseInt(row9.e6)+parseInt(row9.e2)).toLocaleString();
                                                            str16+='<br/>'+(Math.round((s6+s10)* 100) / 100).toLocaleString();
                                                        }
                                                    }); */
                                                
                                                
                                                
                                            });
                                            
                                        
                                        });

                                    });
                                });
                            });
                        });
                    });
                });

                str1 += '</td>';
                str2 += '</td>';
                str3 += '</td>';
                str4 += '</td>';
                str5 += '</td>';
                str6 += '</td>';
                str7 += '</td>';
                str8 += '</td>';
                str9 += '</td>';
                str10 += '</td>';
                str11 += '</td>';
                str12 += '</td>';
                str13 += '</td>';
                str14 += '</td>';
                str15 += '</td>';
                str16 += '</td></tr>';
                html += str1 + str2 + str3 + str4 + str5 + str6 + str7 + str8 + str9 + str10 + str11 + str12 + str13 + str14 + str15 + str16;
                //console.log(str1+str2+str3+str4+str5+str6+str7+str8+str9+str10+str11+str12+str13+str14+str15+str16+'</tr>');
            });
            tableBody.innerHTML = html;

        }
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
            link.download = 'รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณจำแนกตามโครงสร้างองค์กรตามแหล่งเงินตามแผนงาน/โครงการโดยสามารถแสดงได้ทุกระดับย่อยของหน่วยงบประมาณ.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
        function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');
    
    // ตั้งค่ามาร์จินและขนาดกระดาษ
    const marginLeft = 5;
    const marginRight = 5;
    const marginTop = 15;
    const marginBottom = 10;
    const pageWidth = doc.internal.pageSize.width;
    const usableWidth = pageWidth - marginLeft - marginRight;
    
    // ตั้งค่าฟอนต์
    doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
    doc.addFont("THSarabun.ttf", "THSarabun", "normal");
    doc.setFont("THSarabun");
    doc.setFontSize(10);
    doc.text('รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร', marginLeft, marginTop - 5);
    
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
                    const matches = htmlPart.match(/&nbsp;/g);
                    const spaces = matches ? matches.length : 0;
                    
                    // แปลง HTML entities เป็นข้อความ
                    const div = document.createElement('div');
                    div.innerHTML = htmlPart;
                    const originalText = div.textContent;
                    
                    // สร้างข้อความใหม่ที่มีการเยื้องด้วยช่องว่าง
                    const indent = Math.floor(spaces / 8); // ทุก 8 &nbsp; = 1 ระดับ
                    
                    // 1. ปรับข้อความให้ไม่มีช่องว่างนำ
                    const trimmedText = originalText.trim();
                    
                    // 2. สร้างช่องว่างขึ้นใหม่ตามระดับการเยื้อง
                    const spacesPerIndent = 4; // จำนวนช่องว่างต่อระดับการเยื้อง 1 ระดับ
                    const leadingSpaces = ' '.repeat(indent * spacesPerIndent);
                    
                    // 3. เพิ่มช่องว่างนำหน้าข้อความ
                    const indentedText = leadingSpaces + trimmedText;
                    
                    // ตั้งค่าเซลล์ให้รักษาช่องว่าง
                    firstCell.style.whiteSpace = 'pre';
                    firstCell.textContent = indentedText;
                    
                    // จัดการกับ &nbsp; ในข้อความ
                    // ในต้นฉบับจริง &nbsp; จะถูกใช้ 8 ตัวต่อระดับการเยื้อง
                    
                    // ตรวจนับจำนวน &nbsp; โดยตรงจาก HTML
                    let indentLevel = 0;
                    const nbspCount = (part.match(/&nbsp;/g) || []).length;
                    indentLevel = Math.floor(nbspCount / 8); // ทุก 8 &nbsp; = 1 ระดับ
                    
                    // สร้างช่องว่างแทน &nbsp; ในข้อความจริง
                    let cleanText = part.replace(/&nbsp;/g, ' ');
                    // แปลง HTML entities กลับเป็นข้อความปกติ
                    const tempDiv2 = document.createElement('div');
                    tempDiv2.innerHTML = cleanText;
                    let textContent = tempDiv2.textContent;
                    
                    // เพิ่มการเยื้องด้วย CSS สำหรับ PDF
                    if (indentLevel > 0) {
                        firstCell.style.paddingLeft = (indentLevel * 8) + 'mm';
                    }
                    
                    firstCell.textContent = textContent.trim();
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
                
                rowData.forEach(cellData => {
                    const cell = document.createElement('td');
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
            theme: 'grid',
            styles: {
                font: "THSarabun",
                fontSize: fontSize,
                cellPadding: 1,
                lineWidth: 0.1,
                lineColor: [0, 0, 0],
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
                valign: 'middle'
            },
            columnStyles: columnStyles,
            margin: { top: marginTop, right: marginRight, bottom: marginBottom, left: marginLeft },
            tableWidth: usableWidth,
            showHead: 'everyPage',
            tableLineWidth: 0.1,
            tableLineColor: [0, 0, 0],
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
                }
            }
        });
    } finally {
        // ลบตารางชั่วคราว
        if (tempTable.parentNode) {
            tempTable.parentNode.removeChild(tempTable);
        }
    }
    
    doc.save('รายงานเปรียบเทียบงบประมาณ.pdf');
}

        function exportXLS() {
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
            link.download = 'รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณจำแนกตามโครงสร้างองค์กรตามแหล่งเงินตามแผนงาน/โครงการโดยสามารถแสดงได้ทุกระดับย่อยของหน่วยงบประมาณ.xls';
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