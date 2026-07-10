@echo off
title Crosby Dental Clinic
cd /d %~dp0

for /f "delims=" %%i in ('where php') do set PHP_PATH=%%i

REM --- Step 1: Ensure the backup task exists (only creates it once, skips if already set up) ---
schtasks /Query /TN "CrosbyDentalBackup" >nul 2>&1
if errorlevel 1 (
    echo Setting up automatic backups for the first time...
    schtasks /Create /SC MINUTE /MO 5 /TN "CrosbyDentalBackup" /TR "\"%PHP_PATH%\" \"%~dp0artisan\" backup:auto" /F /RL HIGHEST /RU "%USERNAME%" >nul 2>&1
)

REM --- Step 2: Start the Laravel server on port 8001 ---
start /min "" "%PHP_PATH%" artisan serve --port=8001

REM --- Step 3: Wait a moment for the server to boot, then open the browser ---
timeout /t 2 /nobreak >nul
start http://127.0.0.1:8001/login

exit