<div class="row">
    <div class="col-12 col-sm-6">
        <h3>{{$title}}</h2>
    </div>
    <div class="col-10 col-sm-4">
        {!! Form::open(['method' => 'get','class'=>'form-inline my-2 my-lg-0']) !!}
            <div class="input-group w-100">
                <input class="form-control" type="search" name="search" placeholder="Pesquisar" aria-label="Search" value="{{ Request::get('search', Request::get('name')) }}">
                <div class="input-group-prepend">
                    <button class="btn btn-outline-success" type="submit"><span data-feather="search"></span></button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
    <div class="col-2 col-sm-2">
        <a href="{{ route("{$route}.create") }}" class="btn btn-primary w-100 p-0 py-2 mt-sm-0  mt-1 "><span data-feather="plus"></span><span class="hide-mobile">Adicionar</span></a>
    </div>
</div>

