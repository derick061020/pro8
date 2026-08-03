<?php

namespace Modules\Offline\Services;

use App\Models\Tenant\Cash;
use App\Models\Tenant\Document;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Item;
use App\Models\Tenant\Person;
use App\Models\Tenant\PersonAddress;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNoteItem;
use App\Models\Tenant\SaleNotePayment;
use App\Models\Tenant\Series;
use App\Models\Tenant\User;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\HotelFloor;
use Modules\Hotel\Models\HotelRate;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentItem;
use Modules\Hotel\Models\HotelRentItemPayment;
use Modules\Hotel\Models\HotelRentOrder;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRoomRate;

/**
 * Catálogo de lo que se sincroniza entre el terminal Windows y el servidor
 * online, y de cómo hacerlo.
 *
 * Hay dos direcciones y no se mezclan:
 *
 *  - PULL  (servidor -> terminal): datos maestros. El servidor manda la
 *          verdad y el terminal la copia tal cual, **conservando el id
 *          original**. Gracias a eso un item_id o un hotel_room_id significan
 *          lo mismo de los dos lados y no hay que traducir claves foráneas.
 *
 *  - PUSH  (terminal -> servidor): lo que se genera vendiendo. El servidor le
 *          asigna un id nuevo y el terminal se guarda la equivalencia en
 *          offline_id_maps.
 *
 * `priority` define el orden de viaje: primero los clientes nuevos, después
 * las cajas, después los comprobantes que los referencian.
 */
class EntityRegistry
{
    public const PUSH = 'push';
    public const PULL = 'pull';

    /** Se reconstruye en el servidor con el motor de facturación (CoreFacturalo). */
    public const STRATEGY_FACTURALO = 'facturalo';

    /** Se aplica campo por campo sobre el modelo Eloquent. */
    public const STRATEGY_GENERIC = 'generic';

    private static ?array $cache = null;

    /**
     * @return array<string, array> definiciones indexadas por alias
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        return self::$cache = array_merge(self::pullDefinitions(), self::pushDefinitions());
    }

    public static function get(string $alias): ?array
    {
        return self::all()[$alias] ?? null;
    }

    public static function has(string $alias): bool
    {
        return isset(self::all()[$alias]);
    }

    /**
     * Definiciones de una dirección, ya ordenadas por prioridad.
     */
    public static function forDirection(string $direction): array
    {
        $entities = array_filter(
            self::all(),
            fn (array $definition) => $definition['direction'] === $direction
        );

        uasort($entities, fn ($a, $b) => $a['priority'] <=> $b['priority']);

        return $entities;
    }

    /**
     * Alias correspondiente a una clase de modelo, o null si no se sincroniza.
     *
     * Person aparece en las dos direcciones (baja como 'customer' y sube como
     * 'person'), así que hay que decir cuál se busca.
     */
    public static function aliasForModel(string $modelClass, string $direction = self::PUSH): ?string
    {
        foreach (self::forDirection($direction) as $alias => $definition) {
            if ($definition['model'] === $modelClass) {
                return $alias;
            }
        }

        return null;
    }

    public static function modelFor(string $alias): ?string
    {
        return self::get($alias)['model'] ?? null;
    }

    /**
     * Entidades que el trait observa para encolar automáticamente.
     */
    public static function watched(): array
    {
        return array_filter(
            self::forDirection(self::PUSH),
            fn (array $definition) => $definition['watch'] ?? false
        );
    }

    // -----------------------------------------------------------------------
    // Datos maestros que bajan del servidor
    // -----------------------------------------------------------------------

    private static function pullDefinitions(): array
    {
        return [
            'establishment' => [
                'model'     => Establishment::class,
                'label'     => 'Establecimientos',
                'direction' => self::PULL,
                'priority'  => 5,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'user' => [
                'model'     => User::class,
                'label'     => 'Usuarios',
                'direction' => self::PULL,
                'priority'  => 6,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
                // La contraseña viaja ya hasheada para poder iniciar sesión
                // en el terminal sin internet.
                'columns'   => null,
            ],

            'series' => [
                'model'     => Series::class,
                'label'     => 'Series',
                'direction' => self::PULL,
                'priority'  => 7,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'item' => [
                'model'     => Item::class,
                'label'     => 'Productos',
                'direction' => self::PULL,
                'priority'  => 10,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'customer' => [
                'model'     => Person::class,
                'label'     => 'Clientes y proveedores',
                'direction' => self::PULL,
                'priority'  => 11,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'hotel_floor' => [
                'model'     => HotelFloor::class,
                'label'     => 'Pisos',
                'direction' => self::PULL,
                'priority'  => 20,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'hotel_category' => [
                'model'     => HotelCategory::class,
                'label'     => 'Categorías de habitación',
                'direction' => self::PULL,
                'priority'  => 21,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'hotel_rate' => [
                'model'     => HotelRate::class,
                'label'     => 'Tarifas',
                'direction' => self::PULL,
                'priority'  => 22,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'hotel_room' => [
                'model'     => HotelRoom::class,
                'label'     => 'Habitaciones',
                'direction' => self::PULL,
                'priority'  => 23,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],

            'hotel_room_rate' => [
                'model'     => HotelRoomRate::class,
                'label'     => 'Tarifas por habitación',
                'direction' => self::PULL,
                'priority'  => 24,
                'strategy'  => self::STRATEGY_GENERIC,
                'children'  => [],
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // Movimiento generado en el terminal que sube al servidor
    // -----------------------------------------------------------------------

    private static function pushDefinitions(): array
    {
        return [
            // Cliente dado de alta durante el corte de internet. Viaja primero
            // porque los comprobantes y las estadías lo referencian.
            'person' => [
                'model'     => Person::class,
                'label'     => 'Clientes creados offline',
                'direction' => self::PUSH,
                'priority'  => 10,
                'strategy'  => self::STRATEGY_GENERIC,
                'watch'     => true,
                'children'  => [
                    'addresses' => ['fk' => 'person_id', 'model' => PersonAddress::class],
                ],
                // Si el cliente ya existe en el servidor con el mismo documento
                // se reutiliza en vez de duplicarlo.
                'match'     => ['identity_document_type_id', 'number'],
                'translate' => [],
            ],

            'cash' => [
                'model'     => Cash::class,
                'label'     => 'Cajas',
                'direction' => self::PUSH,
                'priority'  => 20,
                'strategy'  => self::STRATEGY_GENERIC,
                'watch'     => true,
                'children'  => [],
                'match'     => [],
                'translate' => [],
            ],

            // Comprobante electrónico.
            //
            // No pasa por la bandeja de salida genérica: el sistema ya trae un
            // canal propio (Document::send_server + DocumentController::sendServer)
            // que reconstruye el comprobante en el servidor con CoreFacturalo,
            // lo firma y lo manda a SUNAT. Duplicar esa lógica sería arriesgado,
            // así que el motor offline solo se encarga de dispararla.
            //
            // Por eso watch = false: el pendiente se mide con send_server, no
            // con offline_sync_queue.
            'document' => [
                'model'     => Document::class,
                'label'     => 'Comprobantes',
                'direction' => self::PUSH,
                'priority'  => 30,
                'strategy'  => self::STRATEGY_FACTURALO,
                'watch'     => false,
                'children'  => [],
                'match'     => ['series', 'number', 'document_type_id'],
                'translate' => [],
            ],

            'sale_note' => [
                'model'     => SaleNote::class,
                'label'     => 'Notas de venta',
                'direction' => self::PUSH,
                'priority'  => 31,
                'strategy'  => self::STRATEGY_GENERIC,
                'watch'     => true,
                'children'  => [
                    'items'    => ['fk' => 'sale_note_id', 'model' => SaleNoteItem::class],
                    'payments' => ['fk' => 'sale_note_id', 'model' => SaleNotePayment::class],
                ],
                'match'     => ['external_id'],
                'translate' => ['customer_id' => 'person'],
            ],

            // Estadía de hotel con sus consumos, pagos y pedidos.
            'hotel_rent' => [
                'model'     => HotelRent::class,
                'label'     => 'Estadías de hotel',
                'direction' => self::PUSH,
                'priority'  => 40,
                'strategy'  => self::STRATEGY_GENERIC,
                'watch'     => true,
                'children'  => [
                    'items' => [
                        'fk'       => 'hotel_rent_id',
                        'model'    => HotelRentItem::class,
                        'children' => [
                            'payments' => ['fk' => 'hotel_rent_item_id', 'model' => HotelRentItemPayment::class],
                        ],
                    ],
                    'orders' => ['fk' => 'hotel_rent_id', 'model' => HotelRentOrder::class],
                ],
                'match'     => [],
                'translate' => [
                    'customer_id' => 'person',
                    'document_id' => 'document',
                ],
                // Dos terminales no pueden alquilar la misma habitación en el
                // mismo tramo: el servidor lo detecta y marca conflicto en vez
                // de pisar el dato.
                'conflict'  => 'hotel_room_overlap',
            ],
        ];
    }
}
