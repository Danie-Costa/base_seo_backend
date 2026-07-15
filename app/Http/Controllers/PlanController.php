<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('price')->paginate(10);

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.automatic.create', [
            'route' => 'plans',
            'title' => 'Criar Plano',
            'data' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plano criado!');
    }

    public function edit(Plan $plan)
    {
        return view('admin.automatic.edit', [
            'route' => 'plans',
            'title' => 'Editar Plano',
            'data' => $plan,
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plano atualizado!');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plano removido!');
    }
}
