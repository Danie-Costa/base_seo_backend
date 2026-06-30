@php
    $isEdit = !empty($data);
    $isDisabled = $disabled ?? false;
    $selectedCompanyId = old('company_id', $selectedCompanyId ?? $data->company_id ?? null);
@endphp

@if(!empty($returnCompanyId))
    <input type="hidden" name="return_company_id" value="{{ $returnCompanyId }}">
@endif

<div class="card shadow-sm">
    <div class="card-header"></div>

    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $data->name ?? '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   required>

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $data->email ?? '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   required>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Empresa</label>
            <select name="company_id"
                    class="form-select @error('company_id') is-invalid @enderror"
                    {{ $isDisabled ? 'disabled' : '' }}>
                <option value="">Selecione</option>

                @foreach(($companies ?? collect()) as $company)
                    <option value="{{ $company->id }}" {{ (string) $selectedCompanyId === (string) $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>

            @error('company_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">
                Senha {{ $isEdit ? '(deixe em branco para nao alterar)' : '' }}
            </label>
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   value="{{ old('password', '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   {{ $isEdit ? '' : 'required' }}>

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Perfil</label>
            <select name="rule"
                    class="form-select @error('rule') is-invalid @enderror"
                    {{ $isDisabled ? 'disabled' : '' }}
                    required>
                <option value="">Selecione</option>

                @foreach(\App\Models\User::RULES as $key => $label)
                    <option value="{{ $key }}" {{ old('rule', $data->rule ?? '') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            @error('rule')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
