<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'product');
        $categories = Category::where('type', $type)
            ->whereNull('parent_id')
            ->with('company', 'children')
            ->orderBy('name')->paginate(10);

        return view('admin.categories.index', compact('categories', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'product');

        return view('admin.categories.form', [
            'data' => null,
            'type' => $type,
            'companies' => \App\Models\Company::orderBy('name')->get(),
            'parents' => Category::where('type', $type)->whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:product,post'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        // Valida profundidade máxima (3 níveis)
        if ($data['parent_id']) {
            $parent = Category::find($data['parent_id']);
            if ($parent && $parent->level >= 2) {
                return back()->withErrors(['parent_id' => 'Máximo de 3 níveis de subcategoria.']);
            }
        }

        $data['slug'] = Str::slug($data['name']);
        if (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] .= '-' . uniqid();
        }

        Category::create($data);

        return redirect()->route('admin.categories.index', ['type' => $data['type']])
            ->with('success', 'Categoria criada!');
    }

    public function show(Category $category)
    {
        $category->load('parent', 'children.children');
        $type = $category->type;

        return view('admin.categories.form', [
            'data' => $category,
            'type' => $type,
            'companies' => \App\Models\Company::orderBy('name')->get(),
            'parents' => Category::where('type', $type)->whereNull('parent_id')->orderBy('name')->get(),
            'readonly' => true,
        ]);
    }

    public function edit(Category $category)
    {
        $category->load('parent', 'children.children');
        $type = $category->type;

        return view('admin.categories.form', [
            'data' => $category,
            'type' => $type,
            'companies' => \App\Models\Company::orderBy('name')->get(),
            'parents' => Category::where('type', $type)->whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:product,post'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        // Impede vincular a si mesma ou a um descendente
        if ($data['parent_id']) {
            if ($data['parent_id'] == $category->id) {
                return back()->withErrors(['parent_id' => 'Uma categoria não pode ser pai dela mesma.']);
            }
            $parent = Category::find($data['parent_id']);
            if ($parent && $parent->level >= 2) {
                return back()->withErrors(['parent_id' => 'Máximo de 3 níveis de subcategoria.']);
            }
        }

        if ($data['name'] !== $category->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index', ['type' => $category->type])
            ->with('success', 'Categoria atualizada!');
    }

    public function destroy(Category $category)
    {
        $type = $category->type;
        $category->children()->delete();
        $category->delete();

        return redirect()->route('admin.categories.index', compact('type'))
            ->with('success', 'Categoria removida!');
    }
}
