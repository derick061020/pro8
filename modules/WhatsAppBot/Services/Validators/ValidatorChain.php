<?php

namespace Modules\WhatsAppBot\Services\Validators;

class ValidatorChain
{
    /** @var DocumentValidator[] */
    private array $validators = [];

    public function add(DocumentValidator $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function validate(array $draft): ValidationResult
    {
        foreach ($this->validators as $validator) {
            $result = $validator->validate($draft);
            if (!$result->passed) {
                return $result;
            }
        }
        return ValidationResult::ok();
    }
}
