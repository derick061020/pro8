<?php

namespace Modules\ApiPeruDev\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\ApiPeruDev\Data\ServiceData;
use Modules\Services\Data\DocumentApiResolver;

class ServiceController extends Controller
{
    public function service($type, $number)
    {
        // Pasa por el resolver para respetar el proveedor configurado
        // (apiperu / sunat) con fallback automatico a la otra API.
        return (new DocumentApiResolver())->service($type, $number);
    }

    public function exchange($date)
    {
        return (new ServiceData)->exchange($date);
    }
}
