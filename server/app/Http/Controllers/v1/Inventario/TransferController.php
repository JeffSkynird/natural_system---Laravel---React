<?php

namespace App\Http\Controllers\v1\Inventario;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Transfer;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        try {
            $data = Transfer::join('inventories', 'transfers.inventory_id', '=', 'inventories.id')
            ->join('products as p', 'inventories.product_id', '=', 'p.id')
            ->join('warehouses as w1', 'transfers.warehouse_origin', '=', 'w1.id')
            ->join('warehouses as w2', 'transfers.warehouse_destination', '=', 'w2.id')
            ->selectRaw('p.image,p.name,inventories.jp_code,inventories.supplier_code,w1.name as warehouse_origin,w2.name as warehouse_destination,transfers.created_at')->get();
            return json_encode([
                "status" => "200",
                'data'=>$data,
                "message" => 'Data obtenida con éxito',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return json_encode([
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
                WarehouseProduct::create([
                    'product_id' => $val['product_id'],
                    'warehouse_id' => $val['warehouse_id'],
                    'is_stored'=>1
                ]);
               $ord = OrderProduct::where([
                ['order_id', '=', $ordId],
                ['product_id', '=',  $val['product_id']],
               ])->first();
               if($ord!=null){
                $ord->is_stored=1;
                $ord->save();
               }
            }
            return json_encode([
                "status" => "200",
                "message" => 'Registro exitoso',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function show($id)
    {
        $data = Transfer::find($id);
        return json_encode([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function update(Request $request,$id){
        try {
            $co = Transfer::find($id);
            $co->update($request->all());
            return json_encode([
                "status" => "200",
                "message" => 'Modificación exitosa',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
  
    public function delete($id)
    {
        $data = Transfer::find($id);
        $data->delete();
        return json_encode([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
}
