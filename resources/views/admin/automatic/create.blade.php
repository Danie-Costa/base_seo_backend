@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div  class="mt-3 pt-3 container vh-100">
    <h3 class="py-2">{{$title}}</h3>
    {{ Form::open(['route' => 'admin.'.$route.'.store','class' => '', 'files' => true]) }}
        @include('admin.'.$route.'.form',['data'=>$data])
        <div class="col-6 col-sm-12 mt-2 d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-success btn-block w-50 ">Salvar</button>
        </div>        
    {{ Form::close() }}
</div>
@endsection
@section('js')

   
@endsection
@section('css')
    <style>
        .nav-desk {
            position: relative  !important;
        }
    </style>
@endsection