<?php

namespace App\Http\Controllers\v1\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function save($file)
    {
        if($file!=null){
            $extension = $file->getClientOriginalExtension(); // getting image extension
            $filename =time().'.'.$extension;
            $file->storeAs('public/photos', $filename);
            return  'photos/'.$filename;
        }else{
            return null;
        }

    }
    public function index()
    {
        try {
  
            $data = Product::join('unities', 'products.unity_id', '=', 'unities.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftjoin('warehouses', 'products.warehouse_id', '=', 'warehouses.id')
    ->select('warehouses.name as warehouse','warehouses.id as warehouse_id','products.*','unities.name as unity','categories.name as category')->orderBy('id','desc')->get();
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
            $url = $this->save($request->file('url'));
            $request['image']=$url;
            $request['list_price']=$request->input('list_price')!=null?$request->input('list_price'):0;
            $request['sale_price']=$request->input('sale_price')!=null?$request->input('sale_price'):0;
            $request['user_id']=Auth::id();
            Product::create($request->all());

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
        $data = Product::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function deleteImage($file_path)
    {
        if($file_path!=null){
            File::delete(public_path() . '/storage/'.$file_path);
        
        }
    }

    public function subirFoto(Request $request,$id){
        $url = $this->save($request->file('url'));
        $pr = Product::find($id);
        if($request->file('url')!=null){
            $this->deleteImage($pr->image);
        }
        $pr->image=$url;
        $pr->save();
    }
    public function update(Request $request,$id){
        try {

        
            $co = Product::find($id);
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
        $data = Product::find($id);
        $data->delete();
        return response()->json([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
 
}
