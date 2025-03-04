<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th {
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
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="23" style='text-align: left;'>
                                                    รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</th>
                                            </tr>
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
        $(document).ready(function () {
            $.ajax({
                type: "POST",
                url: "../server/budget_planing_api.php",
                data: {
                    'command': 'kku_bgp_budget-spending-status'
                },
                dataType: "json",
                success: function (response) {
                    all_data = response.bgp;
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
                data = all_data.filter(item => item.pname === category);
            }
            const f1 = [...new Set(data.map(item => item.Alias_Default))];
            const f2 = [...new Set(data.map(item => item.pillar_name))];
            const account = [...new Set(data.map(item => item.type))];
            const sub_account = [...new Set(data.map(item => item.sub_type))];

            console.log(f1);
            console.log(f2);
            console.log(account);
            console.log(sub_account);

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
            var html = '';
            f1.forEach((row1) => {
                console.log("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
                str1 = '<tr><td>' + row1;
                str2 = '<td>';
                str3 = '<td>';
                str4 = '<td>';
                str5 = '<td>';
                str6 = '<td>';
                str7 = '<td>';
                str8 = '<td>';
                str9 = '<td>';
                str10 = '<td>';
                str11 = '<td>';
                str12 = '<td>';
                str13 = '<td>';
                str14 = '<td>';
                str15 = '<td>';
                str16 = '<td>';
                str17 = '<td>';
                str18 = '<td>';
                str19 = '<td>';
                str20 = '<td>';
                str21 = '<td>';
                str22 = '<td>';
                str23 = '<td>';
                f2.forEach((row2) => {
                    const pi = data.filter(item => item.pillar_name === row2 && item.Alias_Default === row1);
                    if (pi.length > 0) {
                        str1 += '<br/>' + '&nbsp;'.repeat(8) + row2;
                        str2 += '<br/>';
                        str3 += '<br/>';
                        str4 += '<br/>';
                        str5 += '<br/>';
                        str6 += '<br/>';
                        str7 += '<br/>';
                        str8 += '<br/>';
                        str9 += '<br/>';
                        str10 += '<br/>';
                        str11 += '<br/>';
                        str12 += '<br/>';
                        str13 += '<br/>';
                        str14 += '<br/>';
                        str15 += '<br/>';
                        str16 += '<br/>';
                        str17 += '<br/>';
                        str18 += '<br/>';
                        str19 += '<br/>';
                        str20 += '<br/>';
                        str21 += '<br/>';
                        str22 += '<br/>';
                        str23 += '<br/>';
                    }
                    account.forEach((row6) => {
                        const ac = pi.filter(item => item.type === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
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
                        if (ac.length > 0) {
                            var sum = sums.t06 + sums.t08 + sums.t02;
                            str1 += '<br/>' + '&nbsp;'.repeat(16) + row6;
                            str2 += '<br/>0';
                            str3 += '<br/>0';
                            str4 += '<br/>0';
                            str5 += '<br/>0';
                            str6 += '<br/>0';
                            str7 += '<br/>0';
                            str8 += '<br/>0';
                            str9 += '<br/>0';
                            str10 += '<br/>0';
                            str11 += '<br/>0';
                            str12 += '<br/>0';
                            str13 += '<br/>0';
                            str14 += '<br/>0';
                            str15 += '<br/>0';
                            str16 += '<br/>0';
                            str17 += '<br/>0';
                            str18 += '<br/>' + sums.t06.toLocaleString();
                            str19 += '<br/>' + sums.t02.toLocaleString();
                            str20 += '<br/>' + sums.t08.toLocaleString();
                            str21 += '<br/>' + sum.toLocaleString();
                            str22 += '<br/>' + (sum).toLocaleString();
                            str23 += '<br/>';
                        }
                        sub_account.forEach((row7) => {
                            const sa = ac.filter(item => item.sub_type === row7 && item.type === row6 && item.pillar_name === row2 && item.Alias_Default === row1);
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
                            if (sa.length > 0) {
                                var sum = sums.t06 + sums.t08 + sums.t02;
                                str1 += '<br/>' + '&nbsp;'.repeat(24) + row7;
                                str2 += '<br/>0';
                                str3 += '<br/>0';
                                str4 += '<br/>0';
                                str5 += '<br/>0';
                                str6 += '<br/>0';
                                str7 += '<br/>0';
                                str8 += '<br/>0';
                                str9 += '<br/>0';
                                str10 += '<br/>0';
                                str11 += '<br/>0';
                                str12 += '<br/>0';
                                str13 += '<br/>0';
                                str14 += '<br/>0';
                                str15 += '<br/>0';
                                str16 += '<br/>0';
                                str17 += '<br/>0';
                                str18 += '<br/>' + sums.t06.toLocaleString();
                                str19 += '<br/>' + sums.t02.toLocaleString();
                                str20 += '<br/>' + sums.t08.toLocaleString();
                                str21 += '<br/>' + sum.toLocaleString();
                                str22 += '<br/>' + (sum).toLocaleString();
                                str23 += '<br/>';
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
                                if (row8.KKU_Item_Name != "") {
                                    var sum = parseInt(row8.t06) + parseInt(row8.t08) + parseInt(row8.t02);
                                    str1 += '<br/>' + '&nbsp;'.repeat(32) + row8.KKU_Item_Name;
                                    str2 += '<br/>0';
                                    str3 += '<br/>0';
                                    str4 += '<br/>0';
                                    str5 += '<br/>0';
                                    str6 += '<br/>0';
                                    str7 += '<br/>0';
                                    str8 += '<br/>0';
                                    str9 += '<br/>0';
                                    str10 += '<br/>0';
                                    str11 += '<br/>0';
                                    str12 += '<br/>0';
                                    str13 += '<br/>0';
                                    str14 += '<br/>0';
                                    str15 += '<br/>0';
                                    str16 += '<br/>0';
                                    str17 += '<br/>0';
                                    str18 += '<br/>' + parseInt(row8.t06).toLocaleString();
                                    str19 += '<br/>' + parseInt(row8.t02).toLocaleString();
                                    str20 += '<br/>' + parseInt(row8.t08).toLocaleString();
                                    str21 += '<br/>' + sum.toLocaleString();
                                    str22 += '<br/>' + (sum).toLocaleString();
                                    str23 += '<br/>';
                                }
                            });

                        });
                    });
                    //});      
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
                str16 += '</td>';
                str17 += '</td>';
                str18 += '</td>';
                str19 += '</td>';
                str20 += '</td>';
                str21 += '</td>';
                str22 += '</td>';
                str23 += '</td></tr>';

                html += str1 + str2 + str3 + str4 + str5 + str6 + str7 + str8 + str9 + str10 + str11 + str12 + str13 + str14 + str15
                    + str16 + str17 + str18 + str19 + str20 + str21 + str22 + str23;
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
            link.download = 'รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            // Add Thai font
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");
            doc.setFontSize(10);
            doc.text('รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน', 14, 10);
            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                theme: 'grid',
                styles: {
                    font: "THSarabun",
                    fontSize: 7,
                    cellPadding: 1,
                    lineWidth: 0.1,
                    lineColor: [0, 0, 0],
                    minCellHeight: 5
                },
                headStyles: {
                    fillColor: [220, 230, 241],
                    textColor: [0, 0, 0],
                    fontSize: 7,
                    fontStyle: 'bold',
                    halign: 'center',
                    valign: 'middle'
                },
                columnStyles: {
                    0: { halign: 'left' },  // คอลัมน์แรกให้ชิดซ้าย
                },
                didParseCell: function (data) {
                    if (data.section === 'body' && data.column.index === 0) {
                        data.cell.styles.halign = 'left'; // จัด text-align left สำหรับคอลัมน์แรก
                    }
                },
                margin: { top: 15, right: 5, bottom: 10, left: 5 },
                tableWidth: 'auto'
            });
            doc.save('รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน.pdf');
        }



        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const { theadRows, theadMerges } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            const allRows = [...theadRows, ...tbodyRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet
            ws['!merges'] = theadMerges;

            // กำหนดให้ Header (thead) อยู่กึ่งกลาง
            theadRows.forEach((row, rowIndex) => {
                row.forEach((_, colIndex) => {
                    const cellAddress = XLSX.utils.encode_cell({ r: rowIndex, c: colIndex });
                    if (!ws[cellAddress]) return;
                    ws[cellAddress].s = {
                        alignment: { horizontal: "center", vertical: "center" }, // จัดให้อยู่กึ่งกลาง
                        font: { bold: true } // ทำให้ header ตัวหนา
                    };
                });
            });

            // ตั้งค่าความกว้างของคอลัมน์ให้พอดีกับเนื้อหา
            ws['!cols'] = new Array(theadRows[0].length).fill({ wch: 15 });

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์เป็น .xls
            const excelBuffer = XLSX.write(wb, { bookType: 'xls', type: 'array' });

            // สร้าง Blob + ดาวน์โหลดไฟล์
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