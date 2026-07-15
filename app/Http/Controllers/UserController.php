<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Services\CompanyService;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('company');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($companyId = $request->input('company_id')) {
            $query->where('company_id', $companyId);
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.automatic.create', [
            'route' => 'users',
            'title' => 'Criar Usuário',
            'data' => null,
            'companies' => Company::orderBy('name')->get(),
            'selectedCompanyId' => $request->input('company_id'),
            'returnCompanyId' => $request->input('return_company_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'rule' => ['required', Rule::in(array_keys(User::RULES))],
        ]);

        $isCompany = str_starts_with($request->route()->getName(), 'company.');

        $data['password'] = bcrypt($data['password']);

        if ($isCompany) {
            $company = (new CompanyService)->myCompany();
            $data['company_id'] = $company->id;
        }

        User::create($data);

        $redirect = $isCompany ? 'company.mycompany' : 'admin.users.index';

        return redirect()->route($redirect)->with('success', 'Usuário criado!');
    }

    public function show(Request $request, $id)
    {
        $query = User::with('company');

        if (str_starts_with($request->route()->getName(), 'company.')) {
            $company = (new CompanyService)->myCompany();
            $query->where('company_id', $company->id);
        }

        $user = $query->findOrFail($id);

        return view('admin.automatic.show', [
            'route' => 'users',
            'title' => 'Visualizar Usuário',
            'data' => $user,
            'companies' => Company::orderBy('name')->get(),
            'selectedCompanyId' => $request->input('company_id', $user->company_id),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $query = User::with('company');

        if (str_starts_with($request->route()->getName(), 'company.')) {
            $company = (new CompanyService)->myCompany();
            $query->where('company_id', $company->id);
        }

        $user = $query->findOrFail($id);

        return view('admin.automatic.edit', [
            'route' => 'users',
            'title' => 'Editar Usuário',
            'data' => $user,
            'companies' => Company::orderBy('name')->get(),
            'selectedCompanyId' => $request->input('company_id', $user->company_id),
            'returnCompanyId' => $request->input('return_company_id'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $query = User::query();

        $isCompany = str_starts_with($request->route()->getName(), 'company.');
        if ($isCompany) {
            $company = (new CompanyService)->myCompany();
            $query->where('company_id', $company->id);
        }

        $user = $query->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['nullable', 'min:6'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'rule' => ['required', Rule::in(array_keys(User::RULES))],
        ]);

        if (blank($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        $redirect = $isCompany ? 'company.mycompany' : 'admin.users.index';

        return redirect()->route($redirect)->with('success', 'Usuário atualizado!');
    }

    public function destroy(Request $request, $id)
    {
        $query = User::query();

        $isCompany = str_starts_with($request->route()->getName(), 'company.');
        if ($isCompany) {
            $company = (new CompanyService)->myCompany();
            $query->where('company_id', $company->id);
        }

        $user = $query->findOrFail($id);

        if ($user->email === 'admin@admin.com') {
            return back()->with('error', 'Não é possível excluir o admin principal.');
        }

        $user->delete();

        $redirect = $isCompany ? 'company.mycompany' : 'admin.users.index';

        return redirect()->route($redirect)->with('success', 'Usuário removido!');
    }
}
