<?php

namespace Modules\Inventory\Imports;

use App\Models\Tenant\Establishment;
use App\Models\Tenant\Item;
use App\Models\Tenant\Series;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\InventoryTransfer;

class TransfersImport implements ToCollection
{
    use Importable;

    protected $data;
    protected $response;
    protected $document_type_id;

    
    /**
     * Handle the collection of data.
     *
     * @param \Illuminate\Support\Collection $collection
     * @return void
     */
    public function collection(Collection $rows)
    {
        // La serie de la transferencia masiva sera elegido por el usuario que hace el importe
        $establishment_id = auth()->user()->establishment->id;

        $series = Series::query()
                ->select('number')
                ->where('establishment_id', $establishment_id)
                ->where('document_type_id', 'U4')
                ->first();
        
        if (is_null($series)) {
            $this->data[] = "La sede actual no tiene serie para el tipo de documento 'U4' asignada.";
            return;
        }


        unset($rows[0]);
        $transfer = InventoryTransfer::create([
            'description' => 'Transferencia de productos masivas',
            'warehouse_id' => null,
            'warehouse_destination_id' => null,
            'transfer_collect_id' => null,
            'quantity' => $rows->count(),         
            'number' => '#',
            'series' => $series->number,
            'document_type_id' => 'U4', // Tipo de documento para transferencias
        ]);

        foreach ($rows as $index => $row) {
            $code_establishment = $row[1];
            $code_establishment_destination = $row[2];
            $internal_id = $row[0];
            $quantiy = $row[3];

            $warehouse = Establishment::where('code', $code_establishment)->first();


            $series = Series::query()
                ->select('number')
                ->where('establishment_id', $warehouse->id)
                ->where('document_type_id', 'U4')
                ->first();
            

            if (is_null($series)) {
                $this->data[] = "(#$index) La sede con código {$code_establishment} no tiene serie para el tipo de documento 'U4' asignada.";
                continue;
            }

            $warehouse_destination = Establishment::where('code', $code_establishment_destination)->first();
            
            $origin_warehouse = $warehouse->warehouse->id;
            $destination_warehouse = $warehouse_destination->warehouse->id;

            $item = Item::where('internal_id', $internal_id)->first();

            $row = InventoryTransfer::query()
                ->create([
                    'description' => "Transferencia de productos masivas",
                    'warehouse_id' => $origin_warehouse ,
                    'warehouse_destination_id' => $destination_warehouse,
                    'quantity' => 1,
                    'document_type_id' => $this->document_type_id,
                    'transfer_collect_id' => $transfer->id,
                    'series' => $series->number,
                    'number' => '#',
                ]);

            $inventory = new Inventory;
            $inventory->type = 2;
            $inventory->item_id = $item->id;
            $inventory->description = 'Traslado';
            $inventory->warehouse_id = $origin_warehouse;
            $inventory->warehouse_destination_id = $destination_warehouse;
            $inventory->quantity = $quantiy; 
            $inventory->inventories_transfer_id = $row->id;
            $inventory->save();
            
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function setDocumentTypeId($document_type_id)
    {
        $this->document_type_id = $document_type_id;

        return $this;
    }   

}