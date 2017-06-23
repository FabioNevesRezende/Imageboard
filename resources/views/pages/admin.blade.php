@extends('boardbase')


@section('titulo', 'Admin')

@section('conteudo')

@if(Auth::id() === 1)
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-10"></div>
        <div class="col-sm-2">
            <a href="/migrate"><button type="button" class="btn btn-danger">Migrate</button></a>
            <a href="/migrate/refresh"><button type="button" class="btn btn-danger">Migrate:refresh</button></a>
        </div>
    </div>
</div>
@endif

@foreach($reports as $report)
<div class="alert alert-success" role="alert">
    <strong>Report número: </strong>{{ $report->id }}<br>
    <strong>Referência: </strong><a href="/{{ $report->board }}/{{ $report->post_id }}">{{ $report->post_id }}</a><br>
    <strong>Motivo: </strong><p>{{ $report->motivo }}</p>
</div>
<hr>
@endforeach


@endsection
