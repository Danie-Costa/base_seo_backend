<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        Lead::create($data);

        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }

    public function index()
    {
        $leads = Lead::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.leads.index', compact('leads'));
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.leads.index')->with('success', 'Lead removido.');
    }
}
