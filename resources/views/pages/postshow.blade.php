@extends('boardbase')


@section('titulo', $nomeBoard)

@section('conteudo')

@include('partials._boardpostheader')
<hr>
@include('partials._postposts')
@include('partials._modalban')
@include('partials._modalreport')
@endsection
