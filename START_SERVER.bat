@echo off
echo ========================================
echo SIHANPANGAN851 - Server Startup
echo ========================================
echo.
echo Checking Node.js installation...
node --version
if errorlevel 1 (
    echo ERROR: Node.js tidak terinstall!
    echo Silakan install Node.js dari https://nodejs.org
    pause
    exit
)
echo.
echo Installing dependencies...
call npm install
echo.
echo Starting server...
echo Server akan berjalan di http://localhost:3000
echo.
echo Tekan Ctrl+C untuk menghentikan server
echo ========================================
node server.js
pause
