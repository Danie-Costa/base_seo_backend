<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientAuthController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest:client')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('client');
    }

    public function showLoginForm()
    {
        return view('auth.client.login');
    }

    public function showRegisterForm()
    {
        return view('auth.client.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients,email,NULL,id,company_id,1'],
            'cnpj' => ['required', 'string', 'max:14'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Company fixa (single-tenant) — ajustar se multi-tenant
        $company = \App\Models\Company::first();

        $client = Client::create([
            'company_id' => $company?->id,
            'name' => $request->name,
            'email' => $request->email,
            'cnpj' => preg_replace('/\D/', '', $request->cnpj),
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('client')->login($client);

        return redirect($this->redirectTo);
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function loggedOut(Request $request)
    {
        return redirect('/');
    }
}
