<?php

namespace Modules\Restaurant\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Modules\Restaurant\Models\PrintOrder;
use Modules\Restaurant\Models\Printer;

class PrintPublisherService
{
    /**
     * Publica una orden de impresión en el canal Redis del tenant.
     * Canal: impresion:{fqdn_tenant}
     *
     * Si la orden no trae nombre de impresora, se busca la impresora
     * marcada como predeterminada (is_default) en BD y se asigna.
     * El registro se actualiza para que el endpoint /pending también
     * devuelva el nombre resuelto a BuhoPrinter al reconectar.
     */
    public function publish(PrintOrder $order): void
    {
        try {
            $printerName = $this->resolvePrinterName($order);

            $fqdn    = $this->resolveFqdn();
            $channel = "impresion:{$fqdn}";

            $payload = json_encode([
                'id'           => $order->id,
                'name_printer' => $printerName,
                'pdf_b64'      => $order->pdf_b64,
                'status'       => $order->status,
                'created_at'   => $order->created_at?->toIso8601String(),
            ]);

            Redis::connection('pubsub')->publish($channel, $payload);

            Log::info("PrintPublisher: orden #{$order->id} publicada en canal [{$channel}] con impresora [{$printerName}]");
        } catch (\Throwable $e) {
            Log::error("PrintPublisher: error publicando orden #{$order->id} — {$e->getMessage()}");
        }
    }

    /**
     * Resuelve el nombre de impresora para la orden.
     * Si la orden no trae nombre, busca la impresora predeterminada en BD
     * y actualiza el registro para que /pending lo devuelva correctamente.
     */
    private function resolvePrinterName(PrintOrder $order): string
    {
        if (!empty($order->name_printer)) {
            return $order->name_printer;
        }

        $default = Printer::where('active', true)
            ->where('is_default', true)
            ->first();

        $resolvedName = $default?->name ?? '';

        if ($resolvedName) {
            // Persiste el nombre resuelto para que /pending lo retorne correctamente
            $order->updateQuietly(['name_printer' => $resolvedName]);
            Log::info("PrintPublisher: orden #{$order->id} sin impresora — asignada predeterminada [{$resolvedName}]");
        } else {
            Log::warning("PrintPublisher: orden #{$order->id} sin impresora y sin impresora predeterminada configurada.");
        }

        return $resolvedName;
    }

    private function resolveFqdn(): string
    {
        try {
            $hostname = app(\Hyn\Tenancy\Contracts\CurrentHostname::class);
            return $hostname?->fqdn ?? 'local';
        } catch (\Throwable) {
            return 'local';
        }
    }
}
