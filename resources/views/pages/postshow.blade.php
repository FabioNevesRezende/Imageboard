@extends('boardbase')


@section('titulo', $siglaBoard)

@section('conteudo')

@include('partials._boardpostheader')
<hr>
@include('partials._postposts')
@include('partials._modalban')
@include('partials._modalreport')
@include('partials._modaldeleteboard')
@endsection
