<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Services\ImageService;
use App\Http\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $posts = Post::where('company_id', $company->id)
            ->with('categories')
            ->orderBy('created_at', 'desc')->paginate(10);

        return view('company.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('company.automatic.create', [
            'route' => 'posts',
            'title' => 'Novo Post',
            'data' => null,
        ]);
    }

    public function store(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        $data['company_id'] = $company->id;
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('cover_image')) {
            $dir = 'posts/' . $data['slug'] . '-' . uniqid();
            $data['cover_image'] = ImageService::convertToWebP($request->file('cover_image'), $dir);
        }

        $post = Post::create($data);

        if ($request->filled('categories')) {
            $post->categories()->sync($request->categories);
        }

        return redirect()->route('company.posts.index')->with('success', 'Post criado!');
    }

    public function edit($id)
    {
        $company = (new CompanyService)->myCompany();
        $data = Post::where('company_id', $company->id)
            ->with('categories')->findOrFail($id);

        return view('company.automatic.edit', [
            'route' => 'posts',
            'title' => 'Editar Post',
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = (new CompanyService)->myCompany();
        $post = Post::where('company_id', $company->id)->findOrFail($id);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        if ($data['title'] !== $post->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($request->hasFile('cover_image')) {
            $dir = 'posts/' . ($data['slug'] ?? $post->slug) . '-' . uniqid();
            $data['cover_image'] = ImageService::convertToWebP($request->file('cover_image'), $dir);
        }

        $post->update($data);

        if ($request->has('categories')) {
            $post->categories()->sync($request->categories ?? []);
        }

        return redirect()->route('company.posts.index')->with('success', 'Post atualizado!');
    }

    public function destroy($id)
    {
        $company = (new CompanyService)->myCompany();
        $post = Post::where('company_id', $company->id)->findOrFail($id);
        $post->categories()->detach();
        $post->delete();

        return redirect()->route('company.posts.index')->with('success', 'Post removido!');
    }
}
