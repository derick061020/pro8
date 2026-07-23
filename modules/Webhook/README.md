# Módulo Webhook — Webhooks salientes

Notifica por HTTP POST a URLs externas (n8n, ERPs, integradores) cuando ocurren
eventos en el sistema. Cada tenant configura sus suscripciones en
**Configuraciones Globales → Webhooks**: una URL + los eventos que quiere escuchar.

## Arquitectura

```
Observer Eloquent (created/updated)
        │
        ▼
WebhookDispatcher::dispatch(evento, $model)
   ├─ 1 query: suscripciones activas que escuchan el evento (early return si no hay)
   ├─ arma el envelope (uuid, evento, api_version, data del PayloadBuilder)
   ├─ crea filas webhook_deliveries (status=pending) — misma transacción que el origen
   └─ DB::connection('tenant')->afterCommit(...) → SendWebhookJob::dispatch(website_id, delivery_id)
        │   (si no hay transacción activa, encola de inmediato)
        ▼
SendWebhookJob (cola `database`, tabla jobs en BD system)
   ├─ switch al tenant: app(Environment::class)->tenant(Website::find($website_id))
   ├─ POST con Guzzle: timeout 10s, sin redirects, solo 2xx = éxito
   ├─ éxito → delivery.success + resetea consecutive_failures
   └─ fallo → reintenta con backoff: 1min, 10min, 1h, 6h, 24h (6 intentos en total)
        └─ agotados → delivery.failed + consecutive_failures++
             └─ al llegar a 10 → la suscripción se desactiva sola (disabled_at)
```

Reglas de diseño:

- **El dispatcher jamás rompe la operación de origen**: todo va en try/catch con `report()`.
- **El job solo transporta enteros** (`website_id`, `delivery_id`) — nunca modelos tenant
  serializados, porque el worker no tiene la conexión tenant configurada al arrancar.
- El reenvío manual reutiliza la misma fila de `webhook_deliveries` (mismo `event_id`),
  lo que da idempotencia al receptor.

## Tablas (BD del tenant)

| Tabla | Semántica |
|---|---|
| `webhook_subscriptions` | URL + secret + eventos suscritos + estado (activo / fallos consecutivos) |
| `webhook_deliveries` | Una fila por evento×suscripción: payload enviado, status, intentos, respuesta |

## Catálogo de eventos

| Evento | Disparador |
|---|---|
| `document.created` | Documento de venta emitido (cualquier vía: web, API, POS, móvil) |
| `document.accepted` | SUNAT aceptó el documento (CDR disponible) |
| `document.observed` | SUNAT aceptó con observaciones |
| `document.rejected` | SUNAT rechazó el documento |
| `document.voided` | Comunicación de baja aceptada (documento anulado) |
| `purchase.created` | Compra registrada |

## Formato del payload

Todos los eventos comparten el mismo envelope:

```json
{
  "id": "9f8a7b6c-....",            // uuid del evento, sirve como clave de idempotencia
  "event": "document.accepted",
  "api_version": "1.0",
  "created_at": "2026-06-11T10:30:00-05:00",
  "data": { ... }                    // depende del evento
}
```

`data` de los eventos `document.*` (replica el response del API de documentos):

```json
{
  "id": 123,
  "external_id": "ad79...",
  "number": "F001-45",
  "filename": "20123456789-01-F001-45",
  "state_type_id": "05",
  "state_type_description": "Aceptado",
  "number_to_letter": "CIENTO DIECIOCHO CON 00/100 SOLES",
  "hash": "...",
  "qr": "...",
  "date_of_issue": "2026-06-11",
  "document_type_id": "01",
  "currency_type_id": "PEN",
  "exchange_rate_sale": 1,
  "total": "118.00",
  "customer": { "identity_document_type_id": "6", "number": "20...", "name": "..." },
  "links": {
    "xml": "https://tenant.dominio.com/downloads/document/xml/{external_id}",
    "pdf": "https://tenant.dominio.com/downloads/document/pdf/{external_id}",
    "cdr": "https://tenant.dominio.com/downloads/document/cdr/{external_id}"
  },
  "sunat_response": { "code": "0", "description": "La Factura ... ha sido aceptada" }
}
```

Notas: `links.cdr` solo tiene valor en estados con CDR (`05`, `07`);
`sunat_response` es `null` en `document.created`.

`data` de `purchase.created`: `id`, `external_id`, `number`, `date_of_issue`,
`document_type_id`, `state_type_id`, `currency_type_id`, `exchange_rate_sale`,
`total`, `supplier {identity_document_type_id, number, name}`, `items_count`.

## Headers enviados

| Header | Contenido |
|---|---|
| `Content-Type` | `application/json` |
| `X-Webhook-Event` | Nombre del evento (`document.created`, ...) |
| `X-Webhook-Id` | UUID del evento (igual en reintentos/reenvíos → idempotencia) |
| `X-Webhook-Signature` | HMAC-SHA256 del body crudo, con el secret de la suscripción |
| `User-Agent` | `FacturaloPeru-Webhooks/1.0` |

## Cómo verificar la firma (lado receptor)

La verificación es **opcional** (un endpoint de n8n funciona sin tocar nada), pero
recomendada: evita que un tercero que descubra la URL inyecte eventos falsos.

```php
$secret    = 'whsec_...'; // secret mostrado al crear la suscripción
$rawBody   = file_get_contents('php://input');   // SIEMPRE el body crudo
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';

if (!hash_equals(hash_hmac('sha256', $rawBody, $secret), $signature)) {
    http_response_code(401);
    exit;
}

$event = json_decode($rawBody, true);
// $event['event'], $event['data'], $event['id']...

http_response_code(200); // responder 2xx en menos de 10 segundos
```

Importante: verificar contra el **body crudo**; si se re-serializa el JSON la firma
no coincidirá.

## Cómo añadir un nuevo evento (3 pasos)

**Paso 1 — Registrarlo en `Services/WebhookEvents.php`:**

```php
const SALE_NOTE_CREATED = 'sale_note.created';

private static array $map = [
    // ...
    self::SALE_NOTE_CREATED => [
        'label' => 'Nota de venta emitida',
        'payload' => SaleNoteCreatedPayload::class,
    ],
];
```

Con esto los checkboxes de la UI y la validación del formulario se actualizan solos.
Si el evento depende de un cambio de estado del documento, basta una línea más en
`$documentStateMap` (reutilizando `DocumentPayload`) y no hay paso 3.

**Paso 2 — Crear el payload builder** en `Services/Payloads/` implementando
`PayloadBuilderInterface`:

```php
class SaleNoteCreatedPayload implements PayloadBuilderInterface
{
    public function build($model): array
    {
        return [ 'id' => $model->id, /* ... */ ];
    }
}
```

**Paso 3 — Disparar desde el observer del modelo** (siempre el mismo patrón de una línea):

```php
public function created(SaleNote $sale_note)
{
    app(WebhookDispatcher::class)->dispatch(WebhookEvents::SALE_NOTE_CREATED, $sale_note);
}
```

Si el modelo no tiene observer, crearlo en `app/Observers/` y registrarlo en
`AppServiceProvider::boot()`: `SaleNote::observe(SaleNoteObserver::class);`

## Operación

- **Migraciones**: `php artisan tenancy:migrate` (las tablas viven en la BD de cada tenant).
- **Worker**: los envíos salen por la cola `database` (tabla `jobs` de la BD system);
  el supervisor existente (`queue:work`) los procesa sin cambios. En dev:
  `php artisan queue:work --once`.
- **Reactivar una suscripción auto-desactivada**: botón ▶ en la pantalla de webhooks
  (resetea el contador de fallos), después de que el cliente corrija su endpoint.
- **Reenviar una entrega**: pestaña Entregas → botón reenviar. `response_body` se
  trunca a 2000 caracteres en el log.
- El secret se muestra completo **solo al crear** la suscripción; para rotarlo,
  eliminar y crear de nuevo la suscripción.
