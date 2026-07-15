<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('company', 'categories');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.automatic.create', [
            'route' => 'posts',
            'title' => 'Criar Post',
            'data' => null,
            'companies' => \App\Models\Company::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('cover_image')) {
            $dir = 'posts/' . Str::slug($data['title']) . '-' . uniqid();
            $data['cover_image'] = ImageService::convertToWebP($request->file('cover_image'), $dir);
        }

        $post = Post::create($data);

        if ($request->filled('categories')) {
            $post->categories()->sync($request->categories);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post criado!');
    }

    public function edit(Post $post)
    {
        $post->load('categories');

        return view('admin.automatic.edit', [
            'route' => 'posts',
            'title' => 'Editar Post',
            'data' => $post,
            'companies' => \App\Models\Company::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
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

        return redirect()->route('admin.posts.index')->with('success', 'Post atualizado!');
    }

    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post removido!');
    }
}
