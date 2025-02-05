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
                        <h4>รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                                </div>
                                
                                <div class="table-responsive" id="content_table">
                                    <!-- <table>
                                        <tr>
                                            <th colspan="12">อัตราใหม่</th>
                                        </tr>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ประเภทบุคลากร</th>
                                            <th>ประเภทตำแหน่ง</th>
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>คุณวุฒิ</th>
                                            <th>เลขประจำตำแหน่ง</th>
                                            <th>สถานที่ปฏิบัติงาน</th>
                                            <th>อัตราเงินเดือน</th>
                                            <th>แหล่งงบประมาณ</th>
                                            <th>ประเภทสัญญา</th>
                                            <th>ระยะเวลาสัญญา</th>
                                            <th>หมายเหตุอื่นๆ</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>All_PositionTypes</td>
                                            <td>Position</td>
                                            <td>Position Qualifications</td>
                                            <td>Position_Number</td>
                                            <td>Salary rate</td>
                                            <td>Fund(FT)</td>
                                            <td>Rate status</td>
                                            <td>ข้ามก่อนค่ะ</td>
                                        </tr>
                                    </table>                                   
                                    <table>
                                        <tr>
                                            <th colspan="9">อัตราเดิม</th>                                           
                                        </tr>
                                        <tr>
                                        <th colspan="9" style="text-align:left">ประเภทบุคลากร : ลูกจ้างของมหาวิทยาลัย</th>                                          
                                        </tr>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ประเภทตำแหน่ง</th>
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>คุณวุฒิ</th>
                                            <th>เลขประจำตำแหน่ง</th>
                                            <th>อัตราเงินเดือน</th>
                                            <th>แหล่งงบประมาณ</th>
                                            <th>สถานะอัตรา</th>
                                            <th>หมายเหตุอื่นๆ</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>All_PositionTypes</td>
                                            <td>Position</td>
                                            <td>Position Qualifications</td>
                                            <td>Position_Number</td>
                                            <td>Salary rate</td>
                                            <td>Fund(FT)</td>
                                            <td>Rate status</td>
                                            <td>ข้ามก่อนค่ะ</td>
                                        </tr>
                                    </table>
                                    
                                    <div class="section-header">ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : วิชาการระยะสั้น</div>
                                    <table>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ประเภทตำแหน่ง</th>
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>คุณวุฒิ</th>
                                            <th>เลขประจำตำแหน่ง</th>
                                            <th>อัตราเงินเดือน</th>
                                            <th>แหล่งงบประมาณ</th>
                                            <th>สถานะอัตรา</th>
                                            <th>หมายเหตุอื่นๆ</th>
                                        </tr>
                                    </table>

                                    <div class="section-header">ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ผู้เกษียณอายุราชการ</div>
                                    <table>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ชื่อ - นามสกุล</th>
                                            <th>ประเภทตำแหน่ง</th>
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>คุณวุฒิ</th>
                                            <th>เลขประจำตำแหน่ง</th>
                                            <th>อัตราเงินเดือน</th>
                                            <th>แหล่งงบประมาณ</th>
                                            <th>ระยะเวลาการจ้าง</th>
                                            <th>สถานะอัตรา</th>
                                            <th>หมายเหตุอื่นๆ</th>
                                        </tr>
                                    </table>

                                    <div class="section-header">ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ชาวต่างประเทศ</div>
                                    <table>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ชื่อ - นามสกุล</th>
                                            <th>ประเภทตำแหน่ง</th>
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>คุณวุฒิ</th>
                                            <th>เลขประจำตำแหน่ง</th>
                                            <th>อัตราเงินเดือน</th>
                                            <th>แหล่งงบประมาณ</th>
                                            <th>ระยะเวลาการจ้าง</th>
                                            <th>สถานะอัตรา</th>
                                            <th>หมายเหตุอื่นๆ</th>
                                        </tr>
                                    </table>

                                    <div class="section-header">ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ผู้ปฏิบัติงานในมหาวิทยาลัย</div>
                                    <table>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ชื่อ - นามสกุล</th>
                                            <th>ประเภทตำแหน่ง</th>
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>คุณวุฒิ</th>
                                            <th>เลขประจำตำแหน่ง</th>
                                            <th>อัตราเงินเดือน</th>
                                            <th>แหล่งงบประมาณ</th>
                                            <th>ระยะเวลาการจ้าง</th>
                                            <th>สถานะอัตรา</th>
                                            <th>หมายเหตุอื่นๆ</th>
                                        </tr>
                                    </table> -->
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
                    console.log(response.f4);
                    var new_header=true;
                    if(response.f4.length>0)
                    {
                        const tableContainer = document.createElement('div');
                        const table = document.createElement('table');

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
                        response.f4.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            // Define the columns to display
                            const columns = [
                                { value: index+1 },
                                { value: row.Personnel_Type },
                                { value: row.All_PositionTypes },
                                { value: row.Position },
                                { value: "" },
                                { value: row.New_Position_No_of_Uni_Staff_Gov },
                                { value: "" },
                                { value: row.Salary_rate },
                                { value: row.Fund_FT },
                                { value: "" },
                                { value: "" },
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
                    if(response.c1.length>0)
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
                        response.c1.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            // Define the columns to display
                            const columns = [
                                { value: index+1 },
                                { value: row.All_PositionTypes },
                                { value: row.Position },
                                { value: row.Position_Qualifications },
                                { value: row.Position_Number },
                                { value: row.Salary_rate },
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
                    if(response.c2.length>0)
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
                        response.c2.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            // Define the columns to display
                            const columns = [
                                { value: index+1 },
                                { value: row.All_PositionTypes },
                                { value: row.Position },
                                { value: row.Position_Qualifications },
                                { value: row.Position_Number },
                                { value: row.Salary_rate },
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
                    if(response.c3.length>0)
                    {
                        const tableContainer = document.createElement('div');
                        const table = document.createElement('table');
                        table.setAttribute('border', '1');
                        if(new_header)
                        {
                            // Create the header row with colspan
                            const headerRow1 = document.createElement('tr');
                            const headerCell1 = document.createElement('th');
                            headerCell1.setAttribute('colspan', '10');
                            headerCell1.textContent = 'อัตราเดิม';
                            headerRow1.appendChild(headerCell1);
                            table.appendChild(headerRow1);
                            new_header=false;
                        }
                        

                        // Create the second header row with colspan and left-aligned text
                        const headerRow2 = document.createElement('tr');
                        const headerCell2 = document.createElement('th');
                        headerCell2.setAttribute('colspan', '10');
                        headerCell2.setAttribute('style', 'text-align:left');
                        headerCell2.textContent = 'ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ผู้เกษียณอายุราชการ';
                        headerRow2.appendChild(headerCell2);
                        table.appendChild(headerRow2);

                        // Create the column headers
                        const columnHeaders = [
                            'ลำดับ', 'ประเภทตำแหน่ง', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                            'อัตราเงินเดือน', 'แหล่งงบประมาณ','ระยะเวลาการจ้าง', 'สถานะอัตรา', 'หมายเหตุอื่นๆ'
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
                        response.c3.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            // Define the columns to display
                            const columns = [
                                { value: index+1 },
                                { value: row.All_PositionTypes },
                                { value: row.Position },
                                { value: row.Position_Qualifications },
                                { value: row.Position_Number },
                                { value: row.Salary_rate },
                                { value: row.Fund_FT },
                                { value: row.Contract_Period_Short_Term },
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
                    if(response.c4.length>0)
                    {
                        const tableContainer = document.createElement('div');
                        const table = document.createElement('table');
                        table.setAttribute('border', '1');
                        if(new_header)
                        {
                            // Create the header row with colspan
                            const headerRow1 = document.createElement('tr');
                            const headerCell1 = document.createElement('th');
                            headerCell1.setAttribute('colspan', '10');
                            headerCell1.textContent = 'อัตราเดิม';
                            headerRow1.appendChild(headerCell1);
                            table.appendChild(headerRow1);
                            new_header=false;
                        }
                        

                        // Create the second header row with colspan and left-aligned text
                        const headerRow2 = document.createElement('tr');
                        const headerCell2 = document.createElement('th');
                        headerCell2.setAttribute('colspan', '10');
                        headerCell2.setAttribute('style', 'text-align:left');
                        headerCell2.textContent = 'ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ชาวต่างประเทศ';
                        headerRow2.appendChild(headerCell2);
                        table.appendChild(headerRow2);

                        // Create the column headers
                        const columnHeaders = [
                            'ลำดับ', 'ประเภทตำแหน่ง', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                            'อัตราเงินเดือน', 'แหล่งงบประมาณ','ระยะเวลาการจ้าง', 'สถานะอัตรา', 'หมายเหตุอื่นๆ'
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
                        response.c4.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            // Define the columns to display
                            const columns = [
                                { value: index+1 },
                                { value: row.All_PositionTypes },
                                { value: row.Position },
                                { value: row.Position_Qualifications },
                                { value: row.Position_Number },
                                { value: row.Salary_rate },
                                { value: row.Fund_FT },
                                { value: row.Contract_Period_Short_Term },
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
                    if(response.c5.length>0)
                    {
                        const tableContainer = document.createElement('div');
                        const table = document.createElement('table');
                        table.setAttribute('border', '1');
                        if(new_header)
                        {
                            // Create the header row with colspan
                            const headerRow1 = document.createElement('tr');
                            const headerCell1 = document.createElement('th');
                            headerCell1.setAttribute('colspan', '10');
                            headerCell1.textContent = 'อัตราเดิม';
                            headerRow1.appendChild(headerCell1);
                            table.appendChild(headerRow1);
                            new_header=false;
                        }
                        

                        // Create the second header row with colspan and left-aligned text
                        const headerRow2 = document.createElement('tr');
                        const headerCell2 = document.createElement('th');
                        headerCell2.setAttribute('colspan', '10');
                        headerCell2.setAttribute('style', 'text-align:left');
                        headerCell2.textContent = 'ประเภทบุคลากร : พนักงานมหาวิทยาลัย ประเภทการจ้าง : ผู้ปฏิบัติงานในมหาวิทยาลัย';
                        headerRow2.appendChild(headerCell2);
                        table.appendChild(headerRow2);

                        // Create the column headers
                        const columnHeaders = [
                            'ลำดับ', 'ประเภทตำแหน่ง', 'ชื่อตำแหน่ง', 'คุณวุฒิ', 'เลขประจำตำแหน่ง',
                            'อัตราเงินเดือน', 'แหล่งงบประมาณ','ระยะเวลาการจ้าง', 'สถานะอัตรา', 'หมายเหตุอื่นๆ'
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
                        response.c5.forEach((row, index) => {
                            const tr = document.createElement('tr');

                            // Define the columns to display
                            const columns = [
                                { value: index+1 },
                                { value: row.All_PositionTypes },
                                { value: row.Position },
                                { value: row.Position_Qualifications },
                                { value: row.Position_Number },
                                { value: row.Salary_rate },
                                { value: row.Fund_FT },
                                { value: row.Contract_Period_Short_Term },
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
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells.join(","));
            }
            const csvContent = "\uFEFF" + rows.join("\n"); // Add BOM
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.csv');
            link.style.visibility = 'hidden';
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

        function exportXLS() {
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells);
            }
            let xlsContent = "<table>";
            rows.forEach(row => {
                xlsContent += "<tr>" + row.map(cell => `<td>${cell}</td>`).join('') + "</tr>";
            });
            xlsContent += "</table>";

            const blob = new Blob([xlsContent], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.xls');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>