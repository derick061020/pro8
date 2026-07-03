<?php

namespace Modules\Report\Jobs;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Traits\JobReportTrait;
use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Hyn\Tenancy\Environment;

class ProcessReportSalesConsolidated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, StorageDocument, JobReportTrait;

    public $tray_id;
    public $website_id;
    public $request;
    public $user_id;

    public $timeout = 1800; // 30 min, ajustable

    public function __construct($tray_id, $website_id, $request, $user_id)
    {
        $this->tray_id    = $tray_id;
        $this->website_id = $website_id;
        $this->request    = $request;
        $this->user_id    = $user_id;
    }

    public function handle()
    {
        // En background no hay timeout HTTP — podemos darle memoria de sobra
        ini_set('memory_limit', '1024M');
        ini_set('pcre.backtrack_limit', '5000000');
        set_time_limit(0);

        try {
            // 1. Tenancy + login (igual que ProcessReportGeneralItems)
            $website = $this->findWebsite($this->website_id);
            $tenancy = app(Environment::class);
            $tenancy->tenant($website);
            $this->login($this->user_id);

            // 2. Mismos datos que el pdf() inline
            $controller = new \Modules\Report\Http\Controllers\ReportSaleConsolidatedController();
            $records = $controller->getRecordsSalesConsolidated($this->request)->get();

            $company = Company::first();
            $establishment = (!empty($this->request['establishment_id']))
                ? Establishment::findOrFail($this->request['establishment_id'])
                : auth()->user()->establishment;
            $params = $this->request;

            // 3. Generar PDF y guardarlo
            $tray     = $this->findDownloadTray($this->tray_id);
            $path     = $this->getReportPath('pdf');
            $filename = 'Reporte_Consolidado_Items_Ventas_'.date('YmdHis').'-'.$tray->user_id;

            $pdf = PDF::loadView(
                'report::sales_consolidated.report_pdf',
                compact('records', 'company', 'establishment', 'params')
            );

            $this->uploadStorage($filename, $pdf->output(), $path);
            unset($pdf);

            // 4. Marcar como terminado en la bandeja
            $this->finishedDownloadTray($tray, $filename, $path);

        } catch (\Throwable $th) {
            $tray = $this->findDownloadTray($this->tray_id);
            if ($tray) {
                $tray->date_end = date('Y-m-d H:i:s');
                $tray->status   = 'FAILED';
                $tray->save();
            }
            $this->fail($th);
        }
    }
}