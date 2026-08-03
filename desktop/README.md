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
sudo pacman -S go rsync nsis

# 1. Bajar PHP, MariaDB y Caddy para Windows (una sola vez, ~400 MB)
./desktop/build/fetch-runtime.sh

# 2. Dejar el proyecto listo para empaquetar
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Armar el instalador
./desktop/build/build.sh
```

Resultado: `desktop/build/dist/pro8-terminal-<commit>.exe`

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

2. **Correr el instalador** en la PC. Pide:
   - dirección del servidor online
   - token de acceso
   - código del terminal (único por PC, ej. `T01`)
   - nombre visible (ej. `Caja principal`)
   - el archivo `.sql` del paso 1

3. **Primer arranque.** El launcher levanta los servicios y corre
   `offline:install`, que crea las bases, restaura el respaldo, registra el
   negocio y hace el pareo. Tarda varios minutos según el tamaño del respaldo.

4. **Verificar.** En el sistema, *Configuración → Modo offline*: debe decir
   **EN LÍNEA** y mostrar la numeración reservada.

> Falta un dato en el instalador: el campo `tenant_uuid` de `config\pairing.json`
> queda vacío y hay que completarlo a mano con el nombre de la base del servidor
> (ej. `tenant_miempresa`) antes del primer arranque. Está pendiente agregarlo
> como campo del instalador.

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
- **Launcher (Go)** y **instalador (NSIS)** — escritos, **sin compilar**: la
  máquina donde se desarrollaron no tiene `go` ni `makensis` instalados. Hay que
  correr `./desktop/build/build.sh` y corregir lo que aparezca.
- **Prueba de punta a punta** — pendiente. No se probó contra un servidor real
  ni en una PC con Windows.

Lo que hay que probar antes de ponerlo en un local:

1. Compilar el launcher y el instalador.
2. Instalar en una PC de prueba con un respaldo real.
3. Emitir un comprobante con el cable de red desconectado y verificar que toma
   el correlativo del bloque reservado.
4. Reconectar y confirmar que sube sin duplicar.
5. Repetir con una estadía de hotel.
