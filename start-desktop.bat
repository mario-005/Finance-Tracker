@echo off
echo ========================================
echo   Keuangan Desktop Application
echo ========================================
echo.
echo Starting Laravel server...
start "Laravel Server - Keuangan" php artisan serve --port=8000

echo Waiting for server to start...
timeout /t 4 /nobreak

echo.
echo Launching Electron application...
npx electron .

pause
