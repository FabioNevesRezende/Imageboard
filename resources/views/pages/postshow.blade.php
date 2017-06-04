@extends('boardbase')


@section('titulo', $nomeBoard)

@section('conteudo')

@include('partials._boardpostheader')
<hr>
@include('partials._postposts')
@endsection
