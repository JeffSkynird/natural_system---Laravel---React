<?php

namespace App\Http\Controllers\v1\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use \Validator;
use App\Models\User;
class RoleController extends Controller
{
    public function deleteRole(Request $request)
    {
        try {
            $role= Role::where('name', '=',$request->input('rol'))->first();
            $role->delete();

            return response([
                "message" => "Borrado exitoso",
                "type" => "success",
            ]);
        } catch (\Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
                'type' => 'error',
            ]);
        }
    }
    public function getRoleByUser(Request $request){
        try{
            $user = User::find($request->input('user_id'));
            if($user!=null){
                if(count($user->getRoleNames())!=0){
                    $permissionNames = $user->getRoleNames()[0];
                    return response([
                        'role'=>$permissionNames,
                    ]);

                }else{
                    return response([
                        'role'=>'',

                    ]);

                }
            }else{
                return response([
                    'role'=>'',
                ]);
            }
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
    public function exists($value,$array,$id){
        $normalize="ASCII//TRANSLIT";
        $res = false;
        foreach ($array as $i){
            if(strtolower(iconv( 'UTF-8', $normalize,$value))==strtolower(iconv( 'UTF-8', $normalize,$i->name))){
                if($i->id!=$id){
                    $res = true;
                }
            }
        }
        return $res;
    }
    public function create(Request $request){
        try{
            if(!$this->exists($request['nombre'], Role::all(),0)){
                $role = Role::create(['guard_name' => 'api','name' => $request['nombre']]);

                return response([
                    'data'=>$role,
                    'type'=>'success',
                    'message'=>'Registro exitoso'                                                                                                                         
                ]);
            }else{
                return response([
                    'type'=>'error',
                    'message'=>'El nombre del rol ya existe'                                                    
                ]);
            }
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
    public function editar(Request $request)
    {
        try {
            //VACIOS

            $vacios = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'type' => "error",
                ]);
            }

            if(!$this->exists($request['name'], Role::all(),$request->input('id'))){ 
                $role= Role::where('name', '=',$request->input('rol'))->first();
                $role->name = $request->input('name');

                $role->save();
                return response([
                    'message' => "Edición exitosa",
                    'type' => 'success',
                ]);

            }else{
                return response([
                    'message' => "El nombre del rol ya existe",
                    'type' => 'error',
                ]);

            }

        } catch (\Exception $exception) {
            return response([
                'message' =>  $exception->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function asignOnePermission(Request $request){
        try{
            $role = Role::find($request['id_rol']);
            $role->givePermissionTo($request['permiso']);

            return response([
                'message'=>'Permiso asignado correctamente'
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
    public function asignMultiplePermission(Request $request){
        try{
            $permisos = $request->input('permisos');
            $id_rol = $request->input('id_rol');
            $role = Role::where('name', '=',$request->input('rol'))->first();
            $role->syncPermissions([]);
            //Asigno permisos al rol
            foreach ($permisos as $valor){
                $role->givePermissionTo($valor);
            }
            return response([
                'message'=>'Permisos asignados correctamente',
                '1message'=>$permisos,
                'role'=>$role
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
    public function getPermisionsByRole(Request $request){
        try{

            $infoRol = Role::where('name', '=',$request->input('rol'))->first();



            return response([
                'message'=>'Rol asignado correctamente'
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }

    public function asignRole(Request $request){
        try{
            $id_user = $request->input('id_user');
            $rol = $request->input('rol');

            $user = User::find($id_user);
            $user->roles()->detach();
            $user->assignRole($rol);

            return response([
                'message'=>'Rol asignado correctamente'
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }
    }
    public function roles(){
        try{
            $roles = Role::all();

            return response([
                'roles'=>$roles
            ],200);
        }catch(\Exception $exception){
            return response([
                'message'=>$exception->getMessage()
            ],400);
        }

    }

}
