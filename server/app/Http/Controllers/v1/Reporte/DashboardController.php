<?php

namespace App\Http\Controllers\v1\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

use Illuminate\Http\Request;
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
                'created_at'
            )->orderBy('created_at','DESC')->groupBy('months','created_at')->where('status','A')->take(6)->get();

            $meses =array();
            $cantidad =array();
            $ventas =array();
            foreach ($invoices as $value) {
                    array_push($meses,$value->months);
                    array_push($cantidad,$value->count);
                    array_push($ventas,$value->sums);
            }
            return response()->json([
                "status" => "200",
                'data'=>array(
                    'meses'=>$meses,
                    'cantidad'=>$cantidad,
                    'ventas'=>$ventas
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
}
