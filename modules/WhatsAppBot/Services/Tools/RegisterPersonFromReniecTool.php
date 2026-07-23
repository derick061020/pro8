<?php

namespace Modules\WhatsAppBot\Services\Tools;

use App\Models\Tenant\Person;
use Illuminate\Support\Facades\Log;
use Modules\ApiPeruDev\Data\ServiceData;

class RegisterPersonFromReniecTool implements ToolInterface
{
    private const DOC_TYPE_DNI = '1';
    private const DOC_TYPE_RUC = '6';

    public function name(): string
    {
        return 'register_person_from_reniec';
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => 'Consulta RENIEC/SUNAT vía apiperu.dev para registrar automáticamente un cliente nuevo cuando su DNI o RUC no está en el catálogo. Si el cliente ya existía, devuelve el existente sin crear duplicado.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'document_type' => [
                            'type' => 'string',
                            'enum' => ['dni', 'ruc'],
                            'description' => 'Tipo de documento a consultar.',
                        ],
                        'document_number' => [
                            'type' => 'string',
                            'description' => 'Número del documento. DNI=8 dígitos, RUC=11 dígitos.',
                        ],
                    ],
                    'required' => ['document_type', 'document_number'],
                ],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $type = strtolower((string) ($arguments['document_type'] ?? ''));
        $number = trim((string) ($arguments['document_number'] ?? ''));

        if (!in_array($type, ['dni', 'ruc'], true)) {
            return ['status' => 'error', 'error' => 'document_type debe ser "dni" o "ruc".'];
        }

        $expectedLength = $type === 'dni' ? 8 : 11;
        if (strlen($number) !== $expectedLength || !ctype_digit($number)) {
            return ['status' => 'error', 'error' => "El {$type} debe tener exactamente {$expectedLength} dígitos numéricos."];
        }

        $identityTypeId = $type === 'dni' ? self::DOC_TYPE_DNI : self::DOC_TYPE_RUC;

        $existing = Person::query()
            ->where('type', 'customers')
            ->where('number', $number)
            ->first();

        if ($existing) {
            return [
                'status' => 'already_exists',
                'person' => $this->formatPerson($existing),
            ];
        }

        try {
            $service = new ServiceData();
            $response = $service->service($type, $number);
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] RENIEC lookup failed', [
                'exception' => $e->getMessage(),
                'type' => $type,
                'number' => $number,
            ]);
            return ['status' => 'error', 'error' => 'No se pudo consultar RENIEC/SUNAT en este momento.'];
        }

        if (empty($response['success']) || empty($response['data']['name'])) {
            return [
                'status' => 'not_found_in_reniec',
                'error' => "El {$type} {$number} no fue encontrado en RENIEC/SUNAT.",
            ];
        }

        $data = $response['data'];

        $person = Person::create([
            'type' => 'customers',
            'identity_document_type_id' => $identityTypeId,
            'number' => $number,
            'name' => $data['name'],
            'trade_name' => $data['trade_name'] ?? $data['name'],
            'country_id' => 'PE',
            'department_id' => $data['department_id'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'address' => $data['address'] ?? null,
            'condition' => $data['condition'] ?? '',
            'state' => $data['state'] ?? '',
            'enabled' => true,
        ]);

        Log::info('[WhatsAppBot] Person registrado desde RENIEC', [
            'person_id' => $person->id,
            'document' => $number,
        ]);

        return [
            'status' => 'registered',
            'person' => $this->formatPerson($person),
        ];
    }

    private function formatPerson(Person $person): array
    {
        return [
            'id' => (int) $person->id,
            'document_type' => $person->identity_document_type_id,
            'document_number' => $person->number,
            'name' => $person->name,
            'trade_name' => $person->trade_name,
            'address' => $person->address,
        ];
    }
}
