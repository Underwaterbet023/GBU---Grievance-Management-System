@echo off
title GBU Grievance Management System
echo Starting GBU Grievance Management System...
echo.
echo Keep this window open while using the website.
echo Press Ctrl+C to stop the server.
echo.
start "" http://localhost:8000
if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" -S localhost:8000 -t public
) else (
    php -S localhost:8000 -t public
)
pause
