<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\ModelTenant;

/**
 * Configuración del motor offline.
 *
 * Es un registro único (id = 1). Define si esta instalación actúa como
 * terminal Windows (`client`) o como servidor online (`server`).
 *
 * @property bool        $is_client
 * @property string      $mode
 * @property string|null $terminal_code
 * @property string|null $terminal_name
 * @property string|null $token_server
 * @property string|null $url_server
 * @property bool        $sync_enabled
 * @property int         $sync_interval
 * @property bool        $is_online
 */
class OfflineConfiguration extends ModelTenant
{
    public const MODE_CLIENT = 'client';
    public const MODE_SERVER = 'server';

    protected $fillable = [
        'is_client',
        'mode',
        'terminal_code',
        'terminal_name',
        'token_server',
        'url_server',
        'sync_enabled',
        'sync_interval',
        'is_online',
        'last_ping_at',
        'last_push_at',
        'last_pull_at',
        'git_remote',
        'git_branch',
        'app_version',
    ];

    protected $casts = [
        'is_client'     => 'bool',
        'sync_enabled'  => 'bool',
        'is_online'     => 'bool',
        'sync_interval' => 'int',
        'last_ping_at'  => 'datetime',
        'last_push_at'  => 'datetime',
        'last_pull_at'  => 'datetime',
    ];

    /**
     * Registro único de configuración. Lo crea si no existe, para que una
     * instalación recién migrada no reviente donde antes se usaba firstOrFail.
     */
    public static function current(): self
    {
        $record = static::query()->first();

        if (!$record) {
            $record = static::query()->create([
                'is_client' => false,
                'mode'      => self::MODE_SERVER,
            ]);
        }

        return $record;
    }

    public function isClient(): bool
    {
        return $this->mode === self::MODE_CLIENT || (bool)$this->is_client;
    }

    public function isServer(): bool
    {
        return !$this->isClient();
    }

    /**
     * El terminal está en condiciones de sincronizar: pareado y habilitado.
     */
    public function canSync(): bool
    {
        return $this->isClient()
            && $this->sync_enabled
            && !empty($this->url_server)
            && !empty($this->token_server)
            && !empty($this->terminal_code);
    }

    /**
     * URL base del servidor online, sin barra final.
     */
    public function serverUrl(): ?string
    {
        return $this->url_server ? rtrim($this->url_server, '/') : null;
    }
}
