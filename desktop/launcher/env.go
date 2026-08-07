package main

import (
	"bufio"
	"os"
	"strings"
)

// Lectura del .env del sistema.
//
// El launcher necesita un par de valores de ahí (sobre todo el nombre de la
// base) antes de que PHP pueda arrancar, así que no puede pedírselos a Laravel.
// Alcanza con leer las líneas "CLAVE=valor": el .env del terminal lo escribe el
// instalador desde una plantilla, no tiene interpolaciones ni valores raros.

// envValue devuelve el valor de una clave del .env, o el respaldo si el archivo
// no existe todavía o la clave no está.
func envValue(key, fallback string) string {
	file, err := os.Open(paths.EnvFile())
	if err != nil {
		return fallback
	}
	defer file.Close()

	scanner := bufio.NewScanner(file)

	for scanner.Scan() {
		line := strings.TrimSpace(scanner.Text())

		if line == "" || strings.HasPrefix(line, "#") {
			continue
		}

		name, value, found := strings.Cut(line, "=")

		if !found || strings.TrimSpace(name) != key {
			continue
		}

		return strings.Trim(strings.TrimSpace(value), `"'`)
	}

	return fallback
}
