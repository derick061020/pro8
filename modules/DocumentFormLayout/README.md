# DocumentFormLayout

Persistencia del layout (datos generales personalizables) del formulario de generación de
comprobantes (`resources/js/views/tenant/documents/invoice_generate.vue`).

Análogo a `modules/ItemFormLayout`, pero para documentos.

## Modelo

Tabla `document_form_layouts` (tenant DB). Una fila por `variant`:

| Columna             | Tipo            | Notas                                                 |
| ------------------- | --------------- | ----------------------------------------------------- |
| `id`                | bigint PK       |                                                       |
| `variant`           | string(32)      | UNIQUE. Valor soportado hoy: `invoice`.               |
| `pinned_fields`     | json            | `[{ field_key, width (1..12), order }, ...]`          |
| `updated_by_user_id`| bigint nullable | Auditoría informativa.                                 |
| `created_at` / `updated_at` | timestamps |                                                       |

La configuración es **única por tenant** (no por usuario). Afecta a todos los usuarios.

## Endpoints (web, auth + locked.tenant)

- `GET /document-form-layout/{variant}` → `{ success, data: { variant, pinned_fields } }`.
  Devuelve `pinned_fields: []` si no hay fila guardada.
- `PUT /document-form-layout/{variant}` con `{ pinned_fields: [{ field_key, width, order }] }` → upsert.

`field_key` proviene del catálogo de frontend
(`resources/js/views/tenant/documents/_document_form_fields_catalog.js`); el backend no lo valida
contra una lista cerrada para permitir extender el catálogo sin migración.

## Cómo añadir un nuevo campo pineable

1. Agregar entrada en `resources/js/views/tenant/documents/_document_form_fields_catalog.js`.
2. Soportar su slot por `field_key` en `invoice_generate.vue` (dentro de `<document-form-pinned-bar>`).
3. Si el campo tiene una ubicación original (p. ej. en Información Adicional), marcarla con
   `v-show="!isLayoutPinned('<field_key>')"` para evitar duplicación.

No requiere migración — `pinned_fields` es JSON libre.
