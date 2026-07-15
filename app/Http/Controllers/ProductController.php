<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('company', 'images', 'categories');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.automatic.create', [
            'route' => 'products',
            'title' => 'Criar Produto',
            'data' => null,
            'companies' => \App\Models\Company::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'active' => ['boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

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

        return redirect()->route('admin.products.index')->with('success', 'Produto criado!');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'categories');

        return view('admin.automatic.edit', [
            'route' => 'products',
            'title' => 'Editar Produto',
            'data' => $product,
            'companies' => \App\Models\Company::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
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

        // Cover
        if ($request->filled('cover_image_id')) {
            $product->images()->update(['is_cover' => false]);
            $product->images()->where('id', $request->cover_image_id)->update(['is_cover' => true]);
        }

        // Remove imagens
        if ($request->filled('remove_images')) {
            $product->images()->whereIn('id', $request->remove_images)->delete();
        }

        // Upload novas
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

        return redirect()->route('admin.products.index')->with('success', 'Produto atualizado!');
    }

    public function destroy(Product $product)
    {
        $product->categories()->detach();
        $product->images()->delete();
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produto removido!');
    }
}
