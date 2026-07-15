<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Services\CompanyService;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $clients = Client::where('company_id', $company->id)
            ->orderBy('name')->paginate(10);

        return view('company.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('company.automatic.create', [
            'route' => 'clients',
            'title' => 'Novo Cliente',
            'data' => null,
        ]);
    }

    public function store(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'],
            'cnpj' => ['required', 'string', 'max:14', 'unique:clients,cnpj'],
        ]);

        $data['company_id'] = $company->id;
        Client::create($data);

        return redirect()->route('company.clients.index')->with('success', 'Cliente criado!');
    }

    public function edit($id)
    {
        $company = (new CompanyService)->myCompany();
        $data = Client::where('company_id', $company->id)->findOrFail($id);

        return view('company.automatic.edit', [
            'route' => 'clients',
            'title' => 'Editar Cliente',
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = (new CompanyService)->myCompany();
        $client = Client::where('company_id', $company->id)->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client->id)],
            'cnpj' => ['required', 'string', 'max:14', Rule::unique('clients', 'cnpj')->ignore($client->id)],
        ]);

        $client->update($data);

        return redirect()->route('company.clients.index')->with('success', 'Cliente atualizado!');
    }

    public function destroy($id)
    {
        $company = (new CompanyService)->myCompany();
        $client = Client::where('company_id', $company->id)->findOrFail($id);
        $client->delete();

        return redirect()->route('company.clients.index')->with('success', 'Cliente removido!');
    }
}
