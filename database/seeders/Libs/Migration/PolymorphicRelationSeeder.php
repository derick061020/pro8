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

        $ruc = DB::table('companies')->select('number')->first()->number;
        
        $uuid = Client::where('number', '=' , $ruc, false)->first()->hostname->website->uuid;

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
