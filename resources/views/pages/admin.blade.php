@extends('boardbase')


@section('titulo', 'Admin')

@section('conteudo')
@if(isset($configuracaos))
<div class="admin-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            
        <ul id="admin-buttons">
@if(Auth::id() === 1)
            <li><a href="/seedar"><button type="button" class="btn btn-success">Seedar</button></a></li>
            <li><a href="/limparcache"><button type="button" class="btn btn-default">Limpar Cache</button></a></li>
            <li><a href="/migrate"><button type="button" class="btn btn-warning">Migrate</button></a></li>
            <li><a href="/migrate/refresh"><button type="button" class="btn btn-danger">Migrate:refresh</button></a></li>
@endif
@if($configuracaos->captcha_ativado)
            <li><a href="/togglecaptcha/0"><button type="button" class="btn btn-danger">Desativar captcha</button></a></li>
@elseif(!$configuracaos->captcha_ativado)
            <li><a href="/togglecaptcha/1"><button type="button" class="btn btn-primary">Ativar captcha</button></a></li>
@endif
            <li>
            <li><a href="/logout"><button type="button" class="btn btn-danger">Logout</button></a></li>
            </li>
        </ul>
        </div>
        <div class="col-sm-10"></div>
    </div>
</div>

@foreach($reports as $report)
<div class="alert alert-success" role="alert">
    <strong>Report número: </strong>{{ $report->id }}<br>
    <strong>Referência: </strong><a href="/{{ $report->board }}/{{ $report->post_id }}">{{ $report->post_id }}</a><br>
    <strong>Motivo: </strong><p>{{ $report->motivo }}</p>
</div>
<hr>
@endforeach

</div>
@endif
@endsection
