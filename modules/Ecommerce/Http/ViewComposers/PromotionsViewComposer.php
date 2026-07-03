<?php

namespace Modules\Ecommerce\Http\ViewComposers;

use App\Models\Tenant\Promotion;
use App\Models\Tenant\ConfigurationEcommerce;


class PromotionsViewComposer
{
    public function compose($view)
    {
        // El slider principal debe mostrar solo banners (o registros legacy sin type).
        $view->items = Promotion::where('apply_restaurant', 0)
            ->where(function ($query) {
                $query->whereNull('type')
                    ->orWhere('type', 'banners');
            })
            ->with('item')
            ->orderByDesc('updated_at')
            ->get();

        $config = ConfigurationEcommerce::first();
        $preferences = $config && $config->preferences ? $config->preferences : [];
        $view->full_width_banner = $preferences['full_width_banner'] ?? 0;
    }
}
