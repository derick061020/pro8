<?php

namespace Modules\WhatsAppBot\Services\Tools;

use App\Models\Tenant\Item;

class SearchItemsTool implements ToolInterface
{
    private const IGV_RATE = 0.18;
    private const MAX_RESULTS = 10;
    private const TOO_MANY_THRESHOLD = 8;

    public function name(): string
    {
        return 'search_items';
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => 'Busca productos del catálogo por nombre, código interno o código de barras. Devuelve nombre, código y precio con IGV incluido.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'Término de búsqueda: nombre parcial del producto, código interno o código de barras.',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $query = trim($arguments['query'] ?? '');

        if ($query === '') {
            return ['matches' => [], 'message' => 'Falta el término de búsqueda.'];
        }

        // Tokenizamos el query. Cada token con sus variantes (singular/plural)
        // se busca como un grupo en OR; entre tokens hay AND, asi "blusa azul L"
        // exige que el producto contenga "blusa" Y "azul" Y "L" (en cualquier orden).
        $tokenGroups = $this->buildTokenGroups($query);

        $baseQuery = Item::query()
            ->where('item_type_id', '01')
            ->where(function ($q) use ($query, $tokenGroups) {
                // Codigo exacto (atajo): si coincide con un codigo, va por ese path.
                $q->where('item_code', $query)
                  ->orWhere('internal_id', $query)
                  ->orWhere('item_code_gs1', $query);

                // O cumple los grupos AND.
                $q->orWhere(function ($and) use ($tokenGroups) {
                    foreach ($tokenGroups as $group) {
                        $and->where(function ($or) use ($group) {
                            foreach ($group['variants'] as $v) {
                                $patterns = $group['word_boundary']
                                    ? ["% {$v} %", "% {$v}", "{$v} %", $v]
                                    : ["%{$v}%"];
                                foreach ($patterns as $p) {
                                    $or->orWhere('name', 'like', $p)
                                       ->orWhere('description', 'like', $p)
                                       ->orWhere('second_name', 'like', $p);
                                }
                            }
                        });
                    }
                });
            });

        $totalCount = (clone $baseQuery)->count();

        // Si hay demasiados, no listamos. Devolvemos un sample chico y le
        // pedimos al LLM que pida al vendedor que sea mas especifico.
        if ($totalCount > self::TOO_MANY_THRESHOLD) {
            $sample = (clone $baseQuery)
                ->orderBy('name')
                ->limit(3)
                ->get(['name', 'description', 'second_name'])
                ->map(fn ($item) => $item->description ?: $item->name)
                ->all();

            return [
                'too_many' => true,
                'total_count' => $totalCount,
                'sample_names' => $sample,
                'message' => "Hay {$totalCount} productos que coinciden. Pide al vendedor que sea mas especifico (color, talla, marca, presentacion, etc.).",
            ];
        }

        $items = $baseQuery
            ->with(['warehouses:id,item_id,stock'])
            ->orderByRaw("CASE WHEN LOWER(name) = LOWER(?) THEN 0 WHEN LOWER(name) LIKE LOWER(?) THEN 1 ELSE 2 END", [$query, $query . '%'])
            ->orderBy('name')
            ->limit(self::MAX_RESULTS)
            ->get(['id', 'name', 'description', 'second_name', 'item_code', 'internal_id', 'sale_unit_price', 'has_igv', 'unit_type_id']);

        return [
            'count' => $items->count(),
            'total_count' => $totalCount,
            'matches' => $items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->description ?: $item->name,
                'alternative_name' => $item->name !== ($item->description ?: $item->name) ? $item->name : null,
                'code' => $item->internal_id ?: $item->item_code,
                'price_with_igv' => $this->priceWithIgv($item),
                'stock' => round((float) $item->warehouses->sum('stock'), 2),
                'unit_type_id' => $item->unit_type_id,
                'unit_description' => $this->unitDescription($item->unit_type_id),
            ])->all(),
        ];
    }

    /**
     * Divide el query en tokens y devuelve, por cada token >=3 chars,
     * un array con la palabra original y su variante singular (si aplica).
     * Tokens muy cortos (<3 chars) se descartan para no inundar la búsqueda
     * con resultados irrelevantes (ej. "L", "S", "M" no son discriminantes
     * por LIKE; el LLM puede mapear talla=L a "Talla L" en el nombre).
     */
    private function buildTokenGroups(string $query): array
    {
        $tokens = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $groups = [];

        foreach ($tokens as $token) {
            $len = mb_strlen($token);
            // Tokens muy cortos (1-2 chars como tallas L/M/S/XL) se buscan con
            // word boundary para no matchear cualquier substring (la L de
            // "Azul"). Tokens largos van como LIKE %x% normal.
            $wordBoundary = $len <= 2;

            $variants = [$token];
            $lower = mb_strtolower($token);
            if ($len > 4 && str_ends_with($lower, 'es')) {
                $variants[] = mb_substr($token, 0, $len - 2);
            } elseif ($len > 3 && str_ends_with($lower, 's')) {
                $variants[] = mb_substr($token, 0, $len - 1);
            }

            $groups[] = [
                'variants' => array_values(array_unique($variants)),
                'word_boundary' => $wordBoundary,
            ];
        }

        if (empty($groups)) {
            $groups[] = ['variants' => [$query], 'word_boundary' => false];
        }

        return $groups;
    }

    private function unitDescription(?string $code): string
    {
        return match ($code) {
            'NIU' => 'unidades',
            'KGM' => 'kilogramos',
            'GRM' => 'gramos',
            'LTR' => 'litros',
            'MTR' => 'metros',
            'BLL' => 'barriles',
            'PK' => 'paquetes',
            'BX' => 'cajas',
            'ZZ' => 'servicios',
            default => $code ?: 'unidades',
        };
    }

    private function priceWithIgv(Item $item): float
    {
        $price = (float) $item->sale_unit_price;
        return $item->has_igv ? round($price, 2) : round($price * (1 + self::IGV_RATE), 2);
    }
}
