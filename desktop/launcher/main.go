// Launcher de pro8 para Windows.
//
// Es el .exe que ve el usuario. Su trabajo es que una PC de tienda pueda usar
// pro8 sin que nadie tenga que saber qué es PHP, MariaDB o un servidor web:
// al abrirlo levanta todo, abre el navegador y queda en la bandeja del sistema
// mostrando si está sincronizando con el servidor online o no.
//
// No contiene lógica de negocio: todo lo que decide qué se sincroniza vive en
// el módulo Offline del sistema, y acá solo se lo invoca.
package main

import (
	_ "embed"
	"encoding/json"
	"fmt"
	"log"
	"os"
	"os/exec"
	"path/filepath"
	"time"

	"github.com/getlantern/systray"
)

//go:embed assets/icon.ico
var trayIcon []byte

// Cada cuánto se relee el estado que deja el demonio de sincronización.
const statusRefresh = 10 * time.Second

var (
	paths     Paths
	stack     *Stack
	appURL    string
	logger    *log.Logger
	lastAlert time.Time
)

func main() {
	var err error

	paths, err = ResolvePaths()
	if err != nil {
		fatal("No se pudo determinar dónde está instalado pro8: " + err.Error())
	}

	openLog()

	stack = NewStack(paths)

	systray.Run(onReady, onExit)
}

func onReady() {
	systray.SetIcon(trayIcon)
	systray.SetTitle("pro8")
	systray.SetTooltip("pro8 — iniciando...")

	menuOpen := systray.AddMenuItem("Abrir pro8", "Abre el sistema en el navegador")
	systray.AddSeparator()
	menuStatus := systray.AddMenuItem("Iniciando...", "Estado de la sincronización")
	menuStatus.Disable()
	menuSync := systray.AddMenuItem("Sincronizar ahora", "Sube las ventas pendientes al servidor")
	systray.AddSeparator()
	menuUpdate := systray.AddMenuItem("Actualizar sistema", "Trae la última versión desde el repositorio")
	menuLogs := systray.AddMenuItem("Ver registros", "Abre la carpeta de logs")
	systray.AddSeparator()
	menuQuit := systray.AddMenuItem("Salir", "Cierra pro8 y detiene los servicios")

	go func() {
		if err := startEverything(); err != nil {
			systray.SetTooltip("pro8 — error al iniciar")
			menuStatus.SetTitle("Error: " + err.Error())
			notify("pro8 no pudo iniciar", err.Error())

			return
		}

		openBrowser(appURL)
		watchStatus(menuStatus)
	}()

	for {
		select {
		case <-menuOpen.ClickedCh:
			if appURL != "" {
				openBrowser(appURL)
			}

		case <-menuSync.ClickedCh:
			go syncNow(menuSync)

		case <-menuUpdate.ClickedCh:
			go updateSystem(menuUpdate)

		case <-menuLogs.ClickedCh:
			openFolder(paths.Logs)

		case <-menuQuit.ClickedCh:
			systray.Quit()

			return
		}
	}
}

func onExit() {
	logf("cerrando pro8")
	stack.Stop()
}

// startEverything levanta los servicios y, si es la primera vez, deja la
// instalación lista antes de abrir el navegador.
func startEverything() error {
	logf("iniciando servicios")

	url, err := stack.Start()
	if err != nil {
		logf("error al iniciar: %v", err)

		return err
	}

	appURL = url
	logf("sistema disponible en %s", url)

	if !Exists(paths.InitMarker()) {
		if err := runFirstInstall(); err != nil {
			return err
		}
	}

	return nil
}

// runFirstInstall corre la instalación inicial con los datos que dejó el
// instalador, y marca el equipo como instalado para no repetirla.
func runFirstInstall() error {
	pairing, err := readPairing()
	if err != nil {
		// Sin datos de pareo el sistema igual sirve: se puede configurar a
		// mano desde el panel de "Modo offline".
		logf("sin datos de pareo (%v): se omite la instalación automática", err)

		return nil
	}

	notify("pro8", "Preparando el equipo por primera vez. Puede tardar varios minutos.")
	logf("ejecutando instalación inicial")

	args := []string{
		"offline:install",
		"--dump=" + pairing.Dump,
		"--uuid=" + pairing.TenantUUID,
		"--url=" + pairing.ServerURL,
		"--token=" + pairing.Token,
		"--code=" + pairing.TerminalCode,
		"--name=" + pairing.TerminalName,
	}

	for _, series := range pairing.Series {
		args = append(args, "--series="+series)
	}

	output, err := stack.RunArtisan(args...)
	logf("instalación inicial: %s", output)

	if err != nil {
		return fmt.Errorf("falló la instalación inicial, revisá los registros")
	}

	return os.WriteFile(paths.InitMarker(), []byte(time.Now().Format(time.RFC3339)), 0o644)
}

// Pairing son los datos que carga el instalador para el primer arranque.
type Pairing struct {
	ServerURL    string   `json:"server_url"`
	Token        string   `json:"token"`
	TerminalCode string   `json:"terminal_code"`
	TerminalName string   `json:"terminal_name"`
	TenantUUID   string   `json:"tenant_uuid"`
	Dump         string   `json:"dump"`
	Series       []string `json:"series"`
}

func readPairing() (Pairing, error) {
	var pairing Pairing

	content, err := os.ReadFile(paths.PairingFile())
	if err != nil {
		return pairing, err
	}

	return pairing, json.Unmarshal(content, &pairing)
}

// watchStatus mantiene actualizado lo que se ve en la bandeja.
func watchStatus(item *systray.MenuItem) {
	for {
		status, err := ReadStatus(paths.StatusFile())

		if err != nil {
			systray.SetTooltip("pro8 — funcionando (sin datos de sincronización)")
			item.SetTitle("Sincronización: sin datos todavía")
		} else {
			systray.SetTooltip(status.Summary())
			item.SetTitle(status.Summary())
			warnLowRanges(status)
		}

		time.Sleep(statusRefresh)
	}
}

// warnLowRanges avisa antes de que el terminal se quede sin numeración.
//
// Es el punto flojo del esquema de bloques reservados: si se agotan sin
// internet, no se puede facturar. Mejor molestar con un aviso que descubrirlo
// con el cliente esperando en el mostrador.
func warnLowRanges(status SyncStatus) {
	low := status.LowRanges()

	if len(low) == 0 {
		return
	}

	// Un aviso por hora alcanza: repetirlo cada diez segundos sería inusable.
	if time.Since(lastAlert) < time.Hour {
		return
	}

	lastAlert = time.Now()

	message := "Quedan pocos comprobantes disponibles: "

	for i, r := range low {
		if i > 0 {
			message += ", "
		}

		message += fmt.Sprintf("%s (%d)", r.Series, r.Remaining)
	}

	message += ". Conectá el equipo a internet para reponerlos."

	notify("pro8 — numeración por agotarse", message)
}

func syncNow(item *systray.MenuItem) {
	item.Disable()
	item.SetTitle("Sincronizando...")

	output, err := stack.RunArtisan("tenancy:run", "offline:sync")
	logf("sincronización manual: %s", output)

	if err != nil {
		notify("pro8", "No se pudo sincronizar. Revisá los registros.")
	} else {
		notify("pro8", "Sincronización terminada.")
	}

	item.SetTitle("Sincronizar ahora")
	item.Enable()
}

func updateSystem(item *systray.MenuItem) {
	item.Disable()
	item.SetTitle("Actualizando...")

	output, err := stack.RunArtisan("tenancy:run", "offline:update")
	logf("actualización: %s", output)

	if err != nil {
		notify("pro8", "La actualización falló. Revisá los registros.")
	} else {
		notify("pro8", "Sistema actualizado. Cerrá y volvé a abrir pro8.")
	}

	item.SetTitle("Actualizar sistema")
	item.Enable()
}

// -----------------------------------------------------------------------
// Utilidades
// -----------------------------------------------------------------------

func openLog() {
	_ = os.MkdirAll(paths.Logs, 0o755)

	file, err := os.OpenFile(paths.LogFile("launcher"), os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0o644)
	if err != nil {
		logger = log.New(os.Stderr, "", log.LstdFlags)

		return
	}

	logger = log.New(file, "", log.LstdFlags)
}

func logf(format string, args ...any) {
	if logger != nil {
		logger.Printf(format, args...)
	}
}

func fatal(message string) {
	logf("%s", message)
	notify("pro8", message)
	os.Exit(1)
}

func openBrowser(url string) {
	if err := exec.Command("rundll32", "url.dll,FileProtocolHandler", url).Start(); err != nil {
		logf("no se pudo abrir el navegador: %v", err)
	}
}

func openFolder(path string) {
	if err := exec.Command("explorer", filepath.Clean(path)).Start(); err != nil {
		logf("no se pudo abrir la carpeta: %v", err)
	}
}

// notify muestra un aviso al usuario. Se usa PowerShell porque no agrega
// dependencias y funciona en cualquier Windows 10 u 11.
func notify(title, message string) {
	script := fmt.Sprintf(
		`[reflection.assembly]::LoadWithPartialName('System.Windows.Forms') > $null;`+
			`$n = New-Object System.Windows.Forms.NotifyIcon;`+
			`$n.Icon = [System.Drawing.SystemIcons]::Information;`+
			`$n.BalloonTipTitle = %q; $n.BalloonTipText = %q;`+
			`$n.Visible = $true; $n.ShowBalloonTip(10000); Start-Sleep -Seconds 11; $n.Dispose()`,
		title, message,
	)

	cmd := exec.Command("powershell", "-NoProfile", "-WindowStyle", "Hidden", "-Command", script)
	hideWindow(cmd)

	if err := cmd.Start(); err != nil {
		logf("no se pudo mostrar el aviso: %v", err)
	}
}
