SET WorkingDIR="C:\Oracle\EPM Automate\bin"

REM Log in to EPM Cloud

CALL %WorkingDIR%\epmautomate login epm-service-admin@kku.ac.th kku_EPM_12345! https://epbcs-khonkaenuniversity.epm.ap-singapore-1.ocs.oraclecloud.com/HyperionPlanning 

REM Report Bursting

CALL %WorkingDIR%\epmAutomate executeReportBurstingDefinition "/Library/EPM Report Planning/Bursting_EPM_Report_planning_kku_strategic_plan"

REM Delete รายงานเก่าในระบบ Cloud
CALL %WorkingDIR%\epmautomate deletefile Bursting_EPM_Report_planning_kku_strategic_plan.xlsx

REM Export รายงานจาก Library
CALL %WorkingDIR%\epmautomate exportLibraryDocument "Library/EPM Report Planning/Bursting_EPM_Report_planning_kku_strategic_plan.xlsx"

REM Download รายงานลงเครื่อง
CALL %WorkingDIR%\epmautomate downloadfile "Bursting_EPM_Report_planning_kku_strategic_plan.xlsx"

REM Logout ออกจากระบบ
CALL %WorkingDIR%\epmautomate logout

move Bursting_EPM_Report_planning_kku_strategic_plan.xlsx C:\xampp\htdocs\kku-report\server\automateEPM\planning\planning_kku_strategic_plan

python "C:/xampp/htdocs/kku-report/server/automateEPM/planning/planning_kku_strategic_plan/xlsx2csv.py"

EXIT