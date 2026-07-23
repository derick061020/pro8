<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Modules\Inventory\Exports\KardexExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use App\Models\Tenant\Kardex;
use App\Models\Tenant\Item;
use Carbon\Carbon;
use Modules\Inventory\Models\Guide;
use Modules\Inventory\Models\InventoryKardex;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Http\Resources\ReportKardexCollection;
use Modules\Inventory\Http\Resources\ReportKardexLotsCollection;

use Modules\Inventory\Models\ItemWarehouse;
use Modules\Item\Models\ItemLotsGroup;
use Modules\Item\Models\ItemLot;

use Modules\Inventory\Http\Resources\ReportKardexLotsGroupCollection;
use Modules\Inventory\Http\Resources\ReportKardexItemLotCollection;
use Modules\Inventory\Models\Devolution;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\Document;
use App\Models\Tenant\Inventory as TenantInventory;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\PurchaseSettlement;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\TemporaryKardexRecord;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Modules\Inventory\Http\Resources\ReportTemporaryKardexCollection;
use Modules\Inventory\Models\Inventory;
use Modules\Order\Models\OrderNote;

class ReportKardexController extends Controller
{
    protected $models = [
        "App\Models\Tenant\Document",
        "App\Models\Tenant\Purchase",
        "App\Models\Tenant\PurchaseSettlement",
        "App\Models\Tenant\SaleNote",
        "Modules\Inventory\Models\Inventory",
        "Modules\Order\Models\OrderNote",
        Devolution::class,
        Dispatch::class
    ];

    public function index()
    {
        return view('inventory::reports.kardex.index');
    }


    public function filter()
    {
        $warehouses = [];
        $user = User::query()->find(auth()->id());
        if ($user->type === 'admin') {
            $warehouses[] = [
                'id' => 'all',
                'name' => 'Todos'
            ];
            $records = Warehouse::query()
                ->get();
        } else {
            $records = Warehouse::query()
                ->where('establishment_id', $user->establishment_id)
                ->get();
        }

        foreach ($records as $record) {
            $warehouses[] = [
                'id' => $record->id,
                'name' => $record->description,
            ];
        }

        return [
            'warehouses' => $warehouses
        ];
    }

    public function filterByWarehouse($warehouse_id)
    {
        $query = Item::query()->whereNotIsSet()
            ->with('warehouses')
            ->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']]);

        if ($warehouse_id !== 'all') {
            $query->whereHas('warehouses', function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            });
        }

        $items = $query->latest()
            ->take(config('tenant.items_per_page'))
            ->get()
            ->transform(function ($row) {
                $full_description = $this->getFullDescription($row);
                return [
                    'id' => $row->id,
                    'full_description' => $full_description,
                    'internal_id' => $row->internal_id,
                    'description' => $row->description,
                    'warehouses' => $row->warehouses
                ];
            });

        return [
            'items' => $items
        ];
    }

    public function records(Request $request)
    {
        $records = $this->getDataRecords($request);
        $balance = 0;
        if($request->date_start){
            $balanceRequest = $request->duplicate();
            $dateStartInitial = Carbon::parse($request->date_start)->subDay()->format('Y-m-d');
            $balanceRequest->merge([
                'date_end' => $dateStartInitial,
                'date_start' => null,
            ]);
            $dataBalance = $this->getDataRecords($balanceRequest);
            $balance = $this->getFirstBalanceKardex($dataBalance);
        }
        
        TemporaryKardexRecord::truncate();

        $callback = function(Collection $documents) use (&$balance, $request) {
            $rows = [];
            // $now = now();
            // $columns = (new TemporaryKardexRecord())->getFillable();
            // $columns[] = 'created_at';
            // $columns[] = 'updated_at';

            foreach($documents as $row){
                $rowKardex = $row->getKardexReportCollection($balance, $request->warehouse_id);
                $rowKardex["inventory_kardex_id"] = ($rowKardex["id"])??0;
                unset($rowKardex["id"]);
                // $rowKardex['created_at'] = $now;
                // $rowKardex['updated_at'] = $now;

                // $rows[] = collect($columns)
                //     ->mapWithKeys(function ($column) use ($rowKardex) {
                //         return [$column => $rowKardex[$column] ?? null];
                //     })
                //     ->all();

                $rows[] = $rowKardex;
            }

            TemporaryKardexRecord::insert($rows);
        } ;
        

        $records->chunk(500, $callback);

        return new ReportTemporaryKardexCollection(TemporaryKardexRecord::paginate(config('tenant.items_per_page')));
    }

    public function getFirstBalanceKardex($records){

        $balance = 0 ;
        $lastRow = null;

        $setLastRow = function ($records) use (&$balance, &$lastRow) {
            foreach($records as $row) {
                $lastRow = $row->getKardexReportCollection($balance);
                $lastRow["inventory_kardex_id"] = $lastRow["id"] ?? 0;
            }
        };

        if ($records instanceof \Illuminate\Database\Eloquent\Builder || $records instanceof \Illuminate\Database\Query\Builder) {
            $records->chunk(500, $setLastRow);
        } else {
            $setLastRow($records);
        }

        if ($lastRow) {
            return $lastRow["balance"] ?? 0;
        }
        return 0;
    }

    public function recordsOld(Request $request){

        $records = $this->getRecords($request->all());

        return new ReportKardexCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function records_lots()
    {
        $records = ItemWarehouse::with(['item'])->whereHas('item', function ($q) {
            $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ'], ['lot_code', '!=', null]]);
            $q->whereNotIsSet();
        });

        return new ReportKardexLotsCollection($records->paginate(config('tenant.items_per_page')));

    }


    /**
     * @param $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|InventoryKardex
     */
    public function getRecords($request)
    {
        $warehouse_id = $request['warehouse_id'];
        $item_id = $request['item_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];

        $records = $this->data($item_id, $warehouse_id, $date_start, $date_end);

        return $records;

    }


    /**
     * @param $item_id
     * @param $date_start
     * @param $date_end
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|InventoryKardex
     */
    private function data($item_id, $warehouse_id, $date_start, $date_end)
    {
        $data = InventoryKardex::with(['inventory_kardexable']);
        if($warehouse_id !== 'all') {
            $data->where('warehouse_id', $warehouse_id);
        }
        if ($date_start) {
            $data->where('date_of_issue', '>=', $date_start);
        }
        if ($date_end) {
            $data->where('date_of_issue', '<=', $date_end);
        }
        if ($item_id) {
            $data->where('item_id', $item_id);
        }

        $data
            ->orderBy('item_id')
            ->orderBy('id')
            ->get()->transform(function($row) {
                return $row->getCollectionData();
            });

        // dd($data->first());
        return $data;

    }


    public function getFullDescription($row)
    {
        $desc = ($row->internal_id) ? $row->internal_id . ' - ' . $row->description : $row->description;
        $category = ($row->category) ? " - {$row->category->name}" : "";
        $brand = ($row->brand) ? " - {$row->brand->name}" : "";

        $desc = "{$desc} {$category} {$brand}";

        return $desc;
    }

    private function getDataRecords($request)
    {
        $warehouse_id = $request->input('warehouse_id');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $item_id = $request->input('item_id');

        $query = InventoryKardex::query()
            ->with([
                'item' => function($query) {
                    $query->select('id','description');
                }
                , 'inventory_kardexable' => function (MorphTo $morphTo) {
                $morphTo->constrain([
                    Document::class => function ($query) {
                        $query
                            ->without([
                                'user',
                                'soap_type',
                                'state_type',
                                'document_type',
                                'currency_type',
                                'group',
                                'items',
                                'invoice',
                                'note',
                                'payments',
                                'fee'
                            ])
                            ->with([
                                'note' => function ($query) {
                                    $query
                                        ->with([
                                            'affected_document' => function ($query) {
                                                $query->without([
                                                        'user',
                                                        'soap_type',
                                                        'state_type',
                                                        'document_type',
                                                        'currency_type',
                                                        'group',
                                                        'items',
                                                        'invoice',
                                                'note',
                                                'payments',
                                                'fee'
                                                ])->select('id', 'series', 'number');
                                            }
                                        ])
                                        ->select('id', 'document_id', 'affected_document_id', 'data_affected_document');
                                }, 
                                'sale_note' ,
                                'order_note' => function($query) {
                                    $query->select('prefix', 'id');
                                }
                            ])
                            ->select('id', 'sale_note_id', 'order_note_id', 'dispatch_id', 'sale_notes_relateds', 'series', 'number', 'has_prepayment', 'document_type_id', 'date_of_issue' );

                    },
                    Purchase::class => function ($query) {
                        $query
                            ->without([
                                'user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items', 'purchase_payments'
                            ])
                            ->select('id', 'series', 'number', 'date_of_issue');
                    }, 
                    PurchaseSettlement::class => function ($query) {
                        $query->select('id', 'series', 'number', 'date_of_issue');
                    },
                    SaleNote::class => function($query) {
                        $query
                            ->without([
                                'user',
                                'soap_type',
                                'state_type',
                                'currency_type',
                                'items',
                                'payments'
                            ])
                            ->with([
                                'order_note' => function($query) {
                                    $query->select('prefix', 'id');
                                }
                            ])
                            ->select('order_note_id', 'series', 'number', 'prefix', 'id', 'date_of_issue');

                    },
                    OrderNote::class => function ($query) {
                        $query
                            ->without([
                                'user',
                                'soap_type',
                                'state_type',
                                'currency_type',
                                'items',
                            ])
                            ->select('prefix', 'id', 'date_of_issue');
                    },
                    Dispatch::class => function($query) {
                        $query
                            ->without(['user', 'soap_type', 'state_type', 'document_type', 'unit_type', 'transport_mode_type', 'items', 'reference_document']
                            )
                            ->with(['transfer_reason_type', 'reference_document',
                                'sale_note' => function ($query) {
                                    $query->select('series', 'number', 'prefix', 'id');
                                },
                                'order_note' => function($query) {
                                    $query->select('prefix', 'id');
                                }
                            ])
                            ->select('id', 'reference_sale_note_id', 'reference_order_note_id', 'reference_document_id', 'transfer_reason_type_id', 'series', 'number', 'date_of_issue');
                    },
                    Devolution::class => function ($query) {
                        $query->select('prefix', 'id', 'date_of_issue');
                    },
                    Inventory::class => function ($query) {
                        $query
                            ->without([
                                'transaction',
                                'warehouse',
                                'warehouse_destination',
                                'item'
                            ])
                            ->with([
                                'guide' => function($query) {
                                    $query->select('id', 'series', 'number', 'date_of_issue');
                                },
                                'inventories_transfer' => function($query) {
                                    $query->select('id', 'series', 'created_at', 'number');
                                },
                            ])
                            ->select('id', 'type', 'inventory_transaction_id', 'inventories_transfer_id', 'description', 'date_of_issue', 'guide_id', 'warehouse_destination_id', 'warehouse_id');

                    },
                ]);
            }]);

        if ($warehouse_id!='all') {
            $query->where('warehouse_id', $warehouse_id);
        }

        if ($date_start) {
            $query->where('date_of_issue', '>=', $date_start);
        }

        if ($date_end) {
            $query->where('date_of_issue', '<=', $date_end);
        }

        if ($item_id) {
            $query->where('item_id', $item_id);
        }

        $records = $query->orderBy('item_id')
            ->orderBy('id');
        
        return $records;
    
    }

    private function getData($request)
    {
        $company = Company::query()->first();
        $establishment = Establishment::query()->find(auth()->user()->establishment_id);
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $item_id = $request->input('item_id');
        $item = Item::query()->findOrFail($request->input('item_id'));

        $warehouse = Warehouse::query()
            ->where('establishment_id', $establishment->id)
            ->first();

        $query = InventoryKardex::query()
            ->with(['inventory_kardexable'])
            ->where('warehouse_id', $warehouse->id);

        if ($date_start) {
            $query->where('date_of_issue', '>=', $date_start);
        }

        if ($date_end) {
            $query->where('date_of_issue', '<=', $date_end);
        }

        if ($item_id) {
            $query->where('item_id', $item_id);
        }

        $records = $query->orderBy('item_id')
            ->orderBy('id')
            ->get();

        return [
            'company' => $company,
            'establishment' => $establishment,
            'warehouse' => $warehouse,
            'item_id' => $item_id,
            'item' => $item,
            'models' => $this->models,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'records' => $records,
            'balance' => 0,
        ];
    }

    /**
     * PDF
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {
        $data = $this->getData($request);
        $balance = 0;
        if($request->date_start){
            $balanceRequest = $request->duplicate();
            $dateStartInitial = Carbon::parse($request->date_start)->subDay()->format('Y-m-d');
            $balanceRequest->merge([
                'date_end' => $dateStartInitial,
                'date_start' => null,
            ]);
            $dataBalance = $this->getData($balanceRequest);
            $balance = $this->getFirstBalanceKardex($dataBalance["records"]);
        }

        $data["balance"] = $balance;
        $pdf = PDF::loadView('inventory::reports.kardex.report_pdf', $data);
        $filename = 'Reporte_Kardex' . date('YmdHis');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Excel
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function excel(Request $request)
    {
        $data = $this->getData($request);
        $balance = 0;
        if($request->date_start){
            $balanceRequest = $request->duplicate();
            $dateStartInitial = Carbon::parse($request->date_start)->subDay()->format('Y-m-d');
            $balanceRequest->merge([
                'date_end' => $dateStartInitial,
                'date_start' => null,
            ]);
            $dataBalance = $this->getData($balanceRequest);
            $balance = $this->getFirstBalanceKardex($dataBalance["records"]);
        }
        $data['balance'] = $balance;

        $kardexExport = new KardexExport();
        $kardexExport
            ->balance($data['balance'])
            ->item_id($data['item_id'])
            ->records($data['records'])
            ->models($data['models'])
            ->company($data['company'])
            ->establishment($data['establishment'])
            ->item($data['item']);

        return $kardexExport->download('ReporteKar' . Carbon::now() . '.xlsx');
    }

    public function getRecords2($request)
    {

        $item_id = $request['item_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $warehouse_id = $request['warehouse_id'];

        $records = $this->data2($item_id, $date_start, $date_end, $warehouse_id);

        return $records;
    }

    private function data2($item_id, $date_start, $date_end, $warehouse_id = null)
    {

        // $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

        if ($date_start && $date_end) {

            $data = ItemLotsGroup::whereBetween('date_of_due', [$date_start, $date_end])
                ->orderBy('item_id')->orderBy('id');

        } else {

            $data = ItemLotsGroup::orderBy('item_id')->orderBy('id');
        }

        if ($item_id) {
            $data = $data->where('item_id', $item_id);
        }

        return $data;

    }

    public function records_lots_kardex(Request $request)
    {
        $records = $this->getRecords2($request->all());

        return new ReportKardexLotsGroupCollection($records->paginate(config('tenant.items_per_page')), $request->warehouse_id);


    }


    public function getRecords3($request)
    {

        $item_id = $request['item_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $warehouse_id = $request['warehouse_id'];

        $records = $this->data3($item_id, $date_start, $date_end, $warehouse_id);

        return $records;

    }


    private function data3($item_id, $date_start, $date_end, $warehouse_id = null)
    {

        // $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

        if ($date_start && $date_end) {

            $data = ItemLot::whereBetween('date', [$date_start, $date_end])
                ->orderBy('item_id')->orderBy('id');

        } else {

            $data = ItemLot::orderBy('item_id')->orderBy('id');
        }

        if ($item_id) {
            $data = $data->where('item_id', $item_id);
        }

        if($warehouse_id && $warehouse_id != 'all') {
            $data = $data->where('warehouse_id', $warehouse_id);
        }

        return $data;
    }

    public function records_series_kardex(Request $request)
    {
        $records = $this->getRecords3($request->all());

        return new ReportKardexItemLotCollection($records->paginate(config('tenant.items_per_page')));

    }




    // public function search(Request $request) {
    //     //return $request->item_selected;
    //     $balance = 0;
    //     $d = $request->d;
    //     $a = $request->a;
    //     $item_selected = $request->item_selected;

    //     $items = Item::query()->whereNotIsSet()
    //         ->where([['item_type_id', '01'], ['unit_type_id', '!=','ZZ']])
    //         ->latest()
    //         ->get();

    //     $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

    //     if($d && $a){

    //         $reports = InventoryKardex::with(['inventory_kardexable'])
    //                     ->where([['item_id', $request->item_selected],['warehouse_id', $warehouse->id]])
    //                     ->whereBetween('date_of_issue', [$d, $a])
    //                     ->orderBy('id')
    //                     ->paginate(config('tenant.items_per_page'));

    //     }else{

    //         $reports = InventoryKardex::with(['inventory_kardexable'])
    //                     ->where([['item_id', $request->item_selected],['warehouse_id', $warehouse->id]])
    //                     ->orderBy('id')
    //                     ->paginate(config('tenant.items_per_page'));

    //     }

    //     //return json_encode($reports);

    //     $models = $this->models;

    //     return view('inventory::reports.kardex.index', compact('items', 'reports', 'balance','models', 'a', 'd','item_selected'));
    // }
    public function getPdfGuide($guide_id)
    {
        $company = Company::query()->first();

        $record = Guide::query()
            ->with('inventory_transaction', 'warehouse', 'document_type', 'items', 'items.item')
            ->find($guide_id);

        // dd($record->inventory_transaction);

        $items = [];
        foreach ($record->items as $item) {

            $is_lot_group = (bool)$item->item->lots_enabled;
            $is_serie = (bool)$item->item->series_enabled;
            $lots = null;
            $series = null;
            if($record->inventory_transaction->type == 'input') {
                if($is_lot_group){
                    $lots = ItemLotsGroup::where('item_id', $item->item_id)->where('created_at', $record->created_at)->get();
                }
                if($is_serie){
                    $series = $item->item->item_lots->where('created_at', $record->created_at)->all();
                }
            } else {
                if($is_lot_group){
                    $lots = ItemLotsGroup::where('item_id', $item->item_id)->where('updated_at', $record->created_at)->get();
                }
                if($is_serie){
                    $series = $item->item->item_lots->where('updated_at', $record->created_at)->all();
                }
            }

            $items[] = [
                'item_internal_id' => $item->item->internal_id,
                'item_name' => $item->item_name,
                'unit_type_id' => $item->item->unit_type_id,
                'quantity' => $item->quantity,
                'lot_enabled' => $is_lot_group,
                'lot' => $is_lot_group?$lots:null,
                'series_enabled' => $is_serie,
                'series' => $is_serie?$series:null,
            ];
        }

        // dd($items);

        $data = [
            'company_number' => $company->number,
            'document_type_name' => $record->document_type->description,
            'document_number' => $record->series . '-' . $record->number,
            'document_date_of_issue' => $record->date_of_issue->format('d/m/Y'),
            'warehouse_name' => $record->warehouse->description,
            'transaction_name' => $record->inventory_transaction->name,
            'items' => $items
        ];

        $pdf = PDF::loadView('inventory::reports.kardex.guide', $data);
        $pdf->setPaper('A4', 'portrait');
        // $pdf->setPaper('A4', 'landscape');
        $filename = 'Guia_' . date('YmdHis');

        return $pdf->stream($filename . '.pdf');
    }
}
