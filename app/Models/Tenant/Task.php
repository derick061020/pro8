<?php

namespace App\Models\Tenant;

use Hyn\Tenancy\Environment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


/**
 * Class Task
 *
 * @package App\Models\Tenant
 * @mixin IdeHelperTask
 * @property string $class
 * @property string $execution_time
 * @property string|null $output
 * @property string $uuid
 */
class Task extends ModelTenant
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['class', 'execution_time', 'output', 'uuid'];

    protected static function booted()
    {

        $uuid = Str::uuid()->toString();

        static::creating(function (Task $task) use ($uuid) {
            $task->uuid = $uuid;
        });

        static::created(function (Task $task) {
            $environment = app(Environment::class);
            $tenant      = $environment->tenant();

            Log::info("Task created for tenant {$tenant->id}: {$task->class} at {$task->execution_time}");
            Log::info("Tenant  uuid {$tenant->uuid}");
            DB::connection('system')->table('tenant_tasks')->insert([
               'class' => $task->class, 
               'execution_time' => $task->execution_time,
               'output' => $task->output,
               'uuid' => $task->uuid,
                'uuid_tenant' => $tenant->uuid,
            ]);

        });

        static::deleted(function (Task $task) {
            DB::connection('system')->table('tenant_tasks')->where('uuid', $task->uuid)->delete();
        });
    }

}
