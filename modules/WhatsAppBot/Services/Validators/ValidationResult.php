<?php

namespace Modules\WhatsAppBot\Services\Validators;

class ValidationResult
{
    public function __construct(
        public readonly bool $passed,
        public readonly ?string $reason = null,
        public readonly string $code = ''
    ) {
    }

    public static function ok(): self
    {
        return new self(true);
    }

    public static function fail(string $reason, string $code = ''): self
    {
        return new self(false, $reason, $code);
    }
}
