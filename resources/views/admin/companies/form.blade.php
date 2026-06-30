@php
    $isDisabled = $disabled ?? false;
    $company = $data ?? null;
    $socialTypes = $socialTypes ?? \App\Models\Social::TYPES;
    $currentSocials = $company ? $company->socials->keyBy('name') : collect();
@endphp

<div class="card shadow-sm">
    <div class="card-header"></div>

    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $company->name ?? '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">CNPJ</label>
            <input type="text"
                   name="cnpj"
                   class="form-control @error('cnpj') is-invalid @enderror"
                   value="{{ old('cnpj', $company->cnpj ?? '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   required>
            @error('cnpj')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Endereco</label>
            <input type="text"
                   name="address"
                   class="form-control @error('address') is-invalid @enderror"
                   value="{{ old('address', $company->address ?? '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   required>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Telefone principal</label>
            <input type="text"
                   name="primary_phone"
                   class="form-control @error('primary_phone') is-invalid @enderror"
                   value="{{ old('primary_phone', $company->primary_phone ?? '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   required>
            @error('primary_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email principal</label>
            <input type="email"
                   name="primary_email"
                   class="form-control @error('primary_email') is-invalid @enderror"
                   value="{{ old('primary_email', $company->primary_email ?? '') }}"
                   {{ $isDisabled ? 'disabled' : '' }}
                   required>
            @error('primary_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <strong>Formas de contato</strong>
    </div>

    <div class="card-body">
        <div class="row">
            @foreach($socialTypes as $name => $socialType)
                @php
                    $social = $currentSocials->get($name);
                @endphp

                <div class="col-12 col-md-6 mb-4">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <i class="{{ $socialType['icon'] }} mr-2" aria-hidden="true"></i>
                            <strong>{{ $socialType['label'] }}</strong>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Titulo</label>
                            <input type="text"
                                   name="socials[{{ $name }}][title]"
                                   class="form-control @error('socials.' . $name . '.title') is-invalid @enderror"
                                   value="{{ old('socials.' . $name . '.title', $social->title ?? $socialType['label']) }}"
                                   {{ $isDisabled ? 'disabled' : '' }}>

                            @error('socials.' . $name . '.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Contato ou link</label>
                            <input type="text"
                                   name="socials[{{ $name }}][link]"
                                   class="form-control @error('socials.' . $name . '.link') is-invalid @enderror"
                                   value="{{ old('socials.' . $name . '.link', $social->link ?? '') }}"
                                   placeholder="{{ $socialType['placeholder'] }}"
                                   {{ $isDisabled ? 'disabled' : '' }}>

                            @error('socials.' . $name . '.link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($company && $company->socials->isNotEmpty())
            <div class="border-top pt-3 mt-2">
                <strong class="d-block mb-3">Contatos cadastrados</strong>

                <div class="row">
                    @foreach($company->socials as $social)
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="{{ $social->icon }} mr-2" aria-hidden="true"></i>
                                <span class="font-weight-bold mr-2">{{ $social->title }}:</span>
                                <span>{{ $social->link }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@if($company)
    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Usuarios da empresa</strong>
            <a href="{{ route('admin.users.create', ['company_id' => $company->id, 'return_company_id' => $company->id]) }}" class="btn btn-sm btn-primary">
                Adicionar usuario
            </a>
        </div>

        <div class="card-body">
            @if($company->users->isEmpty())
                <p class="mb-0 text-muted">Nenhum usuario vinculado a esta empresa.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Perfil</th>
                                <th class="text-right">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ \App\Models\User::RULES[$user->rule] ?? '-' }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.users.show', ['user' => $user->id, 'company_id' => $company->id, 'return_company_id' => $company->id]) }}" class="btn btn-sm btn-info">Ver</a>
                                        <a href="{{ route('admin.users.edit', ['user' => $user->id, 'company_id' => $company->id, 'return_company_id' => $company->id]) }}" class="btn btn-sm btn-primary">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endif
