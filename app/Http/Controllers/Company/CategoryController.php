<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $company = (new CompanyService)->myCompany();
        $type = $request->input('type', 'product');
        $categories = Category::where('company_id', $company->id)
            ->where('type', $type)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('name')->paginate(10);

        return view('company.categories.index', compact('categories', 'type'));
    }

    public function create(Request $request)
    {
        $company = (new CompanyService)->myCompany();
        $type = $request->input('type', 'product');

        return view('company.categories.form', [
            'data' => null,
            'type' => $type,
            'parents' => Category::where('company_id', $company->id)
                ->where('type', $type)->whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:product,post'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        if ($data['parent_id']) {
            $parent = Category::find($data['parent_id']);
            if ($parent && $parent->level >= 2) {
                return back()->withErrors(['parent_id' => 'Máximo de 3 níveis de subcategoria.']);
            }
        }

        $data['company_id'] = $company->id;
        $data['slug'] = Str::slug($data['name']);
        if (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] .= '-' . uniqid();
        }

        Category::create($data);

        return redirect()->route('company.categories.index', ['type' => $data['type']])
            ->with('success', 'Categoria criada!');
    }

    public function show($id)
    {
        $company = (new CompanyService)->myCompany();
        $data = Category::where('company_id', $company->id)
            ->with('parent', 'children.children')->findOrFail($id);
        $type = $data->type;

        return view('company.categories.form', [
            'data' => $data,
            'type' => $type,
            'parents' => Category::where('company_id', $company->id)
                ->where('type', $type)->whereNull('parent_id')->orderBy('name')->get(),
            'readonly' => true,
        ]);
    }

    public function edit($id)
    {
        $company = (new CompanyService)->myCompany();
        $data = Category::where('company_id', $company->id)
            ->with('parent', 'children.children')->findOrFail($id);
        $type = $data->type;

        return view('company.categories.form', [
            'data' => $data,
            'type' => $type,
            'parents' => Category::where('company_id', $company->id)
                ->where('type', $type)->whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = (new CompanyService)->myCompany();
        $category = Category::where('company_id', $company->id)->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:product,post'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        if ($data['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'Não pode ser pai dela mesma.']);
        }

        if ($data['parent_id']) {
            $parent = Category::find($data['parent_id']);
            if ($parent && $parent->level >= 2) {
                return back()->withErrors(['parent_id' => 'Máximo de 3 níveis de subcategoria.']);
            }
        }

        if ($data['name'] !== $category->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return redirect()->route('company.categories.index', ['type' => $category->type])
            ->with('success', 'Categoria atualizada!');
    }

    public function destroy($id)
    {
        $company = (new CompanyService)->myCompany();
        $category = Category::where('company_id', $company->id)->findOrFail($id);
        $type = $category->type;
        $category->children()->delete();
        $category->delete();

        return redirect()->route('company.categories.index', compact('type'))
            ->with('success', 'Categoria removida!');
    }
}
