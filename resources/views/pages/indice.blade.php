@extends('main')


@section('titulo', 'Imageboard Brasil')

@section('stylesheets')
    <link rel="stylesheet" href="/css/style.css" >
@stop

@section('conteudo')
<div class="container-fluid">

@if(isset($regras))
    <div class="row">

        <div class="col-sm-4"></div>
        
        <div class="col-sm-4 text-center div-indice">
            <br><b>Regras</b><br><br>
            @foreach($regras->where('board_name', 'is', null) as $ind => $regra)
            {{ $ind+1 }} - {{ $regra->descricao }}
            @if(Auth::check())
                <a 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="Deletar regra" 
                    class="mini-btn" 
                    href="/deleteregra/{{ $regra->id }}"><span class="glyphicon glyphicon-remove"></span></a>
            @endif
            <br>
            @endforeach
            <br><br>
        </div>
        
        <div class="col-sm-4"></div>
        
    </div>
@endif

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

@if(isset($noticias))
    <br>
    <hr>
    @foreach($noticias as $noticia)
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 div-indice"> 
            @if(Auth::check() && $noticia->autor_id === Auth::id() || Auth::id() === 1)
                <a 
                data-toggle="tooltip" 
                data-placement="top" 
                title="Editar noticia" 
                class="mini-btn" 
                href="/editnoticia/{{ $noticia->id }}"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;
                <a 
                data-toggle="tooltip" 
                data-placement="top" 
                title="Deletar noticia" 
                class="mini-btn" 
                href="/deletenoticia/{{ $noticia->id }}"><span class="glyphicon glyphicon-remove"></span></a>
                <br>
            @endif
            <h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $noticia->assunto }}</h4>
            <small class="noticia-data">{{ $noticia->autor->name }}, {{ $noticia->data_post }}</small><br>
                <div class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                {{ $noticia->conteudo }}<br><br>
                </div>
            </div>
            <div class="col-sm-2"></div>
        </div>
        <br>
    @endforeach
@endif

</div>

@endsection


@section('scripts')
@endsection