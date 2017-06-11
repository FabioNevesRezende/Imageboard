@extends('boardbase')


@section('titulo', $nomeBoard)

@section('conteudo')

@include('partials._boardpostheader')
<hr>
@include('partials._boardposts')
@include('partials._modalban')
@endsection
