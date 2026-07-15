<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Services\CompanyService;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::withCount('users');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%")
                  ->orWhere('primary_email', 'like', "%{$search}%");
            });
        }

        $companies = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.automatic.create', [
            'route' => 'companies',
            'title' => 'Criar Empresa',
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
            ->with('success', 'Empresa criada!');
    }

    public function show(Company $company)
    {
        $company->load(['users', 'socials']);

        return view('admin.automatic.show', [
            'route' => 'companies',
            'title' => 'Visualizar Empresa',
            'data' => $company,
            'socialTypes' => Social::TYPES,
        ]);
    }

    public function edit(Company $company)
    {
        $company->load(['users', 'socials']);

        return view('admin.automatic.edit', [
            'route' => 'companies',
            'title' => 'Editar Empresa',
            'data' => $company,
            'socialTypes' => Social::TYPES,
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $data = $this->validateData($request, $company->id);

        $company->update($data);
        $this->syncSocials($company, $request);

        return redirect()
            ->route('admin.companies.edit', $company->id)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Empresa removida com sucesso!');
    }

    // ── Company (user logado) ──

    public function myCompany(Request $request)
    {
        $CompanyService = new CompanyService();
        $currentCompany = $CompanyService->myCompany(['users', 'socials']);

        return view('company.myCompany', [
            'company' => $currentCompany,
            'socialTypes' => Social::TYPES,
            'createUserRules' => [
                'company' => 'Empresa',
                'manager' => 'Gerente',
            ],
        ]);
    }

    public function myCompanyUpdate(Request $request)
    {
        $CompanyService = new CompanyService();
        $currentCompany = $CompanyService->myCompany(['users', 'socials']);
        $data = $this->validateData($request, $currentCompany->id);

        $currentCompany->update($data);

        return view('company.myCompany', [
            'company' => $currentCompany,
            'socialTypes' => Social::TYPES,
            'createUserRules' => [
                'company' => 'Empresa',
                'manager' => 'Gerente',
            ],
        ]);
    }

    // ── Validação ──

    protected function validateData(Request $request, ?int $companyId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'required', 'string', 'max:20',
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

    // ── Redes sociais ──

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
