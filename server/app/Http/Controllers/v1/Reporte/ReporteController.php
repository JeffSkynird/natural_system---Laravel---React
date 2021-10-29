<?php

namespace App\Http\Controllers\v1\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Kardex;
use App\Models\Product;
use Illuminate\Http\Request;
use PDF;
class ReporteController extends Controller
{
    public function reporte(Request $request)
    {
        $data = $this->obtenerData($request->input('tipo'));
       
        return $data->download('report.pdf');
    }
    public function obtenerData($variable){
        switch ($variable) {
            case 'clientes':
                 
                 return PDF::loadView('clientes', ['data' => Client::all(),'tipo'=>'clientes']);
                break;
                case 'productos':
                    $data = Product::join('unities', 'products.unity_id', '=', 'unities.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*','unities.name as unity','categories.name as category')->orderBy('id','desc')->get();
                    return PDF::loadView('productos', ['data' => $data,'tipo'=>'productos']); 
                break;
                case 'facturas':
                    $data  = Invoice::leftjoin('clients', 'invoices.client_id', '=', 'clients.id')
          
                    ->selectRaw('invoices.created_at,invoices.id,invoices.final_consumer,invoices.total,clients.document,clients.names')->get();
                    return PDF::loadView('facturas', ['data' => $data,'tipo'=>'facturas']); 

                break;
                case 'kardex':
                    $data = Kardex::join('products', 'kardexes.product_id', '=', 'products.id')
                    ->join('users', 'kardexes.user_id', '=', 'users.id')
                    ->selectRaw('products.id,products.name,products.bar_code,kardexes.concept,kardexes.quantity,kardexes.stock,kardexes.type,users.names,users.last_names,kardexes.created_at')->get();
                    return PDF::loadView('kardex', ['data' => $data,'tipo'=>'kardex']); 

                    break; 
        }
    }
}
