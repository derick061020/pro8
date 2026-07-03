<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $suscriptionData = self::getModuleData();
        $suscriptionRow = self::getSystemModuleConnection()->where($suscriptionData)->first();
        if ($suscriptionRow === null) {
            $suscriptionRow = self::getSystemModuleConnection()->insert($suscriptionData);

        }
    }

    public static function getModuleData(): array
    {
        return [
            'value' => 'claims_book',
            'description' => 'Libro de Reclamaciones',
            'sort' => 25,
        ];
    }

    public static function getSystemModuleConnection(): Builder
    {
        return DB::connection('system')->table('modules');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $suscriptionData = self::getModuleData();

        $suscriptionRow = self::getSystemModuleConnection()->where($suscriptionData)->first();
        if ($suscriptionRow != null) {
            DB::connection('system')->table('modules')->delete($suscriptionRow->id);
        }
    }
};