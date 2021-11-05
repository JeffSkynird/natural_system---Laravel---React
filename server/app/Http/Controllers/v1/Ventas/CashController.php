<?php

namespace App\Http\Controllers\v1\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cash;
use App\Models\Denomination;
use App\Models\Split;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashController extends Controller
{
   
    public function index()
    {
        try {
            $data = Cash::all();
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
            $amountStart = $request->input('start_amount');
            $userId = Auth::id();
            //COMPRUEBO SI YA CERRO LA CAJA
            $cashC = Cash::where([
                ['status', '=', 'C'],
                ['user_id', '=', $userId],
                [DB::raw('DATE(created_at)'), '=',DB::raw('DATE(CURRENT_TIMESTAMP)')]
            ])->first();
            if(!is_null($cashC)){
                return response()->json([
                    "status" => "200",
                    "message" => 'Ya ha cerrado la caja hoy',
                    "type" => 'error'
                ]);
            }
            //COMPRUEBO SI NO EXISTE UNA CAJA YA ABIERTA
            $cash = Cash::where([
                ['status', '=', 'A'],
                ['user_id', '=', $userId],
                [DB::raw('DATE(created_at)'), '=',DB::raw('DATE(CURRENT_TIMESTAMP)')]
            ])->first();
            if(!is_null($cash)){
                return response()->json([
                    "status" => "200",
                    "message" => 'Ya ha abierto la caja hoy',
                    "type" => 'error'
                ]);
            }
            Cash::create([
                'user_id' => $userId,
                'status' => 'A',
                'start_amount' => $amountStart,
                'final_amount'=> 0
            ]);
            return response()->json([
                "status" => "200",
                "message" => 'Caja abierta con éxito',
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
    public function close(Request $request)
    {
        try {
      
            $userId = Auth::id();
            //COMPRUEBO SI NO EXISTE UNA CAJA YA ABIERTA
            $cash = Cash::where([
                ['user_id', '=', $userId],
                [DB::raw('DATE(created_at)'), '=',DB::raw('DATE(CURRENT_TIMESTAMP)')]
            ])->first();
            if(is_null($cash)){
                return response()->json([
                    "status" => "200",
                    "message" => 'No ha abierto la caja hoy',
                    "type" => 'error'
                ]);
            }
            if($cash->status == 'C'){
                return response()->json([
                    "status" => "200",
                    "message" => 'Ya ha cerrado la caja hoy',
                    "type" => 'error'
                ]);
            }
            $cash->status = 'C';
            $cash->save();
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
        $data = Cash::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function splits(){
        $denominations=Denomination::select('id','name','amount')->get();
        return $denominations;
    }
    public function saveSplit(Request $request){
        try {
            $data = $request->input('data');
            $userId = Auth::id();
            $cash = Cash::where([
                ['user_id', '=', $userId],
                [DB::raw('DATE(created_at)'), '=',DB::raw('DATE(CURRENT_TIMESTAMP)')]
            ])->first();
            if(is_null($cash)){
                return response()->json([
                    "status" => "200",
                    "message" => 'No ha abierto la caja hoy',
                    "type" => 'error'
                ]);
            }
            if($cash->status == 'C'){
                return response()->json([
                    "status" => "200",
                    "message" => 'Ya ha cerrado la caja hoy',
                    "type" => 'error'
                ]);
            }
            $totalDenom = 0;
            foreach ($data as $key) {
               Split::create([
                    'cash_id' => $cash->id,
                    'denomination_id' => $key['id'],
                    'quantity' => $key['quantity']
                ]);
                
                $denomi = Denomination::find($key['id']);
                if(!is_null($denomi)){
                    $totalDenom+=$totalDenom+($denomi->amount*$key['quantity']);

                }
            }
            $cash->final_amount = $totalDenom;
            $cash->save();
          
            return response()->json([
                "status" => "200",
                "message" => 'Caja registrada con exitoso',
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
    public function cashIsOpen(){
        $userId = Auth::id();
        $cash = Cash::where([
            ['user_id', '=', $userId],
            [DB::raw('DATE(created_at)'), '=',DB::raw('DATE(CURRENT_TIMESTAMP)')]
        ])->first();
        if(is_null($cash)){
            return response()->json([
                "status" => "200",
                'abierta'=>0,
                "message" => 'No ha abierto la caja aún.',
                "type" => 'error'
            ]);
        }
        if($cash->status == 'C'){
            return response()->json([
                "status" => "200",
                'abierta'=>$cash->status,
                "message" => 'Ya ha cerrado la caja hoy.',
                "type" => 'error'
            ]);
        }
        return response()->json([
            "status" => "200",
            'abierta'=>$cash->status,
            "message" => 'Caja abierta',
            "type" => 'success'
        ]);
    }

    public function update(Request $request,$id){
        try {
            $co = Cash::find($id);
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
        $data = Cash::find($id);
        $data->delete();
        return response()->json([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
}
