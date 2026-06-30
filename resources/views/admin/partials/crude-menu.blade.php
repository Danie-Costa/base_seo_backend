@if(!isset($data->deleted_at) || $data->deleted_at == NULL )
    @if(in_array($route.'.show', $crudMenu))
        <a href="{{route($route.'.show', $data->id)}}"
            class="btn d-flex justify-content-center align-items-center btn-info" data-toggle="tooltip"
            title="Ver">
            <span data-feather="eye"></span>
        </a>
    @endif
    @if(in_array($route.'.edit', $crudMenu))
        <a href="{{route($route.'.edit', $data->id)}}"
            class="btn d-flex justify-content-center align-items-center btn-primary" data-toggle="tooltip"
            title="Atualizar">
            <span data-feather="edit"></span>
        </a>
    @endif
    @if(in_array($route.'.destroy', $crudMenu))
        {{ Form::hidden('_method', 'DELETE') }}
        <button class="btn d-flex justify-content-center align-items-center btn-outline-danger" type="submit"
            onclick="return confirmDelete(event)"
            data-toggle="tooltip"
            title="Excluir">
            <span data-feather="trash-2"></span>
        </button>
    @endif
@endif

@section('Swal')
<script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.currentTarget.form;  // Captura o formulário que o botão pertence

        Swal.fire({
            title: 'Tem certeza?',
            text: "Você deseja excluir este item!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, delete isso!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submete o formulário se o usuário confirmar
            }
        });
    }
</script>
@endsection
