SET WorkingDIR="C:\Oracle\EPM Automate\bin"
 
REM Log in to EPM Cloud
 
CALL %WorkingDIR%\epmautomate login epm-service-admin@kku.ac.th kku_EPM_12345! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning > C:\xampp\htdocs\automateEPM\planning\planning_faculty_action_plan\WF_Outbound_Process.log
 
REM Report Bursting
 
CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "/Library/EPM Report Planning/Bursting_EPM_Report_planning_faculty_action_plan" >> C:\xampp\htdocs\automateEPM\planning\planning_faculty_action_plan\WF_Outbound_Process.log
 
REM Export the New reports

CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Planning/Bursting_EPM_Report_planning_faculty_action_plan.xlsx"
 
REM Download the new Reports to local drive
 
CALL %WorkingDIR%\epmautomate downloadfile Bursting_EPM_Report_planning_faculty_action_plan.xlsx

timeout /t 2 /nobreak

move Bursting_EPM_Report_planning_faculty_action_plan.xlsx C:\xampp\htdocs\automateEPM\planning\planning_faculty_action_plan\

REM timeout /t 1 /nobreak

python "C:/xampp/htdocs/automateEPM/planning/planning_faculty_action_plan/xlsx2csv.py"

REM Delee the Old Reports
CALL %WorkingDIR%\epmautomate deletefile Bursting_EPM_Report_planning_faculty_action_plan.xlsx >> C:\xampp\htdocs\automateEPM\planning\planning_faculty_action_plan\WF_Outbound_Process.log
 
CALL %WorkingDIR%\epmautomate logout
 
EXIT