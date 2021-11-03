<?php

namespace App\Http\Controllers\v1\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Kardex;
use App\Models\Product;
use Illuminate\Http\Request;
use PDF;

class ReporteController extends Controller
{
    public function reporte(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $barcode = $request->input('codigo_barras');
        $filtro = $request->input('numero_factura');
        $usuario = $request->input('usuario');

        $filtros = [
            "barcode"=>$barcode,
            "numero_factura"=>$filtro,
            "desde"=>$desde,
            "hasta"=>$hasta,
            "usuario"=>$usuario
        ];
        $data = $this->obtenerData($request->input('tipo'), $filtros);
        if ($data != null) {
            return $data->download('report.pdf');
        } else {
            return response()->json(['error' => 'No se encontraron datos'], 404);
        }
    }
    public function addGeneralCondition($tableRef,$data,$desde,$hasta,$usuario){
        if(!is_null($desde)&&!is_null($hasta)){
            $data->whereBetween($tableRef.'created_at',[$desde,$hasta]);
        }
        if(!is_null($usuario)){
            $data->where($tableRef.'user_id',$usuario);
        }
    }
    public function obtenerData($variable, $filtro)
    {
        $desde= $filtro['desde'];
        $hasta= $filtro['hasta'];
        $usuario= $filtro['usuario'];
        switch ($variable) {
            case 'clientes':
                $data = Client::orderBy('id');
                $this->addGeneralCondition('',$data,$desde,$hasta,$usuario);
                return PDF::loadView('clientes', ['data' => $data->get(), 'tipo' => 'clientes']);
                break;
            case 'productos':
                $data = Product::join('unities', 'products.unity_id', '=', 'unities.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->select('products.*', 'unities.name as unity', 'categories.name as category')->orderBy('id', 'desc');
                $this->addGeneralCondition('products.',$data,$desde,$hasta,$usuario);
                if(!is_null($filtro['barcode'])){
                    $data->where('products.bar_code', $filtro['barcode']);
                }
               return PDF::loadView('productos', ['data' => $data->get(), 'tipo' => 'productos']);
                break;
            case 'facturas':

                if ($filtro['numero_factura'] != null) {
                    $data  = Invoice::leftjoin('clients', 'invoices.client_id', '=', 'clients.id')
                        ->where('invoices.id', '=', $filtro['numero_factura'])
                        ->selectRaw('invoices.created_at,invoices.id,invoices.final_consumer,invoices.total,invoices.iva,clients.document,clients.names');
                    $datbodya  = InvoiceProduct::join('products', 'invoice_products.product_id', '=', 'products.id')
                        ->where('invoice_products.invoice_id', '=', $filtro['numero_factura'])
                        ->selectRaw('products.name,products.bar_code,invoice_products.quantity,invoice_products.subtotal')->get();

                    $subTotal = InvoiceProduct::where('invoice_id', '=', $filtro['numero_factura'])->sum('subtotal');

                    return PDF::loadView('facturas_detalle', ['data' => $data->first(), 'tipo' => 'facturas', 'body' => $datbodya, 'subtotal' => $subTotal]);
                    
                } else {
                    $data  = Invoice::leftjoin('clients', 'invoices.client_id', '=', 'clients.id')
                        ->selectRaw('invoices.created_at,invoices.id,invoices.final_consumer,invoices.total,invoices.iva,clients.document,clients.names');
                    $this->addGeneralCondition('invoices.',$data,$desde,$hasta,$usuario);

                    return PDF::loadView('facturas', ['data' => $data->get(), 'tipo' => 'facturas']);
                }


                break;
            case 'kardex':
                $data = Kardex::join('products', 'kardexes.product_id', '=', 'products.id')
                    ->join('users', 'kardexes.user_id', '=', 'users.id')
                    ->selectRaw('products.id,products.name,products.bar_code,kardexes.concept,kardexes.quantity,kardexes.stock,kardexes.type,users.names,users.last_names,kardexes.created_at');
                    $this->addGeneralCondition('kardexes.',$data,$desde,$hasta,$usuario);
                    if(!is_null($filtro['barcode'])){
                        $data->where('products.bar_code', $filtro['barcode']);
                    }
                    return PDF::loadView('kardex', ['data' => $data->get(), 'tipo' => 'kardex']);

                break;
        }
    }
}
