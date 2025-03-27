DEL C:\CSV_Process\PLN_Notification\*.xlsx

SET WorkingDIR="C:\Oracle\EPM Automate\bin"

REM Log in to EPM Cloud

CALL %WorkingDIR%\epmautomate login warissarac PluEm_Passw0rd! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning

REM Report Bursting

CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "/Library/EPM Report Planning/Bursting_EPM_Report_planning_kku_revised_action_plan"

REM Export the New reports

CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Planning/Bursting_EPM_Report_planning_kku_revised_action_plan.xlsx"

REM Download the new Reports to local drive

CALL %WorkingDIR%\epmautomate downloadfile Bursting_EPM_Report_planning_kku_revised_action_plan.xlsx

REM Delee the Old Reports

CALL %WorkingDIR%\epmautomate deletefile Planning_Status.xlsx

CALL %WorkingDIR%\epmautomate logout

move Bursting_EPM_Report_planning_kku_revised_action_plan.xlsx C:\xampp\htdocs\automateEPM\planning\planning_kku_revised_action_plan\

python "C:/xampp/htdocs/automateEPM/planning/planning_kku_revised_action_plan/xlsx2csv.py"

EXIT