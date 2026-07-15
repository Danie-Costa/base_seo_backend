@php $isEdit = !empty($plan); @endphp

@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    <h3 class="py-2">{{ $isEdit ? 'Editar Plano' : 'Criar Plano' }}</h3>

    {{ Form::open(['route' => $isEdit ? ['admin.plans.update', $plan->id] : 'admin.plans.store', 'method' => $isEdit ? 'put' : 'post']) }}

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $plan->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Descricao</label>
                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $plan->description ?? '') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Preco (R$)</label>
                <input type="number" step="0.01" min="0" name="price"
                       class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $plan->price ?? '0') }}" required>
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-success w-50">Salvar</button>
    </div>

    {{ Form::close() }}
</div>
@endsection
