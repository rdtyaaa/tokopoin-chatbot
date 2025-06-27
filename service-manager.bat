@echo off
chcp 65001 >nul
title Service Manager - Laravel & Node.js

:MAIN_MENU
cls
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                    SERVICE MANAGER                           ║
echo ║              Laravel, Node.js, Docker ^& Redis                ║
echo ╠══════════════════════════════════════════════════════════════╣
echo ║  1. Start All Services                                       ║
echo ║  2. Restart PHP Server (localhost:8000)                      ║
echo ║  3. Restart Queue Worker                                     ║
echo ║  4. Restart Node Server                                      ║
echo ║  5. Restart Ngrok Tunnel                                     ║
echo ║  6. Restart Docker Desktop                                   ║
echo ║  7. Restart Redis Container                                  ║
echo ║  8. Stop All Services                                        ║
echo ║  9. View Service Status                                      ║
echo ║  0. Exit                                                     ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
set /p choice="Pilih opsi (0-9): "

if "%choice%"=="1" goto START_ALL
if "%choice%"=="2" goto RESTART_PHP
if "%choice%"=="3" goto RESTART_QUEUE
if "%choice%"=="4" goto RESTART_NODE
if "%choice%"=="5" goto RESTART_NGROK
if "%choice%"=="6" goto RESTART_DOCKER
if "%choice%"=="7" goto RESTART_REDIS
if "%choice%"=="8" goto STOP_ALL
if "%choice%"=="9" goto STATUS
if "%choice%"=="0" goto EXIT
goto MAIN_MENU

:START_ALL
echo Starting all services...
echo.

echo [1/3] Starting Docker Desktop...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
timeout /t 5 /nobreak >nul

echo [2/3] Starting Redis Container...
docker start redis-server >nul 2>&1
if %errorlevel% neq 0 (
    echo Creating new Redis container...
    docker run -d --name redis-server -p 6379:6379 redis:latest >nul 2>&1
)
timeout /t 3 /nobreak >nul

echo [3/3] Starting all services in tabs...
wt new-tab --title "Ngrok Tunnel" -- ngrok http --domain=chow-magnetic-bluebird.ngrok-free.app 8000 ; new-tab -d . --title "PHP Server" -- php -S localhost:8000 ; new-tab -d .\kode --title "Queue Worker" -- php -d max_execution_time=0 artisan queue:work redis --timeout=600 --memory=512 --tries=3 ; new-tab -d .\node-chat-server --title "Node Server" -- node server.js

echo.
echo ✓ All services started successfully!
echo ✓ Docker Desktop: Started
echo ✓ Redis: Running on localhost:6379
echo ✓ Laravel: http://localhost:8000
echo ✓ Public URL: https://chow-magnetic-bluebird.ngrok-free.app
echo.
pause
goto MAIN_MENU

:RESTART_PHP
echo Restarting PHP Server...
echo Stopping existing PHP processes...
taskkill /f /fi "CommandLine eq php -S localhost:8000" >nul 2>&1
timeout /t 2 /nobreak >nul
echo Starting new PHP Server tab...
wt new-tab -d . --title "PHP Server" -- php -S localhost:8000
echo ✓ PHP Server restarted in new tab!
pause
goto MAIN_MENU

:RESTART_QUEUE
echo Restarting Queue Worker...
echo Stopping existing Queue Worker processes...
taskkill /f /fi "CommandLine eq php artisan queue:work redis --timeout=600 --memory=512 --tries=3" >nul 2>&1
timeout /t 2 /nobreak >nul
echo Starting new Queue Worker tab...
wt new-tab -d .\kode --title "Queue Worker" -- php artisan queue:work redis --timeout=600 --memory=512 --tries=3
echo ✓ Queue Worker restarted in new tab!
pause
goto MAIN_MENU

:RESTART_NODE
echo Restarting Node Server...
echo Stopping existing Node processes...
taskkill /f /fi "CommandLine eq node server.js" >nul 2>&1
timeout /t 2 /nobreak >nul
echo Starting new Node Server tab...
wt new-tab -d .\node-chat-server --title "Node Server" -- node server.js
echo ✓ Node Server restarted in new tab!
pause
goto MAIN_MENU

:RESTART_NGROK
echo Restarting Ngrok Tunnel...
echo Stopping existing Ngrok processes...
taskkill /f /im ngrok.exe >nul 2>&1
timeout /t 3 /nobreak >nul
echo Starting new Ngrok tab...
wt new-tab --title "Ngrok Tunnel" -- ngrok http --domain=chow-magnetic-bluebird.ngrok-free.app 8000
echo ✓ Ngrok Tunnel restarted in new tab!
pause
goto MAIN_MENU

:RESTART_DOCKER
echo Restarting Docker Desktop...
echo Stopping Docker Desktop...
taskkill /f /im "Docker Desktop.exe" >nul 2>&1
timeout /t 5 /nobreak >nul
echo Starting Docker Desktop...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
echo Waiting for Docker to start...
timeout /t 10 /nobreak >nul
echo ✓ Docker Desktop restarted!
pause
goto MAIN_MENU

:RESTART_REDIS
echo Restarting Redis Container...
echo Stopping Redis container...
docker stop redis-server >nul 2>&1
timeout /t 3 /nobreak >nul
echo Starting Redis container...
docker start redis-server >nul 2>&1
if %errorlevel% neq 0 (
    echo Creating new Redis container...
    docker run -d --name redis-server -p 6379:6379 redis:latest >nul 2>&1
)
echo ✓ Redis Container restarted!
pause
goto MAIN_MENU

:STOP_ALL
echo Stopping all services...
echo.
echo Stopping PHP Server...
taskkill /f /fi "CommandLine eq php -S localhost:8000" >nul 2>&1

echo Stopping Queue Worker...
taskkill /f /fi "CommandLine eq php artisan queue:work redis --timeout=600 --memory=512 --tries=3" >nul 2>&1

echo Stopping Node Server...
taskkill /f /fi "CommandLine eq node server.js" >nul 2>&1

echo Stopping Ngrok Tunnel...
taskkill /f /im ngrok.exe >nul 2>&1

echo Stopping Redis Container...
docker stop redis-server >nul 2>&1

echo Stopping Docker Desktop...
taskkill /f /im "Docker Desktop.exe" >nul 2>&1

echo.
echo ✓ All services stopped!
pause
goto MAIN_MENU

:STATUS
cls
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                      SERVICE STATUS                          ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo Checking Docker Desktop...
tasklist /im "Docker Desktop.exe" >nul 2>&1
if %errorlevel%==0 (
    echo ✓ Docker Desktop: RUNNING
) else (
    echo ✗ Docker Desktop: NOT RUNNING
)

echo.
echo Checking Redis Container...
docker ps --filter "name=redis-server" --format "table {{.Status}}" | find "Up" >nul 2>&1
if %errorlevel%==0 (
    echo ✓ Redis Container: RUNNING on localhost:6379
) else (
    echo ✗ Redis Container: NOT RUNNING
)

echo.
echo Checking PHP Server (localhost:8000)...
netstat -an | find "8000" >nul
if %errorlevel%==0 (
    echo ✓ PHP Server: RUNNING on localhost:8000
) else (
    echo ✗ PHP Server: NOT RUNNING
)

echo.
echo Checking Queue Worker...
tasklist /fi "CommandLine eq php artisan queue:work redis --timeout=600 --memory=512 --tries=3" | find "php.exe" >nul 2>&1
if %errorlevel%==0 (
    echo ✓ Queue Worker: RUNNING
) else (
    echo ✗ Queue Worker: NOT RUNNING
)

echo.
echo Checking Node Server...
tasklist /fi "CommandLine eq node server.js" | find "node.exe" >nul 2>&1
if %errorlevel%==0 (
    echo ✓ Node Server: RUNNING
) else (
    echo ✗ Node Server: NOT RUNNING
)

echo.
echo Checking Ngrok Tunnel...
tasklist /im ngrok.exe >nul 2>&1
if %errorlevel%==0 (
    echo ✓ Ngrok Tunnel: RUNNING
    echo   Public URL: https://chow-magnetic-bluebird.ngrok-free.app
) else (
    echo ✗ Ngrok Tunnel: NOT RUNNING
)

echo.
echo ═══════════════════════════════════════════════════════════════
echo Active Services:
echo • Docker Desktop
echo • Redis:      localhost:6379
echo • Laravel:    http://localhost:8000
echo • Public URL: https://chow-magnetic-bluebird.ngrok-free.app
echo ═══════════════════════════════════════════════════════════════
echo.
pause
goto MAIN_MENU

:EXIT
echo.
echo Terima kasih! Keluar dari Service Manager...
timeout /t 2 /nobreak >nul
exit /b

REM Error handling
:ERROR
echo An error occurred. Please check your configuration.
pause
goto MAIN_MENU