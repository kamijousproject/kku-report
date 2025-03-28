SET WorkingDIR="C:\Oracle\EPM Automate\bin"
 
REM Log in to EPM Cloud
 
CALL %WorkingDIR%\epmautomate login warissarac PluEm_Passw0rd! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning
 
REM Report Bursting
 
CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "Library/EPM Report Workforce/Bursting_EPM_Report_workforce_4year_plan"
 
REM Delee the Old Reports
 
CALL %WorkingDIR%\epmautomate deletefile EPM_Report_workforce_4year_plan.xlsx
 
REM Export the New reports
 
CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Workforce/EPM_Report_workforce_4year_plan.xlsx"
 
REM Download the new Reports to local drive
 
CALL %WorkingDIR%\epmautomate downloadfile EPM_Report_workforce_4year_plan.xlsx

timeout /t 2 /nobreak

move EPM_Report_workforce_4year_plan.xlsx C:\xampp\htdocs\kku-report\server\automateEPM\workforce\workforce_4year_plan\

timeout /t 1 /nobreak

python "C:/xampp/htdocs/kku-report/server/automateEPM/workforce/workforce_4year_plan/xlsx2csv.py"
 
CALL %WorkingDIR%\epmautomate logout
 
EXIT