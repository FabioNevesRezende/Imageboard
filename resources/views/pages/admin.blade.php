@extends('boardbase')


@section('titulo', 'Admin')

@section('conteudo')


@foreach($reports as $report)
<div class="alert alert-success" role="alert">
    <strong>Report número: </strong>{{ $report->id }}<br>
    <strong>Referência: </strong><a href="/{{ $report->board }}/{{ $report->post_id }}">{{ $report->post_id }}</a><br>
    <strong>Motivo: </strong><p>{{ $report->motivo }}</p>
    
</div>
<hr>
@endforeach


@endsection
