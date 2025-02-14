<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th{
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
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
    <script>
        $(document).ready(function() {
            laodData();
            
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-spending-status'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.bgp);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า               

                    const f1 = [...new Set(response.bgp.map(item => item.Alias_Default))];
                    const f2 = [...new Set(response.bgp.map(item => item.pillar_name))];
                    const account = [...new Set(response.bgp.map(item => item.type))];
                    const sub_account = [...new Set(response.bgp.map(item => item.sub_type))];

                    console.log(f1);
                    console.log(f2);
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
                    var str17=''; 
                    var str18='';
                    var str19='';
                    var str20=''; 
                    var str21='';
                    var str22='';
                    var str23='';
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
                        str17+='<td>'; 
                        str18+='<td>';
                        str19+='<td>';
                        str20+='<td>'; 
                        str21+='<td>';
                        str22+='<td>';
                        str23+='<td>';
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
                            str17+='<br/>'; 
                            str18+='<br/>';
                            str19+='<br/>';
                            str20+='<br/>'; 
                            str21+='<br/>';
                            str22+='<br/>';
                            str23+='<br/>';
                            account.forEach((row6) => {
                                const ac = response.bgp.filter(item =>item.type === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                console.log(ac);
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
                                    str19+='<br/>'+sums.t08.toLocaleString();
                                    str20+='<br/>'+sums.t02.toLocaleString(); 
                                    str21+='<br/>'+sum.toLocaleString();
                                    str22+='<br/>'+(sum).toLocaleString();
                                    str23+='<br/>';
                                }   
                                sub_account.forEach((row7) => {
                                    const sa = ac.filter(item =>item.sub_type === row7 &&item.type === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
                                    //console.log(sa);
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
                                        str19+='<br/>'+sums.t08.toLocaleString();
                                        str20+='<br/>'+sums.t02.toLocaleString(); 
                                        str21+='<br/>'+sum.toLocaleString();
                                        str22+='<br/>'+(sum).toLocaleString();
                                        str23+='<br/>';
                                    }
                                    sa.forEach((row8) => {
                                        const parseValue = (value) => {
                                        const number = parseFloat(value.replace(/,/g, ''));
                                        return isNaN(number) ? 0 : number;
                                    };
                                    //console.log(row8);
                                    /* const sums = row8.reduce((acc, item) => {
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
                                        }); */
                                        if(row8.KKU_Item_Name!=""){
                                            var sum=parseInt(row8.t06)+parseInt(row8.t08)+parseInt(row8.t02);
                                            str1+='<br/>'+'&nbsp;'.repeat(32)+row8.KKU_Item_Name;
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
                                            str18+='<br/>'+parseInt(row8.t06).toLocaleString();
                                            str19+='<br/>'+parseInt(row8.t08).toLocaleString();
                                            str20+='<br/>'+parseInt(row8.t02).toLocaleString(); 
                                            str21+='<br/>'+sum.toLocaleString();
                                            str22+='<br/>'+(sum).toLocaleString();
                                            str23+='<br/>';
                                        }
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
                        str23+='</td>';
                        tableBody.innerHTML =str1+str2+str3+str4+str5+str6+str7+str8+str9+str10+str11+str12+str13+str14+str15
                        +str16+str17+str18+str19+str20+str21+str22+str23+'</tr>';
                        //console.log(str1+str2+str3+str4+str5+str6+str7+str8+str9+str10+str11+str12+str13+str14+str15+str16+'</tr>');
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