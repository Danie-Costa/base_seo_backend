<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Http\Services\CompanyService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $files = MediaFile::where('project_id', $company->id)
            ->orderBy('created_at', 'desc')->paginate(10);

        return view('company.files.index', compact('files'));
    }

    public function create()
    {
        return view('company.automatic.create', [
            'route' => 'files',
            'title' => 'Novo Arquivo',
            'data' => null,
        ]);
    }

    public function store(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
        ]);

        $data['project_id'] = $company->id;

        MediaFile::create($data);

        return redirect()->route('company.files.index')->with('success', 'Arquivo adicionado!');
    }

    public function destroy($id)
    {
        $company = (new CompanyService)->myCompany();
        $file = MediaFile::where('project_id', $company->id)->findOrFail($id);
        $file->delete();

        return redirect()->route('company.files.index')->with('success', 'Arquivo removido!');
    }
}
