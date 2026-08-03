#!/usr/bin/env bash
#
# Arma el instalador de pro8 para Windows desde Linux.
#
#   ./desktop/build/build.sh
#
# Pasos:
#   1. Compila el launcher (Go, cross-compile a Windows).
#   2. Prepara la copia del sistema que se va a empaquetar, con vendor/ y los
#      assets ya compilados, para que la PC del cliente no necesite composer
#      ni node.
#   3. Arma el instalador con NSIS.
#
# Requisitos en la máquina de compilación:
#   go, rsync, y haber corrido antes ./desktop/build/fetch-runtime.sh
#   (más composer y npm, para dejar vendor/ y los assets compilados)
#
# makensis es opcional: si no está, en vez del instalador con ventanas se
# genera un ZIP con un .bat que hace lo mismo por consola.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DESKTOP_DIR="$(dirname "$SCRIPT_DIR")"
PROJECT_DIR="$(dirname "$DESKTOP_DIR")"

RUNTIME_DIR="${SCRIPT_DIR}/runtime"
PAYLOAD_DIR="${SCRIPT_DIR}/payload"
DIST_DIR="${SCRIPT_DIR}/dist"

# Rama del repositorio desde la que se actualiza el terminal.
DEPLOY_BRANCH="${DEPLOY_BRANCH:-deploy/offline}"

info() { printf '\033[1;34m→\033[0m %s\n' "$1"; }
ok()   { printf '\033[1;32m✓\033[0m %s\n' "$1"; }
fail() { printf '\033[1;31m✗\033[0m %s\n' "$1" >&2; exit 1; }

for tool in go rsync; do
    command -v "$tool" >/dev/null || fail "Falta $tool. En Arch: sudo pacman -S go rsync"
done

# NSIS no está en los repos oficiales de Arch (vive en AUR: paru -S nsis).
# Sin él igual se puede entregar el terminal, como ZIP con un .bat de
# instalación, así que no se corta el build por eso.
HAS_NSIS=0
command -v makensis >/dev/null && HAS_NSIS=1

# Para el ZIP sirve cualquiera de los tres; en Arch bsdtar viene de fábrica con
# libarchive, así que en la práctica siempre hay alguno.
comprimir_zip() {
    local destino="$1" origen="$2"

    if command -v zip >/dev/null; then
        (cd "$origen" && zip -qr "$destino" .)
    elif command -v bsdtar >/dev/null; then
        (cd "$origen" && bsdtar -a -cf "$destino" .)
    elif command -v python3 >/dev/null; then
        python3 - "$destino" "$origen" <<'PY'
import os, sys, zipfile

destino, origen = sys.argv[1], sys.argv[2]

with zipfile.ZipFile(destino, "w", zipfile.ZIP_DEFLATED) as z:
    for raiz, _, archivos in os.walk(origen):
        for nombre in archivos:
            completo = os.path.join(raiz, nombre)
            z.write(completo, os.path.relpath(completo, origen))
PY
    else
        return 1
    fi
}

if [[ $HAS_NSIS -eq 0 ]]; then
    command -v zip >/dev/null || command -v bsdtar >/dev/null || command -v python3 >/dev/null \
        || fail "No hay con qué empaquetar. Instalá uno: paru -S nsis  /  sudo pacman -S zip"
fi

[[ -f "${RUNTIME_DIR}/php/php.exe" ]] \
    || fail "No está el stack de Windows. Corré primero: ./desktop/build/fetch-runtime.sh"

VERSION="$(cd "$PROJECT_DIR" && git rev-parse --short HEAD)"
info "Compilando versión ${VERSION}"

# ---------------------------------------------------------------------------
# 1. Launcher
# ---------------------------------------------------------------------------

info "Compilando el launcher..."

(
    cd "${DESKTOP_DIR}/launcher"

    # CGO apagado: es lo que permite cross-compilar a Windows desde Linux sin
    # instalar un toolchain de MinGW.
    GOOS=windows GOARCH=amd64 CGO_ENABLED=0 \
        go build -trimpath -ldflags "-H=windowsgui -s -w -X main.version=${VERSION}" \
        -o "${SCRIPT_DIR}/pro8.exe" .
)

ok "Launcher compilado ($(du -h "${SCRIPT_DIR}/pro8.exe" | cut -f1))"

# ---------------------------------------------------------------------------
# 2. Copia del sistema
# ---------------------------------------------------------------------------

info "Preparando la copia del sistema..."

rm -rf "$PAYLOAD_DIR"
mkdir -p "${PAYLOAD_DIR}/app"

# Se copia el árbol de trabajo y no un git archive, porque hacen falta vendor/
# y public/build, que no están versionados.
#
# storage/app/tenancy y storage/app/certificates quedan fuera a propósito: son
# los datos del negocio de la máquina donde se compila. Meter el certificado de
# firma de un cliente dentro de un instalador que se reparte sería un problema
# serio. En el terminal se copian aparte, junto con el respaldo de la base.
rsync -a --delete \
    --exclude '.git' \
    --exclude 'node_modules' \
    --exclude 'desktop/build/cache' \
    --exclude 'desktop/build/runtime' \
    --exclude 'desktop/build/payload' \
    --exclude 'desktop/build/dist' \
    --exclude 'storage/logs/*' \
    --exclude 'storage/framework/cache/*' \
    --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/framework/views/*' \
    --exclude 'storage/app/backups/*' \
    --exclude 'storage/app/tenancy' \
    --exclude 'storage/app/certificates' \
    --exclude '.env' \
    --exclude 'pro8-main-actualizado' \
    --exclude '*.zip' \
    "${PROJECT_DIR}/" "${PAYLOAD_DIR}/app/"

[[ -d "${PAYLOAD_DIR}/app/vendor" ]] \
    || fail "Falta vendor/. Corré: composer install --no-dev --optimize-autoloader"

[[ -d "${PAYLOAD_DIR}/app/public/build" ]] \
    || fail "Faltan los assets compilados. Corré: npm run build"

# El terminal se actualiza con git pull, así que necesita un repositorio
# apuntando a la rama de despliegue.
info "Inicializando el repositorio de actualizaciones..."
(
    cd "${PAYLOAD_DIR}/app"
    ORIGIN="$(cd "$PROJECT_DIR" && git remote get-url origin)"

    git init -q
    git remote add origin "$ORIGIN"
    git config core.autocrlf false
    # Sin objetos: el primer `offline:update` traerá lo que falte.
    printf '%s\n' "$DEPLOY_BRANCH" > .deploy-branch
)

# Configuración propia del terminal
mkdir -p "${PAYLOAD_DIR}/config" "${PAYLOAD_DIR}/runtime"
cp "${DESKTOP_DIR}/config/Caddyfile"             "${PAYLOAD_DIR}/config/"
cp "${DESKTOP_DIR}/config/my.ini"                "${PAYLOAD_DIR}/config/"
cp "${DESKTOP_DIR}/config/env.terminal.example"  "${PAYLOAD_DIR}/config/"
cp "${RUNTIME_DIR}/cacert.pem"                   "${PAYLOAD_DIR}/config/"

rsync -a "${RUNTIME_DIR}/php/"     "${PAYLOAD_DIR}/runtime/php/"
rsync -a "${RUNTIME_DIR}/mariadb/" "${PAYLOAD_DIR}/runtime/mariadb/"
rsync -a "${RUNTIME_DIR}/caddy/"   "${PAYLOAD_DIR}/runtime/caddy/"

# El php.ini va en la carpeta de PHP, que es donde lo busca php-cgi.
cp "${DESKTOP_DIR}/config/php.ini" "${PAYLOAD_DIR}/runtime/php/php.ini"

cp "${SCRIPT_DIR}/pro8.exe" "${PAYLOAD_DIR}/pro8.exe"

ok "Copia lista ($(du -sh "$PAYLOAD_DIR" | cut -f1))"

# ---------------------------------------------------------------------------
# 3. Instalador
# ---------------------------------------------------------------------------

mkdir -p "$DIST_DIR"

if [[ $HAS_NSIS -eq 1 ]]; then
    info "Armando el instalador..."

    makensis -DVERSION="$VERSION" \
             -DPAYLOAD="$PAYLOAD_DIR" \
             -DOUTFILE="${DIST_DIR}/pro8-terminal-${VERSION}.exe" \
             "${DESKTOP_DIR}/installer/pro8.nsi"

    ok "Instalador listo: ${DIST_DIR}/pro8-terminal-${VERSION}.exe"
    du -h "${DIST_DIR}/pro8-terminal-${VERSION}.exe"
else
    # Sin NSIS se entrega un ZIP con un .bat que hace lo mismo que el
    # instalador, pero preguntando por consola. Sirve para probar el terminal
    # sin esperar a tener el toolchain completo.
    info "makensis no está disponible: se genera un paquete ZIP..."

    cp "${DESKTOP_DIR}/installer/instalar.bat" "${PAYLOAD_DIR}/instalar.bat"

    ZIP_FILE="${DIST_DIR}/pro8-terminal-${VERSION}.zip"
    rm -f "$ZIP_FILE"

    comprimir_zip "$ZIP_FILE" "$PAYLOAD_DIR" || fail "Falló la compresión."

    ok "Paquete listo: ${ZIP_FILE}"
    du -h "$ZIP_FILE"

    printf '\n\033[1;33m!\033[0m %s\n' \
        "Se descomprime en C:\\Pro8 y se ejecuta instalar.bat como administrador."
    printf '  %s\n' "Para el instalador con ventanas: paru -S nsis && ./desktop/build/build.sh"
fi
