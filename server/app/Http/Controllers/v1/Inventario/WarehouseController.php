<?php

namespace App\Http\Controllers\v1\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index()
    {
        try {
          
            $data = Warehouse::join('zones', 'warehouses.zone_id', '=', 'zones.id')
            ->leftjoin('warehouse_products', 'warehouse_products.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('warehouses.*,zones.name as zone, count(warehouse_products.*) as products')->groupBy('warehouses.id','zones.name')->get();
           
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
        try {
            $request['user_id']=Auth::id();
            Warehouse::create($request->all());
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
        $data = Warehouse::find($id);
        return json_encode([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function showProducts($id)
    {
        $data = WarehouseProduct::join('inventories', 'warehouse_products.inventory_id', '=', 'inventories.id')
        ->join('products as p', 'inventories.product_id', '=', 'p.id')
        ->join('warehouses as w1', 'warehouse_products.warehouse_id', '=', 'w1.id')
        ->where('w1.id', $id)
        ->selectRaw('p.name,inventories.id as inventory_id,inventories.jp_code,inventories.supplier_code,w1.name as warehouse,w1.id as warehouse_id,warehouse_products.created_at')->get();

        return json_encode([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function update(Request $request,$id){
        try {
            $co = Warehouse::find($id);
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
        $data = Warehouse::find($id);
        $data->delete();
        return json_encode([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
 
}
