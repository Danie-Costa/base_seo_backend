<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::withCount('users');

        $search = $request->input('search');

        if (filled($search)) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('cnpj', 'like', '%' . $search . '%')
                    ->orWhere('primary_email', 'like', '%' . $search . '%');
            });
        }

        $companies = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.automatic.create', [
            'model' => 'companies',
            'title' => 'Criar Empresa',
            'route' => 'companies',
            'data' => null,
            'socialTypes' => Social::TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $company = Company::create($data);
        $this->syncSocials($company, $request);

        return redirect()
            ->route('admin.companies.edit', $company->id)
            ->with('success', 'Empresa criada com sucesso!');
    }

    public function show($id)
    {
        $company = Company::with(['users', 'socials'])->findOrFail($id);

        return view('admin.automatic.show', [
            'model' => 'companies',
            'title' => 'Visualizar Empresa',
            'route' => 'companies',
            'data' => $company,
            'socialTypes' => Social::TYPES,
        ]);
    }

    public function edit($id)
    {
        $company = Company::with(['users', 'socials'])->findOrFail($id);

        return view('admin.automatic.edit', [
            'model' => 'companies',
            'title' => 'Editar Empresa',
            'route' => 'companies',
            'data' => $company,
            'socialTypes' => Social::TYPES,
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $data = $this->validateData($request, $company->id);

        $company->update($data);
        $this->syncSocials($company, $request);

        return redirect()
            ->route('admin.companies.edit', $company->id)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Empresa removida com sucesso!');
    }

    protected function validateData(Request $request, ?int $companyId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'required',
                'string',
                'max:20',
                Rule::unique('companies', 'cnpj')->ignore($companyId),
            ],
            'address' => ['required', 'string', 'max:255'],
            'primary_phone' => ['required', 'string', 'max:30'],
            'primary_email' => ['required', 'email', 'max:255'],
            'socials' => ['array'],
            'socials.*.title' => ['nullable', 'string', 'max:255'],
            'socials.*.link' => ['nullable', 'string', 'max:255'],
        ]);
    }

    protected function syncSocials(Company $company, Request $request): void
    {
        $socialInputs = $request->input('socials', []);
        $activeNames = [];

        foreach (Social::TYPES as $name => $definition) {
            $title = trim((string) data_get($socialInputs, $name . '.title', $definition['label']));
            $link = trim((string) data_get($socialInputs, $name . '.link', ''));

            if ($link === '') {
                continue;
            }

            $activeNames[] = $name;

            $company->socials()->updateOrCreate(
                ['name' => $name],
                [
                    'title' => $title !== '' ? $title : $definition['label'],
                    'icon' => $definition['icon'],
                    'link' => $link,
                    'header' => false,
                    'sidebar' => false,
                    'footer' => true,
                ]
            );
        }

        $company->socials()
            ->whereNotIn('name', $activeNames)
            ->delete();
    }
}
