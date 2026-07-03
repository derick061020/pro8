# ItemFormLayout

Persistencia del layout (barra fija personalizable) del formulario de ítem.

## Modelo

Tabla `item_form_layouts` (tenant DB). Una fila por `variant`:

| Columna             | Tipo            | Notas                                                 |
| ------------------- | --------------- | ----------------------------------------------------- |
| `id`                | bigint PK       |                                                       |
| `variant`           | string(32)      | UNIQUE. Valores: `standard`, `ecommerce`, `restaurant`. |
| `pinned_fields`     | json            | `[{ field_key, width (1..12), order }, ...]`          |
| `updated_by_user_id`| bigint nullable | Auditoría informativa.                                 |
| `created_at` / `updated_at` | timestamps |                                                       |

La configuración es **única por tenant** (no por usuario). Cualquier usuario con acceso al módulo de ítems puede editarla y afecta a todos.

## Endpoints (web, auth + locked.tenant)

- `GET /item-form-layout/{variant}` → `{ success, data: { variant, pinned_fields } }`. Devuelve `pinned_fields: []` si no hay fila guardada.
- `PUT /item-form-layout/{variant}` con `{ pinned_fields: [{ field_key, width, order }] }` → upsert.

El controller normaliza `order` por posición tras `usort` y valida `width ∈ [1..12]` y `field_key` no duplicados vía `UpdateItemFormLayoutRequest`.

`field_key` viene de un catálogo definido en frontend (`resources/js/views/tenant/items/_form_fields_catalog.js`); el backend no lo valida contra una lista cerrada para permitir extender el catálogo sin migración.

## Variantes soportadas hoy en el form unificado

El form canónico es `resources/js/views/tenant/items/form.vue`. Su prop `variant` decide qué pestañas se muestran:

| Variant      | Pestañas visibles                                                          | Endpoint persistencia ítem |
| ------------ | -------------------------------------------------------------------------- | -------------------------- |
| `standard`   | General, Almacenes, Presentaciones, Atributos, Compra, Información adicional, Producción (condicional) | `/items` |
| `ecommerce`  | General, Información adicional                                              | `/items` |
| `restaurant` | General, Insumos, Modificadores                                             | `/items` |

## Variantes hoy fuera del form unificado

Estas vistas siguen usando su propio `form.vue`. Documentadas aquí para evaluar migración futura — la mayoría tiene un modelo de datos distinto y no comparte endpoint con `/items`:

| Vista                                                              | Endpoint              | ¿Migrable a `variant`?                                                                 |
| ------------------------------------------------------------------ | --------------------- | -------------------------------------------------------------------------------------- |
| `modules/Item/Resources/assets/js/views/incentives/form.vue`      | `/incentives`         | No — micro-form, otro modelo. Sólo Producto + comisión.                                |
| `modules/Purchase/Resources/assets/js/views/fixed_asset_items/form.vue` | `/fixed-asset/items` | Posible `variant: fixed_asset` futuro — sólo precio compra + datos básicos.            |
| `modules/Production/Resources/assets/js/view/item_production/form.vue` | `/item-production`    | No — composites con tabla de ítems.                                                    |
| `modules/Ecommerce/Resources/assets/js/views/item_sets/form.vue`  | `/ecommerce/item-sets`| No — modelo de sets con tabla de componentes.                                          |
| `resources/js/views/tenant/item_sets/form.vue`                    | `/item-sets`          | No — idem sets.                                                                        |
| `modules/Digemid/Resources/assets/js/view/index.vue`              | `/items` (flag `pharmacy`) | Ya importa el form principal. Hoy pasa `pharmacy=true`; podría volverse `variant=pharmacy` aparte. |

## Cómo añadir un nuevo campo pineable

1. Agregar entrada en `resources/js/views/tenant/items/_form_fields_catalog.js`.
2. Soportar su slot por `field_key` en `resources/js/views/tenant/items/form.vue` (dentro del `<item-form-pinned-bar>`).
3. Marcar el wrapper original del campo con `v-show="!isPinned('<field_key>')"` para evitar duplicación.

No requiere migración — `pinned_fields` es JSON libre.
