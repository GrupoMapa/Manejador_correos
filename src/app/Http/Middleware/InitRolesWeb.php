<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;

class InitRolesWeb
{
    public function handle($request, Closure $next)
    {
        
        $permissions = '';
        if (Auth::check() && !session()->has('permisos')) {
            $user = Auth::user();
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            Session::put('permisos', $permissions);
            $role = $user->roles->first()->id;
            Session::put('rol', $role);
        }
        //dd($nombresPermisos);
        return $next($request);
    }
}