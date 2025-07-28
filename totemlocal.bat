@echo off
start "" "C:\Program Files\Google\Chrome\Application\chrome.exe" ^
--kiosk ^
--kiosk-printing ^
--app="http://127.0.0.1:8000/totem/confirmacion?imprimir=1"
