@extends('layouts.app')

@section('content')
<div  class="mt-3 pt-3 container">
    <h3 class="py-2">{{$title}}</h3>
    {{ Form::open(['route' => ['admin.'.$route.'.update',$data->id],'class' => '','method' => 'put', 'files' => true]) }}
        @include('admin.'.$route.'.form',['data'=>$data])
        <div class="col-6 col-sm-12 mt-2 d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-success btn-block w-50 ">Salvar</button>
        </div>        
    {{ Form::close() }}
</div>
@endsection
@section('js')
    @stack('incjs')
@endsection
@section('css')
<style>
    .nav-desk {
        position: relative  !important;
    }
</style>
    @stack('inccss')
@endsection