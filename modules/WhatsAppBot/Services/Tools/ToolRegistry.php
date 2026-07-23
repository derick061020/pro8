<?php

namespace Modules\WhatsAppBot\Services\Tools;

use RuntimeException;

class ToolRegistry
{
    private array $tools = [];

    public function register(ToolInterface $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }

    public function get(string $name): ToolInterface
    {
        if (!isset($this->tools[$name])) {
            throw new RuntimeException("Tool [{$name}] is not registered.");
        }
        return $this->tools[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->tools[$name]);
    }

    public function definitions(): array
    {
        return array_values(array_map(
            fn (ToolInterface $tool) => $tool->definition(),
            $this->tools
        ));
    }
}
