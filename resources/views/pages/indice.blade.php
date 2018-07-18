@extends('main')


@section('titulo', 'Imageboard Brasil')

@section('stylesheets')
{!! Html::style('css/style.css') !!}
@stop

@section('conteudo')
<div class="container-fluid">
<div class="row">

    <div class="col-sm-4"></div>
    
    <div class="col-sm-4 text-center div-indice">
        Imageboard ainda em fase de testes.<br>
        Regras:<br><br>
        1 - Não poste conteúdo ilegal.<br>
        <br><br>
        
    </div>
    
    <div class="col-sm-4"></div>
    
</div>

@if($configuracaos->carteira_doacao)
<br>
<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8 text-center div-indice"> 
    <h4>Doações para pagamento de hospedagem e domínio:</h4>
    <a href="https://getmonero.org/" target="_blank">{{ $configuracaos->carteira_doacao }}</a> <br><br>
    
    </div>
    <div class="col-sm-2"></div>
</div>
@endif

</div>

@endsection


@section('scripts')
@endsection