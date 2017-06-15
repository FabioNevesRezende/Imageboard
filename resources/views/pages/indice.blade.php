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
        
        
    </div>
    
    <div class="col-sm-4"></div>
    
</div>
</div>
@endsection


@section('scripts')
@endsection