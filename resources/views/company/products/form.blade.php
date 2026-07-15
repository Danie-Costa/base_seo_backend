@php $isEdit = !empty($data); @endphp

@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    <h3>{{ $isEdit ? 'Editar Produto' : 'Novo Produto' }}</h3>
    {{ Form::open(['route' => $isEdit ? ['company.products.update', $data->id] : 'company.products.store', 'method' => $isEdit ? 'put' : 'post', 'files' => true]) }}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $data->name ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descricao</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description', $data->description ?? '') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preco (R$)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $data->price ?? '0') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categorias</label>
                    <select name="categories[]" multiple class="form-select" style="height:120px">
                        @php $selectedIds = old('categories', $isEdit ? $data->categories?->pluck('id')->toArray() : []); @endphp
                        @foreach(\App\Models\Category::where('type', 'product')->get() as $cat)
                            <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedIds) ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Segure Ctrl para varias</small>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="active" value="1" class="form-check-input" {{ old('active', $data->active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">Ativo</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagens (ate 10)</label>
                <input type="file" name="images[]" multiple class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" id="productImages">
                <small class="text-muted">JPEG, PNG, GIF, WebP. Convertido para WebP ate 200KB.</small>
            </div>
            <div id="imagePreview" class="d-flex flex-wrap gap-2 mb-3"></div>

            @if($isEdit && $data->images->isNotEmpty())
            <div class="row mt-3">
                <div class="col-12">
                    <label class="form-label">Imagens atuais</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($data->images as $img)
                        <div class="position-relative border rounded p-1 text-center {{ $img->is_cover ? 'border-primary border-2' : '' }}" style="width:120px">
                            <img src="{{ asset('storage/' . $img->path) }}" style="width:100px;height:80px;object-fit:cover" class="rounded">
                            <div class="mt-1">
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="cover_image_id" value="{{ $img->id }}" class="form-check-input" id="cover_{{ $img->id }}" {{ $img->is_cover ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="cover_{{ $img->id }}">Capa</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="remove_images[]" value="{{ $img->id }}" class="form-check-input" id="rm_{{ $img->id }}">
                                    <label class="form-check-label small text-danger" for="rm_{{ $img->id }}">X</label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-success w-50">Salvar</button>
    </div>
    {{ Form::close() }}
</div>
@endsection

@push('js')
<script>
document.getElementById('productImages')?.addEventListener('change', function() {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    const dt = new DataTransfer();

    for (const file of this.files) {
        const idx = dt.items.length;
        dt.items.add(file);

        const reader = new FileReader();
        reader.onload = function(ev) {
            const div = document.createElement('div');
            div.className = 'position-relative border rounded p-1 text-center';
            div.style.width = '120px';
            div.dataset.idx = idx;
            div.innerHTML = `
                <img src="${ev.target.result}" style="width:100px;height:80px;object-fit:cover" class="rounded">
                <div class="mt-1">
                    <button type="button" class="btn btn-sm btn-outline-danger p-0 px-1 remove-preview" data-idx="${idx}">Remover</button>
                </div>`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }

    function syncFiles() {
        const newDt = new DataTransfer();
        preview.querySelectorAll('[data-idx]').forEach(el => {
            const i = parseInt(el.dataset.idx);
            if (i < dt.items.length) newDt.items.add(dt.items[i].getAsFile());
        });
        document.getElementById('productImages').files = newDt.files;
    }

    preview.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-preview');
        if (!btn) return;
        const idx = parseInt(btn.dataset.idx);
        const div = preview.querySelector(`[data-idx="${idx}"]`);
        if (div) div.remove();
        syncFiles();
    });
});
</script>
@endpush
