<?php

namespace App\Http\Controllers\v1\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Kardex;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function index()
    {
        try {
            $data = Kardex::join('products', 'kardexes.product_id', '=', 'products.id')
            ->join('users', 'kardexes.user_id', '=', 'users.id')
            ->selectRaw('products.id,products.name,products.bar_code,kardexes.concept,kardexes.quantity,kardexes.stock,kardexes.type,users.names,users.last_names,kardexes.created_at')->get();
            $kp1 = Kardex::where('concept','=','E')->count();
            $kp2 = Kardex::where('concept','=','S')->count();
            return response()->json([
                "status" => "200",
                'data'=>$data,
                'entrada'=>$kp1,
                'salida'=>$kp2,
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
}
