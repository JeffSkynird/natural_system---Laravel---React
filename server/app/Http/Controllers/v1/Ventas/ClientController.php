<?php

namespace App\Http\Controllers\v1\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function kpis(Request $request){
        $fromDate = $request->input('desde');
        $tillDate = $request->input('hasta');
        $ventas = Invoice::whereBetween('created_at',[$fromDate,$tillDate])->count();
        $monto = Invoice::whereBetween('created_at',[$fromDate,$tillDate])->sum('total');
        $clientes = Client::whereBetween('created_at',[$fromDate,$tillDate])->count();
        $proveedores = Supplier::whereBetween('created_at',[$fromDate,$tillDate])->count();

        return response()->json([
            "status" => "200",
            'data'=> [
                'ventas' => $ventas,
                'monto' => floatval($monto),
                'clientes' => $clientes,
                'proveedores' => $proveedores
            ]
            ,
            "message" => 'Data obtenida con éxito',
                "type" => 'success'
        ]);
    }
    public function index()
    {
        try {
            $data = Client::all();
            return response()->json([
                "status" => "200",
                'data'=>$data,
                "message" => 'Data obtenida con éxito',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function create(Request $request)
    {
        try {
            $params= $request->all();
            $params['user_id']=Auth::id();
            Client::create($params);
            return response()->json([
                "status" => "200",
                "message" => 'Registro exitoso',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function show($id)
    {
        $data = Client::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function update(Request $request,$id){
        try {
            $co = Client::find($id);
            $co->update($request->all());
            return response()->json([
                "status" => "200",
                "message" => 'Modificación exitosa',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
  
    public function delete($id)
    {
        $data = Client::find($id);
        $data->delete();
        return response()->json([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
}
