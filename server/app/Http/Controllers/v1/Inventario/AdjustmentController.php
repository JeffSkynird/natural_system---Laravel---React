<?php

namespace App\Http\Controllers\v1\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use App\Models\Kardex;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdjustmentController extends Controller
{
    public function index()
    {
        try {
        
            $data = Adjustment::join('products', 'adjustments.product_id', '=', 'products.id')
            ->join('reasons', 'adjustments.reason_id', '=', 'reasons.id')
            ->selectRaw('products.name,products.bar_code,products.stock,products.fraction,adjustments.quantity as quantity,reasons.name as reason,adjustments.created_at')->get();
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
            $data = $request->input('data');
            foreach ($data as $val) {
         
                Adjustment::create([
                    'product_id' => $val['product_id'],
                    'reason_id' => $val['reason_id'],
                    'quantity'=> $val['quantity'],
                    'status'=>'I',
                    'user_id'=> Auth::id()
                ]);
               $ord = Product::find($val['product_id']);
               if($ord!=null){
                   if($val['reason_id']==1){
                       $ord->stock = doubleval($ord->stock) + doubleval($val['quantity']);
                       $ord->save();
                       Kardex::create([
                        'product_id' => $val['product_id'],
                        'quantity' => $val['quantity'],
                        'type' => 'A',
                        'concept' =>'E',
                        'stock'=>$ord->stock,
                        'user_id'=>Auth::id()
                    ]);
                   }else{
                       if($ord->stock!=0){
                            $ord->stock = doubleval($ord->stock) - doubleval($val['quantity']);
                            $ord->save();
                            Kardex::create([
                                'product_id' => $val['product_id'],
                                'quantity' => $val['quantity'],
                                'type' => 'A',
                                'concept' =>'S',
                                'stock'=>$ord->stock,
                                'user_id'=>Auth::id()
                            ]);
                       }
                      
                   }
              
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
        $data = Adjustment::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function update(Request $request,$id){
        try {
            $co = Adjustment::find($id);
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
        $data = Adjustment::find($id);
        $data->delete();
        return response()->json([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
}
