<div class="modal fade" id="userCreate" tabindex="-1" aria-labelledby="userCreateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userCreateLabel">Novo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ Form::open(['route' => ['company.users.store',$company->id],'class' => '','method' => 'post', 'files' => true]) }}
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"> Senha</label>
                        <input type="password" name="password" class="form-control" value="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Perfil</label>
                        <select name="rule" class="form-select @error('rule') is-invalid @enderror" required> 
                            <option value="">Selecione</option>
                            @foreach($createUserRules as $key => $label)
                                <option value="{{ $key }}">
                                    {{ $label }}
                                </option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

