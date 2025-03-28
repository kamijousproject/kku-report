SET WorkingDIR="C:\Oracle\EPM Automate\bin"
 
REM Log in to EPM Cloud
 
CALL %WorkingDIR%\epmautomate login epm-service-admin@kku.ac.th kku_EPM_12345! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning
 
REM Report Bursting
 
CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "/Library/EPM Report Workforce/Bursting_EPM_Report_workforce_current_positions_allocation"
 
REM Export the New reports

CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Workforce/Bursting_EPM_Report_workforce_current_positions_allocation.xlsx"
 
REM Download the new Reports to local drive
 
CALL %WorkingDIR%\epmautomate downloadfile Bursting_EPM_Report_workforce_current_positions_allocation.xlsx

timeout /t 2 /nobreak

move Bursting_EPM_Report_workforce_current_positions_allocation.xlsx C:\xampp\htdocs\kku-report\server\automateEPM\workforce\workforce_current_positions_allocation\

REM timeout /t 1 /nobreak

python "C:/xampp/htdocs/kku-report/server/automateEPM/workforce/workforce_current_positions_allocation/xlsx2csv.py"

REM Delee the Old Reports
CALL %WorkingDIR%\epmautomate deletefile Bursting_EPM_Report_workforce_current_positions_allocation.xlsx
CALL %WorkingDIR%\epmautomate logout
 
EXIT