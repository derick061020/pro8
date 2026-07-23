<?php

namespace Modules\WhatsAppBot\Services\Tools;

use App\Models\Tenant\Person;

class LookupPersonTool implements ToolInterface
{
    private const MAX_RESULTS = 10;

    public function name(): string
    {
        return 'lookup_person';
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => 'Busca un cliente registrado por número de documento (DNI/RUC) o por nombre/razón social.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'document' => [
                            'type' => 'string',
                            'description' => 'Número de DNI (8 dígitos) o RUC (11 dígitos). Opcional si se busca por nombre.',
                        ],
                        'name' => [
                            'type' => 'string',
                            'description' => 'Nombre o razón social parcial del cliente. Opcional si se busca por documento.',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $document = trim($arguments['document'] ?? '');
        $name = trim($arguments['name'] ?? '');

        if ($document === '' && $name === '') {
            return ['matches' => [], 'message' => 'Provee al menos document o name.'];
        }

        $query = Person::query()->where('type', 'customers');

        if ($document !== '') {
            $query->where('number', $document);
        }

        if ($name !== '') {
            $tokens = array_filter(preg_split('/\s+/', $name));
            foreach ($tokens as $token) {
                $query->where(function ($q) use ($token) {
                    $q->where('name', 'like', "%{$token}%")
                      ->orWhere('trade_name', 'like', "%{$token}%");
                });
            }
        }

        $persons = $query
            ->limit(self::MAX_RESULTS)
            ->get(['id', 'identity_document_type_id', 'number', 'name', 'trade_name', 'email', 'telephone', 'address']);

        return [
            'count' => $persons->count(),
            'matches' => $persons->map(fn ($p) => [
                'id' => $p->id,
                'document_type' => $p->identity_document_type_id,
                'document_number' => $p->number,
                'name' => $p->name,
                'trade_name' => $p->trade_name,
                'email' => $p->email,
                'telephone' => $p->telephone,
                'address' => $p->address,
            ])->all(),
        ];
    }
}
