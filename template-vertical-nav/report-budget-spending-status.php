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

    th,
    td {
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
        top: 44px;
        /* Adjust height based on previous row */
        background: #f4f4f4;
        z-index: 999;
    }

    thead tr:nth-child(3) th {
        position: sticky;
        top: 111px;
        /* Adjust height based on previous rows */
        background: #f4f4f4;
        z-index: 998;
    }

    .form-group {
        display: inline-block;
        margin-right: 20px;
        margin-bottom: 10px;
    }

    label {
        margin-right: 5px;
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
                        <h4>รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                                </div>
                                <label for="fyear">เลือกปีงบประมาณ:</label>
                                <select name="fyear" id="fyear">
                                    <option value="">-- Select --</option>
                                    <option value="2568">2568</option>
                                </select>
                                <br />
                                <label for="scenario">เลือกประเภทงบประมาณ:</label>
                                <select name="scenario" id="scenario" disabled>
                                    <option value="">-- Loading Scenarios --</option>
                                </select>
                                <br />
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="category">เลือกส่วนงาน:</label>
                                        <select name="category" id="category" onchange="fetchData()" disabled>
                                            <option value="">-- Loading Categories --</option>
                                        </select>
                                    </div>
                                    <!-- โหลด SweetAlert2 (ใส่ใน <head> หรือก่อนปิด </body>) -->
                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                    <!-- ปุ่ม -->
                                    <button class="btn btn-primary" onclick="runCmd()" style="margin-bottom: 10px;">อัพเดทข้อมูล</button>

                                    <script>
                                        function runCmd() {
                                            // แสดง SweetAlert ขณะกำลังรัน .cmd
                                            Swal.fire({
                                                title: 'กำลังอัปเดตข้อมูล',
                                                text: 'กรุณารอสักครู่...',
                                                allowOutsideClick: false,
                                                didOpen: () => {
                                                    Swal.showLoading(); // แสดง loading spinner
                                                }
                                            });

                                            // เรียก PHP เพื่อรัน .cmd
                                            fetch('/kku-report/server/automateEPM/budget_planning/run_cmd_budget_planning.php')
                                                .then(response => response.text())
                                                .then(result => {
                                                    // เมื่อทำงานเสร็จ ปิด loading แล้วแสดงผลลัพธ์
                                                    Swal.fire({
                                                        title: 'อัปเดตข้อมูลเสร็จสิ้น',
                                                        html: result, // ใช้ .html เพื่อแสดงผลเป็น <br>
                                                        icon: 'success'
                                                    });
                                                })
                                                .catch(error => {
                                                    Swal.fire({
                                                        title: 'เกิดข้อผิดพลาด',
                                                        text: 'ไม่สามารถอัปเดตข้อมูลได้',
                                                        icon: 'error'
                                                    });
                                                    console.error(error);
                                                });
                                        }
                                    </script>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="8">ปี 2566</th>
                                                <th colspan="8">ปี 2567</th>
                                                <th colspan="4">ปี 2568</th>
                                                <th rowspan="2" colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                <th colspan="2">เงินรายได้</th>
                                                <th colspan="2">รวม</th>

                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                <th colspan="2">เงินรายได้</th>
                                                <th colspan="2">รวม</th>

                                                <th rowspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th rowspan="2">เงินรายได้</th>
                                                <th rowspan="2">เงินนอกงบประมาณ</th>
                                                <th rowspan="2">รวม</th>
                                            </tr>
                                            <tr>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>

                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>

                                <!-- Export buttons -->
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
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
        let all_data;
        let all_data2;
        $(document).ready(function() {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-spending-status'
                },
                dataType: "json",
                success: function(response) {
                    all_data = response.bgp;

                    // เติมข้อมูลใน select ส่วนงาน
                    /* const categories = [...new Set(all_data.map(item => item.pname))];
                    let categoryDropdown = document.getElementById("fyear");
                    categoryDropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
                    categories.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        categoryDropdown.appendChild(option);
                    }); */
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-spending-status2'
                },
                dataType: "json",
                success: function(response) {
                    all_data2 = response.bgp;

                    // เติมข้อมูลใน select ส่วนงาน
                    /* const categories = [...new Set(all_data.map(item => item.pname))];
                    let categoryDropdown = document.getElementById("fyear");
                    categoryDropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
                    categories.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        categoryDropdown.appendChild(option);
                    }); */
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
        });
        $('#fyear').change(function() {
            const scenario = [...new Set(all_data.map(item => item.scenario))];
            let facDropdown = document.getElementById("scenario");
            facDropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
            scenario.forEach(category => {
                let option = document.createElement("option");
                option.value = category;
                option.textContent = category;
                facDropdown.appendChild(option);
            });
            $('#scenario').prop('disabled', false);
        });
        $('#scenario').change(function() {
            let scenario = document.getElementById("scenario").value;
            let data;
            if (scenario == "all") {
                data = all_data;
            } else {
                data = all_data.filter(item => item.scenario === scenario);
            }
            //let all_data2 = all_data.filter(item=>item.scenario===scenario);
            //console.log(all_data2);
            const fac = [...new Set(data.map(item => item.pname))];
            let facDropdown = document.getElementById("category");
            facDropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
            fac.forEach(category => {
                let option = document.createElement("option");
                option.value = category;
                option.textContent = category;
                facDropdown.appendChild(option);
            });
            $('#category').prop('disabled', false);
        });

        function fetchData() {
            let year = document.getElementById("fyear").value;
            let y1 = (parseInt(year) - 1).toString();
            let y2 = (parseInt(year) - 2).toString();
            let category = document.getElementById("category").value;
            let scenario = document.getElementById("scenario").value;
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า               
            if (scenario == "all") {
                data = all_data;
            } else {
                data = all_data.filter(item => item.scenario === scenario);
            }
            if (category == "all") {
                data2 = data;
            } else {
                data2 = data.filter(item => item.pname === category);
            }
            const f1 = [...new Set(data2.map(item => item.Alias_Default))];
            const f2 = [...new Set(data2.map(item => item.pillar_name))];
            const account = [...new Set(data2.map(item => item.level5))];
            const sub_account = [...new Set(data2.map(item => item.level4))];
            const accname = [...new Set(data2.map(item => item.level3))];
            const lv2 = [...new Set(data2.map(item => item.level2))];
            const lv1 = [...new Set(data2.map(item => item.level1))];
            console.log(f1.length);
            var html = '';
            if (f1.length > 0) {
                const parseValue = (value) => {
                    if (!value || typeof value !== 'string') return 0;
                    const number = parseFloat(value.replace(/,/g, ''));
                    return isNaN(number) ? 0 : number;
                };
                const sums = data2.reduce((acc, item) => {
                    const year = item.Budget_Management_Year; // Get the year from each item

                    // Initialize the year object if it doesn't exist
                    if (!acc[year]) {
                        acc[year] = {
                            t06: 0,
                            t02: 0,
                            t08: 0,
                            e06: 0,
                            e02: 0,
                            e08: 0
                        };
                    }

                    // Add the parsed values to the corresponding year
                    acc[year].t06 += parseValue(item.t06);
                    acc[year].t02 += parseValue(item.t02);
                    acc[year].t08 += parseValue(item.t08);
                    acc[year].e06 += parseValue(item.e06);
                    acc[year].e02 += parseValue(item.e02);
                    acc[year].e08 += parseValue(item.e08);

                    return acc;
                }, {});
                //let n1 =all_data2.filter(item=>item.FISCAL_YEAR===year);
                let n2 = all_data2.filter(item => item.FISCAL_YEAR === y1);
                let n3 = all_data2.filter(item => item.FISCAL_YEAR === y2);

                const sumsn2 = n2.reduce((acc, item) => {
                    return {
                        n06: acc.n06 + parseValue(item.n06),
                        n08: acc.n08 + parseValue(item.n08),
                        n02: acc.n02 + parseValue(item.n02),
                    };
                }, {
                    n06: 0,
                    n08: 0,
                    n02: 0
                });
                const sumsn3 = n3.reduce((acc, item) => {
                    return {
                        n06: acc.n06 + parseValue(item.n06),
                        n08: acc.n08 + parseValue(item.n08),
                        n02: acc.n02 + parseValue(item.n02),

                    };
                }, {
                    n06: 0,
                    n08: 0,
                    n02: 0

                });

                console.log(sumsn3);
                var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                var sumn2 = parseInt(sumsn3 ? sumsn3.n06 : 0) + parseInt(sumsn3 ? sumsn3.n08 : 0) + parseInt(sumsn3 ? sumsn3.n02 : 0);
                var sumn1 = parseInt(sumsn2 ? sumsn2.n06 : 0) + parseInt(sumsn2 ? sumsn2.n08 : 0) + parseInt(sumsn2 ? sumsn2.n02 : 0);

                str1 = '<tr><td style="text-align:center;" nowrap>รวมทั้งสิ้น</td>';
                str2 = '<td>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString() + '</td>';
                str3 = '<td>' + ((sums[y2] ? (sums[y2].e06) : 0) + parseInt(sumsn3 ? sumsn3.n06 : 0)).toLocaleString() + '</td>';
                str4 = '<td>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString() + '</td>';
                str5 = '<td>' + ((sums[y2] ? (sums[y2].e08) : 0) + parseInt(sumsn3 ? sumsn3.n08 : 0)).toLocaleString() + '</td>';
                str6 = '<td>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString() + '</td>';
                str7 = '<td>' + ((sums[y2] ? (sums[y2].e02) : 0) + parseInt(sumsn3 ? sumsn3.n02 : 0)).toLocaleString() + '</td>';
                str8 = '<td>' + sumy2.toLocaleString() + '</td>';
                str9 = '<td>' + (sumacy2 + sumn2).toLocaleString() + '</td>';
                str10 = '<td>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString() + '</td>';
                str11 = '<td>' + ((sums[y1] ? (sums[y1].e06) : 0) + parseInt(sumsn2 ? sumsn2.n06 : 0)).toLocaleString() + '</td>';
                str12 = '<td>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString() + '</td>';
                str13 = '<td>' + ((sums[y1] ? (sums[y1].e08) : 0) + parseInt(sumsn2 ? sumsn2.n08 : 0)).toLocaleString() + '</td>';
                str14 = '<td>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString() + '</td>';
                str15 = '<td>' + ((sums[y1] ? (sums[y1].e02) : 0) + parseInt(sumsn2 ? sumsn2.n02 : 0)).toLocaleString() + '</td>';
                str16 = '<td>' + sumy1.toLocaleString() + '</td>';
                str17 = '<td>' + (sumacy1 + sumn1).toLocaleString() + '</td>';
                str18 = '<td>' + sums[year].t06.toLocaleString() + '</td>';
                str19 = '<td>' + sums[year].t02.toLocaleString() + '</td>';
                str20 = '<td>' + sums[year].t08.toLocaleString() + '</td>';
                str21 = '<td>' + sum.toLocaleString() + '</td>';
                str22 = '<td>' + (sum - (sumacy1 + sumn1)).toLocaleString() + '</td>';
                str23 = '<td>' + ((sumacy1 + sumn1) === 0 ? '100%' : parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%</td></tr>';
                html += str1 + str2 + str3 + str4 + str5 + str6 + str7 + str8 + str9 + str10 + str11 + str12 + str13 + str14 + str15 +
                    str16 + str17 + str18 + str19 + str20 + str21 + str22 + str23;
            }

            f1.forEach((row1) => {
                let n2 = all_data2.filter(item => item.FISCAL_YEAR === y1 && item.Alias_Default === row1);
                let n3 = all_data2.filter(item => item.FISCAL_YEAR === y2 && item.Alias_Default === row1);
                const f = data2.filter(item => item.Alias_Default === row1);
                const parseValue = (value) => {
                    if (!value || typeof value !== 'string') return 0;
                    const number = parseFloat(value.replace(/,/g, ''));
                    return isNaN(number) ? 0 : number;
                };
                const sums = f.reduce((acc, item) => {
                    const year = item.Budget_Management_Year; // Get the year from each item

                    // Initialize the year object if it doesn't exist
                    if (!acc[year]) {
                        acc[year] = {
                            t06: 0,
                            t02: 0,
                            t08: 0,
                            e06: 0,
                            e02: 0,
                            e08: 0
                        };
                    }

                    // Add the parsed values to the corresponding year
                    acc[year].t06 += parseValue(item.t06);
                    acc[year].t02 += parseValue(item.t02);
                    acc[year].t08 += parseValue(item.t08);
                    acc[year].e06 += parseValue(item.e06);
                    acc[year].e02 += parseValue(item.e02);
                    acc[year].e08 += parseValue(item.e08);

                    return acc;
                }, {});
                const sumsn2 = n2.reduce((acc, item) => {
                    return {
                        n06: acc.n06 + parseValue(item.n06),
                        n08: acc.n08 + parseValue(item.n08),
                        n02: acc.n02 + parseValue(item.n02),
                    };
                }, {
                    n06: 0,
                    n08: 0,
                    n02: 0
                });
                const sumsn3 = n3.reduce((acc, item) => {
                    return {
                        n06: acc.n06 + parseValue(item.n06),
                        n08: acc.n08 + parseValue(item.n08),
                        n02: acc.n02 + parseValue(item.n02),

                    };
                }, {
                    n06: 0,
                    n08: 0,
                    n02: 0

                });
                var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                var sumn2 = parseInt(sumsn3 ? sumsn3.n06 : 0) + parseInt(sumsn3 ? sumsn3.n08 : 0) + parseInt(sumsn3 ? sumsn3.n02 : 0);
                var sumn1 = parseInt(sumsn2 ? sumsn2.n06 : 0) + parseInt(sumsn2 ? sumsn2.n08 : 0) + parseInt(sumsn2 ? sumsn2.n02 : 0);
                str1 = '<tr><td style="text-align:left;" nowrap>' + row1;
                str2 = '<td>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                str3 = '<td>' + ((sums[y2] ? (sums[y2].e06) : 0) + parseInt(sumsn3 ? sumsn3.n06 : 0)).toLocaleString();
                str4 = '<td>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                str5 = '<td>' + ((sums[y2] ? (sums[y2].e08) : 0) + parseInt(sumsn3 ? sumsn3.n08 : 0)).toLocaleString();
                str6 = '<td>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                str7 = '<td>' + ((sums[y2] ? (sums[y2].e02) : 0) + parseInt(sumsn3 ? sumsn3.n02 : 0)).toLocaleString();
                str8 = '<td>' + sumy2.toLocaleString();
                str9 = '<td>' + (sumacy2 + sumn2).toLocaleString();
                str10 = '<td>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                str11 = '<td>' + ((sums[y1] ? (sums[y1].e06) : 0) + parseInt(sumsn2 ? sumsn2.n06 : 0)).toLocaleString();
                str12 = '<td>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                str13 = '<td>' + ((sums[y1] ? (sums[y1].e08) : 0) + parseInt(sumsn2 ? sumsn2.n08 : 0)).toLocaleString();
                str14 = '<td>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                str15 = '<td>' + ((sums[y1] ? (sums[y1].e02) : 0) + parseInt(sumsn2 ? sumsn2.n02 : 0)).toLocaleString();
                str16 = '<td>' + sumy1.toLocaleString();
                str17 = '<td>' + (sumacy1 + sumn1).toLocaleString();
                str18 = '<td>' + sums[year].t06.toLocaleString();
                str19 = '<td>' + sums[year].t02.toLocaleString();
                str20 = '<td>' + sums[year].t08.toLocaleString();
                str21 = '<td>' + sum.toLocaleString();
                str22 = '<td>' + (sum - (sumacy1 + sumn1)).toLocaleString();
                str23 = '<td>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                f2.forEach((row2) => {
                    const pi = data2.filter(item => item.pillar_name === row2 && item.Alias_Default === row1);
                    const parseValue = (value) => {
                        const number = parseFloat(value.replace(/,/g, ''));
                        return isNaN(number) ? 0 : number;
                    };
                    const sums = pi.reduce((acc, item) => {
                        const year = item.Budget_Management_Year; // Get the year from each item

                        // Initialize the year object if it doesn't exist
                        if (!acc[year]) {
                            acc[year] = {
                                t06: 0,
                                t02: 0,
                                t08: 0,
                                e06: 0,
                                e02: 0,
                                e08: 0
                            };
                        }

                        // Add the parsed values to the corresponding year
                        acc[year].t06 += parseValue(item.t06);
                        acc[year].t02 += parseValue(item.t02);
                        acc[year].t08 += parseValue(item.t08);
                        acc[year].e06 += parseValue(item.e06);
                        acc[year].e02 += parseValue(item.e02);
                        acc[year].e08 += parseValue(item.e08);

                        return acc;
                    }, {});
                    if (pi.length > 0) {
                        var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                        var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                        var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                        let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                        let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                        str1 += '<br/>' + '&nbsp;'.repeat(8) + row2;
                        str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                        str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                        str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                        str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                        str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                        str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                        str8 += '<br/>' + sumy2.toLocaleString();
                        str9 += '<br/>' + sumacy2.toLocaleString();
                        str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                        str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                        str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                        str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                        str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                        str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                        str16 += '<br/>' + sumy1.toLocaleString();
                        str17 += '<br/>' + sumacy1.toLocaleString();
                        str18 += '<br/>' + sums[year].t06.toLocaleString();
                        str19 += '<br/>' + sums[year].t02.toLocaleString();
                        str20 += '<br/>' + sums[year].t08.toLocaleString();
                        str21 += '<br/>' + sum.toLocaleString();
                        str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                        str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                    }
                    account.forEach((row6) => {
                        const ac = pi.filter(item => item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                        const parseValue = (value) => {
                            const number = parseFloat(value.replace(/,/g, ''));
                            return isNaN(number) ? 0 : number;
                        };
                        const sums = ac.reduce((acc, item) => {
                            const year = item.Budget_Management_Year; // Get the year from each item

                            // Initialize the year object if it doesn't exist
                            if (!acc[year]) {
                                acc[year] = {
                                    t06: 0,
                                    t02: 0,
                                    t08: 0,
                                    e06: 0,
                                    e02: 0,
                                    e08: 0
                                };
                            }

                            // Add the parsed values to the corresponding year
                            acc[year].t06 += parseValue(item.t06);
                            acc[year].t02 += parseValue(item.t02);
                            acc[year].t08 += parseValue(item.t08);
                            acc[year].e06 += parseValue(item.e06);
                            acc[year].e02 += parseValue(item.e02);
                            acc[year].e08 += parseValue(item.e08);

                            return acc;
                        }, {});
                        if (ac.length > 0) {
                            var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                            var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                            var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                            let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                            let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                            str1 += '<br/>' + '&nbsp;'.repeat(16) + row6;
                            str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                            str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                            str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                            str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                            str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                            str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                            str8 += '<br/>' + sumy2.toLocaleString();
                            str9 += '<br/>' + sumacy2.toLocaleString();
                            str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                            str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                            str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                            str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                            str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                            str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                            str16 += '<br/>' + sumy1.toLocaleString();
                            str17 += '<br/>' + sumacy1.toLocaleString();
                            str18 += '<br/>' + sums[year].t06.toLocaleString();
                            str19 += '<br/>' + sums[year].t02.toLocaleString();
                            str20 += '<br/>' + sums[year].t08.toLocaleString();
                            str21 += '<br/>' + sum.toLocaleString();
                            str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                            str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                        }
                        sub_account.forEach((row7) => {
                            const sa = ac.filter(item => item.level4 === row7 && item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                            //console.log(sa);
                            const parseValue = (value) => {
                                const number = parseFloat(value.replace(/,/g, ''));
                                return isNaN(number) ? 0 : number;
                            };
                            const sums = sa.reduce((acc, item) => {
                                const year = item.Budget_Management_Year; // Get the year from each item

                                // Initialize the year object if it doesn't exist
                                if (!acc[year]) {
                                    acc[year] = {
                                        t06: 0,
                                        t02: 0,
                                        t08: 0,
                                        e06: 0,
                                        e02: 0,
                                        e08: 0
                                    };
                                }

                                // Add the parsed values to the corresponding year
                                acc[year].t06 += parseValue(item.t06);
                                acc[year].t02 += parseValue(item.t02);
                                acc[year].t08 += parseValue(item.t08);
                                acc[year].e06 += parseValue(item.e06);
                                acc[year].e02 += parseValue(item.e02);
                                acc[year].e08 += parseValue(item.e08);

                                return acc;
                            }, {});
                            if (sa.length > 0) {
                                var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                str1 += '<br/>' + '&nbsp;'.repeat(24) + row7;
                                str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                str8 += '<br/>' + sumy2.toLocaleString();
                                str9 += '<br/>' + sumacy2.toLocaleString();
                                str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                str16 += '<br/>' + sumy1.toLocaleString();
                                str17 += '<br/>' + sumacy1.toLocaleString();
                                str18 += '<br/>' + sums[year].t06.toLocaleString();
                                str19 += '<br/>' + sums[year].t02.toLocaleString();
                                str20 += '<br/>' + sums[year].t08.toLocaleString();
                                str21 += '<br/>' + sum.toLocaleString();
                                str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                            }
                            accname.forEach((row8) => {
                                const sa2 = sa.filter(item => item.level3 === row8 && item.level4 === row7 && item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                const parseValue = (value) => {
                                    const number = parseFloat(value.replace(/,/g, ''));
                                    return isNaN(number) ? 0 : number;
                                };
                                const sums = sa2.reduce((acc, item) => {
                                    const year = item.Budget_Management_Year; // Get the year from each item

                                    // Initialize the year object if it doesn't exist
                                    if (!acc[year]) {
                                        acc[year] = {
                                            t06: 0,
                                            t02: 0,
                                            t08: 0,
                                            e06: 0,
                                            e02: 0,
                                            e08: 0
                                        };
                                    }

                                    // Add the parsed values to the corresponding year
                                    acc[year].t06 += parseValue(item.t06);
                                    acc[year].t02 += parseValue(item.t02);
                                    acc[year].t08 += parseValue(item.t08);
                                    acc[year].e06 += parseValue(item.e06);
                                    acc[year].e02 += parseValue(item.e02);
                                    acc[year].e08 += parseValue(item.e08);

                                    return acc;
                                }, {});
                                if (sa2.length > 0 && row8 != null) {
                                    var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                    var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                    var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                    let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                    let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                    str1 += '<br/>' + '&nbsp;'.repeat(32) + row8;
                                    str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                    str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                    str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                    str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                    str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                    str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                    str8 += '<br/>' + sumy2.toLocaleString();
                                    str9 += '<br/>' + sumacy2.toLocaleString();
                                    str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                    str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                    str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                    str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                    str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                    str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                    str16 += '<br/>' + sumy1.toLocaleString();
                                    str17 += '<br/>' + sumacy1.toLocaleString();
                                    str18 += '<br/>' + sums[year].t06.toLocaleString();
                                    str19 += '<br/>' + sums[year].t02.toLocaleString();
                                    str20 += '<br/>' + sums[year].t08.toLocaleString();
                                    str21 += '<br/>' + sum.toLocaleString();
                                    str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                    str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                                }
                                if (sa2.length > 0 && row8 == null) {
                                    const item_name = [...new Set(sa2.map(item => item.KKU_Item_Name2))];

                                    item_name.forEach((row8_null) => {
                                        let all_item = sa2.filter(item => item.KKU_Item_Name2 === row8_null);
                                        const parseValue = (value) => {
                                            const number = parseFloat(value.replace(/,/g, ''));
                                            return isNaN(number) ? 0 : number;
                                        };
                                        const sums = all_item.reduce((acc, item) => {
                                            const year = item.Budget_Management_Year; // Get the year from each item

                                            // Initialize the year object if it doesn't exist
                                            if (!acc[year]) {
                                                acc[year] = {
                                                    t06: 0,
                                                    t02: 0,
                                                    t08: 0,
                                                    e06: 0,
                                                    e02: 0,
                                                    e08: 0
                                                };
                                            }

                                            // Add the parsed values to the corresponding year
                                            acc[year].t06 += parseValue(item.t06);
                                            acc[year].t02 += parseValue(item.t02);
                                            acc[year].t08 += parseValue(item.t08);
                                            acc[year].e06 += parseValue(item.e06);
                                            acc[year].e02 += parseValue(item.e02);
                                            acc[year].e08 += parseValue(item.e08);

                                            return acc;
                                        }, {});
                                        if (row8_null != "" && row8_null != null) {
                                            var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                            var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                            var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                            let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                            let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                            str1 += '<br/>' + '&nbsp;'.repeat(32) + row8_null;
                                            str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                            str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                            str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                            str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                            str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                            str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                            str8 += '<br/>' + sumy2.toLocaleString();
                                            str9 += '<br/>' + sumacy2.toLocaleString();
                                            str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                            str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                            str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                            str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                            str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                            str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                            str16 += '<br/>' + sumy1.toLocaleString();
                                            str17 += '<br/>' + sumacy1.toLocaleString();
                                            str18 += '<br/>' + sums[year].t06.toLocaleString();
                                            str19 += '<br/>' + sums[year].t02.toLocaleString();
                                            str20 += '<br/>' + sums[year].t08.toLocaleString();
                                            str21 += '<br/>' + sum.toLocaleString();
                                            str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                            str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                                        }
                                    });
                                }
                                lv2.forEach((row9) => {
                                    const l2 = sa2.filter(item => item.level2 === row9 && item.level3 === row8 && item.level4 === row7 && item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                    const parseValue = (value) => {
                                        const number = parseFloat(value.replace(/,/g, ''));
                                        return isNaN(number) ? 0 : number;
                                    };
                                    const sums = l2.reduce((acc, item) => {
                                        const year = item.Budget_Management_Year; // Get the year from each item

                                        // Initialize the year object if it doesn't exist
                                        if (!acc[year]) {
                                            acc[year] = {
                                                t06: 0,
                                                t02: 0,
                                                t08: 0,
                                                e06: 0,
                                                e02: 0,
                                                e08: 0
                                            };
                                        }

                                        // Add the parsed values to the corresponding year
                                        acc[year].t06 += parseValue(item.t06);
                                        acc[year].t02 += parseValue(item.t02);
                                        acc[year].t08 += parseValue(item.t08);
                                        acc[year].e06 += parseValue(item.e06);
                                        acc[year].e02 += parseValue(item.e02);
                                        acc[year].e08 += parseValue(item.e08);

                                        return acc;
                                    }, {});
                                    if (l2.length > 0 && row9 != null) {
                                        var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                        var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                        var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                        let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                        let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                        str1 += '<br/>' + '&nbsp;'.repeat(40) + row9;
                                        str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                        str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                        str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                        str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                        str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                        str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                        str8 += '<br/>' + sumy2.toLocaleString();
                                        str9 += '<br/>' + sumacy2.toLocaleString();
                                        str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                        str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                        str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                        str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                        str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                        str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                        str16 += '<br/>' + sumy1.toLocaleString();
                                        str17 += '<br/>' + sumacy1.toLocaleString();
                                        str18 += '<br/>' + sums[year].t06.toLocaleString();
                                        str19 += '<br/>' + sums[year].t02.toLocaleString();
                                        str20 += '<br/>' + sums[year].t08.toLocaleString();
                                        str21 += '<br/>' + sum.toLocaleString();
                                        str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                        str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                                    }
                                    if (l2.length > 0 && row9 == null && row8 != null) {

                                        const item_name = [...new Set(l2.map(item => item.KKU_Item_Name2))];
                                        //console.log(item_name);
                                        item_name.forEach((row9_null) => {
                                            let all_item = l2.filter(item => item.KKU_Item_Name2 === row9_null);
                                            //console.log(all_item);
                                            const parseValue = (value) => {
                                                const number = parseFloat(value.replace(/,/g, ''));
                                                return isNaN(number) ? 0 : number;
                                            };
                                            const sums = all_item.reduce((acc, item) => {
                                                const year = item.Budget_Management_Year; // Get the year from each item

                                                // Initialize the year object if it doesn't exist
                                                if (!acc[year]) {
                                                    acc[year] = {
                                                        t06: 0,
                                                        t02: 0,
                                                        t08: 0,
                                                        e06: 0,
                                                        e02: 0,
                                                        e08: 0
                                                    };
                                                }

                                                // Add the parsed values to the corresponding year
                                                acc[year].t06 += parseValue(item.t06);
                                                acc[year].t02 += parseValue(item.t02);
                                                acc[year].t08 += parseValue(item.t08);
                                                acc[year].e06 += parseValue(item.e06);
                                                acc[year].e02 += parseValue(item.e02);
                                                acc[year].e08 += parseValue(item.e08);

                                                return acc;
                                            }, {});
                                            if (row9_null != "" && row9_null != null) {
                                                var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                                var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                                var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                                let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                                let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                                str1 += '<br/>' + '&nbsp;'.repeat(40) + row9_null;
                                                str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                                str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                                str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                                str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                                str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                                str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                                str8 += '<br/>' + sumy2.toLocaleString();
                                                str9 += '<br/>' + sumacy2.toLocaleString();
                                                str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                                str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                                str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                                str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                                str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                                str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                                str16 += '<br/>' + sumy1.toLocaleString();
                                                str17 += '<br/>' + sumacy1.toLocaleString();
                                                str18 += '<br/>' + sums[year].t06.toLocaleString();
                                                str19 += '<br/>' + sums[year].t02.toLocaleString();
                                                str20 += '<br/>' + sums[year].t08.toLocaleString();
                                                str21 += '<br/>' + sum.toLocaleString();
                                                str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                                str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                                            }
                                        });
                                    }
                                    lv1.forEach((row10) => {
                                        const l1 = l2.filter(item => item.level1 === row10 && item.level2 === row9 && item.level3 === row8 && item.level4 === row7 && item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                        const parseValue = (value) => {
                                            const number = parseFloat(value.replace(/,/g, ''));
                                            return isNaN(number) ? 0 : number;
                                        };
                                        const sums = l1.reduce((acc, item) => {
                                            const year = item.Budget_Management_Year; // Get the year from each item

                                            // Initialize the year object if it doesn't exist
                                            if (!acc[year]) {
                                                acc[year] = {
                                                    t06: 0,
                                                    t02: 0,
                                                    t08: 0,
                                                    e06: 0,
                                                    e02: 0,
                                                    e08: 0
                                                };
                                            }

                                            // Add the parsed values to the corresponding year
                                            acc[year].t06 += parseValue(item.t06);
                                            acc[year].t02 += parseValue(item.t02);
                                            acc[year].t08 += parseValue(item.t08);
                                            acc[year].e06 += parseValue(item.e06);
                                            acc[year].e02 += parseValue(item.e02);
                                            acc[year].e08 += parseValue(item.e08);

                                            return acc;
                                        }, {});
                                        if (l1.length > 0 && row10 != null) {
                                            var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                            var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                            var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                            let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                            let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                            str1 += '<br/>' + '&nbsp;'.repeat(48) + row10;
                                            str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                            str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                            str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                            str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                            str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                            str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                            str8 += '<br/>' + sumy2.toLocaleString();
                                            str9 += '<br/>' + sumacy2.toLocaleString();
                                            str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                            str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                            str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                            str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                            str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                            str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                            str16 += '<br/>' + sumy1.toLocaleString();
                                            str17 += '<br/>' + sumacy1.toLocaleString();
                                            str18 += '<br/>' + sums[year].t06.toLocaleString();
                                            str19 += '<br/>' + sums[year].t02.toLocaleString();
                                            str20 += '<br/>' + sums[year].t08.toLocaleString();
                                            str21 += '<br/>' + sum.toLocaleString();
                                            str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                            str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                                            const item_name = [...new Set(l1.map(item => item.KKU_Item_Name2))];
                                            item_name.forEach((row10_item) => {
                                                let all_item = l1.filter(item => item.KKU_Item_Name2 === row10_item);
                                                const parseValue = (value) => {
                                                    const number = parseFloat(value.replace(/,/g, ''));
                                                    return isNaN(number) ? 0 : number;
                                                };
                                                const sums = all_item.reduce((acc, item) => {
                                                    const year = item.Budget_Management_Year; // Get the year from each item

                                                    // Initialize the year object if it doesn't exist
                                                    if (!acc[year]) {
                                                        acc[year] = {
                                                            t06: 0,
                                                            t02: 0,
                                                            t08: 0,
                                                            e06: 0,
                                                            e02: 0,
                                                            e08: 0
                                                        };
                                                    }

                                                    // Add the parsed values to the corresponding year
                                                    acc[year].t06 += parseValue(item.t06);
                                                    acc[year].t02 += parseValue(item.t02);
                                                    acc[year].t08 += parseValue(item.t08);
                                                    acc[year].e06 += parseValue(item.e06);
                                                    acc[year].e02 += parseValue(item.e02);
                                                    acc[year].e08 += parseValue(item.e08);

                                                    return acc;
                                                }, {});
                                                if (row10_item != "" && row10_item != null) {
                                                    var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                                    var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                                    var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                                    let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                                    let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                                    str1 += '<br/>' + '&nbsp;'.repeat(48) + row10_item;
                                                    str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                                    str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                                    str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                                    str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                                    str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                                    str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                                    str8 += '<br/>' + sumy2.toLocaleString();
                                                    str9 += '<br/>' + sumacy2.toLocaleString();
                                                    str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                                    str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                                    str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                                    str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                                    str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                                    str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                                    str16 += '<br/>' + sumy1.toLocaleString();
                                                    str17 += '<br/>' + sumacy1.toLocaleString();
                                                    str18 += '<br/>' + sums[year].t06.toLocaleString();
                                                    str19 += '<br/>' + sums[year].t02.toLocaleString();
                                                    str20 += '<br/>' + sums[year].t08.toLocaleString();
                                                    str21 += '<br/>' + sum.toLocaleString();
                                                    str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                                    str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                                                }
                                            });
                                        }
                                        if (l1.length > 0 && row9 != null && row10 == null) {
                                            const item_name = [...new Set(l1.map(item => item.KKU_Item_Name2))];
                                            item_name.forEach((row10_null) => {
                                                let all_item = l1.filter(item => item.KKU_Item_Name2 === row10_null);
                                                const parseValue = (value) => {
                                                    const number = parseFloat(value.replace(/,/g, ''));
                                                    return isNaN(number) ? 0 : number;
                                                };
                                                const sums = all_item.reduce((acc, item) => {
                                                    const year = item.Budget_Management_Year; // Get the year from each item

                                                    // Initialize the year object if it doesn't exist
                                                    if (!acc[year]) {
                                                        acc[year] = {
                                                            t06: 0,
                                                            t02: 0,
                                                            t08: 0,
                                                            e06: 0,
                                                            e02: 0,
                                                            e08: 0
                                                        };
                                                    }

                                                    // Add the parsed values to the corresponding year
                                                    acc[year].t06 += parseValue(item.t06);
                                                    acc[year].t02 += parseValue(item.t02);
                                                    acc[year].t08 += parseValue(item.t08);
                                                    acc[year].e06 += parseValue(item.e06);
                                                    acc[year].e02 += parseValue(item.e02);
                                                    acc[year].e08 += parseValue(item.e08);

                                                    return acc;
                                                }, {});
                                                if (row10_null != "" && row10_null != null) {
                                                    var sumy2 = sums[y2] ? (sums[y2].t06 || 0) + (sums[y2].t08 || 0) + (sums[y2].t02 || 0) : 0;
                                                    var sumy1 = sums[y1] ? ((sums[y1].t06 || 0) + (sums[y1].t08 || 0) + (sums[y1].t02 || 0)) : 0;
                                                    var sum = sums[year].t06 + sums[year].t08 + sums[year].t02;
                                                    let sumacy1 = sums[y1] ? ((sums[y1].e06 || 0) + (sums[y1].e08 || 0) + (sums[y1].e02 || 0)) : 0;
                                                    let sumacy2 = sums[y2] ? (sums[y2].e06 || 0) + (sums[y2].e08 || 0) + (sums[y2].e02 || 0) : 0;
                                                    str1 += '<br/>' + '&nbsp;'.repeat(48) + row10_null;
                                                    str2 += '<br/>' + (sums[y2] ? (sums[y2].t06) : 0).toLocaleString();
                                                    str3 += '<br/>' + (sums[y2] ? (sums[y2].e06) : 0).toLocaleString();
                                                    str4 += '<br/>' + (sums[y2] ? (sums[y2].t08) : 0).toLocaleString();
                                                    str5 += '<br/>' + (sums[y2] ? (sums[y2].e08) : 0).toLocaleString();
                                                    str6 += '<br/>' + (sums[y2] ? (sums[y2].t02) : 0).toLocaleString();
                                                    str7 += '<br/>' + (sums[y2] ? (sums[y2].e02) : 0).toLocaleString();
                                                    str8 += '<br/>' + sumy2.toLocaleString();
                                                    str9 += '<br/>' + sumacy2.toLocaleString();
                                                    str10 += '<br/>' + (sums[y1] ? (sums[y1].t06) : 0).toLocaleString();
                                                    str11 += '<br/>' + (sums[y1] ? (sums[y1].e06) : 0).toLocaleString();
                                                    str12 += '<br/>' + (sums[y1] ? (sums[y1].t08) : 0).toLocaleString();
                                                    str13 += '<br/>' + (sums[y1] ? (sums[y1].e08) : 0).toLocaleString();
                                                    str14 += '<br/>' + (sums[y1] ? (sums[y1].t02) : 0).toLocaleString();
                                                    str15 += '<br/>' + (sums[y1] ? (sums[y1].e02) : 0).toLocaleString();
                                                    str16 += '<br/>' + sumy1.toLocaleString();
                                                    str17 += '<br/>' + sumacy1.toLocaleString();
                                                    str18 += '<br/>' + sums[year].t06.toLocaleString();
                                                    str19 += '<br/>' + sums[year].t02.toLocaleString();
                                                    str20 += '<br/>' + sums[year].t08.toLocaleString();
                                                    str21 += '<br/>' + sum.toLocaleString();
                                                    str22 += '<br/>' + (sum - sumacy1).toLocaleString();
                                                    str23 += '<br/>' + ((sumacy1 + sumn1) === 0 ? '100%' : (parseFloat((((sum - (sumacy1 + sumn1)) * 100) / (sumacy1 + sumn1))).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '%');
                                                }
                                            });
                                        }


                                    });

                                });

                            });

                        });
                    });
                    //});      
                });
                var sumy2 = parseInt(sumsn3 ? sumsn3.n06 : 0) + parseInt(sumsn3 ? sumsn3.n08 : 0) + parseInt(sumsn3 ? sumsn3.n02 : 0);
                var sumy1 = parseInt(sumsn2 ? sumsn2.n06 : 0) + parseInt(sumsn2 ? sumsn2.n08 : 0) + parseInt(sumsn2 ? sumsn2.n02 : 0);

                str1 += '<br/>' + "ไม่สอดคล้องกับยุทธศาสตร์ ไม่สอดคล้องพันธกิจ";
                str2 += '<br/>' + (0).toLocaleString();
                str3 += '<br/>' + parseInt(sumsn3 ? sumsn3.n06 : 0).toLocaleString();
                str4 += '<br/>' + (0).toLocaleString();
                str5 += '<br/>' + parseInt(sumsn3 ? sumsn3.n08 : 0).toLocaleString();
                str6 += '<br/>' + (0).toLocaleString();
                str7 += '<br/>' + parseInt(sumsn3 ? sumsn3.n02 : 0).toLocaleString();
                str8 += '<br/>' + (0).toLocaleString();
                str9 += '<br/>' + sumy2.toLocaleString();
                str10 += '<br/>' + (0).toLocaleString();
                str11 += '<br/>' + parseInt(sumsn2 ? sumsn2.n06 : 0).toLocaleString();
                str12 += '<br/>' + (0).toLocaleString();
                str13 += '<br/>' + parseInt(sumsn2 ? sumsn2.n08 : 0).toLocaleString();
                str14 += '<br/>' + (0).toLocaleString();
                str15 += '<br/>' + parseInt(sumsn2 ? sumsn2.n02 : 0).toLocaleString();
                str16 += '<br/>' + (0).toLocaleString();
                str17 += '<br/>' + sumy1.toLocaleString();
                str18 += '<br/>' + (0).toLocaleString();
                str19 += '<br/>' + (0).toLocaleString();
                str20 += '<br/>' + (0).toLocaleString();
                str21 += '<br/>' + (0).toLocaleString();
                str22 += '<br/>' + (sumy1 === 0 ? 0 : (-sumy1)).toLocaleString();
                str23 += '<br/>0%';

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
                str16 += '</td>';
                str17 += '</td>';
                str18 += '</td>';
                str19 += '</td>';
                str20 += '</td>';
                str21 += '</td>';
                str22 += '</td>';
                str23 += '</td></tr>';

                html += str1 + str2 + str3 + str4 + str5 + str6 + str7 + str8 + str9 + str10 + str11 + str12 + str13 + str14 + str15 +
                    str16 + str17 + str18 + str19 + str20 + str21 + str22 + str23;
            });
            tableBody.innerHTML = html;

        }

        function exportCSV() {
            const table = document.getElementById('reportTable');
            const csvRows = [];
            const filters = getFilterValues();
            const reportHeader = [
                `"รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน"`,
                `"ส่วนงาน/หน่วยงาน: ${filters.department}"`
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

            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function getFilterValues() {
            return {

                department: document.getElementById('category').options[document.getElementById('category').selectedIndex].text
            };
        }

        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            // ตั้งค่ามาร์จินและขนาดกระดาษ
            const marginLeft = 5;
            const marginRight = 5;
            const marginTop = 25;
            const marginBottom = 10;
            const pageWidth = doc.internal.pageSize.width;
            const usableWidth = pageWidth - marginLeft - marginRight;

            // ตั้งค่าฟอนต์
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");
            const filterValues = getFilterValues();
            doc.setFontSize(12);
            doc.text("รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน", 150, 10, {
                align: 'center'
            });
            doc.setFontSize(10);
            doc.text(`ส่วนงาน/หน่วยงาน: ${filterValues.department}`, 15, 20);

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
                    margin: {
                        top: marginTop,
                        right: marginRight,
                        bottom: marginBottom,
                        left: marginLeft
                    },
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

            doc.save('รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน.pdf');
        }



        function exportXLS() {
            const table = document.getElementById('reportTable');
            const filterValues = getFilterValues();

            // สร้าง Workbook
            const wb = XLSX.utils.book_new();

            // สร้างส่วนหัวรายงาน
            const headerRows = [
                ["รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน"],
                ["ส่วนงาน/หน่วยงาน:", filterValues.department],
                [""] // แถวว่าง
            ];

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const {
                theadRows,
                theadMerges
            } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br/>, ไม่ merge) ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            // รวมทุกแถวเข้าด้วยกัน: headerRows + theadRows + tbodyRows
            const allRows = [...headerRows, ...theadRows, ...tbodyRows];

            // สร้าง worksheet จากข้อมูลทั้งหมด
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // จัดการ styles สำหรับส่วนหัว
            // ถ้า A1 ไม่มี s ให้สร้างใหม่
            if (!ws['A1'].s) ws['A1'].s = {};
            ws['A1'].s.font = {
                bold: true,
                sz: 15
            };
            ws['A1'].s.alignment = {
                horizontal: "center"
            };

            // ถ้า A2 ไม่มี s ให้สร้างใหม่
            if (!ws['A2'].s) ws['A2'].s = {};
            ws['A2'].s.font = {
                bold: true
            };

            // ปรับ merges สำหรับหัวข้อแรก
            const headerMerges = [];

            // merge หัวข้อแรกให้กินพื้นที่ทั้งแถว
            const maxCols = Math.max(...allRows.map(row => row.length));
            if (maxCols > 1) {
                headerMerges.push({
                    s: {
                        r: 0,
                        c: 0
                    },
                    e: {
                        r: 0,
                        c: maxCols - 1
                    }
                });
            }

            // ปรับ theadMerges (บวก offset จาก headerRows)
            const headerRowCount = headerRows.length;
            const updatedTheadMerges = theadMerges.map(merge => ({
                s: {
                    r: merge.s.r + headerRowCount,
                    c: merge.s.c
                },
                e: {
                    r: merge.e.r + headerRowCount,
                    c: merge.e.c
                }
            }));

            // รวม merges ทั้งหมด
            ws['!merges'] = [...headerMerges, ...updatedTheadMerges];

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // ปรับความกว้างคอลัมน์ (ถ้าต้องการ)
            // ws['!cols'] = [{ wch: 20 }, { wch: 20 }, ...]; // ตั้งค่าความกว้างคอลัมน์

            // เขียนไฟล์เป็น .xls (BIFF8)
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xls',
                type: 'array',
                cellStyles: true
            });

            // สร้าง Blob + ดาวน์โหลด
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสถานะการใช้จ่ายงบประมาณตามแหล่งเงิน.xls';
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
                return {
                    theadRows,
                    theadMerges
                };
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
                        .replace(/<\/?[^>]+>/g, '') // ลบ tag อื่น ถ้าเหลือ
                        .trim();

                    rowData[colIndex] = text;

                    // ดู rowSpan/colSpan
                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        // Push merges object
                        theadMerges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            },
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            }
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

            return {
                theadRows,
                theadMerges
            };
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