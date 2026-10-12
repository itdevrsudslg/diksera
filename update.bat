@echo off
rem Pembungkus agar update.ps1 bisa dijalankan tanpa mengubah execution policy.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0update.ps1" %*
