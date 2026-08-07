package main

import (
	"fmt"
	"net"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
	"sync"
	"time"
)

// Stack levanta y supervisa los tres procesos que hacen falta para que pro8
// corra en la PC: la base de datos, el intérprete PHP y el servidor web.
//
// Se apagan siempre en orden inverso al arranque, y MariaDB al final, para no
// cortarle la base a una petición que todavía está en vuelo.
type Stack struct {
	paths Paths

	mu      sync.Mutex
	running []*exec.Cmd
}

func NewStack(paths Paths) *Stack {
	return &Stack{paths: paths}
}

// Start deja el sistema listo para usar y devuelve la URL local.
func (s *Stack) Start() (string, error) {
	if err := s.paths.EnsureDirs(); err != nil {
		return "", fmt.Errorf("no se pudieron crear las carpetas de trabajo: %w", err)
	}

	if err := s.verifyRuntime(); err != nil {
		return "", err
	}

	if err := s.startMariaDB(); err != nil {
		return "", err
	}

	// Antes de PHP: cualquier comando de artisan levanta los proveedores del
	// sistema, y esos consultan la base apenas arrancan. Sin base central ni
	// APP_KEY no corre ni siquiera la instalación inicial.
	if err := s.ensureCentralDatabase(); err != nil {
		return "", err
	}

	if err := s.ensureAppKey(); err != nil {
		return "", err
	}

	if err := s.startPHP(); err != nil {
		return "", err
	}

	if err := s.startCaddy(); err != nil {
		return "", err
	}

	url := "http://127.0.0.1:" + HTTPPort

	if err := waitHTTP(url, 60*time.Second); err != nil {
		return "", fmt.Errorf("el sistema no respondió a tiempo: %w", err)
	}

	return url, nil
}

// verifyRuntime avisa con un mensaje entendible si falta alguna pieza, en
// lugar de fallar más adelante con un error del sistema operativo.
func (s *Stack) verifyRuntime() error {
	required := map[string]string{
		"PHP":     s.paths.PHP(),
		"MariaDB": s.paths.MySQLD(),
		"Caddy":   s.paths.Caddy(),
	}

	for name, path := range required {
		if !Exists(path) {
			return fmt.Errorf("falta %s en la instalación (%s). Reinstalá pro8", name, path)
		}
	}

	if !Exists(filepath.Join(s.paths.App, "artisan")) {
		return fmt.Errorf("no se encontró el sistema en %s. Reinstalá pro8", s.paths.App)
	}

	return nil
}

func (s *Stack) startMariaDB() error {
	if err := s.ensureDataDir(); err != nil {
		return err
	}

	cmd := s.command(s.paths.MySQLD(),
		"--defaults-file="+s.paths.MySQLIni(),
		"--datadir="+s.paths.MySQLData(),
		"--port="+MySQLPort,
	)

	if err := s.launch(cmd, "mariadb"); err != nil {
		return fmt.Errorf("no se pudo iniciar la base de datos: %w", err)
	}

	if err := waitTCP("127.0.0.1:"+MySQLPort, 60*time.Second); err != nil {
		return fmt.Errorf("la base de datos no respondió: %w", err)
	}

	return nil
}

// ensureDataDir crea el directorio de datos de MariaDB la primera vez.
//
// El nombre del instalador cambió entre versiones de MariaDB, así que se
// prueban los dos que existen en la práctica.
func (s *Stack) ensureDataDir() error {
	if Exists(filepath.Join(s.paths.MySQLData(), "mysql")) {
		return nil
	}

	candidates := []string{
		filepath.Join(s.paths.Runtime, "mariadb", "bin", "mariadb-install-db.exe"),
		filepath.Join(s.paths.Runtime, "mariadb", "bin", "mysql_install_db.exe"),
	}

	for _, installer := range candidates {
		if !Exists(installer) {
			continue
		}

		cmd := s.command(installer, "--datadir="+s.paths.MySQLData())

		if output, err := cmd.CombinedOutput(); err != nil {
			return fmt.Errorf("no se pudo preparar la base de datos: %s", string(output))
		}

		return nil
	}

	// Las distribuciones portables de MariaDB para Windows ya traen el
	// directorio de datos armado; si no hay instalador, se asume ese caso.
	return nil
}

// ensureCentralDatabase crea la base central del sistema si no está.
//
// El sistema no arranca sin ella: los proveedores preguntan por la tabla
// configurations al bootear, y con la base ausente eso revienta con "Unknown
// database" en cualquier comando, incluido offline:install, que es justamente
// el que iba a crearla. Con la base creada y vacía, en cambio, la consulta
// responde que no hay tabla y el arranque sigue.
//
// La base del negocio no se toca acá: la crea offline:install junto con la
// restauración del respaldo del servidor.
func (s *Stack) ensureCentralDatabase() error {
	name := envValue("DB_DATABASE", "pro8")

	// El nombre viaja dentro de un CREATE DATABASE, así que se admite solo lo
	// que puede ser un identificador y no necesita comillas.
	if !isPlainIdentifier(name) {
		return fmt.Errorf("el nombre de la base en app\\.env no es válido: %q", name)
	}

	client := s.paths.MySQLCli()

	if !Exists(client) {
		return fmt.Errorf("falta el cliente de MariaDB (%s). Reinstalá pro8", client)
	}

	args := []string{
		"--host=127.0.0.1",
		"--port=" + MySQLPort,
		"--user=" + envValue("DB_USERNAME", "root"),
	}

	if password := envValue("DB_PASSWORD", ""); password != "" {
		args = append(args, "--password="+password)
	}

	args = append(args, "--execute=CREATE DATABASE IF NOT EXISTS `"+name+
		"` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")

	output, err := s.command(client, args...).CombinedOutput()
	if err != nil {
		return fmt.Errorf("no se pudo crear la base %s: %s", name, strings.TrimSpace(string(output)))
	}

	return nil
}

// ensureAppKey genera la clave de la aplicación si el .env todavía trae el
// marcador de la plantilla.
//
// El instalador no puede generarla: lo intenta antes de que exista la base y
// artisan falla ahí mismo, en silencio. Sin clave válida el sistema arranca
// pero no puede cifrar la sesión, así que no se llega ni a la pantalla de
// ingreso.
func (s *Stack) ensureAppKey() error {
	if strings.HasPrefix(envValue("APP_KEY", ""), "base64:") {
		return nil
	}

	logf("generando la clave de la aplicación")

	output, err := s.RunArtisan("key:generate", "--force")
	if err != nil {
		return fmt.Errorf("no se pudo generar la clave de la aplicación: %s", strings.TrimSpace(output))
	}

	return nil
}

// isPlainIdentifier acepta lo que MariaDB permite como nombre de base sin
// necesidad de escapes.
func isPlainIdentifier(name string) bool {
	if name == "" {
		return false
	}

	for _, r := range name {
		switch {
		case r >= 'a' && r <= 'z', r >= 'A' && r <= 'Z', r >= '0' && r <= '9':
		case r == '_', r == '-', r == '$':
		default:
			return false
		}
	}

	return true
}

func (s *Stack) startPHP() error {
	// php-cgi atiende a Caddy por FastCGI. PHP_FCGI_MAX_REQUESTS=0 evita que
	// el proceso se recicle solo y deje la caja sin sistema en medio de una
	// venta.
	cmd := s.command(s.paths.PHPCGI(), "-b", "127.0.0.1:"+PHPCGIPort)
	cmd.Env = append(os.Environ(),
		"PHP_FCGI_MAX_REQUESTS=0",
		"PHP_FCGI_CHILDREN=8",
	)

	if err := s.launch(cmd, "php"); err != nil {
		return fmt.Errorf("no se pudo iniciar PHP: %w", err)
	}

	return waitTCP("127.0.0.1:"+PHPCGIPort, 30*time.Second)
}

func (s *Stack) startCaddy() error {
	cmd := s.command(s.paths.Caddy(), "run", "--config", s.paths.Caddyfile(), "--adapter", "caddyfile")

	if err := s.launch(cmd, "caddy"); err != nil {
		return fmt.Errorf("no se pudo iniciar el servidor web: %w", err)
	}

	return nil
}

// StartSyncDaemon deja corriendo la sincronización en segundo plano.
//
// En Windows no hay cron, así que en vez de depender del Programador de
// tareas el demonio vive mientras pro8 esté abierto.
//
// Lo llama el launcher recién después de la instalación inicial: antes de eso
// no hay negocio que sincronizar y el demonio se caería al arrancar.
func (s *Stack) StartSyncDaemon() {
	cmd := s.artisan("tenancy:run", "offline:daemon")

	if err := s.launch(cmd, "sync"); err != nil {
		// No es fatal: el sistema funciona igual, solo que sin sincronizar
		// hasta que se use el botón del panel.
		logf("no se pudo iniciar el demonio de sincronización: %v", err)
	}
}

// RunArtisan corre un comando de artisan y espera el resultado.
func (s *Stack) RunArtisan(args ...string) (string, error) {
	cmd := s.artisan(args...)

	output, err := cmd.CombinedOutput()

	return string(output), err
}

func (s *Stack) artisan(args ...string) *exec.Cmd {
	full := append([]string{"artisan"}, args...)

	return s.command(s.paths.PHP(), full...)
}

// command prepara un proceso que corre dentro de la carpeta de la aplicación
// y sin ventana de consola: el usuario no tiene que ver pantallas negras.
func (s *Stack) command(name string, args ...string) *exec.Cmd {
	cmd := exec.Command(name, args...)
	cmd.Dir = s.paths.App
	hideWindow(cmd)

	return cmd
}

// launch arranca el proceso en segundo plano y le redirige la salida a un
// archivo de log, que es lo único que queda para diagnosticar en sitio.
func (s *Stack) launch(cmd *exec.Cmd, name string) error {
	file, err := os.OpenFile(s.paths.LogFile(name), os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0o644)
	if err == nil {
		cmd.Stdout = file
		cmd.Stderr = file
	}

	if err := cmd.Start(); err != nil {
		return err
	}

	s.mu.Lock()
	s.running = append(s.running, cmd)
	s.mu.Unlock()

	return nil
}

// Stop apaga todo lo que se levantó, en orden inverso.
func (s *Stack) Stop() {
	s.mu.Lock()
	processes := make([]*exec.Cmd, len(s.running))
	copy(processes, s.running)
	s.running = nil
	s.mu.Unlock()

	for i := len(processes) - 1; i >= 0; i-- {
		cmd := processes[i]

		if cmd.Process == nil {
			continue
		}

		_ = cmd.Process.Kill()
		_, _ = cmd.Process.Wait()
	}
}

// waitTCP espera a que un puerto acepte conexiones.
func waitTCP(address string, timeout time.Duration) error {
	deadline := time.Now().Add(timeout)

	for time.Now().Before(deadline) {
		conn, err := net.DialTimeout("tcp", address, 2*time.Second)

		if err == nil {
			_ = conn.Close()

			return nil
		}

		time.Sleep(500 * time.Millisecond)
	}

	return fmt.Errorf("%s no respondió en %s", address, timeout)
}

// waitHTTP espera a que la aplicación conteste. Cualquier código de respuesta
// sirve: incluso un 500 significa que PHP y el servidor web están vivos, y ese
// error se diagnostica mejor desde el navegador.
func waitHTTP(url string, timeout time.Duration) error {
	deadline := time.Now().Add(timeout)
	client := &http.Client{Timeout: 5 * time.Second}

	for time.Now().Before(deadline) {
		response, err := client.Get(url)

		if err == nil {
			_ = response.Body.Close()

			return nil
		}

		time.Sleep(500 * time.Millisecond)
	}

	return fmt.Errorf("%s no respondió en %s", url, timeout)
}
