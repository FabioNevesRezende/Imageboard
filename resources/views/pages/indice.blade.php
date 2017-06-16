@extends('main')


@section('titulo', 'Imageboard Brasil')

@section('stylesheets')
{!! Html::style('css/style.css') !!}
@stop

@section('conteudo')
<div class="container-fluid">
<div class="row">
    <div class="col-sm-4"></div>
    
    <div class="col-sm-4 text-center div-regras">
        Imageboard ainda em fase de testes.<br>
        Regras:<br><br>
        1 - Não poste pornografia infantil.<br>
        2 - Sem flood ou spam<br>
        3 - Sem attwhorismo<br>
        4 - Sem descarrilamento de fios<br>
        5 - Sem panelismo<br>
        <br><br>
        Doações para pagamento de hospedagem e domínio:<br>
        <span class="glyphicon glyphicon-bitcoin bitcoin-symbol"></span> 1zHm6j74ZC7BohhkT9f5HW8dnifNzLdoq <span class="glyphicon glyphicon-bitcoin bitcoin-symbol"></span>
        
    </div>
    
    <div class="col-sm-4"></div>
    
</div>
</div>
@endsection


@section('scripts')
@endsection