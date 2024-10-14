<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use \App\Models\User;


class AuthController extends Controller
{
    function form_registro() {
        return view('register');
    }

    function form_login() {
        return view('login');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->save();
            
            // Obtén el rol 'admin' (asegúrate de que exista)
            $adminRole = Role::where('name', 'admin')->first();
            
            if ($adminRole) {
                // Asigna el rol 'admin' al usuario
                $user->assignRole($adminRole);
            }
            
            Auth::login($user);
        } catch (\Exception $e) {
            // Maneja la excepción
            return $e->getMessage();
        }
        
        return redirect()->route('home');
    }
    
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended();
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect()->route('login');
    }

}