<?php

namespace App\Console\Commands;

use App\Models\Tenant\Item;
use App\Models\Tenant\ItemUnitType;
use App\Models\Tenant\ItemUnitTypePrice;
use App\Models\Tenant\PriceLabel;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;

class FillItemPriceLabelsCommand extends Command
{
    /**
     * --tenant=    : id(s) de website separados por coma. Vacío = todos.
     * --overwrite  : reemplaza el precio aunque ya exista (por defecto solo llena los faltantes).
     * --dry        : no escribe, solo muestra lo que haría.
     *
     * @var string
     */
    protected $signature = 'items:fill-price-labels {--tenant=} {--overwrite} {--dry}';

    /**
     * @var string
     */
    protected $description = 'Crea la presentación base faltante y llena item_unit_type_prices para cada etiqueta de precio activa usando el precio base del producto';

    public function handle()
    {
        $tenants = explode(',', (string) $this->option('tenant'));

        $websites = (count($tenants) === 1 && $tenants[0] === '')
            ? Website::all()
            : Website::whereIn('id', $tenants)->get();

        if ($websites->isEmpty()) {
            $this->error('No se encontraron tenants para procesar.');
            return 1;
        }

        foreach ($websites as $website) {
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $tenancy->tenant($website);

            $this->line("== Tenant #{$website->id} ({$website->uuid}) ==");
            $this->fillForCurrentTenant();
        }

        return 0;
    }

    private function fillForCurrentTenant(): void
    {
        $labels = PriceLabel::where('is_active', true)->get();

        if ($labels->isEmpty()) {
            $this->warn('  Sin etiquetas de precio activas, se omite.');
            return;
        }

        $overwrite = (bool) $this->option('overwrite');
        $dry = (bool) $this->option('dry');

        $unitTypesCreated = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $noPrice = 0;
        $services = 0;

        // Iteramos productos: necesitan al menos una presentación para tener listas de precios.
        Item::with('item_unit_types')->chunkById(200, function ($items) use ($labels, $overwrite, $dry, &$unitTypesCreated, &$created, &$updated, &$skipped, &$noPrice, &$services) {
            foreach ($items as $item) {
                // Los servicios (ZZ) no usan presentaciones ni listas de precios.
                if ($item->unit_type_id === 'ZZ') {
                    $services++;
                    continue;
                }

                $unitTypes = $item->item_unit_types;

                // Si no tiene presentación, creamos la base a partir del propio item.
                if ($unitTypes->isEmpty()) {
                    $basePrice = $this->resolveBasePrice(null, $item);
                    if ($basePrice === null) {
                        $noPrice++;
                        continue;
                    }

                    if ($dry) {
                        $unitTypesCreated++;
                        $created += $labels->count();
                        continue;
                    }

                    $unitType = $this->createBaseUnitType($item, $basePrice);
                    $unitTypesCreated++;
                    $unitTypes = collect([$unitType]);
                }

                foreach ($unitTypes as $unitType) {
                    $basePrice = $this->resolveBasePrice($unitType, $item);
                    if ($basePrice === null) {
                        $noPrice++;
                        continue;
                    }

                    $existing = ItemUnitTypePrice::where('item_unit_type_id', $unitType->id)
                        ->pluck('id', 'price_label_id');

                    foreach ($labels as $label) {
                        if ($existing->has($label->id)) {
                            if (!$overwrite) {
                                $skipped++;
                                continue;
                            }
                            if (!$dry) {
                                ItemUnitTypePrice::where('id', $existing->get($label->id))
                                    ->update(['price' => $basePrice, 'is_active' => true]);
                            }
                            $updated++;
                            continue;
                        }

                        if (!$dry) {
                            ItemUnitTypePrice::create([
                                'item_unit_type_id' => $unitType->id,
                                'price_label_id'    => $label->id,
                                'price'             => $basePrice,
                                'is_active'         => true,
                            ]);
                        }
                        $created++;
                    }
                }
            }
        });

        $prefix = $dry ? '  [DRY] ' : '  ';
        $this->info("{$prefix}presentaciones creadas: {$unitTypesCreated} | precios creados: {$created} | actualizados: {$updated} | omitidos (ya existían): {$skipped} | sin precio base: {$noPrice} | servicios omitidos: {$services}");
    }

    /**
     * Crea la presentación base de un item replicando su unidad.
     * OJO: price_default es un selector pequeño (1/2/3), NO el monto.
     * El precio real vive en item_unit_type_prices; price1 se llena por compat legacy.
     * Mismo criterio de barcode que ItemController::store.
     */
    private function createBaseUnitType(Item $item, float $basePrice): ItemUnitType
    {
        $unitType = ItemUnitType::create([
            'item_id'       => $item->id,
            'description'   => $item->description,
            'unit_type_id'  => $item->unit_type_id,
            'quantity_unit' => 1,
            'price1'        => $basePrice,
            'price2'        => 0,
            'price3'        => 0,
            'price_default' => 1,
        ]);

        $unitType->barcode = $unitType->id . $unitType->unit_type_id . $unitType->quantity_unit;
        $unitType->save();

        return $unitType;
    }

    /**
     * Precio base: price1 legacy de la presentación -> sale_unit_price del item.
     * (price_default es un flag, no un monto.)
     */
    private function resolveBasePrice(?ItemUnitType $unitType, Item $item): ?float
    {
        if ($unitType && $unitType->price1 !== null && (float) $unitType->price1 > 0) {
            return (float) $unitType->price1;
        }

        $salePrice = $item->sale_unit_price ?? null;
        if ($salePrice !== null && (float) $salePrice > 0) {
            return (float) $salePrice;
        }

        return null;
    }
}
