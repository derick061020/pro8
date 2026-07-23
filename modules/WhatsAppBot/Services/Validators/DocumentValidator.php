<?php

namespace Modules\WhatsAppBot\Services\Validators;

interface DocumentValidator
{
    public function validate(array $draft): ValidationResult;
}
