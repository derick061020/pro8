<?php

namespace Database\Seeders\Libs\Migration;

use App\Models\System\Client;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Recorre las relaciones polimórficas declaradas en `PolymorphicRelation`
 * y reporta los valores `*_type` que existen actualmente en cada tabla,
 * comparándolos con los modelos esperados.
 *
 * Útil durante la migración de datos prox → pro8 para detectar columnas
 * `*_type` que apunten a namespaces antiguos o a modelos que ya no existen.
 */
class PolymorphicRelationSeeder extends Seeder
{
    public function run(): void
    {
        // Este seeder es para migrar datos historicos prox -> pro8. Cuando se
        // ejecuta durante la creacion de un tenant nuevo, la tabla companies
        // aun esta vacia (la company se inserta despues, en ClientController)
        // y los seeders polimorficos no aplican porque no hay datos viejos.
        $companyRow = DB::table('companies')->select('number')->first();
        if (!$companyRow) {
            $this->log('SKIP: companies vacia (tenant recien creado, nada que migrar)');
            return;
        }
        $ruc = $companyRow->number;

        $client = Client::where('number', '=', $ruc, false)->first();
        if (!$client || !$client->hostname || !$client->hostname->website) {
            $this->log("SKIP: no encuentro Client/hostname/website para RUC {$ruc}");
            return;
        }
        $uuid = $client->hostname->website->uuid;

        $this->log("RUN {$uuid}");
        foreach (PolymorphicRelation::cases() as $relation) {
            $this->inspect($relation);
        }
    }

    protected function inspect(PolymorphicRelation $relation): void
    {
        $table     = $relation->table();
        $morph     = $relation->morph();
        $typeCol   = "{$morph}_type";
        $expected  = $relation->models();


        foreach ($expected as $old => $actul) {
            $this->log("TABLE {$table} | COLUMN {$typeCol} | OLD {$old} | ACTUAL {$actul}");
            DB::table($table)
                ->where($typeCol, $old)
                ->update([$typeCol => $actul]);
        }

    }

    protected function log(string $message): void
    {
        if (isset($this->command)) {
            $this->command->getOutput()->writeln($message);
        }
    }
}
