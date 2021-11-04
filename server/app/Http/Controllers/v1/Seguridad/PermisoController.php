<?php

namespace App\Http\Controllers\v1\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use DB;
class PermisoController extends Controller
{
    public function create(Request $request){
        try{
            $permission = Permission::create(['guard_name' => 'api','name' => $request['nombre']]);
            return $permission;
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
public function getPermissionsOwn(Request $request){
        try{
		$user = Auth::user();

		$permissionNames = $user->getRoleNames()[0];
		$rol=Role::where('name', '=',$request->input('rol'))->first();
		$permi=DB::table('role_has_permissions')->whereIn('role_id', [$rol->id])->get();
		$array=array();
		foreach ($permi as $per) {
			$permiso=Permission::find($per->permission_id);
			array_push($array,$permiso->name);
		}		
            return response([
                'permisos'=>$array,
            ]);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
	public function getRolPermissions(Request $request){
        try{
	$rol=Role::where('name', '=',$request->input('rol'))->first();
		$permi=DB::table('role_has_permissions')->whereIn('role_id', [$rol->id])->get();
		$array=array();
		foreach ($permi as $per) {
			$permiso=Permission::find($per->permission_id);
			array_push($array,$permiso->name);
		}
            return response([
                'permisos'=>$array,
            ]);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }

    public function getOwnPermissions(){
        try{
            $user = Auth::user();
            $permissionNames = $user->getRoleNames()[0];
	$rol=Role::where('name', '=',$permissionNames)->first();
		$permi=DB::table('role_has_permissions')->whereIn('role_id', [$rol->id])->get();
		$array=array();
		foreach ($permi as $per) {
			$permiso=Permission::find($per->permission_id);
			array_push($array,$permiso->name);
		}
            return response([
                'permisos'=>$array,
            ]);
        }catch(\Exception $exception){
            return response([
                "user" => $user,
                "permision" => $user->getRoleNames(),
                'message'=>$exception->getMessage()
            ],400);
        }
    }
    public function getPermissionsById(Request $request){
        try{
          
            $user = User::find(Auth::id());
            $permissionNames = $user->getAllPermissions()->pluck('name');
            return response([
                'permisos'=>$permissionNames
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
    public function permisos(){
        try{
            $data = Permission::all()->pluck('name');
           
            return response([
                'permisos'=>$data
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
        
    }
    public function revokePermission(Request $request){
        
        try{
            $id_permiso = $request->input('id_permiso');
            $id_rol = $request->input('id_rol');
            $role = Role::find($id_rol);
            $permission = Permission::find($id_permiso);
            $role->revokePermissionTo($permission);
            return response([
                'roles'=>'Permiso revocado correctamente'
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
}
