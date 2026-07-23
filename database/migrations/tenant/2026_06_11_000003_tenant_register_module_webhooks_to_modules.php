<?php

use Illuminate\Database\Migrations\Migration;
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
        $moduleData = self::getModuleData();
        $moduleRow = self::getSystemModuleConnection()->where($moduleData)->first();
        if ($moduleRow === null) {
            $moduleId = self::getSystemModuleConnection()->insertGetId($moduleData);
        } else {
            $moduleId = $moduleRow->id;
        }

        // Asignar el módulo a usuarios con módulos restringidos, para que les aparezca en el menú
        $userIds = DB::connection('tenant')->table('module_user')->distinct()->pluck('user_id');
        foreach ($userIds as $userId) {
            $exists = DB::connection('tenant')->table('module_user')
                ->where(['user_id' => $userId, 'module_id' => $moduleId])
                ->first();
            if ($exists === null) {
                DB::connection('tenant')->table('module_user')->insert([
                    'user_id' => $userId,
                    'module_id' => $moduleId,
                ]);
            }
        }
    }

    public static function getModuleData(): array
    {
        return [
            'value'       => 'webhooks',
            'description' => 'Webhooks',
            'order_menu'  => 26,
        ];
    }

    public static function getSystemModuleConnection(): Builder
    {
        return DB::connection('tenant')->table('modules');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $moduleData = self::getModuleData();

        $moduleRow = self::getSystemModuleConnection()->where($moduleData)->first();
        if ($moduleRow != null) {
            DB::connection('tenant')->table('module_user')->where('module_id', $moduleRow->id)->delete();
            self::getSystemModuleConnection()->delete($moduleRow->id);
        }
    }
};
