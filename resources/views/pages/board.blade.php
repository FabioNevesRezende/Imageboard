@extends('boardbase')


@section('titulo', $siglaBoard)

@section('conteudo')

@include('partials._boardpostheader')
<hr>
@include('partials._boardposts')

@if(Auth::check())
@include('partials._modalban')
@include('partials._modaldeleteboard')
@include('partials._modalmoverpost')
@endif

@include('partials._modalreport')
@endsection
