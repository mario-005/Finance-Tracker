# Keuangan Desktop Application Launcher
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   Keuangan Desktop Application" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Start Laravel server in background
Write-Host "Starting Laravel server..." -ForegroundColor Yellow
$laravelProcess = Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--port=8000" -PassThru -WindowStyle Minimized

Write-Host "Waiting for server to start..." -ForegroundColor Yellow
Start-Sleep -Seconds 4

Write-Host "Launching Electron application..." -ForegroundColor Green
Write-Host ""

# Launch Electron
& npx electron .

# Clean up
if ($laravelProcess) {
    Stop-Process -Id $laravelProcess.Id -ErrorAction SilentlyContinue
}

Write-Host "Application closed." -ForegroundColor Gray
