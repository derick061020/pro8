<?php

namespace Modules\Webhook\Services;

class WebhookSignature
{
    /**
     * Firma HMAC-SHA256 del cuerpo crudo del request.
     * El receptor debe verificar contra el body crudo (php://input),
     * nunca contra un JSON re-serializado.
     */
    public static function sign(string $rawBody, string $secret): string
    {
        return hash_hmac('sha256', $rawBody, $secret);
    }
}
