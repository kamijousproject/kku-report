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
th {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
    vertical-align: top;
}
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
    top: 44px; /* Adjust height based on previous row */
    background: #f4f4f4;
    z-index: 999;
}

thead tr:nth-child(3) th {
    position: sticky;
    top: 89px; /* Adjust height based on previous rows */
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
        <?php include('../component/left-nev.php') ?>
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานผลการอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานผลการอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานผลการอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                                </div>
                                <label for="category">เลือกส่วนงาน:</label>
                                <select name="category" id="category" onchange="fetchData()">
                                    <option value="">-- Loading Categories --</option>
                                </select>
                                <div class="table-responsive" id="content_table">
                                    
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
        $(document).ready(function() {
            laodData();
        });
        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_approval-requests'
                },
                dataType: "json",
                success: function(response) {
                    all_data=response;                                             
                    const fac = [...new Set(all_data.all_fac.map(item => item.pname))];
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
            
        }
        function fetchData() {
            document.getElementById('content_table').innerHTML="";
            let category = document.getElementById("category").value;
            let data_f4;
            let data_c1;
            let data_c2;
            let data_c3;
            let data_c4;
            let data_c5;
            if(category=="all"){
                data_f4=all_data.f4;
                data_c1=all_data.c1;
                data_c2=all_data.c2;
                data_c3=all_data.c3;
                data_c4=all_data.c4;
                data_c5=all_data.c5;
            }
            else{
                data_f4= all_data.f4.filter(item=>item.pname===category);
                data_c1= all_data.c1.filter(item=>item.pname===category);
                data_c2= all_data.c2.filter(item=>item.pname===category);
                data_c3= all_data.c3.filter(item=>item.pname===category);
                data_c4= all_data.c4.filter(item=>item.pname===category);
                data_c5= all_data.c5.filter(item=>item.pname===category);
            }
            var new_header=true;
            if(data_f4.length>0)
            {
                console.log("new");
                const tableContainer = document.createElement('div');
                const table = document.createElement('table');
                table.setAttribute('id', 't1')
                // Create the header row with colspan
                const headerRow = document.createElement('tr');
                const headerCell = document.createElement('th');
                headerCell.setAttribute('colspan', '12');
                headerCell.textContent = 'อัตราใหม่';
                headerRow.appendChild(headerCell);
                table.appendChild(headerRow);

                // Create the column headers
                const columnHeaders = [
                    'ลำดับ', 'ประเภทบุคลากร', 'ประเภทตำแหน่ง', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                    'สถานที่ปฏิบัติงาน', 'อัตราเงินเดือน', 'แหล่งงบประมาณ', 'ประเภทสัญญา', 'ระยะเวลาสัญญา', 'หมายเหตุอื่นๆ'
                ];
                const headerRow2 = document.createElement('tr');
                columnHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow2.appendChild(th);
                });
                table.appendChild(headerRow2);

                // Create the table body
                const tableBody = document.createElement('tbody');

                // Loop through response.f4 and create rows
                data_f4.forEach((row, index) => {
                    const tr = document.createElement('tr');

                    // Define the columns to display
                    const columns = [
                        { value: index+1 },
                        { value: row.Approved_Personnel_Type2 },
                        { value: row.All_PositionTypes },
                        { value: row.Position },
                        { value: row.Position_Qualififcations },
                        { value: row.New_Position_Number },
                        { value: row.Field_of_Study },
                        { value: (parseFloat(row.Salary_rate|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { value: row.Fund_FT },
                        { value: row.Contract_Type},
                        { value: row.Hiring_Start_End_Date },
                        { value: "" }
                    ];

                    // Create and append table cells
                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = col.value;
                        tr.appendChild(td);
                    });

                    tableBody.appendChild(tr);
                });

                // Append the table body to the table
                table.appendChild(tableBody);
                tableContainer.appendChild(table);
                // Append the table to a container in the HTML (e.g., a div with id="table-container")
                document.getElementById('content_table').appendChild(table);
            }
            if(data_c1.length>0)
            {
                const tableContainer = document.createElement('div');
                const table = document.createElement('table');
                if(new_header)
                {
                    // Create the header row with colspan
                    const headerRow1 = document.createElement('tr');
                    const headerCell1 = document.createElement('th');
                    headerCell1.setAttribute('colspan', '9');
                    headerCell1.textContent = 'อัตราเดิม';
                    headerRow1.appendChild(headerCell1);
                    table.appendChild(headerRow1);
                    new_header=false;
                }
                

                // Create the second header row with colspan and left-aligned text
                const headerRow2 = document.createElement('tr');
                const headerCell2 = document.createElement('th');
                headerCell2.setAttribute('colspan', '9');
                headerCell2.setAttribute('style', 'text-align:left');
                headerCell2.textContent = 'ประเภทบุคลากร : ลูกจ้างของมหาวิทยาลัย';
                headerRow2.appendChild(headerCell2);
                table.appendChild(headerRow2);

                // Create the column headers
                const columnHeaders = [
                    'ลำดับ', 'ประเภทตำแหน่ง', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                    'อัตราเงินเดือน', 'แหล่งงบประมาณ', 'สถานะอัตรา', 'หมายเหตุอื่นๆ'
                ];
                const headerRow3 = document.createElement('tr');
                columnHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow3.appendChild(th);
                });
                table.appendChild(headerRow3);

                // Create the table body
                const tableBody = document.createElement('tbody');

                // Loop through data and create rows
                data_c1.forEach((row, index) => {
                    const tr = document.createElement('tr');

                    // Define the columns to display
                    const columns = [
                        { value: index+1 },
                        { value: row.All_Position_Types },
                        { value: row.POSITION },
                        { value: row.POSITION_QUALIFIFCATIONS },
                        { value: row.POSITION_NUMBER },
                        { value: (parseFloat(row.Salary_rate|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { value: row.Fund_FT },
                        { value: row.rate_status },
                        { value: "" }
                    ];

                    // Create and append table cells
                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = col.value;
                        tr.appendChild(td);
                    });

                    tableBody.appendChild(tr);
                });

                // Append the table body to the table
                table.appendChild(tableBody);
                tableContainer.appendChild(table);
                // Append the table to a container in the HTML (e.g., a div with id="table-container")
                document.getElementById('content_table').appendChild(tableContainer);
            }
            if(data_c2.length>0)
            {
                const tableContainer = document.createElement('div');
                const table = document.createElement('table');
                if(new_header)
                {
                    // Create the header row with colspan
                    const headerRow1 = document.createElement('tr');
                    const headerCell1 = document.createElement('th');
                    headerCell1.setAttribute('colspan', '9');
                    headerCell1.textContent = 'อัตราเดิม';
                    headerRow1.appendChild(headerCell1);
                    table.appendChild(headerRow1);
                    new_header=false;
                }
                

                // Create the second header row with colspan and left-aligned text
                const headerRow2 = document.createElement('tr');
                const headerCell2 = document.createElement('th');
                headerCell2.setAttribute('colspan', '9');
                headerCell2.setAttribute('style', 'text-align:left');
                headerCell2.textContent = 'ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : วิชาการระยะสั้น';
                headerRow2.appendChild(headerCell2);
                table.appendChild(headerRow2);

                // Create the column headers
                const columnHeaders = [
                    'ลำดับ', 'ประเภทตำแหน่ง', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                    'อัตราเงินเดือน', 'แหล่งงบประมาณ', 'สถานะอัตรา', 'หมายเหตุอื่นๆ'
                ];
                const headerRow3 = document.createElement('tr');
                columnHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow3.appendChild(th);
                });
                table.appendChild(headerRow3);

                // Create the table body
                const tableBody = document.createElement('tbody');

                // Loop through data and create rows
                data_c2.forEach((row, index) => {
                    const tr = document.createElement('tr');

                    // Define the columns to display
                    const columns = [
                        { value: index+1 },
                        { value: row.All_Position_Types },
                        { value: row.POSITION },
                        { value: row.POSITION_QUALIFIFCATIONS },
                        { value: row.POSITION_NUMBER },
                        { value: (parseFloat(row.Salary_rate|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { value: row.Fund_FT },
                        { value: row.rate_status },
                        { value: "" }
                    ];

                    // Create and append table cells
                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = col.value;
                        tr.appendChild(td);
                    });

                    tableBody.appendChild(tr);
                });

                // Append the table body to the table
                table.appendChild(tableBody);
                tableContainer.appendChild(table);
                // Append the table to a container in the HTML (e.g., a div with id="table-container")
                document.getElementById('content_table').appendChild(tableContainer);
            }
            if(data_c3.length>0)
            {
                const tableContainer = document.createElement('div');
                const table = document.createElement('table');
                table.setAttribute('border', '1');
                if(new_header)
                {
                    // Create the header row with colspan
                    const headerRow1 = document.createElement('tr');
                    const headerCell1 = document.createElement('th');
                    headerCell1.setAttribute('colspan', '9');
                    headerCell1.textContent = 'อัตราเดิม';
                    headerRow1.appendChild(headerCell1);
                    table.appendChild(headerRow1);
                    new_header=false;
                }
                

                // Create the second header row with colspan and left-aligned text
                const headerRow2 = document.createElement('tr');
                const headerCell2 = document.createElement('th');
                headerCell2.setAttribute('colspan', '9');
                headerCell2.setAttribute('style', 'text-align:left');
                headerCell2.textContent = 'ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ผู้เกษียณอายุราชการ';
                headerRow2.appendChild(headerCell2);
                table.appendChild(headerRow2);

                // Create the column headers
                const columnHeaders = [
                    'ลำดับ', 'ชื่อ - นามสกุล', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                    'อัตราเงินเดือน', 'แหล่งงบประมาณ','ระยะเวลาการจ้าง', 'สถานะอัตรา'
                ];
                const headerRow3 = document.createElement('tr');
                columnHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow3.appendChild(th);
                });
                table.appendChild(headerRow3);

                // Create the table body
                const tableBody = document.createElement('tbody');

                // Loop through data and create rows
                data_c3.forEach((row, index) => {
                    const tr = document.createElement('tr');

                    // Define the columns to display
                    const columns = [
                        { value: index+1 },
                        { value: row.WORKERS_NAME_SURNAME },
                        { value: row.POSITION },
                        { value: row.POSITION_QUALIFIFCATIONS },
                        { value: row.POSITION_NUMBER },
                        { value: (parseFloat(row.Salary_rate|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { value: row.Fund_FT },
                        { value: row.HIRING_START_END_DATE },
                        { value: row.rate_status }
                    ];

                    // Create and append table cells
                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = col.value;
                        tr.appendChild(td);
                    });

                    tableBody.appendChild(tr);
                });

                // Append the table body to the table
                table.appendChild(tableBody);
                tableContainer.appendChild(table);
                // Append the table to a container in the HTML (e.g., a div with id="table-container")
                document.getElementById('content_table').appendChild(tableContainer);
            }
            if(data_c4.length>0)
            {
                const tableContainer = document.createElement('div');
                const table = document.createElement('table');
                table.setAttribute('border', '1');
                if(new_header)
                {
                    // Create the header row with colspan
                    const headerRow1 = document.createElement('tr');
                    const headerCell1 = document.createElement('th');
                    headerCell1.setAttribute('colspan', '9');
                    headerCell1.textContent = 'อัตราเดิม';
                    headerRow1.appendChild(headerCell1);
                    table.appendChild(headerRow1);
                    new_header=false;
                }
                

                // Create the second header row with colspan and left-aligned text
                const headerRow2 = document.createElement('tr');
                const headerCell2 = document.createElement('th');
                headerCell2.setAttribute('colspan', '9');
                headerCell2.setAttribute('style', 'text-align:left');
                headerCell2.textContent = 'ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ชาวต่างประเทศ';
                headerRow2.appendChild(headerCell2);
                table.appendChild(headerRow2);

                // Create the column headers
                const columnHeaders = [
                    'ลำดับ', 'ชื่อ - นามสกุล', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                    'อัตราเงินเดือน', 'แหล่งงบประมาณ','ระยะเวลาการจ้าง', 'สถานะอัตรา'
                ];
                const headerRow3 = document.createElement('tr');
                columnHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow3.appendChild(th);
                });
                table.appendChild(headerRow3);

                // Create the table body
                const tableBody = document.createElement('tbody');

                // Loop through data and create rows
                data_c4.forEach((row, index) => {
                    const tr = document.createElement('tr');

                    // Define the columns to display
                    const columns = [
                        { value: index+1 },
                        { value: row.WORKERS_NAME_SURNAME },
                        { value: row.POSITION },
                        { value: row.POSITION_QUALIFIFCATIONS },
                        { value: row.POSITION_NUMBER },
                        { value: (parseFloat(row.Salary_rate|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { value: row.Fund_FT },
                        { value: row.HIRING_START_END_DATE },
                        { value: row.rate_status }
                    ];

                    // Create and append table cells
                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = col.value;
                        tr.appendChild(td);
                    });

                    tableBody.appendChild(tr);
                });

                // Append the table body to the table
                table.appendChild(tableBody);
                tableContainer.appendChild(table);
                // Append the table to a container in the HTML (e.g., a div with id="table-container")
                document.getElementById('content_table').appendChild(tableContainer);
            }
            if(data_c5.length>0)
            {
                const tableContainer = document.createElement('div');
                const table = document.createElement('table');
                table.setAttribute('border', '1');
                if(new_header)
                {
                    // Create the header row with colspan
                    const headerRow1 = document.createElement('tr');
                    const headerCell1 = document.createElement('th');
                    headerCell1.setAttribute('colspan', '9');
                    headerCell1.textContent = 'อัตราเดิม';
                    headerRow1.appendChild(headerCell1);
                    table.appendChild(headerRow1);
                    new_header=false;
                }
                

                // Create the second header row with colspan and left-aligned text
                const headerRow2 = document.createElement('tr');
                const headerCell2 = document.createElement('th');
                headerCell2.setAttribute('colspan', '9');
                headerCell2.setAttribute('style', 'text-align:left');
                headerCell2.textContent = 'ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ผู้ปฏิบัติงานในมหาวิทยาลัย';
                headerRow2.appendChild(headerCell2);
                table.appendChild(headerRow2);

                // Create the column headers
                const columnHeaders = [
                    'ลำดับ', 'ชื่อ - นามสกุล', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                    'อัตราเงินเดือน', 'แหล่งงบประมาณ','ระยะเวลาการจ้าง', 'สถานะอัตรา'
                ];
                const headerRow3 = document.createElement('tr');
                columnHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow3.appendChild(th);
                });
                table.appendChild(headerRow3);

                // Create the table body
                const tableBody = document.createElement('tbody');

                // Loop through data and create rows
                data_c5.forEach((row, index) => {
                    const tr = document.createElement('tr');

                    // Define the columns to display
                    const columns = [
                        { value: index+1 },
                        { value: row.WORKERS_NAME_SURNAME },
                        { value: row.POSITION },
                        { value: row.POSITION_QUALIFIFCATIONS },
                        { value: row.POSITION_NUMBER },
                        { value: (parseFloat(row.Salary_rate|| 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,') },
                        { value: row.Fund_FT },
                        { value: row.HIRING_START_END_DATE },
                        { value: row.rate_status }
                        
                    ];

                    // Create and append table cells
                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.textContent = col.value;
                        tr.appendChild(td);
                    });

                    tableBody.appendChild(tr);
                });

                // Append the table body to the table
                table.appendChild(tableBody);
                tableContainer.appendChild(table);
                // Append the table to a container in the HTML (e.g., a div with id="table-container")
                document.getElementById('content_table').appendChild(tableContainer);
            }
                         
        }
        function exportCSV() {
    const tables = document.querySelectorAll('#content_table table');
    let csvContent = "\uFEFF"; // เพิ่ม BOM สำหรับภาษาไทย

    tables.forEach((table, tableIndex) => {
        //const title = table.getAttribute('data-title') || `Table ${tableIndex + 1}`;
        //csvContent += `\n\n${title}\n`; // ตั้งชื่อให้แต่ละตาราง
        const rows = table.querySelectorAll("tr");

        rows.forEach(row => {
            const cells = row.querySelectorAll("th, td");
            const rowData = Array.from(cells).map(cell => `"${cell.innerText.replace(/"/g, '""')}"`);
            csvContent += rowData.join(",") + "\n";
        });
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'รายงานผลการอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');
    doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
    doc.addFont("THSarabun.ttf", "THSarabun", "normal");
    doc.setFont("THSarabun");

    const tables = document.querySelectorAll('#content_table table');
    let yOffset = 20;

    tables.forEach((table, tableIndex) => {
        //const title = table.getAttribute('data-title') || `Table ${tableIndex + 1}`;
        if (yOffset > 250) {  // ถ้าพื้นที่ไม่พอให้ขึ้นหน้าใหม่
            doc.addPage();
            yOffset = 10;
        }

        doc.setFontSize(10);
        //doc.text(title, 10, yOffset);
        //yOffset += 5;

        doc.autoTable({
            html: table,
            startY: yOffset,
            theme: 'grid',
            styles: {
                    font: "THSarabun",
                    fontSize: 7,
                    cellPadding: { top: 1, right: 1, bottom: 1, left: 1 },
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
                    valign: 'middle',
                    minCellHeight: 5
                },
            columnStyles: { 0: { cellWidth: 10 }, 1: { cellWidth: 30 } },
            didParseCell: function(data) {
                    // Center align all header cells
                    if (data.section === 'head') {
                        data.cell.styles.halign = 'center';
                        data.cell.styles.valign = 'middle';
                        data.cell.styles.lineBreak = true;
                        
                        // Properly handle <br> tags in header cells
                        if (typeof data.cell.raw === 'string' && data.cell.raw.includes('<br>')) {
                            data.cell.text = data.cell.raw.replace(/<br>/g, '\n');
                        }
                    }
                    
                    // Handle body cells
                    if (data.section === 'body') {
                        // First column (ID) - center align
                        if (data.column.index === 0) {
                            data.cell.styles.halign = 'center';
                        }
                        // Text columns - left align
                        else if (data.column.index >= 1 && data.column.index <= 4) {
                            data.cell.styles.halign = 'left';
                        }
                        // Number columns - center align
                        else {
                            data.cell.styles.halign = 'center';
                        }
                    }
                    
                    // Footer row
                    if (data.section === 'foot') {
                        data.cell.styles.fontStyle = 'bold';
                        data.cell.styles.textColor = 'DimGray';
                        data.cell.styles.fillColor = 'white';
                        // First column left align
                        if (data.column.index <= 4) {
                            data.cell.styles.halign = 'center';
                        } else {
                            data.cell.styles.halign = 'center';
                        }
                    }
                },
                // Handle text wrapping for cells with <br>
                willDrawCell: function(data) {
                    if (data.section === 'head' || data.section === 'body') {
                        // Replace <br> with newlines for proper rendering
                        if (typeof data.cell.text === 'string') {
                            data.cell.text = data.cell.text.replace(/<br\s*\/?>/gi, '\n');
                        } else if (Array.isArray(data.cell.text)) {
                            data.cell.text = data.cell.text.map(line => 
                                typeof line === 'string' ? line.replace(/<br\s*\/?>/gi, '\n') : line
                            );
                        }
                    }
                },
            didDrawPage: function () {
                doc.setFontSize(14);
                doc.text('รายงานผลการอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน', 20, 10);
            }
        });

        yOffset = doc.lastAutoTable.finalY;
    });

    doc.save('รายงานผลการอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน.pdf');
}
function exportXLS() {
    const tables = document.querySelectorAll('#content_table table'); // ดึงทุก Table
    const wb = XLSX.utils.book_new();
    let wsData = [];
    let merges = [];
    let rowOffset = 0; // ตำแหน่งแถวปัจจุบันใน Excel

    tables.forEach((table) => {
        const skipMap = {};

        // ✅ Loop ผ่านทุกแถวของ Table
        for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
            const tr = table.rows[rowIndex];
            const rowData = [];
            let colIndex = 0;

            for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                while (skipMap[`${rowOffset},${colIndex}`]) {
                    rowData.push(""); 
                    colIndex++;
                }

                const cell = tr.cells[cellIndex];
                let cellText = cell.innerText.trim();
                const isHeader = cell.tagName.toLowerCase() === "th"; // ✅ ตรวจสอบว่าเป็น Header หรือไม่

                rowData[colIndex] = {
                    v: cellText,
                    s: {
                        alignment: {
                            vertical: "center",
                            horizontal: isHeader ? "center" : "left" // ✅ Header ชิดกลาง, Body ชิดซ้าย
                        },
                        font: isHeader ? { bold: true } : {} // ✅ Header ตัวหนา
                    }
                };

                const rowspan = cell.rowSpan || 1;
                const colspan = cell.colSpan || 1;

                if (rowspan > 1 || colspan > 1) {
                    merges.push({
                        s: { r: rowOffset, c: colIndex },
                        e: { r: rowOffset + rowspan - 1, c: colIndex + colspan - 1 }
                    });

                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            if (!(r === 0 && c === 0)) {
                                skipMap[`${rowOffset + r},${colIndex + c}`] = true;
                            }
                        }
                    }
                }
                colIndex++;
            }
            wsData.push(rowData);
            rowOffset++;
        }

        // ✅ เพิ่มแถวว่างเพื่อเว้นระยะห่างระหว่างตาราง
        wsData.push([]);
        rowOffset++;
    });

    // ✅ สร้าง Worksheet และนำ merges ไปใช้
    const ws = XLSX.utils.aoa_to_sheet(wsData);
    ws['!merges'] = merges;
    ws['!cols'] = new Array(wsData[0].length).fill({ width: 15 });

    // ✅ เพิ่ม Sheet เดียว และ Export
    XLSX.utils.book_append_sheet(wb, ws, "รายงาน");
    XLSX.writeFile(wb, 'รายงานผลการอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน.xlsx');
}

    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>