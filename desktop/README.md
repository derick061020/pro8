# pro8 Terminal — instalación de escritorio con modo offline

Un `.exe` para Windows que instala pro8 completo en la PC del local. Sirve para
seguir vendiendo cuando se cae internet: la caja opera contra una base local y,
cuando vuelve la conexión, sincroniza con el pro8 online.

---

## 1. Cómo funciona

```
   PC del local (terminal)                        Servidor online
   ─────────────────────────                      ────────────────────
   pro8.exe  (bandeja del sistema)
     ├── MariaDB   :3399                          pro8 (Laravel + MySQL)
     ├── php-cgi   :9123        ── HTTPS ──▶      /api/offline/*
     ├── Caddy     :8099
     └── offline:daemon  ───────────────────▶     recibe ventas
                         ◀───────────────────     entrega maestros
```

El terminal es una instalación normal de pro8 con el módulo **Offline** en modo
`client`. Todo lo específico vive en `modules/Offline`; el launcher solo levanta
los servicios y llama comandos de artisan.

### Qué viaja en cada dirección

**Del servidor al terminal** (datos maestros, *pull*): establecimientos,
usuarios, series, productos, clientes, y del módulo Hotel los pisos, categorías,
tarifas y habitaciones.

Se copian **conservando el id del servidor**. Es la decisión de diseño que
sostiene todo lo demás: si un producto tuviera id 40 en el servidor y 12 en el
terminal, cada venta subida apuntaría a otro producto. Al compartir ids no hay
que traducir claves foráneas.

**Del terminal al servidor** (movimiento, *push*): clientes creados offline,
cajas, notas de venta, comprobantes y estadías de hotel con sus consumos, pagos
y pedidos.

Cada cambio se anota en `offline_sync_queue` con un uuid propio. El servidor usa
ese uuid para descartar reenvíos, así que un corte a mitad de una subida no
duplica nada. Los envíos fallidos se reintentan con espera creciente (1, 2, 4…
hasta 30 minutos) y después de 8 intentos quedan marcados para revisión manual
en el panel.

### Comprobantes electrónicos

No pasan por la cola genérica. El sistema ya traía su propio canal
(`Document::send_server` + `DocumentController::sendServer`) que reconstruye el
CPE en el servidor con CoreFacturalo, lo firma y lo manda a SUNAT. Se reutiliza
tal cual: es la parte con implicancias tributarias y no conviene tener dos
implementaciones de lo mismo.

### Numeración

El servidor le **presta bloques de correlativos** a cada terminal (por defecto
500 números por serie) y se compromete a no emitir números dentro de ese bloque.
El enganche está en `Functions::newNumber()`, el único punto donde el sistema
asigna correlativo.

> **Limitación conocida.** Si un bloque se agota mientras el equipo está sin
> internet, no se puede emitir hasta recuperar la conexión. Para reducirlo: se
> pide reposición automática cuando quedan menos de 100 números, el panel marca
> en rojo por debajo de 50, y el launcher avisa por globo en la bandeja. Aun
> así, un local con mucho volumen y cortes largos debería usar bloques más
> grandes (`--size`).

### Conflictos

Casi todo lo que sube el terminal es información nueva, que no compite con nada.
La excepción es el hotel: dos terminales incomunicados pueden alquilar la misma
habitación en el mismo tramo. En ese caso el servidor **no pisa el dato**: marca
la estadía como conflicto y la deja en el panel para que alguien decida.

---

## 2. Preparar el servidor online

1. **Aplicar las tablas del motor.** Con `tenancy:migrate` funcionando:

   ```bash
   php artisan tenancy:migrate --force
   ```

   Si falla (en este proyecto viene fallando), aplicar el SQL directo sobre cada
   base de tenant:

   ```bash
   mysql -u root -p tenant_miempresa < database/sql/offline_sync_engine.sql
   ```

2. **Crear el usuario del terminal y su token.** El token es el `api_token` de
   un usuario del tenant. Conviene un usuario dedicado por local, para poder
   cortarle el acceso sin afectar a nadie más.

3. **Dar de alta las series.** Si el terminal va a emitir con la misma serie que
   el servidor, no hace falta nada extra: el reparto de bloques se encarga.

---

## 3. Compilar el instalador (desde Linux)

Se compila entero desde Arch, sin necesidad de una máquina Windows.

```bash
# Dependencias de la máquina de compilación
sudo pacman -S go rsync
paru -S nsis          # NSIS no está en los repos oficiales, vive en AUR

# 1. Bajar PHP, MariaDB y Caddy para Windows (una sola vez, unos 400 MB)
./desktop/build/fetch-runtime.sh

# 2. Dejar el proyecto listo para empaquetar
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Armar el instalador
./desktop/build/build.sh
```

> Si copiás y pegás estos bloques en zsh, sacá los comentarios: zsh interactivo
> no trata `#` como comentario y falla al intentar expandir cosas como `~400`.

Resultado: `desktop/build/dist/pro8-terminal-<commit>.exe`

**Sin NSIS**, el build no se corta: genera
`desktop/build/dist/pro8-terminal-<commit>.zip`, que se descomprime en `C:\Pro8`
y se instala ejecutando `instalar.bat` como administrador. Pide los mismos datos
que el instalador gráfico, solo que por consola. Sirve para probar el terminal
sin esperar a tener el toolchain completo.

Para el ZIP se usa `zip`, `bsdtar` o `python3`, lo que haya. En Arch `bsdtar`
viene con libarchive, así que no hace falta instalar nada.

Estructura que queda instalada en la PC:

```
C:\Pro8\
  pro8.exe            launcher (bandeja del sistema)
  app\                el sistema (repositorio git para las actualizaciones)
  runtime\php\        PHP 8.1 NTS + php-cgi
  runtime\mariadb\    MariaDB 10.11
  runtime\caddy\      Caddy 2
  config\             Caddyfile, my.ini, php.ini, pairing.json
  data\mysql\         la base del negocio
  logs\               launcher, php, mariadb, caddy
```

Los puertos (8099 web, 3399 base, 9123 PHP) son poco comunes a propósito: en una
PC de tienda es normal que ya haya un XAMPP o un Laragon ocupando los habituales.

---

## 4. Instalar un terminal

1. **Exportar la base del negocio desde el servidor.** El terminal tiene que
   arrancar con los mismos datos —y los mismos ids— que el servidor:

   ```bash
   mysqldump -u root -p tenant_miempresa > tenant_miempresa.sql
   ```

2. **Copiar los archivos del negocio.** El instalador no los trae: son datos de
   un cliente concreto y no pueden viajar dentro de un paquete que se reparte.
   Del servidor, a la instalación del terminal:

   ```
   storage/app/tenancy/<uuid>/   →   C:\Pro8\app\storage\app\tenancy\<uuid>\
   ```

   Ahí vive el **certificado de firma**. Sin él el terminal arranca y vende,
   pero no puede firmar comprobantes electrónicos estando sin internet.

3. **Correr el instalador** en la PC. Pide:
   - dirección del servidor online
   - token de acceso
   - código del terminal (único por PC, ej. `T01`)
   - nombre visible (ej. `Caja principal`)
   - nombre de la base del negocio en el servidor (ej. `tenant_miempresa`)
   - el archivo `.sql` del paso 1

4. **Primer arranque.** El launcher levanta los servicios y corre
   `offline:install`, que crea las bases, restaura el respaldo, registra el
   negocio, instala el motor de sincronización y hace el pareo. Tarda varios
   minutos según el tamaño del respaldo.

5. **Verificar.** En el sistema, *Configuración → Modo offline*: debe decir
   **EN LÍNEA** y mostrar la numeración reservada.

---

## 5. Uso diario

El usuario solo abre pro8 desde el escritorio. El ícono en la bandeja muestra el
estado:

| Texto | Significado |
|---|---|
| `en línea · todo sincronizado` | Trabajando normal |
| `en línea · subiendo N` | Sincronizando lo que quedó pendiente |
| `SIN CONEXIÓN · N sin subir` | Vendiendo offline; se subirá solo |
| `en línea · N con error` | Hay cambios rechazados: revisar el panel |

Menú de la bandeja: abrir pro8, sincronizar ahora, actualizar el sistema y ver
los registros.

El panel del sistema (*Modo offline*) muestra lo mismo con más detalle y permite
reservar más numeración o reintentar los cambios trabados.

---

## 6. Actualizaciones

El terminal se actualiza desde el repositorio git:

```
Bandeja → Actualizar sistema
```

o a mano:

```bash
php artisan tenancy:run offline:update
```

Hace `git fetch` + `git reset --hard` de la rama de despliegue, corre las
migraciones y limpia cachés. **La copia del código es descartable**: cualquier
cambio hecho a mano en los archivos se pisa. La base de datos y el `.env` no se
tocan nunca.

Si hay ventas sin sincronizar el comando se niega a actualizar (una migración
podría cambiar la forma de lo que está en la cola). Con `--force` se salta esa
protección.

### Rama de despliegue

El terminal apunta a una rama que incluye `vendor/` y `public/build/`, para que
la PC del cliente no necesite composer ni node. En el repositorio actual esas
carpetas están en `.gitignore`, así que hay que armar la rama de despliegue
aparte cada vez que se publica una versión.

> **Sobre la decisión de actualizar con `git pull` directo:** deja el código
> fuente completo y un token de acceso en la máquina del cliente. Se mitiga
> usando una *deploy key* de solo lectura y una rama que contenga únicamente lo
> necesario para correr, pero si en algún momento se quiere evitar del todo, la
> alternativa es publicar paquetes firmados en GitHub Releases y que el updater
> baje eso.

---

## 7. Comandos

Todos se corren dentro de `C:\Pro8\app` con el PHP embebido
(`..\runtime\php\php.exe`). Como el sistema es multi-tenant, van con
`tenancy:run`:

```bash
php artisan tenancy:run offline:status      # estado del terminal
php artisan tenancy:run offline:sync        # sincronizar ahora
php artisan tenancy:run offline:daemon      # sincronización continua
php artisan tenancy:run offline:update      # actualizar desde git
php artisan tenancy:run offline:pair        # reparear con el servidor
```

`offline:install` es la excepción: corre sin `tenancy:run`, porque justamente
crea el tenant.

### El arranque en frío tiene un orden obligado

Cualquier comando de artisan levanta el sistema entero, y el sistema consulta la
base apenas arranca (`AppServiceProvider` pregunta por la tabla
`configurations`). O sea: **sin base central no corre ni el comando que iba a
crear la base central**. Por eso el launcher, antes de tocar artisan, crea la
base con el cliente de MariaDB y genera el `APP_KEY` si el `.env` todavía trae
el marcador de la plantilla (`Stack.ensureCentralDatabase` / `ensureAppKey`).
Con la base creada y vacía el arranque sí funciona: la consulta responde que no
existe la tabla y sigue de largo.

Una vez instalado, **no hay que volver a correr `key:generate`**: hyn deriva de
esa clave la contraseña con la que se conecta a la base del negocio, así que
cambiarla deja el sistema sin acceso a sus propios datos. El launcher la genera
una sola vez, cuando el `.env` todavía trae el marcador de la plantilla.

Del lado de PHP, `offline:install` restaura el respaldo **antes** de registrar
el negocio, y registra con las migraciones automáticas de hyn desactivadas: la
base ya viene completa del servidor, y correr la historia entera de migraciones
sobre un sistema con años encima es justamente lo que suele fallar. Lo único que
se aplica después es `database/sql/offline_sync_engine.sql`, que es idempotente.

`offline:status --json` devuelve el estado en JSON. Ese formato es contrato con
el launcher (`desktop/launcher/status.go`): si cambia, hay que actualizar los
dos lados.

---

## 8. Diagnóstico

| Síntoma | Dónde mirar |
|---|---|
| No abre | `C:\Pro8\logs\launcher.log` |
| Error 500 | `C:\Pro8\app\storage\logs\` y `C:\Pro8\logs\php-error.log` |
| No sincroniza | Panel *Modo offline* → últimas sincronizaciones |
| No arranca la base | `C:\Pro8\logs\mariadb.log` |
| No puede facturar | Panel → numeración reservada (bloque agotado) |

La tabla `offline_sync_logs` guarda cada operación de sincronización con su
resultado, que es lo primero a revisar en sitio.

---

## 9. Estado de este desarrollo

Lo que está escrito y verificado hasta dónde se pudo:

- **Motor de sincronización (PHP)** — completo. Sintaxis validada; el panel
  compila con Vite.
- **Panel web** — completo y compilando.
- **Launcher (Go)** — compila para Windows (`GOOS=windows`, modo GUI) y **ya
  corrió en Windows**: levanta MariaDB, php-cgi y Caddy, y el sistema responde
  en `http://127.0.0.1:8099`.
- **Instalador (NSIS)** — compila y se instaló en `C:\Pro8`.
- **Prueba de punta a punta** — pendiente. No se probó el pareo contra un
  servidor real: la primera corrida murió en `offline:install` porque el PHP
  embebido era 8.1 y el `vendor/` empaquetado pide 8.2 (ver más abajo).

Lo que hay que probar antes de ponerlo en un local:

1. Instalar en una PC de prueba con un respaldo real.
2. Emitir un comprobante con el cable de red desconectado y verificar que toma
   el correlativo del bloque reservado.
3. Reconectar y confirmar que sube sin duplicar.
4. Repetir con una estadía de hotel.

### La versión de PHP no es libre

El `vendor/` se empaqueta tal como quedó en la máquina de compilación, así que
el PHP embebido tiene que ser al menos el que pide ese `vendor/`. Hoy son 8.2
(symfony 7.4), y por eso `fetch-runtime.sh` fija **8.2.30**: la última rama que
Laravel 9.52 soporta oficialmente.

Si algún día se resuelve el `vendor/` con dependencias que pidan más, `build.sh`
corta el build comparando `runtime/php/.version` contra
`vendor/composer/platform_check.php`; la salida dice qué versión subir en
`fetch-runtime.sh`. Sin ese control el instalador sale bien y el terminal recién
falla en el cliente, con *"Composer detected issues in your platform"* en cada
comando de artisan.
