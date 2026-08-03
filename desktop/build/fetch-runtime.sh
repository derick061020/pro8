#!/usr/bin/env bash
#
# Descarga el stack de Windows (PHP, MariaDB, Caddy) que se empaqueta dentro
# del instalador.
#
# Se corre una sola vez por máquina de compilación: lo descargado queda en
# desktop/build/cache/ y se reutiliza en los builds siguientes.
#
#   ./desktop/build/fetch-runtime.sh
#
# Las versiones están fijadas a propósito. Actualizarlas es una decisión
# consciente y hay que probar el terminal después, sobre todo PHP: la
# facturación electrónica es sensible a los cambios de soap y openssl.

set -euo pipefail

PHP_VERSION="8.1.31"
MARIADB_VERSION="10.11.10"
CADDY_VERSION="2.8.4"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CACHE_DIR="${SCRIPT_DIR}/cache"
RUNTIME_DIR="${SCRIPT_DIR}/runtime"

PHP_URL="https://windows.php.net/downloads/releases/archives/php-${PHP_VERSION}-nts-Win32-vs16-x64.zip"
MARIADB_URL="https://archive.mariadb.org/mariadb-${MARIADB_VERSION}/winx64-packages/mariadb-${MARIADB_VERSION}-winx64.zip"
CADDY_URL="https://github.com/caddyserver/caddy/releases/download/v${CADDY_VERSION}/caddy_${CADDY_VERSION}_windows_amd64.zip"
CACERT_URL="https://curl.se/ca/cacert.pem"

info()  { printf '\033[1;34m→\033[0m %s\n' "$1"; }
fail()  { printf '\033[1;31m✗\033[0m %s\n' "$1" >&2; exit 1; }

command -v curl  >/dev/null || fail "Falta curl."
command -v unzip >/dev/null || fail "Falta unzip."

mkdir -p "$CACHE_DIR" "$RUNTIME_DIR"

# descargar <url> <archivo-destino>
descargar() {
    local url="$1" destino="$2"

    if [[ -f "$destino" ]]; then
        info "Ya descargado: $(basename "$destino")"
        return
    fi

    info "Descargando $(basename "$destino")..."
    curl -fL --retry 3 --progress-bar -o "${destino}.tmp" "$url" \
        || fail "No se pudo descargar $url"
    mv "${destino}.tmp" "$destino"
}

# --- PHP -------------------------------------------------------------------

descargar "$PHP_URL" "${CACHE_DIR}/php.zip"

if [[ ! -f "${RUNTIME_DIR}/php/php.exe" ]]; then
    info "Extrayendo PHP..."
    rm -rf "${RUNTIME_DIR}/php"
    mkdir -p "${RUNTIME_DIR}/php"
    unzip -q "${CACHE_DIR}/php.zip" -d "${RUNTIME_DIR}/php"

    [[ -f "${RUNTIME_DIR}/php/php-cgi.exe" ]] \
        || fail "El paquete de PHP no trae php-cgi.exe; hace falta la variante NTS."
fi

# --- MariaDB ---------------------------------------------------------------

descargar "$MARIADB_URL" "${CACHE_DIR}/mariadb.zip"

if [[ ! -f "${RUNTIME_DIR}/mariadb/bin/mysqld.exe" ]]; then
    info "Extrayendo MariaDB..."
    rm -rf "${RUNTIME_DIR}/mariadb" "${CACHE_DIR}/mariadb-tmp"
    mkdir -p "${CACHE_DIR}/mariadb-tmp"
    unzip -q "${CACHE_DIR}/mariadb.zip" -d "${CACHE_DIR}/mariadb-tmp"

    # El zip trae una carpeta raíz con el nombre de la versión.
    mv "${CACHE_DIR}/mariadb-tmp/mariadb-${MARIADB_VERSION}-winx64" "${RUNTIME_DIR}/mariadb"
    rm -rf "${CACHE_DIR}/mariadb-tmp"

    # Se recorta lo que no sirve en un terminal: pesa cientos de megas y el
    # instalador ya es grande.
    rm -rf "${RUNTIME_DIR}/mariadb/include" \
           "${RUNTIME_DIR}/mariadb/lib/plugin/debug" \
           "${RUNTIME_DIR}/mariadb/mysql-test" \
           "${RUNTIME_DIR}/mariadb/sql-bench" \
           "${RUNTIME_DIR}/mariadb/docs"
fi

# --- Caddy -----------------------------------------------------------------

descargar "$CADDY_URL" "${CACHE_DIR}/caddy.zip"

if [[ ! -f "${RUNTIME_DIR}/caddy/caddy.exe" ]]; then
    info "Extrayendo Caddy..."
    rm -rf "${RUNTIME_DIR}/caddy"
    mkdir -p "${RUNTIME_DIR}/caddy"
    unzip -q "${CACHE_DIR}/caddy.zip" -d "${RUNTIME_DIR}/caddy"
fi

# --- Certificados ----------------------------------------------------------
#
# Windows no le da a PHP un almacén de certificados usable, así que sin esto
# fallan las llamadas HTTPS al servidor online y a SUNAT.

descargar "$CACERT_URL" "${RUNTIME_DIR}/cacert.pem"

info "Stack listo en ${RUNTIME_DIR}"
du -sh "${RUNTIME_DIR}"/* 2>/dev/null || true
