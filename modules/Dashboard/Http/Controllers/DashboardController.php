<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Exports\AccountsReceivable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Dashboard\Helpers\DashboardData;
use Modules\Dashboard\Helpers\DashboardKpi;
use Modules\Dashboard\Helpers\DashboardUtility;
use Modules\Dashboard\Helpers\DashboardSalePurchase;
use Modules\Dashboard\Helpers\DashboardView;
use Modules\Dashboard\Helpers\DashboardStock;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Document;
use App\Models\Tenant\Company;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Arr;
use Modules\Dashboard\Helpers\DashboardInventory;
use App\Models\Tenant\Configuration;

/**
 * Class DashboardController
 *
 * @package Modules\Dashboard\Http\Controllers
 * @mixin Controller
 */
class DashboardController extends Controller
{
    public function index()
    {
        // dd('aqui');
        if(auth()->user()->type != 'admin' && !auth()->user()->searchModule('dashboard')){
            return redirect()->route('tenant.documents.index');
        } elseif (auth()->user()->type == 'admin' && !auth()->user()->searchModule('dashboard')) {
            return redirect()->route('tenant.documents.index');
        }

        $company = Company::select('soap_type_id')->first();
        $soap_company  = $company->soap_type_id;
        $configuration = Configuration::first();

        return view('dashboard::index', compact('soap_company','configuration'));
    }

    public function filter()
    {
        return [
            'establishments' => DashboardView::getEstablishments()
        ];
    }

    public function globalData(Request $request)
    {
        return response()->json((new DashboardData())->globalData($request->all()), 200);
    }

    public function cashFlow(Request $request)
    {
        return response()->json((new DashboardData())->cashFlow($request->all()), 200);
    }

    public function lowStock(Request $request)
    {
        return response()->json((new DashboardData())->lowStock($request->all()), 200);
    }

    public function salesWeek(Request $request)
    {
        return response()->json((new DashboardData())->salesWeek($request->all()), 200);
    }

    public function paymentMethods(Request $request)
    {
        return response()->json((new DashboardData())->paymentMethods($request->all()), 200);
    }

    public function sunatStatus(Request $request)
    {
        return response()->json((new DashboardData())->sunatStatus($request->all()), 200);
    }

    public function debtors(Request $request)
    {
        return response()->json((new DashboardData())->debtors($request->all()), 200);
    }

    public function monthGoal()
    {
        return response()->json((new DashboardData())->monthGoal(), 200);
    }

    public function data(Request $request)
    {
        return [
            'data' => (new DashboardData())->data($request->all()),
        ];
    }

    public function kpi(Request $request)
    {
        return [
            'data' => (new DashboardKpi())->data($request->all()),
        ];
    }

    public function monthlyComparison(Request $request)
    {
        return [
            'data' => (new DashboardKpi())->monthlyComparison($request->all()),
        ];
    }

    public function salesGrowth(Request $request)
    {
        return [
            'data' => (new DashboardKpi())->salesGrowth($request->all()),
        ];
    }

    // public function unpaid(Request $request)
    // {
    //     return [
    //             'records' => (new DashboardView())->getUnpaid($request->all())
    //     ];
    // }

    // public function unpaidall()
    // {

    //     return Excel::download(new AccountsReceivable, 'Allclients.xlsx');

    // }

    public function data_aditional(Request $request)
    {
        return [
            'data' => (new DashboardSalePurchase())->data($request->all()),
        ];
    }

    public function igvSales(Request $request)
    {
        return [
            'data' => (new DashboardData())->salesTotalByRange(
                $request->input('establishment_id'),
                $request->input('date_start'),
                $request->input('date_end')
            ),
        ];
    }

    public function igvPurchases(Request $request)
    {
        return [
            'data' => (new DashboardSalePurchase())->purchasesTotalByRange(
                $request->input('establishment_id'),
                $request->input('date_start'),
                $request->input('date_end')
            ),
        ];
    }

    public function stockByProduct(Request $request)
    {
        return  (new DashboardStock())->data($request);
    }


    public function utilities(Request $request)
    {
        return [
            'data' => (new DashboardUtility())->data($request->all()),
        ];
    }

    public function df()
    {
        $path = app_path();
        //df -m -h --output=used,avail,pcent /

        $used = new Process(['df' ,'-m', '-h', '--output=used','/']);
        $used->run();
        if (!$used->isSuccessful()) {
            return ['error'];
            throw new ProcessFailedException($used);
        }
        $disc_used = $used->getOutput();
        $array[] = str_replace("\n","",$disc_used);

        $avail = new Process(['df', '-m', '-h', '--output=avail', '/']);
        $avail->run();
        if (!$avail->isSuccessful()) {
            return ['error'];
            throw new ProcessFailedException($avail);
        }
        $disc_avail = $avail->getOutput();
        $array[] = str_replace("\n","",$disc_avail);

        $pcent = new Process(['df' ,'-m' ,'-h' , '--output=pcent' ,'/']);
        $pcent->run();
        if (!$pcent->isSuccessful()) {
            return ['error'];
            throw new ProcessFailedException($pcent);
        }
        $disc_pcent = $pcent->getOutput();
        $array[] = str_replace("\n","",$disc_pcent);

        return $array;


    }

    /**
     * Extensión de ventas por producto
     *
     */
    public function salesByProduct()
    {
        return view('dashboard::sales_by_product');
    }

    public function productOfDue(Request $request)
    {
        return  (new DashboardInventory())->data($request);
    }

}
