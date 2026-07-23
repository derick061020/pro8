<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Website;
use Symfony\Component\Console\Output\BufferedOutput;

class TenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:run';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the scheduled tasks of the tenants';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $task_tenant = DB::connection('system')->table('tenant_tasks')->where('execution_time', Carbon::now()->format('H:i').':00')->get();
        foreach ($task_tenant as $task) {
            try {
                $website = Website::where('uuid', $task->uuid_tenant)->first();

                if (! $website) {
                    $output = "Tenant no encontrado: {$task->uuid_tenant}";
                } else {
                    $buffer = new BufferedOutput();

                    Artisan::call('tenancy:run', [
                        'run'      => $task->class,
                        '--tenant' => [$website->id],
                    ], $buffer);

                    $output = $buffer->fetch();

                    // Activar la conexión del tenant y guardar el output en su propia tabla tasks
                    app(Environment::class)->tenant($website);
                    Task::where('uuid', $task->uuid)->update(['output' => $output]);
                }
            }
            catch (\Exception $e) {
                $output = $e->getMessage();
            }

            DB::connection('system')->table('tenant_tasks')
                ->where('id', $task->id)
                ->update([
                    'output'     => $output,
                    'updated_at' => Carbon::now(),
                ]);
        };
    }
}
