<?php

namespace App\Http\Controllers\v1\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Kardex;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\WarehouseOrder;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        try {

            $data = Order::join('suppliers', 'orders.supplier_id', '=', 'suppliers.id')
                ->selectRaw('orders.*,suppliers.business_name as supplier')->groupBy('orders.id', 'suppliers.business_name')->get();
            $totalCompras = Order::sum('total');
            return response()->json([
                "status" => "200",
                'data' => $data,
                'total_compras'=>floatval($totalCompras),
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
    public function createDetailOrder($product, $quantity, $order)
    {
        OrderProduct::create([
            'order_id' => $order,
            'quantity' => $quantity,
            'product_id' => $product
        ]);
        $pr = Product::find($product);
        $pr->stock =doubleval($pr->stock)+doubleval($quantity);
        $pr->save();
        Kardex::create([
            'product_id' => $product,
            'quantity' => $quantity,
            'type' => 'C',
            'concept' =>'E',
            'stock'=>$pr->stock,
            'user_id'=>Auth::id()
        ]);
     
    }
    public function obtenerDetallePedido($id)
    {
        try {
            $data = OrderProduct::join('products as pro', 'order_products.product_id', '=', 'pro.id')
                ->join('orders as or', 'order_products.order_id', '=', 'or.id')
                ->join('suppliers as su', 'or.supplier_id', '=', 'su.id')
                ->where('or.id', $id)
                ->selectRaw('order_products.id as id_detalle,su.business_name as supplier,su.id as supplier_id,pro.id as product_id,pro.name as product,order_products.quantity')->groupBy('order_products.id', 'su.id', 'pro.id', 'order_products.quantity')->get();
            return response()->json([
                "status" => "200",
                'data' => $data,
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
    public function guardarAlmacenProducto($data)
    {
    }
    public function distribuirPedido(Request $request, $id)
    {
        $data = $request->input('data');
        try {
            foreach ($data as $val) {
                $w = WarehouseOrder::where([
                    ['inventory_id', '=', $val['inventory_id']],
                    ['order_id', '=', $id],
                ])->first();
                if(!is_null($w)){
                    $w->warehouse_id = $val['warehouse_id'];
                    $w->save();
                    //GUARDADO ALMACEN PRODUCTOS
                    WarehouseProduct::create([
                        'warehouse_id'=>$val['warehouse_id'],
                        'inventory_id'=>$val['inventory_id']
                    ]);
                }
              
            }

            return response()->json([
                "status" => "200",
                "message" => 'Datos guardados con éxito',
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
            $data = $request->all();
            foreach ($data['suppliers'] as $val) {
                $order = Order::create([
                    'supplier_id' => $val['supplier_id'],
                    'total'=>$data['total'],
                    'user_id' => Auth::id()

                ]);
                foreach ($val['products'] as $val2) {
                    $this->createDetailOrder($val2['product_id'], $val2['quantity'], $order->id);
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
    private function storeProductWarehouse($poduct, $warehouse)
    {
        try {
            WarehouseProduct::create([
                'product_id' => $poduct,
                'warehouse_id' => $warehouse,
            ]);
        } catch (\Exception $e) {
        }
    }
    public function viewStatusOrder()
    {
        $ar = array(
            [
                'id' => 'I',
                'name' => 'Pendiente'
            ], [
                'id' => 'E',
                'name' => 'Entregado'
            ], [
                'id' => 'A',
                'name' => 'Anulado'
            ]
        );
        return response()->json([
            "status" => "200",
            "data" => $ar,
            "type" => 'success'
        ]);
    }
    public function storeInventory(Request $request)
    {
        try {

            $order = $request->input('order');
            $status = $request->input('status');
            $or = Order::find($order);
            $or->status = $status;
            $or->save();
            if ($status == "E") {
                $oderProducts = OrderProduct::where('order_id', $order)->get();
                foreach ($oderProducts as $val) {
                    $cantidad = $val->quantity;
                    for ($i = 1; $i <= $cantidad; $i++) {
                        $inv = Inventory::create([
                            'supplier_code' => 'AAAAA',
                            'jp_code' => 'PPPP',
                            'product_id' => $val->product_id
                        ]);
                        WarehouseOrder::create([
                            'inventory_id' => $inv->id,
                            'order_id' => $order
                        ]);
                    }
                }
            }
            return response()->json([
                "status" => "200",
                "message" => 'Estado actualizado con éxito',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
        }
    }
    public function viewOrderInventory($id)
    {
        try {
            $order = $id;
           /*  $data = WarehouseOrder::join('inventories', 'warehouse_orders.inventory_id', '=', 'inventories.id')
                ->join('products', 'inventories.product_id', '=', 'products.id')
                ->where([
                    ['order_id', '=', $order],
                    ['warehouse_orders.warehouse_id', '=', null],
                ])
                ->selectRaw('inventories.id as inventory_id,products.id as product_id,products.name,inventories.jp_code,inventories.supplier_code')->get(); */
                $data = OrderProduct::join('products', 'order_products.product_id', '=', 'products.id')
                ->where([
                    ['order_id', '=', $order],
                    ['order_products.is_stored', '=', 0],
                ])
                ->selectRaw('products.id as product_id,products.name,products.bar_code,products.stock')->get();

            return response()->json([
                "status" => "200",
                "message" => 'Datos obtenidos con éxito',
                "data" => $data,
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
        $data = Order::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function autorize($id)
    {
        $idUser = Auth::id();

        $data = Order::find($id);
        $data->authorized_by = $idUser;
        $data->save();
        return response()->json([
            "status" => "200",
            "message" => 'Autorización exitosa',
            "type" => 'success'
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $idUser = Auth::id();
            $data = $request->all();
            $ord = Order::find($id);
            $ord->delete();
            foreach ($data['suppliers'] as $val) {
                $order = Order::create([
                    'supplier_id' => $val['supplier_id'],
                    'created_by' => $idUser,
                    'requested_by' => $idUser,
                    'authorized_by' => !is_null($data['authorize']) ? ($data['authorize'] == 1 ? $idUser : null) : null
                ]);
                foreach ($val['products'] as $val2) {
                    $this->createDetailOrder($val2['product_id'], $val2['quantity'], $order->id);
                }
            }
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
        try {
            $data = Order::find($id);

            $data->delete();
            return response()->json([
                "status" => "200",
                "message" => 'Eliminación exitosa',
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
}
