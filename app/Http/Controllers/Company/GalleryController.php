<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Image;
use App\Http\Services\CompanyService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $galleries = Gallery::where('project_id', $company->id)
            ->withCount('images')
            ->orderBy('created_at', 'desc')->paginate(10);

        return view('company.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('company.automatic.create', [
            'route' => 'galleries',
            'title' => 'Nova Galeria',
            'data' => null,
        ]);
    }

    public function store(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $data['project_id'] = $company->id;

        Gallery::create($data);

        return redirect()->route('company.galleries.index')->with('success', 'Galeria criada!');
    }

    public function show($id)
    {
        $company = (new CompanyService)->myCompany();
        $gallery = Gallery::where('project_id', $company->id)
            ->with('images')->findOrFail($id);

        return view('company.galleries.show', compact('gallery'));
    }

    public function destroy($id)
    {
        $company = (new CompanyService)->myCompany();
        $gallery = Gallery::where('project_id', $company->id)->findOrFail($id);
        $gallery->delete();

        return redirect()->route('company.galleries.index')->with('success', 'Galeria removida!');
    }

    public function uploadImage(Request $request, $id)
    {
        $company = (new CompanyService)->myCompany();
        $gallery = Gallery::where('project_id', $company->id)->findOrFail($id);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
        ]);

        $data['gallery_id'] = $gallery->id;
        $data['project_id'] = $company->id;

        Image::create($data);

        return redirect()->route('company.galleries.show', $gallery->id)->with('success', 'Imagem adicionada!');
    }

    public function destroyImage($id)
    {
        $image = Image::findOrFail($id);
        $image->delete();

        return back()->with('success', 'Imagem removida!');
    }
}
