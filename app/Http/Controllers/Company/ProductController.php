<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ImageService;
use App\Http\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $products = Product::where('company_id', $company->id)
            ->with('images', 'categories')
            ->orderBy('name')->paginate(10);

        return view('company.products.index', compact('products'));
    }

    public function create()
    {
        return view('company.automatic.create', [
            'route' => 'products',
            'title' => 'Novo Produto',
            'data' => null,
        ]);
    }

    public function store(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'active' => ['boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        $data['company_id'] = $company->id;
        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $request->boolean('active');
        $product = Product::create($data);

        if ($request->filled('categories')) {
            $product->categories()->sync($request->categories);
        }

        if ($request->hasFile('images')) {
            $dir = 'products/' . $product->id;
            $order = 0;
            foreach ($request->file('images') as $file) {
                if ($order >= 10) break;
                $path = ImageService::convertToWebP($file, $dir);
                $product->images()->create([
                    'path' => $path,
                    'order' => $order,
                    'is_cover' => $order === 0,
                ]);
                $order++;
            }
        }

        return redirect()->route('company.products.index')->with('success', 'Produto criado!');
    }

    public function edit($id)
    {
        $company = (new CompanyService)->myCompany();
        $data = Product::where('company_id', $company->id)
            ->with('images', 'categories')->findOrFail($id);

        return view('company.automatic.edit', [
            'route' => 'products',
            'title' => 'Editar Produto',
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = (new CompanyService)->myCompany();
        $product = Product::where('company_id', $company->id)->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'active' => ['boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:product_images,id'],
            'cover_image_id' => ['nullable', 'integer', 'exists:product_images,id'],
        ]);

        if ($data['name'] !== $product->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['active'] = $request->boolean('active');
        $product->update($data);

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories ?? []);
        }

        if ($request->filled('cover_image_id')) {
            $product->images()->update(['is_cover' => false]);
            $product->images()->where('id', $request->cover_image_id)->update(['is_cover' => true]);
        }

        if ($request->filled('remove_images')) {
            $product->images()->whereIn('id', $request->remove_images)->delete();
        }

        if ($request->hasFile('images')) {
            $currentCount = $product->images()->count();
            $dir = 'products/' . $product->id;
            $order = $currentCount;

            foreach ($request->file('images') as $file) {
                if ($order >= 10) break;
                $path = ImageService::convertToWebP($file, $dir);
                $product->images()->create(['path' => $path, 'order' => $order]);
                $order++;
            }
        }

        return redirect()->route('company.products.index')->with('success', 'Produto atualizado!');
    }

    public function destroy($id)
    {
        $company = (new CompanyService)->myCompany();
        $product = Product::where('company_id', $company->id)->findOrFail($id);
        $product->categories()->detach();
        $product->images()->delete();
        $product->delete();

        return redirect()->route('company.products.index')->with('success', 'Produto removido!');
    }
}
