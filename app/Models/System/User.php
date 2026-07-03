<?php

namespace App\Models\System;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// ── AGREGADO (RECIENTE) ──────────────────────────────────────
// Estos 'use' permiten a Laravel saber cómo procesar los correos
// y los tokens necesarios para cambiar la contraseña.
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    // CanResetPassword: Activa las funciones internas de resetear clave.
    // Notifiable: Permite que este modelo pueda enviar emails.
    use Authenticatable, Authorizable, UsesSystemConnection, CanResetPassword, Notifiable;

    /**
     * Nombre físico de la tabla en la conexión system.
     * Producción: suele ser "users". Local pro8: a veces "system_users" sin tabla "users".
     */
    public static function systemUsersTable(): string
    {
        static $resolved;

        if ($resolved !== null) {
            return $resolved;
        }

        $connectionName = (new static())->getConnectionName();
        $schema = Schema::connection($connectionName);

        if ($schema->hasTable('users')) {
            return $resolved = 'users';
        }

        if ($schema->hasTable('system_users')) {
            return $resolved = 'system_users';
        }

        throw new RuntimeException(
            'No existe la tabla users ni system_users en la conexión ['.$connectionName.'] para el modelo System\\User.'
        );
    }

    public function getTable()
    {
        return static::systemUsersTable();
    }

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'whatsapp_number', 'address_contact', 'introduction',
        'reseller_id', 'api_token', 'status', 'module_permissions', 'can_create_clients', 'is_master',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'module_permissions' => 'array',
        'can_create_clients' => 'boolean',
        'is_master' => 'boolean',
    ];

    
    /**
     * 
     * Retorna nombre de la conexión
     *
     * @return string
     */
    public function getDbConnectionName()
    {
        return $this->getConnection()->getName();
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reseller_id');
    }

    public function administrators(): HasMany
    {
        return $this->hasMany(self::class, 'reseller_id');
    }

    /**
     * Clientes del panel sistema asignados a este subadministrador reseller.
     */
    public function assignedClients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'reseller_admin_clients', 'admin_user_id', 'client_id');
    }

    /**
     * Subadministradores reseller: solo si can_create_clients es true pueden registrar nuevos clientes.
     * El usuario principal reseller (sin reseller_id) siempre puede.
     */
    public function canCreateClients(): bool
    {
        if ($this->reseller_id === null) {
            return true;
        }

        return (bool) $this->can_create_clients;
    }

    /**
     * Define dinámicamente si el usuario es el Super Admin (Master).
     */
    public function isSystemMaster(): bool
    {
        return $this->reseller_id === null || $this->id === 1;
    }

    /**
     * Subadministrador reseller marcado como maestro en base de datos (columna is_master).
     */
    public function isResellerSystemMasterAdministrator(): bool
    {
        return $this->reseller_id !== null && (bool) $this->is_master;
    }

    /**
     * Usuario principal reseller (sin reseller_id) tiene acceso total al panel sistema.
     */
    public function canAccessSystemModule(string $moduleKey): bool
    {
        if ($this->isSystemMaster()) {
            return true;
        }

        // Si se desea que el ResellerSystemMasterAdministrator tenga todo, se puede chequear acá.
        // Pero la instrucción dice: "El sistema debe verificar si el usuario logueado tiene el módulo... 
        // Excepción: Si la sesión es del Master (reseller_id null)" - Ya cubierto arriba.
        if ($this->isResellerSystemMasterAdministrator()) {
            return true;
        }

        $perms = $this->module_permissions ?? [];

        return is_array($perms) && in_array($moduleKey, $perms, true);
    }

    /**
     * Primer segmento de ruta del panel (p. ej. payment-orders) frente a permisos guardados.
     */
    public function canAccessSystemPath(?string $firstPathSegment): bool
    {
        if ($this->isSystemMaster()) {
            return true;
        }

        if ($this->isResellerSystemMasterAdministrator()) {
            return true;
        }

        $firstPathSegment = $firstPathSegment ?? '';

        if ($firstPathSegment === 'dashboard' || $firstPathSegment === 'clients') {
            return $this->canAccessSystemModule('clients');
        }

        if ($firstPathSegment === 'configurations') {
            return $this->canAccessSystemModule('configurations');
        }

        $alwaysAllowed = ['', 'phone', 'users', 'guest-register'];
        if (in_array($firstPathSegment, $alwaysAllowed, true)) {
            return true;
        }

        return $this->canAccessSystemModule($firstPathSegment);
    }

} 