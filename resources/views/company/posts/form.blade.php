@php $isEdit = !empty($data); @endphp

@extends('layouts.app')


@section('content')

<div class="mt-3 pt-3 container">
    <h3>{{ $isEdit ? 'Editar Post' : 'Novo Post' }}</h3>
    {{ Form::open(['route' => $isEdit ? ['company.posts.update', $data->id] : 'company.posts.store', 'method' => $isEdit ? 'put' : 'post', 'files' => true]) }}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Titulo</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $data->title ?? '') }}" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Categorias</label>
                    <select name="categories[]" multiple class="form-select" style="height:120px">
                        @php $selectedIds = old('categories', $isEdit ? $data->categories?->pluck('id')->toArray() : []); @endphp
                        @foreach(\App\Models\Category::where('type', 'post')->get() as $cat)
                            <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedIds) ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Segure Ctrl para varias</small>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Capa</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    @if($isEdit && $data->cover_image)
                        <img src="{{ asset('storage/' . $data->cover_image) }}" style="max-height:60px" class="mt-1 rounded">
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Publicado em</label>
                    <input type="datetime-local" name="published_at" class="form-control"
                           value="{{ old('published_at', isset($data->published_at) ? $data->published_at->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Conteudo</label>
                <textarea name="content" id="postEditor" rows="15" class="form-control">{{ old('content', $data->content ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-success w-50">Salvar</button>
    </div>
    {{ Form::close() }}
</div>
@endsection

@push('js')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#postEditor',
    height: 500,
    plugins: 'lists link image preview code',
    toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | alignleft aligncenter alignright | code',
    images_upload_handler: function (blobInfo, progress) {
        return new Promise(function (resolve, reject) {
            const formData = new FormData();
            formData.append('image', blobInfo.blob(), blobInfo.filename());
            fetch('{{ route('admin.upload-image') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData,
            })
            .then(r => r.json())
            .then(r => resolve(r.url))
            .catch(reject);
        });
    },
});
</script>
@endpush
