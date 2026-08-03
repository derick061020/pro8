;
; Instalador de pro8 para Windows.
;
; Se arma desde Linux con makensis (ver desktop/build/build.sh).
;
; Instala el sistema completo con su propio PHP, MariaDB y servidor web, sin
; tocar nada de lo que ya haya en la PC. Al final pide los datos para parear
; el terminal con el servidor online.
;

Unicode true

!include "MUI2.nsh"
!include "nsDialogs.nsh"
!include "LogicLib.nsh"
!include "FileFunc.nsh"
!include "WordFunc.nsh"

!insertmacro WordReplace

!ifndef VERSION
  !define VERSION "dev"
!endif

!ifndef PAYLOAD
  !error "Falta -DPAYLOAD con la carpeta a empaquetar"
!endif

!ifndef OUTFILE
  !define OUTFILE "pro8-terminal-${VERSION}.exe"
!endif

Name "pro8 Terminal"
OutFile "${OUTFILE}"
InstallDir "C:\Pro8"
RequestExecutionLevel admin
SetCompressor /SOLID lzma

VIProductVersion "1.0.0.0"
VIAddVersionKey "ProductName"    "pro8 Terminal"
VIAddVersionKey "FileDescription" "Sistema pro8 con modo offline"
VIAddVersionKey "FileVersion"    "${VERSION}"
VIAddVersionKey "LegalCopyright" "pro8"

; ---------------------------------------------------------------------------
; Variables de la pantalla de pareo
; ---------------------------------------------------------------------------

Var Dialog
Var ServerURL
Var ServerToken
Var TerminalCode
Var TerminalName
Var TenantUUID
Var DumpPath

Var TxtServerURL
Var TxtServerToken
Var TxtTerminalCode
Var TxtTerminalName
Var TxtTenantUUID
Var TxtDumpPath

; ---------------------------------------------------------------------------
; Páginas
; ---------------------------------------------------------------------------

!define MUI_ICON "..\launcher\assets\icon.ico"
!define MUI_UNICON "..\launcher\assets\icon.ico"
!define MUI_ABORTWARNING

!insertmacro MUI_PAGE_DIRECTORY
Page custom PairingPage PairingPageLeave
!insertmacro MUI_PAGE_INSTFILES

!define MUI_FINISHPAGE_RUN "$INSTDIR\pro8.exe"
!define MUI_FINISHPAGE_RUN_TEXT "Iniciar pro8 ahora"
!insertmacro MUI_PAGE_FINISH

!insertmacro MUI_UNPAGE_CONFIRM
!insertmacro MUI_UNPAGE_INSTFILES

!insertmacro MUI_LANGUAGE "Spanish"

; ---------------------------------------------------------------------------
; Pantalla: datos del servidor
; ---------------------------------------------------------------------------

Function PairingPage
  nsDialogs::Create 1018
  Pop $Dialog

  ${If} $Dialog == error
    Abort
  ${EndIf}

  !insertmacro MUI_HEADER_TEXT "Conexión con el servidor" \
    "Estos datos permiten que el terminal suba sus ventas cuando hay internet."

  ${NSD_CreateLabel} 0 0 100% 10u "Dirección del servidor pro8 online:"
  Pop $0
  ${NSD_CreateText} 0 11u 100% 12u "https://"
  Pop $TxtServerURL

  ${NSD_CreateLabel} 0 26u 100% 10u "Token de acceso (se genera en el servidor):"
  Pop $0
  ${NSD_CreateText} 0 37u 100% 12u ""
  Pop $TxtServerToken

  ${NSD_CreateLabel} 0 52u 48% 10u "Código del terminal (único por PC):"
  Pop $0
  ${NSD_CreateText} 0 63u 48% 12u "T01"
  Pop $TxtTerminalCode

  ${NSD_CreateLabel} 52% 52u 48% 10u "Nombre del terminal:"
  Pop $0
  ${NSD_CreateText} 52% 63u 48% 12u "Caja principal"
  Pop $TxtTerminalName

  ; Sin este dato el terminal no sabe sobre qué base restaurar el respaldo.
  ${NSD_CreateLabel} 0 78u 100% 10u "Base del negocio en el servidor (ej. tenant_miempresa):"
  Pop $0
  ${NSD_CreateText} 0 89u 100% 12u "tenant_"
  Pop $TxtTenantUUID

  ${NSD_CreateLabel} 0 104u 100% 10u "Respaldo (.sql) exportado desde el servidor:"
  Pop $0
  ${NSD_CreateFileRequest} 0 115u 82% 12u ""
  Pop $TxtDumpPath
  ${NSD_CreateBrowseButton} 84% 115u 16% 12u "Buscar"
  Pop $0
  ${NSD_OnClick} $0 BrowseDump

  nsDialogs::Show
FunctionEnd

Function BrowseDump
  nsDialogs::SelectFileDialog open "" "Respaldo SQL|*.sql|Todos los archivos|*.*"
  Pop $0

  ${If} $0 != ""
    ${NSD_SetText} $TxtDumpPath $0
  ${EndIf}
FunctionEnd

Function PairingPageLeave
  ${NSD_GetText} $TxtServerURL    $ServerURL
  ${NSD_GetText} $TxtServerToken  $ServerToken
  ${NSD_GetText} $TxtTerminalCode $TerminalCode
  ${NSD_GetText} $TxtTerminalName $TerminalName
  ${NSD_GetText} $TxtTenantUUID   $TenantUUID
  ${NSD_GetText} $TxtDumpPath     $DumpPath

  ${If} $TerminalCode == ""
    MessageBox MB_ICONEXCLAMATION "Indicá un código para este terminal (por ejemplo T01)."
    Abort
  ${EndIf}

  ${If} $DumpPath != ""
    ${IfNot} ${FileExists} $DumpPath
      MessageBox MB_ICONEXCLAMATION "No se encontró el archivo de respaldo indicado."
      Abort
    ${EndIf}

    ; El respaldo sin saber sobre qué base restaurarlo no sirve de nada.
    ${If} $TenantUUID == ""
    ${OrIf} $TenantUUID == "tenant_"
      MessageBox MB_ICONEXCLAMATION "Indicá el nombre de la base del negocio en el servidor."
      Abort
    ${EndIf}
  ${Else}
    MessageBox MB_ICONQUESTION|MB_YESNO \
      "Sin el respaldo el terminal se instala vacío y habrá que cargarle los datos a mano.$\n$\n¿Continuar igual?" \
      IDYES continuar
    Abort
    continuar:
  ${EndIf}
FunctionEnd

; ---------------------------------------------------------------------------
; Instalación
; ---------------------------------------------------------------------------

Section "pro8" SecMain
  SetOutPath "$INSTDIR"

  DetailPrint "Copiando el sistema (puede tardar varios minutos)..."
  File /r "${PAYLOAD}\*.*"

  ; Carpetas de trabajo que no viajan en el paquete
  CreateDirectory "$INSTDIR\data"
  CreateDirectory "$INSTDIR\logs"

  DetailPrint "Configurando el entorno..."
  Call WriteEnv
  Call WritePairing

  DetailPrint "Generando la clave de la aplicación..."
  nsExec::ExecToLog '"$INSTDIR\runtime\php\php.exe" artisan key:generate --force'
  Pop $0

  DetailPrint "Creando accesos directos..."
  CreateDirectory "$SMPROGRAMS\pro8"
  CreateShortcut "$SMPROGRAMS\pro8\pro8.lnk" "$INSTDIR\pro8.exe" "" "$INSTDIR\pro8.exe" 0
  CreateShortcut "$SMPROGRAMS\pro8\Desinstalar pro8.lnk" "$INSTDIR\uninstall.exe"
  CreateShortcut "$DESKTOP\pro8.lnk" "$INSTDIR\pro8.exe" "" "$INSTDIR\pro8.exe" 0

  ; Arranque con Windows: en una caja se espera que el sistema ya esté abierto.
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Run" "pro8" '"$INSTDIR\pro8.exe"'

  WriteUninstaller "$INSTDIR\uninstall.exe"

  ; Entrada en "Agregar o quitar programas"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\pro8" \
    "DisplayName" "pro8 Terminal"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\pro8" \
    "DisplayVersion" "${VERSION}"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\pro8" \
    "UninstallString" '"$INSTDIR\uninstall.exe"'
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\pro8" \
    "InstallLocation" "$INSTDIR"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\pro8" \
    "DisplayIcon" "$INSTDIR\pro8.exe"
  WriteRegDWORD HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\pro8" \
    "NoModify" 1
SectionEnd

; Escribe app\.env a partir de la plantilla del terminal.
Function WriteEnv
  ; Si ya existe un .env (reinstalación sobre una instalación en uso), no se
  ; toca: adentro está la configuración del negocio.
  ${If} ${FileExists} "$INSTDIR\app\.env"
    DetailPrint "Se conserva el .env existente."
    Return
  ${EndIf}

  CopyFiles /SILENT "$INSTDIR\config\env.terminal.example" "$INSTDIR\app\.env"
FunctionEnd

; Deja los datos de pareo para que el launcher haga la instalación inicial.
Function WritePairing
  ; El archivo es JSON y las rutas de Windows vienen con barras invertidas,
  ; que ahí son escapes. Se pasan a barras normales, que PHP acepta igual.
  StrCpy $R0 $DumpPath

  ${If} $R0 != ""
    ${WordReplace} $R0 "\" "/" "+" $R0
  ${EndIf}

  FileOpen $0 "$INSTDIR\config\pairing.json" w

  FileWrite $0 '{$\r$\n'
  FileWrite $0 '  "server_url": "$ServerURL",$\r$\n'
  FileWrite $0 '  "token": "$ServerToken",$\r$\n'
  FileWrite $0 '  "terminal_code": "$TerminalCode",$\r$\n'
  FileWrite $0 '  "terminal_name": "$TerminalName",$\r$\n'
  FileWrite $0 '  "tenant_uuid": "$TenantUUID",$\r$\n'
  FileWrite $0 '  "dump": "$R0",$\r$\n'
  FileWrite $0 '  "series": []$\r$\n'
  FileWrite $0 '}$\r$\n'

  FileClose $0
FunctionEnd

; ---------------------------------------------------------------------------
; Desinstalación
; ---------------------------------------------------------------------------

Section "Uninstall"
  ; El launcher deja procesos vivos (MariaDB, PHP, Caddy); si no se cierran,
  ; los archivos quedan bloqueados y la desinstalación falla a medias.
  DetailPrint "Deteniendo pro8..."
  nsExec::ExecToLog 'taskkill /F /IM pro8.exe'
  Pop $0
  nsExec::ExecToLog 'taskkill /F /IM caddy.exe'
  Pop $0
  nsExec::ExecToLog 'taskkill /F /IM php-cgi.exe'
  Pop $0
  nsExec::ExecToLog 'taskkill /F /IM mysqld.exe'
  Pop $0
  Sleep 2000

  MessageBox MB_ICONQUESTION|MB_YESNO \
    "¿Borrar también la base de datos local?$\n$\nSi hay ventas sin sincronizar, se pierden." \
    IDYES borrarDatos IDNO conservarDatos

  borrarDatos:
    RMDir /r "$INSTDIR\data"
    Goto seguir

  conservarDatos:
    DetailPrint "Se conserva $INSTDIR\data"

  seguir:
  RMDir /r "$INSTDIR\app"
  RMDir /r "$INSTDIR\runtime"
  RMDir /r "$INSTDIR\config"
  RMDir /r "$INSTDIR\logs"
  Delete "$INSTDIR\pro8.exe"
  Delete "$INSTDIR\uninstall.exe"
  RMDir "$INSTDIR"

  Delete "$SMPROGRAMS\pro8\pro8.lnk"
  Delete "$SMPROGRAMS\pro8\Desinstalar pro8.lnk"
  RMDir "$SMPROGRAMS\pro8"
  Delete "$DESKTOP\pro8.lnk"

  DeleteRegValue HKLM "Software\Microsoft\Windows\CurrentVersion\Run" "pro8"
  DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\pro8"
SectionEnd
