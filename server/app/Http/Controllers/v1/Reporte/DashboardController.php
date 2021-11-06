<?php

namespace App\Http\Controllers\v1\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Cash;
use App\Models\Invoice;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function salesLastMonth(Request $request)
    {
        try {

            $invoices = Invoice::select(
                DB::raw('sum(total) as sums'),
                DB::raw("to_char(created_at,'TMMonth YYYY') as months"),
                DB::raw('count(id) as count'),
                DB::raw('max(created_at) as createdAt')
            )->orderBy(DB::raw('createdAt'))->groupBy('months')->where('status', 'A')->take(6)->get();
            $meses = array();
            $cantidad = array();
            $ventas = array();
            foreach ($invoices as $value) {
                array_push($meses, $value->months);
                array_push($cantidad, $value->count);
                array_push($ventas, $value->sums);
            }
            return response()->json([
                "status" => "200",
                'data' => array(
                    'meses' => $meses,
                    'cantidad' => $cantidad,
                    'ventas' => $ventas
                ),
                "message" => 'Información obtenida con éxito',
                "type" => 'success'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => "500",
                "message" => $exception->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function salesCash(Request $request)
    {
      
        $cashC = Cash::where([
            [DB::raw('DATE(created_at)'), '=',Carbon::now()->format('Y-m-d')]
        ])->sum('final_amount');
        $invoice = Invoice::where([
            ['status', '=', 'A'],
            [DB::raw('DATE(created_at)'), '=', Carbon::now()->format('Y-m-d')]
        ])->sum('total');
        return response()->json([
            "status" => "200",
            'data' => array(
                'factura' => round(doubleval($invoice), 2),
                'caja' => $cashC!=null?round(doubleval($cashC), 2):0,
            ),
            "message" => 'Información obtenida con éxito',
            "type" => 'success'
        ]);
    }
    public function salesPurchases(Request $request)
    {

        $oder = Order::where([
            ['status', '=', 'A'],
            [DB::raw('created_at'), '>=', DB::raw("date_trunc('month', now()) - interval '3 month'")]
        ])->sum('total');
        $invoice = Invoice::where([
            ['status', '=', 'A'],
            [DB::raw('created_at'), '>=', DB::raw("date_trunc('month', now()) - interval '3 month'")]
        ])->sum('total');
        return response()->json([
            "status" => "200",
            'data' => array(
                'ventas' => round(doubleval($invoice), 2),
                'compras' => round(doubleval($oder), 2),
              
            ),
            "message" => 'Información obtenida con éxito',
            "type" => 'success'
        ]);
    }
}
