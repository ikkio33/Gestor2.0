@echo off
set URL=https://soporte.gesnot.cl/Filas/Gestor/public/vista
set CHROME_PATH="C:\Program Files\Google\Chrome\Application\chrome.exe"

REM Abre Chrome en modo kiosko y permite autoplay de audio/video sin clic
start "" %CHROME_PATH% --kiosk --autoplay-policy=no-user-gesture-required --disable-features=MediaSessionService %URL%
