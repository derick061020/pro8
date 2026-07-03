<?php

// Modules/ClaimsBook/Models/Tenant/ClaimChannel.php

namespace Modules\ClaimsBook\Models\Tenant;

use App\Models\Tenant\ModelTenant;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Canal de recepción de reclamos configurado por el tenant.
 * Ejemplos: "Presencial", "Web", "WhatsApp", "Teléfono".
 */
class ClaimChannel extends ModelTenant
{
    use UsesTenantConnection;

    protected $table = 'claim_channels';

    protected $fillable = [
        'name',
        'description',
        'address',
        'telephone',
        'email',
    ];

    /**
     * Retorna los datos formateados para respuestas de API.
     * $estMap: mapa opcional [nombre_lowercase => array] para enriquecer
     *          canales sincronizados con los datos de contacto del establecimiento.
     */
    public function getCollectionData(array $estMap = []): array
    {
        $est = $estMap[strtolower($this->name)] ?? null;

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'address'     => $est['address']   ?? null,
            'telephone'   => $est['telephone'] ?? null,
            'email'       => $est['email']     ?? null,
        ];
    }


}
