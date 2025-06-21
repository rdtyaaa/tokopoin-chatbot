@echo off
chcp 65001 >nul
title Quick Start - All Services

echo ╔══════════════════════════════════════════════════════════════╗
echo ║                    QUICK START                               ║
echo ║        Starting All Services with Tabs...                   ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo [1/3] Starting Docker Desktop...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
timeout /t 5 /nobreak

echo [2/3] Starting Redis Container...
docker start redis-server >nul 2>&1
if %errorlevel% neq 0 (
    echo Creating new Redis container...
    docker run -d --name redis-server -p 6379:6379 redis:latest >nul 2>&1
)
timeout /t 3 /nobreak

echo [3/3] Opening all services in tabs...
wt new-tab --title "Ngrok Tunnel" -- ngrok http --domain=chow-magnetic-bluebird.ngrok-free.app 8000 ; new-tab -d . --title "PHP Server" -- php -S localhost:8000 ; new-tab -d .\kode --title "Queue Worker" -- php artisan queue:work ; new-tab -d .\node-chat-server --title "Node Server" -- node server.js

echo.
echo ✓ All services started successfully in separate tabs!
echo.
echo ═══════════════════════════════════════════════════════════════
echo Your services are now running:
echo • Docker Desktop: Started
echo • Redis:    localhost:6379
echo • Laravel:  http://localhost:8000
echo • Public URL: https://chow-magnetic-bluebird.ngrok-free.app
echo ═══════════════════════════════════════════════════════════════
echo.
echo Check your Windows Terminal - all services are running in tabs!
echo.
echo Press any key to open Service Manager for more options...
pause >nul

REM Launch main service manager
call service-manager.bat