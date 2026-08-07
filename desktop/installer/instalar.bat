@echo off
rem ---------------------------------------------------------------------------
rem  Instalación de pro8 Terminal sin instalador gráfico.
rem
rem  Es la alternativa al .exe de NSIS: hace exactamente lo mismo, pero
rem  preguntando por consola. Sirve para probar el terminal sin depender del
rem  toolchain completo de compilación.
rem
rem  Uso:
rem    1. Descomprimir el ZIP en C:\Pro8
rem    2. Clic derecho sobre este archivo -> "Ejecutar como administrador"
rem ---------------------------------------------------------------------------

setlocal EnableDelayedExpansion
chcp 65001 >nul

set "RAIZ=%~dp0"
set "RAIZ=%RAIZ:~0,-1%"

echo.
echo ===========================================================
echo   pro8 Terminal - instalacion
echo ===========================================================
echo.
echo   Carpeta de instalacion: %RAIZ%
echo.

rem --- Verificar que estamos como administrador ------------------------------
net session >nul 2>&1
if errorlevel 1 (
    echo   [ERROR] Hay que ejecutar este archivo como administrador.
    echo           Clic derecho -^> "Ejecutar como administrador"
    echo.
    pause
    exit /b 1
)

rem --- Verificar que el paquete esta completo --------------------------------
if not exist "%RAIZ%\runtime\php\php.exe" (
    echo   [ERROR] Falta runtime\php. El ZIP no se descomprimio completo.
    pause
    exit /b 1
)

if not exist "%RAIZ%\app\artisan" (
    echo   [ERROR] Falta la carpeta app. El ZIP no se descomprimio completo.
    pause
    exit /b 1
)

rem --- Datos del servidor -----------------------------------------------------
echo   Conexion con el servidor pro8 online
echo   -----------------------------------------------------------
echo.

set /p SERVIDOR="  Direccion del servidor (https://miempresa.com): "
set /p TOKEN="  Token de acceso: "
set /p CODIGO="  Codigo de este terminal (ej. T01): "
set /p NOMBRE="  Nombre del terminal (ej. Caja principal): "

echo.
echo   Nombre de la base de datos del negocio en el servidor.
echo   Es el que figura en el panel del sistema, suele empezar con "tenant_".
set /p TENANT="  Base del negocio (ej. tenant_miempresa): "

echo.
echo   Respaldo .sql exportado desde el servidor. El terminal tiene que
echo   arrancar con los mismos datos que el servidor.
set /p RESPALDO="  Ruta al archivo .sql: "

if "%CODIGO%"=="" (
    echo.
    echo   [ERROR] El codigo del terminal es obligatorio.
    pause
    exit /b 1
)

if not "%RESPALDO%"=="" (
    if not exist "%RESPALDO%" (
        echo.
        echo   [ERROR] No se encontro el archivo %RESPALDO%
        pause
        exit /b 1
    )
)

rem --- Escribir la configuracion ---------------------------------------------
echo.
echo   Escribiendo configuracion...

if not exist "%RAIZ%\data" mkdir "%RAIZ%\data"
if not exist "%RAIZ%\logs" mkdir "%RAIZ%\logs"

rem  Las barras invertidas de la ruta del respaldo hay que duplicarlas: el
rem  archivo es JSON y una barra sola seria un escape.
set "RESPALDO_JSON=%RESPALDO:\=\\%"

(
    echo {
    echo   "server_url": "%SERVIDOR%",
    echo   "token": "%TOKEN%",
    echo   "terminal_code": "%CODIGO%",
    echo   "terminal_name": "%NOMBRE%",
    echo   "tenant_uuid": "%TENANT%",
    echo   "dump": "%RESPALDO_JSON%",
    echo   "series": []
    echo }
) > "%RAIZ%\config\pairing.json"

rem  Un .env existente no se pisa: adentro esta la configuracion del negocio.
if not exist "%RAIZ%\app\.env" (
    copy /y "%RAIZ%\config\env.terminal.example" "%RAIZ%\app\.env" >nul
    echo   Archivo .env creado.
) else (
    echo   Se conserva el .env existente.
)

rem  La clave de la aplicacion la genera el launcher en el primer arranque:
rem  artisan levanta el sistema entero y el sistema consulta la base al
rem  arrancar, y aca todavia no existe ni hay un MariaDB corriendo.

rem --- Accesos directos -------------------------------------------------------
echo   Creando accesos directos...

powershell -NoProfile -Command ^
    "$s=(New-Object -ComObject WScript.Shell).CreateShortcut([Environment]::GetFolderPath('Desktop')+'\pro8.lnk');" ^
    "$s.TargetPath='%RAIZ%\pro8.exe'; $s.WorkingDirectory='%RAIZ%'; $s.IconLocation='%RAIZ%\pro8.exe'; $s.Save()" >nul 2>&1

rem  Arranque con Windows: en una caja se espera que el sistema ya este abierto.
reg add "HKLM\Software\Microsoft\Windows\CurrentVersion\Run" /v pro8 /t REG_SZ /d "\"%RAIZ%\pro8.exe\"" /f >nul

echo.
echo ===========================================================
echo   Instalacion terminada.
echo ===========================================================
echo.
echo   Al abrir pro8 por primera vez se restaura el respaldo y se
echo   parea con el servidor. Puede tardar varios minutos.
echo.

set /p ABRIR="  Abrir pro8 ahora? (S/N): "

if /i "%ABRIR%"=="S" start "" "%RAIZ%\pro8.exe"

endlocal
