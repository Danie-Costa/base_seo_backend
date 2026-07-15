@extends('layouts.app')

@section('content')
<div  class="mt-3 pt-3 container">
    @include('admin.'.$route.'.form',['data'=>$data , 'disabled' =>true])
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