<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
#reportTable th:nth-child(1),
#reportTable td:nth-child(1) {
    width: 300px;
    /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
}

#reportTable th{
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
    border: 1px solid #ddd;
    /* เพิ่มขอบให้เซลล์ */
}

/* ทำให้ข้อมูลในตารางเลื่อนได้ */
.table-responsive {
    max-height: 60vh;
    /* กำหนดความสูงของตาราง */
    overflow-y: auto;
    /* ทำให้สามารถเลื่อนข้อมูลในตารางได้ */
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
                                <button onclick="exportXLSX()" class="btn btn-success m-t-15">Export XLSX</button>
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
            laodData();
            
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-structure-comparison2'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.bgp);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า               

                    const f1 = [...new Set(response.bgp.map(item => item.f1))];
                    const f2 = [...new Set(response.bgp.map(item => item.f2))];
                    const plan_name = [...new Set(response.bgp.map(item => item.plan_name))];
                    const sub_plan_name = [...new Set(response.bgp.map(item => item.sub_plan_name))];
                    const project_name = [...new Set(response.bgp.map(item => item.project_name))];
                    const account = [...new Set(response.bgp.map(item => item.TYPE))];
                    const sub_account = [...new Set(response.bgp.map(item => item.sub_type))];

                    console.log(f1);
                    console.log(f2);
                    console.log(plan_name);
                    console.log(sub_plan_name);
                    console.log(project_name);
                    console.log(account);
                    console.log(sub_account); 
                    
                    var str1=''; 
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
                    f1.forEach((row1) => {  
                        str1+='<tr><td>'+row1;
                        str2+='<td>';
                        str3+='<td>';
                        str4+='<td>';
                        str5+='<td>';
                        str6+='<td>';
                        str7+='<td>';
                        str8+='<td>';
                        str9+='<td>';
                        str10+='<td>';
                        str11+='<td>';
                        str12+='<td>';
                        str13+='<td>';
                        str14+='<td>';
                        str15+='<td>';
                        str16+='<td>';
                        
                        f2.forEach((row2) => {
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
                            plan_name.forEach((row3) => {
                                var p = response.bgp.filter(item => item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                if(p.length>0){
                                    str1+='<br/>'+'&nbsp;'.repeat(16)+row3;
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
                                }
                                sub_plan_name.forEach((row4) => {
                                    var sp = p.filter(item =>item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
                                    if(sp.length>0){
                                        str1+='<br/>'+'&nbsp;'.repeat(24)+row4;
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
                                            console.log(sums);
                                        if(pro.length>0){
                                            var s4=Math.round((((sums.c6+sums.o6)*100)/(sums.a6))* 100) / 100 || 0;
                                            var s9=Math.round((((sums.c2+sums.o2)*100)/(sums.a2))* 100) / 100 || 0;
                                            var s6=Math.round(((sums.e6*100)/(sums.a6))* 100) / 100 || 0;
                                            var s10=Math.round(((sums.e2*100)/(sums.a2))* 100) / 100 || 0;
                                            var s3=(sums.c6+sums.o6);
                                            var s8=(sums.c2+sums.o2);
                                            str1+='<br/>'+'&nbsp;'.repeat(32)+row5;
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
                                        account.forEach((row6) => {
                                            const ac = pro.filter(item =>item.TYPE === row6 &&item.project_name === row5 &&item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
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
                                            if(ac.length>0){
                                                var s4=Math.round((((sums.c6+sums.o6)*100)/(sums.a6))* 100) / 100 || 0;
                                                var s9=Math.round((((sums.c2+sums.o2)*100)/(sums.a2))* 100) / 100 || 0;
                                                var s6=Math.round(((sums.e6*100)/(sums.a6))* 100) / 100 || 0;
                                                var s10=Math.round(((sums.e2*100)/(sums.a2))* 100) / 100 || 0;
                                                var s3=(sums.c6+sums.o6);
                                                var s8=(sums.c2+sums.o2);
                                                str1+='<br/>'+'&nbsp;'.repeat(40)+row6;
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
                                            sub_account.forEach((row7) => {
                                                const sa = pro.filter(item =>item.sub_type === row7 &&item.TYPE === row6 &&item.project_name === row5 &&item.sub_plan_name === row4 && item.plan_name === row3 && item.f2 === row2 && item.f1 === row1);
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
                                                if(sa.length>0){
                                                    var s4=Math.round((((sums.c6+sums.o6)*100)/(sums.a6))* 100) / 100 || 0;
                                                    var s9=Math.round((((sums.c2+sums.o2)*100)/(sums.a2))* 100) / 100 || 0;
                                                    var s6=Math.round(((sums.e6*100)/(sums.a6))* 100) / 100 || 0;
                                                    var s10=Math.round(((sums.e2*100)/(sums.a2))* 100) / 100 || 0;
                                                    var s3=(sums.c6+sums.o6);
                                                    var s8=(sums.c2+sums.o2);
                                                    str1+='<br/>'+'&nbsp;'.repeat(48)+row7;
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
                                                
                                            });
                                        });   
                                    });
                                });
                            });        
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
                        tableBody.innerHTML =str1+str2+str3+str4+str5+str6+str7+str8+str9+str10+str11+str12+str13+str14+str15+str16+'</tr>';
                        console.log(str1+str2+str3+str4+str5+str6+str7+str8+str9+str10+str11+str12+str13+str14+str15+str16+'</tr>');
                    });
                     
                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
        }
    function exportCSV() {
        const rows = [];
        const table = document.getElementById('reportTable');

        for (let row of table.rows) {
            const cells = Array.from(row.cells).map(cell => {
                let text = cell.innerText.trim();

                // เช็คว่าเป็นตัวเลข float (ไม่มี , ในหน้าเว็บ)
                if (!isNaN(text) && text !== "") {
                    text = `"${parseFloat(text).toLocaleString("en-US", { minimumFractionDigits: 2 })}"`;
                }

                return text;
            });

            rows.push(cells.join(",")); // ใช้ , เป็นตัวคั่น CSV
        }

        const csvContent = "\uFEFF" + rows.join("\n"); // ป้องกัน Encoding เพี้ยน
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', 'รายงาน.csv');
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

    function exportXLSX() {
        const table = document.getElementById('reportTable');
        const rows = [];
        const merges = [];
        const rowSpans = {}; // เก็บค่า rowspan
        const colSpans = {}; // เก็บค่า colspan

        for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
            const row = table.rows[rowIndex];
            const cells = [];
            let colIndex = 0;

            for (let cellIndex = 0; cellIndex < row.cells.length; cellIndex++) {
                let cell = row.cells[cellIndex];
                let cellText = cell.innerText.trim();

                // ตรวจสอบว่ามี rowspan หรือ colspan หรือไม่
                let rowspan = cell.rowSpan || 1;
                let colspan = cell.colSpan || 1;

                // หากเป็นเซลล์ที่เคยถูก Merge ข้ามมา ให้ข้ามไป
                while (rowSpans[`${rowIndex},${colIndex}`]) {
                    cells.push(""); // ใส่ค่าว่างแทน Merge
                    colIndex++;
                }

                // เพิ่มค่าลงไปในแถว
                cells.push(cellText);

                // ถ้ามี colspan หรือ rowspan
                if (rowspan > 1 || colspan > 1) {
                    merges.push({
                        s: {
                            r: rowIndex,
                            c: colIndex
                        }, // จุดเริ่มต้นของ Merge
                        e: {
                            r: rowIndex + rowspan - 1,
                            c: colIndex + colspan - 1
                        } // จุดสิ้นสุดของ Merge
                    });

                    // บันทึกตำแหน่งเซลล์ที่ถูก Merge เพื่อกันการซ้ำ
                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            if (r !== 0 || c !== 0) {
                                rowSpans[`${rowIndex + r},${colIndex + c}`] = true;
                            }
                        }
                    }
                }

                colIndex++;
            }
            rows.push(cells);
        }

        // สร้างไฟล์ Excel
        const XLSX = window.XLSX;
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);

        // ✅ เพิ่ม Merge Cells
        ws['!merges'] = merges;

        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // ✅ ดาวน์โหลดไฟล์ Excel
        const excelBuffer = XLSX.write(wb, {
            bookType: 'xlsx',
            type: 'array'
        });
        const blob = new Blob([excelBuffer], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'รายงาน.xlsx';
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