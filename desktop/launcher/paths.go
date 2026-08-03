package main

import (
	"os"
	"path/filepath"
)

// Puertos que usa la instalación local.
//
// Se eligen altos y poco comunes a propósito: en una PC de tienda es habitual
// que ya haya un XAMPP, un Laragon o un MySQL de otro sistema ocupando los
// puertos por defecto, y el cliente no tiene por qué pelearse con eso.
const (
	HTTPPort   = "8099"
	PHPCGIPort = "9123"
	MySQLPort  = "3399"
)

// Paths resuelve la ubicación de cada pieza a partir de dónde está el .exe.
//
// Todo cuelga de una única carpeta (por defecto C:\Pro8), así que la
// instalación es portable: se puede copiar entera a otra PC o a un disco
// externo.
type Paths struct {
	Root    string // C:\Pro8
	App     string // C:\Pro8\app        (código Laravel)
	Runtime string // C:\Pro8\runtime    (php, mariadb, caddy)
	Data    string // C:\Pro8\data       (base de datos)
	Logs    string // C:\Pro8\logs
	Config  string // C:\Pro8\config
}

// ResolvePaths ubica la instalación tomando como referencia el ejecutable.
func ResolvePaths() (Paths, error) {
	executable, err := os.Executable()
	if err != nil {
		return Paths{}, err
	}

	root := filepath.Dir(executable)

	return Paths{
		Root:    root,
		App:     filepath.Join(root, "app"),
		Runtime: filepath.Join(root, "runtime"),
		Data:    filepath.Join(root, "data"),
		Logs:    filepath.Join(root, "logs"),
		Config:  filepath.Join(root, "config"),
	}, nil
}

// EnsureDirs crea las carpetas de trabajo si es la primera vez.
func (p Paths) EnsureDirs() error {
	for _, dir := range []string{p.Data, p.Logs, p.Config, p.MySQLData()} {
		if err := os.MkdirAll(dir, 0o755); err != nil {
			return err
		}
	}

	return nil
}

func (p Paths) PHP() string    { return filepath.Join(p.Runtime, "php", "php.exe") }
func (p Paths) PHPCGI() string { return filepath.Join(p.Runtime, "php", "php-cgi.exe") }
func (p Paths) Caddy() string  { return filepath.Join(p.Runtime, "caddy", "caddy.exe") }
func (p Paths) MySQLD() string { return filepath.Join(p.Runtime, "mariadb", "bin", "mysqld.exe") }
func (p Paths) MySQLCli() string {
	return filepath.Join(p.Runtime, "mariadb", "bin", "mysql.exe")
}

// MySQLData es el directorio de datos de MariaDB. Vive fuera de runtime\ para
// que una actualización del motor no toque nunca la información del negocio.
func (p Paths) MySQLData() string { return filepath.Join(p.Data, "mysql") }

func (p Paths) Caddyfile() string   { return filepath.Join(p.Config, "Caddyfile") }
func (p Paths) MySQLIni() string    { return filepath.Join(p.Config, "my.ini") }
func (p Paths) PairingFile() string { return filepath.Join(p.Config, "pairing.json") }

// InitMarker indica que la base ya se creó y no hay que rehacer el arranque.
func (p Paths) InitMarker() string { return filepath.Join(p.Data, ".initialized") }

// StatusFile es la foto que deja el demonio de sincronización.
func (p Paths) StatusFile() string {
	return filepath.Join(p.App, "storage", "app", "offline-status.json")
}

func (p Paths) LogFile(name string) string {
	return filepath.Join(p.Logs, name+".log")
}

// Exists dice si un archivo o carpeta está presente.
func Exists(path string) bool {
	_, err := os.Stat(path)

	return err == nil
}
