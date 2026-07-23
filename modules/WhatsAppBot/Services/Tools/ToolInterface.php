<?php

namespace Modules\WhatsAppBot\Services\Tools;

interface ToolInterface
{
    public function name(): string;

    public function definition(): array;

    public function execute(array $arguments): array;
}
