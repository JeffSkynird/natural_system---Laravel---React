<?php

namespace App\Http\Controllers\v1\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cash;
use App\Models\Denomination;
use App\Models\Split;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashController extends Controller
{

    public function index()
    {
        try {
            $userId = Auth::id();
            $data = Cash::join('users', 'cashes.user_id', '=', 'users.id')->where('cashes.user_id', $userId)->orderBy('id', 'desc')->selectRaw("cashes.id,start_amount,final_amount,status, concat_ws(' ', users.names, users.last_names) as names,cashes.created_at")->get();
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
    public function create(Request $request)
    {
        try {
            $amountStart = $request->input('start_amount');
            $userId = Auth::id();
            //COMPRUEBO SI YA ABRIO LA CAJA
            $cashC = Cash::where([
                ['status', '=', 'A'],
                ['user_id', '=', $userId],
                [DB::raw('DATE(created_at)'), '=', Carbon::now()->format('Y-m-d')]
            ])->first();
            if (!is_null($cashC)) {
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
                'final_amount' => 0
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
    public function close($id)
    {
        $cash = Cash::find($id);
        $cash->status = 'C';
        $cash->closed_at = Carbon::now();
        $cash->save();
    }
    public function show($id)
    {
        $denominations = Denomination::leftJoin('splits', function ($join) use ($id) {
                $join->on('denominations.id', '=', 'splits.denomination_id');
                $join->on('splits.cash_id', '=', DB::raw($id));
            })
            ->select('denominations.id', 'denominations.name', 'denominations.amount', DB::raw('COALESCE( splits.quantity, 0 ) as quantity'))->get();

        return response()->json([
            "status" => "200",
            'data' => $denominations,
            "message" => 'Datos obtenidos con éxito',
            "type" => 'success'
        ]);;
    }
    public function splits()
    {
        $denominations = Denomination::select('id', 'name', 'amount', DB::raw('0 as quantity'))->get();
        return response()->json([
            "status" => "200",
            'data' => $denominations,
            "message" => 'Datos obtenidos con éxito',
            "type" => 'success'
        ]);;
    }
    public function saveSplit(Request $request)
    {
        try {
            $data = $request->input('data');
            $userId = Auth::id();
            $cash = Cash::where([
                ['user_id', '=', $userId],
                ['status', '=', 'A'],
                [DB::raw('DATE(created_at)'), '=', Carbon::now()->format('Y-m-d')]
            ])->first();
            if (is_null($cash)) {
                return response()->json([
                    "status" => "200",
                    "message" => 'No ha abierto la caja hoy',
                    "type" => 'error'
                ]);
            }
             
            $totalDenom = 0;
            foreach ($data as $key) {
                if ($key['quantity'] > 0) {
                    Split::create([
                        'cash_id' => $cash->id,
                        'denomination_id' => $key['id'],
                        'quantity' => $key['quantity']
                    ]);
                }
                $denomi = Denomination::find($key['id']);
                if (!is_null($denomi)) {
                    $totalDenom += ($denomi->amount * $key['quantity']);
                }
            }
            $cash->final_amount = $totalDenom;
            $cash->save();

            $this->close($cash->id);
            return response()->json([
                "status" => "200",
                "message" => 'Desglose registrado con éxito',
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
    public function cashIsOpen()
    {
        $userId = Auth::id();
        $cash = Cash::where([
            ['user_id', '=', $userId],
            ['status', '=', 'A'],
            [DB::raw('DATE(created_at)'), '=', Carbon::now()->format('Y-m-d')]
        ])->first();

        if (is_null($cash)) {
            return response()->json([
                "status" => "200",
                'abierta' => 0,
                "message" => 'No ha abierto la caja aún',
                "type" => 'error'
            ]);
        }else{
            return response()->json([
                "status" => "200",
                'abierta' => 1,
                "message" => 'Caja abierta',
                "type" => 'success'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
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
