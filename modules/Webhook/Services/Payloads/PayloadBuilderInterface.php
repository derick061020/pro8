<?php

namespace Modules\Webhook\Services\Payloads;

interface PayloadBuilderInterface
{
    /**
     * Construye el contenido de `data` del payload para un modelo dado.
     *
     * @param mixed $model
     */
    public function build($model): array;
}
