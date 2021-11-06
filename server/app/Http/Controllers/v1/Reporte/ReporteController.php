<?php

namespace App\Http\Controllers\v1\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Cash;
use App\Models\Client;
use App\Models\Denomination;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Kardex;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $id_caja = $request->input('id_caja');

        $filtros = [
            "barcode" => $barcode,
            "numero_factura" => $filtro,
            "desde" => $desde,
            "hasta" => $hasta,
            "usuario" => $usuario,
            'id_caja' => $id_caja
        ];
        $data = $this->obtenerData($request->input('tipo'), $filtros);
        if ($data != null) {
            return $data->stream('reporte.pdf');
        } else {
            return response()->json(['error' => 'No se encontraron datos'], 404);
        }
    }
    public function printTicket($id)
    {


        $data  = Invoice::leftjoin('clients', 'invoices.client_id', '=', 'clients.id')
            ->join('users', 'invoices.user_id', '=', 'users.id')
            ->where('invoices.id', '=', $id)
            ->selectRaw('users.dni,users.last_names,invoices.created_at,invoices.id,invoices.final_consumer,invoices.total,invoices.iva,clients.document,clients.names');
        $datbodya  = InvoiceProduct::join('products', 'invoice_products.product_id', '=', 'products.id')
            ->where('invoice_products.invoice_id', '=', $id)
            ->selectRaw('products.sale_price,products.name,products.bar_code,invoice_products.quantity,invoice_products.subtotal')->get();
        $subTotal = InvoiceProduct::where('invoice_id', '=', $id)->sum('subtotal');

        $pdf =  PDF::loadView('ticket', ['data' => $data->first(), 'body' => $datbodya, 'subtotal' => $subTotal]);


        return  $pdf->stream('whateveryourviewname.pdf');
    }

    public function addGeneralCondition($tableRef, $data, $desde, $hasta, $usuario)
    {
        if (!is_null($desde) && !is_null($hasta)) {
            $data->whereBetween($tableRef . 'created_at', [$desde, $hasta]);
        }
        if (!is_null($usuario)) {
            $data->where($tableRef . 'user_id', $usuario);
        }
    }
    public function obtenerData($variable, $filtro)
    {
        $desde = $filtro['desde'];
        $hasta = $filtro['hasta'];
        $usuario = $filtro['usuario'];
        switch ($variable) {
            case 'clientes':
                $data = Client::orderBy('id');
                $this->addGeneralCondition('', $data, $desde, $hasta, $usuario);
                return PDF::loadView('clientes', ['data' => $data->get(), 'tipo' => 'clientes']);
                break;
            case 'compras':
                $data  = Order::join('suppliers', 'orders.supplier_id', '=', 'suppliers.id')
                    ->selectRaw("orders.id,suppliers.business_name,orders.total,to_char(orders.created_at,'DD/MM/YYYY') date");
                $this->addGeneralCondition('orders.', $data, $desde, $hasta, $usuario);
                return PDF::loadView('compras', ['data' => $data->get()]);
                break;
            case 'productos':
                $data = Product::join('unities', 'products.unity_id', '=', 'unities.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->select('products.*', 'unities.name as unity', 'categories.name as category')->orderBy('id', 'desc');
                $this->addGeneralCondition('products.', $data, $desde, $hasta, $usuario);
                if (!is_null($filtro['barcode'])) {
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
                    $this->addGeneralCondition('invoices.', $data, $desde, $hasta, $usuario);


                    return PDF::loadView('facturas', ['data' => $data->get(), 'total' => $data->sum('invoices.total'), 'tipo' => 'facturas']);
                }


                break;
            case 'kardex':
                $data = Kardex::join('products', 'kardexes.product_id', '=', 'products.id')
                    ->join('users', 'kardexes.user_id', '=', 'users.id')
                    ->selectRaw('kardexes.stock*products.fraction total_fraction,products.id,products.name,products.bar_code,kardexes.concept,kardexes.quantity,kardexes.stock,kardexes.type,users.names,users.last_names,kardexes.created_at');
                $this->addGeneralCondition('kardexes.', $data, $desde, $hasta, $usuario);
                if (!is_null($filtro['barcode'])) {
                    $data->where('products.bar_code', $filtro['barcode']);
                }
                return PDF::loadView('kardex', ['data' => $data->get(), 'tipo' => 'kardex']);

                break;

            case 'caja':


                $userId = Auth::id();
                $cash = null;
                if ($filtro['id_caja'] == null) {
                    $cash = Cash::join('users', 'cashes.user_id', '=', 'users.id')
                        ->where([
                            ['cashes.status', '=', 'C'],
                            ['cashes.user_id', '=', $userId],
                            [DB::raw('DATE(cashes.created_at)'), '=', Carbon::now()->format('Y-m-d')]
                        ])->orderBy('id','desc')->selectRaw("TO_CHAR(
                            cashes.created_at,
                            'DD-MM'
                        )  as created,cashes.start_amount,cashes.final_amount,DATE(cashes.created_at) as created_cash,users.dni,users.last_names,cashes.created_at,cashes.id,cashes.updated_at")->first();
                } else {
                    $cash = Cash::join('users', 'cashes.user_id', '=', 'users.id')
                        ->where([
                            ['cashes.id', '=', $filtro['id_caja']],
                        ])->selectRaw("TO_CHAR(
                            cashes.created_at,
                            'DD-MM'
                        )  as created,cashes.start_amount,cashes.final_amount,DATE(cashes.created_at) as created_cash,users.dni,users.last_names,cashes.created_at,cashes.id,cashes.updated_at")->first();
                }


                $invoice = Invoice::where([
                    ['status', '=', 'A'],
                    ['user_id', '=', $userId],
                    [DB::raw('DATE(created_at)'), '=', Carbon::now()->format('Y-m-d')]
                ])->selectRaw("TO_CHAR(
                        created_at,
                        'DD-MM'
                    )  as created,'EGRESO: VENTA' as description,sum(total) as total")->groupBy('created')->get();

                $totalInvoice = Invoice::where([
                    ['status', '=', 'A'],
                    ['user_id', '=', $userId],
                    [DB::raw('DATE(created_at)'), '=', Carbon::now()->format('Y-m-d')]
                ])->sum('total');
                $invoiceSum = $totalInvoice;
                if ($cash->start_amount > 0) {
                    $invoice->prepend(['created' => $cash->created, 'description' => 'INGRESO: APERTURA', 'total' => $cash->start_amount]);
                    $invoiceSum = $cash->start_amount - $invoiceSum;
                }
                $denominations = Denomination::join('splits', function ($join) use ($cash) {
                    $join->on('denominations.id', '=', 'splits.denomination_id');
                    $join->on('splits.cash_id', '=', DB::raw($cash->id));
                })
                    ->select('denominations.id', DB::raw('denominations.amount*splits.quantity as total'), 'denominations.name', 'denominations.amount', DB::raw('COALESCE( splits.quantity, 0 ) as quantity'))->get();
                return PDF::loadView('caja', ['cash' => $cash, 'mov' => $invoice, 'split' => $denominations, 'total_cash' => $cash->final_amount, 'total_invoice' => $totalInvoice, 'total_mov' => $invoiceSum]);

                break;
        }
    }
}
