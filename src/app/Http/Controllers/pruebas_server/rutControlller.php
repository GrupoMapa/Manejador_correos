<?php

namespace App\Http\Controllers\pruebas_server;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class rutControlller extends Controller
{
    function pruebas_server() {
        $data = [];

        try {
            
            $role = Role::create(['name' => 'admin']);
            $permission = Permission::create(['name' => 'all']);
        } catch (\Exception $e) {
            // Maneja la excepción
            if($e->getMessage() == "A role `admin` already exists for guard `web`.")
                array_push( $data, [ 'crear roles y permisos' ,$e->getMessage(), true ]);
            else
                array_push( $data, [ 'crear roles y permisos' ,$e->getMessage(), false ]);
        }

        $user = User::where('name', 'alex')->first(); // Reemplaza 'name' con el campo apropiado en tu modelo de usuario
        $role = Role::where('name', 'admin')->first();
        $permision = Permission::where('name', 'comandos_terminal')->first();
        $permision->assignRole($role);

        if ($user && $role) {
            $user->assignRole($role);
            $user->save();
            array_push( $data, [ 'Asignar rol' ,'asignado' , true ]);
        } else {
            array_push( $data, [ 'Asignar rol' ,'no asignado' , false ]);
        }
        // Obtener el permiso
        $permission = Permission::findByName('fac_1');
        // Asignar el permiso al modelo
        $user->givePermissionTo($permission);

        $roles = DB::select("SELECT * FROM roles WHERE id=1 and name like 'admin'");
        if ($roles) {
            array_push( $data, [ 'base_default' ,'la base por defecto es correcta' , true ]);
        } else {
            array_push( $data, [ 'base_default' ,'la base por defecto es incorrecta o no esta actualizada' , true ]);
        }


        if (Auth::check()) {
            // Hay una sesión activa, obtenemos los roles del usuario
            $user = Auth::user();
            $roles = $user->getRoleNames(); // Obtiene una colección de nombres de roles
        
            if ($roles->isEmpty()) {
                // Si el usuario no tiene roles asignados
                $rolesString = "sin roles asignados.";
            } else {
                // Si el usuario tiene roles asignados
                $rolesString = "Roles: " . $roles->implode(', ');
                $role = $user->getRoleNames()->first();
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();
                $listPermision = '';
                foreach ($permissions as $permission) {
                    $listPermision = $listPermision.','. $permission;
                }
                array_push( $data, [ 'permisos_actual_user' ,$listPermision , true ]);
            }
            array_push( $data, [ 'role_actual_user' ,$rolesString , true ]);
        } else {
            // No hay sesión activa
            $rolesString = "Sin sesión";
            array_push( $data, [ 'roles_actual_user' ,$rolesString , false ]);
        }




        return view('pruebas', ['data'=> $data ]);
    }

    function producto(){
        return view('rosalio');
    }

}
