
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
    top: 45px; /* Adjust height based on previous row */
    background: #f4f4f4;
    z-index: 999;
}

thead tr:nth-child(3) th {
    position: sticky;
    top: 132px; /* Adjust height based on previous rows */
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
                        <h4>รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="8">ปี 2566</th>
                                                <th colspan="8">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="4">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th rowspan="2" colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">เงินอุดหนุนจากรัฐ (FN06)</th>
                                                <th colspan="2">เงินนอกงบประมาณ (FN08)</th>
                                                <th colspan="2">เงินรายได้ (FN02)</th>
                                                <th colspan="2">รวม</th>

                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                <th colspan="2">จำนวน</th>
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
        $(document).ready(function() {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-spending-status'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response.bgp;
                    const fac = [...new Set(all_data.map(item => item.pname))];
                    let dropdown = document.getElementById("category");
                    dropdown.innerHTML = '<option value="">-- Select --</option><option value="all">เลือกทั้งหมด</option>';
                    fac.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category;
                        option.textContent = category;
                        dropdown.appendChild(option);
                    });
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
            
        });

        function fetchData() {
            let category = document.getElementById("category").value;
            const tableBody = document.querySelector('#reportTable tbody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเก่า               
            if(category=="all"){
                data=all_data;
            }
            else{
                data= all_data.filter(item=>item.pname===category);
            }
            const f1 = [...new Set(data.map(item => item.Alias_Default))];
            const f2 = [...new Set(data.map(item => item.pillar_name))];
            const account = [...new Set(data.map(item => item.level5))];
            const sub_account = [...new Set(data.map(item => item.level4))];
            const accname = [...new Set(data.map(item => item.level3))];
            const lv2 = [...new Set(data.map(item => item.level2))];
            const lv1 = [...new Set(data.map(item => item.level1))];
            
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
            var str16='';
            var str17=''; 
            var str18='';
            var str19='';
            var str20=''; 
            var str21='';
            var str22='';
            var str23=''; */
            var html='';
            f1.forEach((row1) => { 
                str1='<tr><td style="text-align:left;" nowrap>'+row1;
                str2='<td>';
                str3='<td>';
                str4='<td>';
                str5='<td>';
                str6='<td>';
                str7='<td>';
                str8='<td>';
                str9='<td>';
                str10='<td>';
                str11='<td>';
                str12='<td>';
                str13='<td>';
                str14='<td>';
                str15='<td>';
                str16='<td>';
                str17='<td>'; 
                str18='<td>';
                str19='<td>';
                str20='<td>'; 
                str21='<td>';
                str22='<td>';
                str23='<td>';
                f2.forEach((row2) => {
                    const pi= data.filter(item =>item.pillar_name === row2 && item.Alias_Default === row1);
                    if(pi.length>0){
                        str1+='<br/>'+'&nbsp;'.repeat(8)+row2;
                        str2+='<br/>';
                        str3+='<br/>';
                        str4+='<br/>';
                        str5+='<br/>';
                        str6+='<br/>';
                        str7+='<br/>';
                        str8+='<br/>';
                        str9+='<br/>';
                        str10+='<br/>';
                        str11+='<br/>';
                        str12+='<br/>';
                        str13+='<br/>';
                        str14+='<br/>';
                        str15+='<br/>';
                        str16+='<br/>';
                        str17+='<br/>'; 
                        str18+='<br/>';
                        str19+='<br/>';
                        str20+='<br/>'; 
                        str21+='<br/>';
                        str22+='<br/>';
                        str23+='<br/>';
                    }
                    account.forEach((row6) => {
                        const ac = pi.filter(item =>item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                        const parseValue = (value) => {
                                const number = parseFloat(value.replace(/,/g, ''));
                                return isNaN(number) ? 0 : number;
                            };
                        const sums = ac.reduce((acc, item) => {
                                return {
                                    t06: acc.t06 + parseValue(item.t06),
                                    t02: acc.t02 + parseValue(item.t02),
                                    t08: acc.t08 + parseValue(item.t08),
                                };
                            }, {
                                t06: 0, t02: 0, t08: 0
                            });
                        if(ac.length>0){
                            var sum=sums.t06+sums.t08+sums.t02;
                            str1+='<br/>'+'&nbsp;'.repeat(16)+row6;
                            str2+='<br/>0';
                            str3+='<br/>0';
                            str4+='<br/>0';
                            str5+='<br/>0';
                            str6+='<br/>0';
                            str7+='<br/>0';
                            str8+='<br/>0';
                            str9+='<br/>0';
                            str10+='<br/>0';
                            str11+='<br/>0';
                            str12+='<br/>0';
                            str13+='<br/>0';
                            str14+='<br/>0';
                            str15+='<br/>0';
                            str16+='<br/>0';
                            str17+='<br/>0'; 
                            str18+='<br/>'+sums.t06.toLocaleString();
                            str19+='<br/>'+sums.t02.toLocaleString();
                            str20+='<br/>'+sums.t08.toLocaleString(); 
                            str21+='<br/>'+sum.toLocaleString();
                            str22+='<br/>'+(sum).toLocaleString();
                            str23+='<br/>';
                        }   
                        sub_account.forEach((row7) => {
                            const sa = ac.filter(item =>item.level4 === row7 &&item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                            //console.log(sa);
                            const parseValue = (value) => {
                                const number = parseFloat(value.replace(/,/g, ''));
                                return isNaN(number) ? 0 : number;
                            };
                            const sums = sa.reduce((acc, item) => {
                                return {
                                    t06: acc.t06 + parseValue(item.t06),
                                    t02: acc.t02 + parseValue(item.t02),
                                    t08: acc.t08 + parseValue(item.t08),
                                };
                            }, {
                                t06: 0, t02: 0, t08: 0
                            });
                            if(sa.length>0){
                                var sum=sums.t06+sums.t08+sums.t02;
                                str1+='<br/>'+'&nbsp;'.repeat(24)+row7;
                                str2+='<br/>0';
                                str3+='<br/>0';
                                str4+='<br/>0';
                                str5+='<br/>0';
                                str6+='<br/>0';
                                str7+='<br/>0';
                                str8+='<br/>0';
                                str9+='<br/>0';
                                str10+='<br/>0';
                                str11+='<br/>0';
                                str12+='<br/>0';
                                str13+='<br/>0';
                                str14+='<br/>0';
                                str15+='<br/>0';
                                str16+='<br/>0';
                                str17+='<br/>0'; 
                                str18+='<br/>'+sums.t06.toLocaleString();
                                str19+='<br/>'+sums.t02.toLocaleString();
                                str20+='<br/>'+sums.t08.toLocaleString(); 
                                str21+='<br/>'+sum.toLocaleString();
                                str22+='<br/>'+(sum).toLocaleString();
                                str23+='<br/>';
                            }
                            accname.forEach((row8) => {
                                const sa2 = sa.filter(item => item.level3 === row8 &&item.level4 === row7 &&item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                const parseValue = (value) => {
                                const number = parseFloat(value.replace(/,/g, ''));
                                    return isNaN(number) ? 0 : number;
                                };
                                const sums = sa2.reduce((acc, item) => {
                                    return {
                                        t06: acc.t06 + parseValue(item.t06),
                                        t02: acc.t02 + parseValue(item.t02),
                                        t08: acc.t08 + parseValue(item.t08),
                                    };
                                }, {
                                    t06: 0, t02: 0, t08: 0
                                });
                                if(sa2.length>0 && row8!=null){
                                    var sum=sums.t06+sums.t08+sums.t02;
                                    str1+='<br/>'+'&nbsp;'.repeat(32)+row8;
                                    str2+='<br/>0';
                                    str3+='<br/>0';
                                    str4+='<br/>0';
                                    str5+='<br/>0';
                                    str6+='<br/>0';
                                    str7+='<br/>0';
                                    str8+='<br/>0';
                                    str9+='<br/>0';
                                    str10+='<br/>0';
                                    str11+='<br/>0';
                                    str12+='<br/>0';
                                    str13+='<br/>0';
                                    str14+='<br/>0';
                                    str15+='<br/>0';
                                    str16+='<br/>0';
                                    str17+='<br/>0'; 
                                    str18+='<br/>'+sums.t06.toLocaleString();
                                    str19+='<br/>'+sums.t02.toLocaleString();
                                    str20+='<br/>'+sums.t08.toLocaleString(); 
                                    str21+='<br/>'+sum.toLocaleString();
                                    str22+='<br/>'+(sum).toLocaleString();
                                    str23+='<br/>';
                                }
                                if(sa2.length>0 && row8==null){
                                    sa2.forEach((row8_null) => {
                                        const parseValue = (value) => {
                                        const number = parseFloat(value.replace(/,/g, ''));
                                        return isNaN(number) ? 0 : number;
                                    };
                                    
                                    if(row8_null.KKU_Item_Name!=""){
                                        var sum=parseInt(row8_null.t06)+parseInt(row8_null.t08)+parseInt(row8_null.t02);
                                        str1+='<br/>'+'&nbsp;'.repeat(32)+row8_null.KKU_Item_Name2;
                                        str2+='<br/>0';
                                        str3+='<br/>0';
                                        str4+='<br/>0';
                                        str5+='<br/>0';
                                        str6+='<br/>0';
                                        str7+='<br/>0';
                                        str8+='<br/>0';
                                        str9+='<br/>0';
                                        str10+='<br/>0';
                                        str11+='<br/>0';
                                        str12+='<br/>0';
                                        str13+='<br/>0';
                                        str14+='<br/>0';
                                        str15+='<br/>0';
                                        str16+='<br/>0';
                                        str17+='<br/>0'; 
                                        str18+='<br/>'+parseInt(row8_null.t06).toLocaleString();
                                        str19+='<br/>'+parseInt(row8_null.t02).toLocaleString();
                                        str20+='<br/>'+parseInt(row8_null.t08).toLocaleString(); 
                                        str21+='<br/>'+sum.toLocaleString();
                                        str22+='<br/>'+(sum).toLocaleString();
                                        str23+='<br/>';
                                        }
                                    });
                                }
                                lv2.forEach((row9) => {
                                    const l2 = sa2.filter(item => item.level2 === row9 &&item.level3 === row8 &&item.level4 === row7 &&item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                    const parseValue = (value) => {
                                    const number = parseFloat(value.replace(/,/g, ''));
                                        return isNaN(number) ? 0 : number;
                                    };
                                    const sums = l2.reduce((acc, item) => {
                                        return {
                                            t06: acc.t06 + parseValue(item.t06),
                                            t02: acc.t02 + parseValue(item.t02),
                                            t08: acc.t08 + parseValue(item.t08),
                                        };
                                    }, {
                                        t06: 0, t02: 0, t08: 0
                                    });
                                    if(l2.length>0 && row9!=null){
                                        var sum=sums.t06+sums.t08+sums.t02;
                                        str1+='<br/>'+'&nbsp;'.repeat(40)+row9;
                                        str2+='<br/>0';
                                        str3+='<br/>0';
                                        str4+='<br/>0';
                                        str5+='<br/>0';
                                        str6+='<br/>0';
                                        str7+='<br/>0';
                                        str8+='<br/>0';
                                        str9+='<br/>0';
                                        str10+='<br/>0';
                                        str11+='<br/>0';
                                        str12+='<br/>0';
                                        str13+='<br/>0';
                                        str14+='<br/>0';
                                        str15+='<br/>0';
                                        str16+='<br/>0';
                                        str17+='<br/>0'; 
                                        str18+='<br/>'+sums.t06.toLocaleString();
                                        str19+='<br/>'+sums.t02.toLocaleString();
                                        str20+='<br/>'+sums.t08.toLocaleString(); 
                                        str21+='<br/>'+sum.toLocaleString();
                                        str22+='<br/>'+(sum).toLocaleString();
                                        str23+='<br/>';
                                    }
                                    if(l2.length>0 && row9==null&& row8!=null){
                                        l2.forEach((row9_null) => {
                                            const parseValue = (value) => {
                                            const number = parseFloat(value.replace(/,/g, ''));
                                            return isNaN(number) ? 0 : number;
                                        };
                                        
                                        if(row9_null.KKU_Item_Name!=""){
                                            var sum=parseInt(row9_null.t06)+parseInt(row9_null.t08)+parseInt(row9_null.t02);
                                            str1+='<br/>'+'&nbsp;'.repeat(40)+row9_null.KKU_Item_Name2;
                                            str2+='<br/>0';
                                            str3+='<br/>0';
                                            str4+='<br/>0';
                                            str5+='<br/>0';
                                            str6+='<br/>0';
                                            str7+='<br/>0';
                                            str8+='<br/>0';
                                            str9+='<br/>0';
                                            str10+='<br/>0';
                                            str11+='<br/>0';
                                            str12+='<br/>0';
                                            str13+='<br/>0';
                                            str14+='<br/>0';
                                            str15+='<br/>0';
                                            str16+='<br/>0';
                                            str17+='<br/>0'; 
                                            str18+='<br/>'+parseInt(row9_null.t06).toLocaleString();
                                            str19+='<br/>'+parseInt(row9_null.t02).toLocaleString();
                                            str20+='<br/>'+parseInt(row9_null.t08).toLocaleString(); 
                                            str21+='<br/>'+sum.toLocaleString();
                                            str22+='<br/>'+(sum).toLocaleString();
                                            str23+='<br/>';
                                            }
                                        });
                                    }
                                    lv1.forEach((row10) => {
                                        const l1 = l2.filter(item => item.level1 === row10 &&item.level2 === row9 &&item.level3 === row8 &&item.level4 === row7 &&item.level5 === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                        const parseValue = (value) => {
                                        const number = parseFloat(value.replace(/,/g, ''));
                                            return isNaN(number) ? 0 : number;
                                        };
                                        const sums = l1.reduce((acc, item) => {
                                            return {
                                                t06: acc.t06 + parseValue(item.t06),
                                                t02: acc.t02 + parseValue(item.t02),
                                                t08: acc.t08 + parseValue(item.t08),
                                            };
                                        }, {
                                            t06: 0, t02: 0, t08: 0
                                        });
                                        if(l1.length>0 && row10!=null){
                                            var sum=sums.t06+sums.t08+sums.t02;
                                            str1+='<br/>'+'&nbsp;'.repeat(48)+row10;
                                            str2+='<br/>0';
                                            str3+='<br/>0';
                                            str4+='<br/>0';
                                            str5+='<br/>0';
                                            str6+='<br/>0';
                                            str7+='<br/>0';
                                            str8+='<br/>0';
                                            str9+='<br/>0';
                                            str10+='<br/>0';
                                            str11+='<br/>0';
                                            str12+='<br/>0';
                                            str13+='<br/>0';
                                            str14+='<br/>0';
                                            str15+='<br/>0';
                                            str16+='<br/>0';
                                            str17+='<br/>0'; 
                                            str18+='<br/>'+sums.t06.toLocaleString();
                                            str19+='<br/>'+sums.t02.toLocaleString();
                                            str20+='<br/>'+sums.t08.toLocaleString(); 
                                            str21+='<br/>'+sum.toLocaleString();
                                            str22+='<br/>'+(sum).toLocaleString();
                                            str23+='<br/>';
                                            l1.forEach((row10_item) => {
                                                const parseValue = (value) => {
                                                    const number = parseFloat(value.replace(/,/g, ''));
                                                    return isNaN(number) ? 0 : number;
                                                };
                                                
                                                if(row10_item.KKU_Item_Name!=""){
                                                    var sum=parseInt(row10_item.t06)+parseInt(row10_item.t08)+parseInt(row10_item.t02);
                                                    str1+='<br/>'+'&nbsp;'.repeat(48)+row10_item.KKU_Item_Name2;
                                                    str2+='<br/>0';
                                                    str3+='<br/>0';
                                                    str4+='<br/>0';
                                                    str5+='<br/>0';
                                                    str6+='<br/>0';
                                                    str7+='<br/>0';
                                                    str8+='<br/>0';
                                                    str9+='<br/>0';
                                                    str10+='<br/>0';
                                                    str11+='<br/>0';
                                                    str12+='<br/>0';
                                                    str13+='<br/>0';
                                                    str14+='<br/>0';
                                                    str15+='<br/>0';
                                                    str16+='<br/>0';
                                                    str17+='<br/>0'; 
                                                    str18+='<br/>'+parseInt(row10_item.t06).toLocaleString();
                                                    str19+='<br/>'+parseInt(row10_item.t02).toLocaleString();
                                                    str20+='<br/>'+parseInt(row10_item.t08).toLocaleString(); 
                                                    str21+='<br/>'+sum.toLocaleString();
                                                    str22+='<br/>'+(sum).toLocaleString();
                                                    str23+='<br/>';
                                                }
                                            });
                                        }
                                        if(l1.length>0 &&row9!=null&& row10==null){
                                            l1.forEach((row10_null) => {
                                                const parseValue = (value) => {
                                                const number = parseFloat(value.replace(/,/g, ''));
                                                return isNaN(number) ? 0 : number;
                                            };
                                            
                                                if(row10_null.KKU_Item_Name!=""){
                                                    var sum=parseInt(row10_null.t06)+parseInt(row10_null.t08)+parseInt(row10_null.t02);
                                                    str1+='<br/>'+'&nbsp;'.repeat(48)+row10_null.KKU_Item_Name2;
                                                    str2+='<br/>0';
                                                    str3+='<br/>0';
                                                    str4+='<br/>0';
                                                    str5+='<br/>0';
                                                    str6+='<br/>0';
                                                    str7+='<br/>0';
                                                    str8+='<br/>0';
                                                    str9+='<br/>0';
                                                    str10+='<br/>0';
                                                    str11+='<br/>0';
                                                    str12+='<br/>0';
                                                    str13+='<br/>0';
                                                    str14+='<br/>0';
                                                    str15+='<br/>0';
                                                    str16+='<br/>0';
                                                    str17+='<br/>0'; 
                                                    str18+='<br/>'+parseInt(row10_null.t06).toLocaleString();
                                                    str19+='<br/>'+parseInt(row10_null.t02).toLocaleString();
                                                    str20+='<br/>'+parseInt(row10_null.t08).toLocaleString(); 
                                                    str21+='<br/>'+sum.toLocaleString();
                                                    str22+='<br/>'+(sum).toLocaleString();
                                                    str23+='<br/>';
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
                                    
                str1+='</td>';
                str2+='</td>';
                str3+='</td>';
                str4+='</td>';
                str5+='</td>';
                str6+='</td>';
                str7+='</td>';
                str8+='</td>';
                str9+='</td>';
                str10+='</td>';
                str11+='</td>';
                str12+='</td>';
                str13+='</td>';
                str14+='</td>';
                str15+='</td>';
                str16+='</td>';
                str17+='</td>'; 
                str18+='</td>';
                str19+='</td>';
                str20+='</td>'; 
                str21+='</td>';
                str22+='</td>';
                str23+='</td></tr>';
                
                html+=str1+str2+str3+str4+str5+str6+str7+str8+str9+str10+str11+str12+str13+str14+str15
                +str16+str17+str18+str19+str20+str21+str22+str23;
            });
            tableBody.innerHTML =html;
                
        }
        function exportCSV() {
            const table = document.getElementById('reportTable');
            const csvRows = [];
            const filters = getFilterValues();
            const reportHeader = [
                `"รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน"`,
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

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน.csv';
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
            const { jsPDF } = window.jspdf;
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
                    doc.text("รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน", 150, 10,{ align: 'center' });
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
            
            doc.save('รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน.pdf');
        }



        function exportXLS() {
            const table = document.getElementById('reportTable');
            const filterValues = getFilterValues();

            // สร้าง Workbook
            const wb = XLSX.utils.book_new();

            // สร้างส่วนหัวรายงาน
            const headerRows = [
                ["รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน"],
                ["ส่วนงาน/หน่วยงาน:", filterValues.department],
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

            // จัดการ styles สำหรับส่วนหัว
            // ถ้า A1 ไม่มี s ให้สร้างใหม่
            if (!ws['A1'].s) ws['A1'].s = {};
            ws['A1'].s.font = { bold: true, sz: 15 };
            ws['A1'].s.alignment = { horizontal: "center" };
            
            // ถ้า A2 ไม่มี s ให้สร้างใหม่
            if (!ws['A2'].s) ws['A2'].s = {};
            ws['A2'].s.font = { bold: true };

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

            // ปรับความกว้างคอลัมน์ (ถ้าต้องการ)
            // ws['!cols'] = [{ wch: 20 }, { wch: 20 }, ...]; // ตั้งค่าความกว้างคอลัมน์

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
            link.download = 'รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน.xls';
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