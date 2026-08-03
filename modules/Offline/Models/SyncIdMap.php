<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\ModelTenant;

/**
 * Equivalencia entre el id de un registro en el terminal y el id que ese
 * mismo registro obtuvo en el servidor online.
 *
 * En el terminal `terminal_code` va vacío: solo existe un servidor contra el
 * cual mapear. En el servidor guarda de qué terminal proviene el id local,
 * porque dos terminales distintos pueden mandar el mismo número.
 *
 * @property string $terminal_code
 * @property string $entity
 * @property int    $local_id
 * @property int    $remote_id
 */
class SyncIdMap extends ModelTenant
{
    protected $table = 'offline_id_maps';

    protected $fillable = ['terminal_code', 'entity', 'local_id', 'remote_id', 'uuid'];

    protected $casts = [
        'local_id'  => 'int',
        'remote_id' => 'int',
    ];

    public static function remember(
        string $entity,
        int $localId,
        int $remoteId,
        ?string $uuid = null,
        string $terminalCode = ''
    ): self {
        return static::updateOrCreate(
            ['terminal_code' => $terminalCode, 'entity' => $entity, 'local_id' => $localId],
            ['remote_id' => $remoteId, 'uuid' => $uuid]
        );
    }

    /**
     * Traduce un id local al id del servidor.
     * Devuelve null si ese registro todavía no se sincronizó.
     */
    public static function toRemote(string $entity, $localId, string $terminalCode = ''): ?int
    {
        if (empty($localId)) {
            return null;
        }

        return static::where('terminal_code', $terminalCode)
            ->where('entity', $entity)
            ->where('local_id', $localId)
            ->value('remote_id');
    }

    public static function toLocal(string $entity, $remoteId, string $terminalCode = ''): ?int
    {
        if (empty($remoteId)) {
            return null;
        }

        return static::where('terminal_code', $terminalCode)
            ->where('entity', $entity)
            ->where('remote_id', $remoteId)
            ->value('local_id');
    }
}
