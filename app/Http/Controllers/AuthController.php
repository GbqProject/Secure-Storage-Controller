<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(){
        return view('auth.login');
    }

    public function login(Request $r){
        $credentials = $r->only('email','password');
        if (Auth::attempt($credentials)){
            $r->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        return back()->withErrors(['email'=>'Credenciales inválidas.']);
    }

    public function logout(Request $r){
        Auth::logout();
        $r->session()->invalidate();
        return redirect('/login');
    }
}
