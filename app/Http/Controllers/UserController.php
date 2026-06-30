<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('company');

        $search = $request->input('search', $request->input('name'));
        $companyId = $request->input('company_id');

        if (filled($search)) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (filled($companyId)) {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('sort')) {
            $direction = str_starts_with($request->sort, '-') ? 'desc' : 'asc';
            $field = ltrim($request->sort, '-');

            if (in_array($field, ['id', 'name', 'email'])) {
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy('name');
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.automatic.create', [
            'model' => 'users',
            'title' => 'Criar Usuario',
            'route' => 'users',
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

        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return $this->redirectAfterSave($request, 'Usuario criado com sucesso!');
    }

    public function show(Request $request, $id)
    {
        $user = User::with('company')->findOrFail($id);

        return view('admin.automatic.show', [
            'model' => 'users',
            'title' => 'Visualizar Usuario',
            'route' => 'users',
            'data' => $user,
            'companies' => Company::orderBy('name')->get(),
            'selectedCompanyId' => $request->input('company_id', $user->company_id),
            'returnCompanyId' => $request->input('return_company_id'),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $user = User::with('company')->findOrFail($id);

        return view('admin.automatic.edit', [
            'model' => 'users',
            'title' => 'Editar Usuario',
            'route' => 'users',
            'data' => $user,
            'companies' => Company::orderBy('name')->get(),
            'selectedCompanyId' => $request->input('company_id', $user->company_id),
            'returnCompanyId' => $request->input('return_company_id'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

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

        return $this->redirectAfterSave($request, 'Usuario atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario removido com sucesso!');
    }

    protected function redirectAfterSave(Request $request, string $message)
    {
        $returnCompanyId = $request->input('return_company_id', $request->input('company_id'));

        if (filled($returnCompanyId)) {
            return redirect()
                ->route('admin.companies.edit', $returnCompanyId)
                ->with('success', $message);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }
}
