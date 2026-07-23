<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use App\Services\SeriesResolver;

class EstablishmentController extends Controller
{
    /**
     *
     * Listado de establecimientos con sus series de documentos populares
     * (factura, boleta, nota de venta).
     *
     * Las series se devuelven con los mismos campos que `getApiRowResource()`
     * salvo `establishment_id`, ya que se agrupa por establecimiento.
     *
     * @return array
     */
    public function withSeries()
    {
        $document_type_ids = ['01', '03', '80'];

        $series_by_establishment = app(SeriesResolver::class)->applyContext(Series::whereIn('document_type_id', $document_type_ids))
            ->get()
            ->groupBy('establishment_id');

        $user_series = auth()->user()->default_document_types
            ->map(fn($item) => $item->only(['id', 'series_id', 'document_type_id']));

        $establishments = Establishment::whereFilterWithOutRelations()
            ->select('id', 'description', 'address', 'code')
            ->get()
            ->map(function ($establishment) use ($series_by_establishment) {
                $series = $series_by_establishment->get($establishment->id, collect())
                    ->map(function (Series $row) {
                        $resource = $row->getApiRowResource();
                        unset($resource['establishment_id']);
                        return $resource;
                    })
                    ->values();

                return [
                    'id' => $establishment->id,
                    'description' => $establishment->description,
                    'address' => $establishment->address,
                    'trade_address' => $establishment->trade_address,
                    'code' => $establishment->code,
                    'series' => $series,
                ];
            });

        $merge = collect(['establishments' => $establishments, 'default_document_types' => $user_series]);

        return [
            'success' => true,
            'data' => $merge,
        ];
    }
}
