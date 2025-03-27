SET WorkingDIR="C:\Oracle\EPM Automate\bin"
 
REM Log in to EPM Cloud
 
CALL %WorkingDIR%\epmautomate login warissarac PluEm_Passw0rd! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning > C:\xampp\htdocs\automateEPM\workforce\workforce_4year_plan\WF_Outbound_Process.log
 
REM Report Bursting
 
CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "Library/EPM Report Workforce/Bursting_EPM_Report_workforce_4year_plan" >> C:\xampp\htdocs\automateEPM\workforce\workforce_4year_plan\WF_Outbound_Process.log
 
REM Delee the Old Reports
 
CALL %WorkingDIR%\epmautomate deletefile EPM_Report_workforce_4year_plan.xlsx >> C:\xampp\htdocs\automateEPM\workforce\workforce_4year_plan\WF_Outbound_Process.log
 
REM Export the New reports
 
CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Workforce/EPM_Report_workforce_4year_plan.xlsx" >> C:\xampp\htdocs\automateEPM\workforce\workforce_4year_plan\WF_Outbound_Process.log
 
REM Download the new Reports to local drive
 
CALL %WorkingDIR%\epmautomate downloadfile EPM_Report_workforce_4year_plan.xlsx >> C:\xampp\htdocs\automateEPM\workforce\workforce_4year_plan\WF_Outbound_Process.log

timeout /t 2 /nobreak

move EPM_Report_workforce_4year_plan.xlsx C:\xampp\htdocs\automateEPM\workforce\workforce_4year_plan\

timeout /t 1 /nobreak

python "C:/xampp/htdocs/automateEPM/workforce/workforce_4year_plan/xlsx2csv.py"
 
CALL %WorkingDIR%\epmautomate logout
 
EXIT