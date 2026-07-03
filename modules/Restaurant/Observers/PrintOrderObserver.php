<?php

namespace Modules\Restaurant\Observers;

use Modules\Restaurant\Models\PrintOrder;
use Modules\Restaurant\Services\PrintPublisherService;

class PrintOrderObserver
{
    public function __construct(
        private readonly PrintPublisherService $publisher
    ) {}

    /**
     * Al crear una PrintOrder, publica inmediatamente en Redis.
     * No hay polling ni SSE — BuhoPrinter escucha el canal.
     */
    public function created(PrintOrder $order): void
    {
        $this->publisher->publish($order);
    }
}
