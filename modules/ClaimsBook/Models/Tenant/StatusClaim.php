<?php

// Modules/ClaimsBook/Models/Tenant/StatusClaim.php

namespace Modules\ClaimsBook\Models\Tenant;

use App\Models\Tenant\ModelTenant;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Modelo de estado para el libro de reclamaciones.
 * Análogo a StatusOrder pero con soporte para estado final (is_final)
 * y sin acciones de inventario o facturación.
 */
class StatusClaim extends ModelTenant
{
    use UsesTenantConnection;

    protected $table = 'status_claims';

    protected $fillable = [
        'description',
        'color',
        'sort_order',
        'is_initial',
        'is_final',
        'action_send_email',
        'assigned_user_id',
    ];

    protected $casts = [
        'sort_order'       => 'integer',
        'is_initial'       => 'boolean',
        'is_final'         => 'boolean',
        'action_send_email'=> 'boolean',
        'assigned_user_id' => 'integer',
    ];

    /**
     * Reclamos asociados a este estado.
     */
    public function claims()
    {
        return $this->hasMany(Claim::class, 'status_claim_id');
    }

    /**
     * Retorna los datos formateados para respuestas de API y colecciones Vue.
     */
    public function getCollectionData(): array
    {
        return [
            'id'               => $this->id,
            'description'      => $this->description,
            'color'            => $this->color,
            'sort_order'       => $this->sort_order,
            'is_initial'       => $this->is_initial,
            'is_final'         => $this->is_final,
            'action_send_email'=> $this->action_send_email,
            'assigned_user_id' => $this->assigned_user_id,
        ];
    }
}
