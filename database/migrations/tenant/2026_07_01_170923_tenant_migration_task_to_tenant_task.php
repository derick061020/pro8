<?php

use App\Models\Tenant\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $uuid_tenant = config('database.connections.tenant.uuid');

        $tasks = Task::all();


        foreach ($tasks as $task) {
            $uuid = Str::uuid()->toString();
            $task->uuid = $uuid;
            $task->save();

            DB::connection('system')->table('tenant_tasks')->insert([
                'class' => $task->class,
                'execution_time' => $task->execution_time,
                'output' => $task->output,
                'uuid' => $task->uuid,
                'uuid_tenant' => $uuid_tenant,
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
