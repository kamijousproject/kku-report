SET WorkingDIR="C:\Oracle\EPM Automate\bin"
 
REM Log in to EPM Cloud
 
CALL %WorkingDIR%\epmautomate login warissarac PluEm_Passw0rd! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning
REM Report Bursting
 
CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "/Library/EPM Report Workforce/Bursting_EPM_Report_workforce_new_position_request"
REM Export the New reports

CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Workforce/Bursting_EPM_Report_workforce_new_position_request.xlsx"
 
REM Download the new Reports to local drive
 
CALL %WorkingDIR%\epmautomate downloadfile Bursting_EPM_Report_workforce_new_position_request.xlsx

timeout /t 2 /nobreak

move Bursting_EPM_Report_workforce_new_position_request.xlsx C:\xampp\htdocs\kku-report\server\automateEPM\workforce\workforce_new_position_request\

REM timeout /t 1 /nobreak

python "C:/xampp/htdocs/kku-report/server/automateEPM/workforce/workforce_new_position_request/xlsx2csv.py"

REM Delee the Old Reports
CALL %WorkingDIR%\epmautomate deletefile Bursting_EPM_Report_workforce_new_position_request.xlsx

CALL %WorkingDIR%\epmautomate logout

python "C:/xampp/htdocs/kku-report/server/automateEPM/workforce/workforce_new_position_request/insert_workforce_new_position_request.py"
 
EXIT