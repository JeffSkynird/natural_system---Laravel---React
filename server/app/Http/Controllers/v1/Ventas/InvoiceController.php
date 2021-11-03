<?php

namespace App\Http\Controllers\v1\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Kardex;
use App\Models\Product;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        try {
            $data = Invoice::leftjoin('clients', 'invoices.client_id', '=', 'clients.id')
          
            ->selectRaw('invoices.created_at,invoices.id,invoices.final_consumer,invoices.total,invoices.iva,clients.document,clients.names')->get();
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
    public function decreaseProductStock($id,$quantity){
        $pr = Product::find($id);
        $pr->stock = $quantity;
        $pr->save();
       
    }
    public function create(Request $request)
    {

        $data = $request->input('data');
        $clientId = $request->input('client_id');
        $finalConsumer = $request->input('final_consumer');
        $total = $request->input('total');
        $iva = $request->input('iva');

        try {
            $inv= Invoice::create([
                'client_id' => $clientId,
                'final_consumer' => $finalConsumer,
                'total'=> $total,
                'iva'=>$iva,
                'status'=>'A',
                'user_id'=>1
            ]);
            foreach ($data as $val) {
                InvoiceProduct::create([
                    'invoice_id' =>  $inv->id,
                    'product_id' => $val['product_id'],
                    'quantity'=> $val['quantity'],
                    'subtotal'=>$val['subtotal'],
                    'user_id'=>1
                ]);
                $this->decreaseProductStock($val['product_id'],$val['stock']);
                Kardex::create([
                    'product_id' => $val['product_id'],
                    'quantity' => $val['quantity'],
                    'type' => 'V',
                    'concept' =>'S',
                    'stock'=>$val['stock'],
                    'user_id'=>1
                ]);
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
