SET WorkingDIR="C:\Oracle\EPM Automate\bin"

REM Log in to EPM Cloud
CALL %WorkingDIR%\epmautomate login epm-service-admin@kku.ac.th kku_EPM_12345! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning > C:\xampp\htdocs\automateEPM\planning\planning_kku_okr_progress\WF_Outbound_Process.log

REM Report Bursting
CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "Library/EPM Report Planning/Bursting_EPM_Report_planning_kku_okr_progress" >> C:\xampp\htdocs\automateEPM\planning\planning_kku_okr_progress\WF_Outbound_Process.log

REM Delete the Old Reports
CALL %WorkingDIR%\epmautomate deletefile EPM_Report_planning_kku_okr_progress.xlsx >> C:\xampp\htdocs\automateEPM\planning\planning_kku_okr_progress\WF_Outbound_Process.log

REM Export the New reports
CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Planning/EPM_Report_planning_kku_okr_progress.xlsx" >> C:\xampp\htdocs\automateEPM\planning\planning_kku_okr_progress\WF_Outbound_Process.log

REM Download the new Reports to local drive
CALL %WorkingDIR%\epmautomate downloadfile EPM_Report_planning_kku_okr_progress.xlsx >> C:\xampp\htdocs\automateEPM\planning\planning_kku_okr_progress\WF_Outbound_Process.log

timeout /t 2 /nobreak

move EPM_Report_planning_kku_okr_progress.xlsx C:\xampp\htdocs\automateEPM\planning\planning_kku_okr_progress\

timeout /t 1 /nobreak

python "C:/xampp/htdocs/automateEPM/planning/planning_kku_okr_progress/xlsx2csv.py"

CALL %WorkingDIR%\epmautomate logout

EXIT
