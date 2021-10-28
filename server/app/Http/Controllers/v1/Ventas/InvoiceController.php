<?php

namespace App\Http\Controllers\v1\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        try {
            $data = Invoice::leftjoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->join('invoice_products', 'invoice_products.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_products.product_id', '=', 'products.id')
            ->selectRaw('invoices.id,invoices.final_consumer,invoices.total,clients.document,clients.names,products.name,products.bar_code')->get();
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

        $data = $request->input('data');
        $ordId = $request->input('order_id');
        try {
            foreach ($data as $val) {
                Invoice::create([
                    'product_id' => $val['product_id'],
                    'warehouse_id' => $val['warehouse_id'],
                    'is_stored'=>1
                ]);
               $ord = Invoice::where([
                ['order_id', '=', $ordId],
                ['product_id', '=',  $val['product_id']],
               ])->first();
               if($ord!=null){
                $ord->is_stored=1;
                $ord->save();
               }
            }
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
        $data = Invoice::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function update(Request $request,$id){
        try {
            $co = Invoice::find($id);
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
        $data = Invoice::find($id);
        $data->delete();
        return response()->json([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
}
