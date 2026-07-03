<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;
use Modules\Restaurant\Models\RestaurantTable;
use App\Models\Tenant\Configuration;
use Illuminate\Support\Facades\DB;

class RestaurantConfiguration extends ModelTenant
{
    protected $fillable = [
        'menu_pos',
        'menu_order',
        'menu_tables',
        'first_menu',
        'menu_bar',
        'menu_kitchen',
        'enabled_environment_1',
        'enabled_environment_2',
        'enabled_environment_3',
        'enabled_environment_4',
        'items_maintenance',
        'tables_quantity',
        'tables_quantity_environment_2',
        'tables_quantity_environment_3',
        'tables_quantity_environment_4',
        'enabled_send_command',
        'enabled_print_command',
        'enabled_print_group_commands',
        'enabled_printsend_command',
        'enabled_command_waiter',
        'enabled_pos_waiter',
        'enabled_close_table',
        'enabled_close_table_mozo',
        'enabled_server_print',
        'replace_template_mozo',
        'printer_enabled',
        'printer_host',
        'printer_status',
        'printer_public_ip',
        'print_local_enabled',
        'printer_name_comanda',
        'printer_name_documents',
        'printer_name_precuenta',
        'printer_per_area_enabled',
        'print_destination',
    ];

    protected $casts = [
        'print_local_enabled'      => 'boolean',
        'printer_enabled'          => 'boolean',
        'printer_per_area_enabled' => 'boolean',
        'print_destination'        => 'boolean',
    ];

    public $timestamps = false;

    public function getCollectionData() {
        // Cargar configuración global con la relación del tipo de descuento para evitar múltiples consultas
        $config = Configuration::with('globalDiscountType')->first();

        $restaurant_tip_factor = optional($config)->restaurant_tip_factor;
        $global_discount_type_id = optional($config)->global_discount_type_id;
        $is_restaurant_active = DB::connection('tenant')->table('business_turns')
        ->where('id', 3)
        ->where('active', 1)
        ->exists();

        // ya tenemos la configuración en $config
        $configurations_global = $config;

        // Obtener información del tipo de descuento global desde la relación cargada
        $global_discount_type = null;
        if ($config && $config->globalDiscountType) {
            $g = $config->globalDiscountType;
            $global_discount_type = (object)[
                'id' => $g->id,
                'description' => $g->description,
            ];
        }

        return [
            'menu_pos' => (bool)$this->menu_pos,
            'menu_order' => (bool)$this->menu_order,
            'menu_tables' => (bool)$this->menu_tables,
            'menu_bar' => (bool)$this->menu_bar,
            'menu_kitchen' => (bool)$this->menu_kitchen,
            'first_menu' => $this->first_menu,
            'tables_quantity' => $this->tables_quantity,
            'tables_quantity_environment_2' => $this->tables_quantity_environment_2,
            'tables_quantity_environment_3' => $this->tables_quantity_environment_3,
            'tables_quantity_environment_4' => $this->tables_quantity_environment_4,
            'enabled_environment_1' => (bool)$this->enabled_environment_1,
            'enabled_environment_2' => (bool)$this->enabled_environment_2,
            'enabled_environment_3' => (bool)$this->enabled_environment_3,
            'enabled_environment_4' => (bool)$this->enabled_environment_4,
            'items_maintenance' => (bool)$this->items_maintenance,
            'enabled_send_command' => (bool)$this->enabled_send_command,
            'enabled_print_command' => (bool)$this->enabled_print_command,
            'enabled_print_group_commands' => (bool)$this->enabled_print_group_commands,
            'enabled_printsend_command' => (bool)$this->enabled_printsend_command,
            'enabled_command_waiter' => (bool)$this->enabled_command_waiter,
            'enabled_pos_waiter' => (bool)$this->enabled_pos_waiter,
            'enabled_close_table' => (bool)$this->enabled_close_table,
            'enabled_close_table_mozo' => (bool)$this->enabled_close_table_mozo,
            'enabled_server_print' => (bool)$this->enabled_server_print,
            'replace_template_mozo' => (bool)$this->replace_template_mozo,
            'printer_enabled'        => (bool)$this->printer_enabled,
            'printer_host'           => $this->printer_host,
            'printer_status'         => $this->printer_status,
            'printer_name_comanda'     => $this->printer_name_comanda,
            'printer_name_documents'   => $this->printer_name_documents,
            'printer_name_precuenta'   => $this->printer_name_precuenta,
            'printer_per_area_enabled' => (bool) $this->printer_per_area_enabled,
            'printers'               => Printer::where('active', true)
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->get(['id', 'name', 'is_default'])
                ->toArray(),
            'restaurant_tip_factor' => $restaurant_tip_factor,
            'global_discount_type' => $global_discount_type ? [
                'id' => $global_discount_type->id,
                'description' => $global_discount_type->description,
            ] : null,
            'is_restaurant_active' => $is_restaurant_active,
            'show_item_description_pack' => (bool)optional($configurations_global)->show_item_description_pack,
            'allow_edit_unit_price_to_seller' => (bool)optional($configurations_global)->allow_edit_unit_price_to_seller,
        ];
    }
}
