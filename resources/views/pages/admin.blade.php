@extends('boardbase')


@section('titulo', 'Admin')

@section('conteudo')
    <h1>&nbsp;&nbsp;&nbsp;Bem vindo, {{ Auth::user()->name }}</h1><br>
    <div class="container-fluid">
        @foreach($reports as $report)
            <div class="alert alert-success" role="alert">
                <strong>Report número: </strong>{{ $report->id }}<br>
                <strong>Referência: </strong><a href="/{{ $report->board }}/{{ $report->post_id }}">{{ $report->post_id }}</a><br>
                <strong>Motivo: </strong><p>{{ $report->motivo }}</p>
            </div>
            <hr>
        @endforeach

        @if(isset($configuracaos))
            <div class="admin-page">
                <div class="row">
                    <div class="col-sm-2">
                        
                    <ul id="admin-buttons">
            @if(Auth::id() === 1)
                        <li><a href="/seedar"><button type="button" class="btn btn-success">Seedar</button></a></li>
                        <li><a href="/limparcache"><button type="button" class="btn btn-default">Limpar Cache</button></a></li>
                        <li><a href="/migrate"><button type="button" class="btn btn-warning">Migrate</button></a></li>
                        <li><a href="/phpinfo" target="_blank"><button type="button" class="btn btn-primary">PhpInfo</button></a></li>
                        <li><a href="/migrate/refresh"><button type="button" class="btn btn-danger">Migrate:refresh + seed</button></a></li>
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
        @endif
        <br>
        <div class="row">
            <div class="col-sm-6 div-indice">
                @if(isset($noticiaEditar) && $noticiaEditar != null)
                    <b>Editar noticia</b>
                    <form class="form-post" role="form" method="POST" action="{{ route('noticias.update_noticia') }}">
                        {{ csrf_field() }}
                        Título:<br>
                        <input type="text" name="assunto" maxlength="256" 
                            value="{{ $noticiaEditar->assunto }}" required><br><br>
                        Notícia:<br>
                        <textarea rows="6" cols="70" 
                            name="conteudo" maxlength="65535" required>{{ $noticiaEditar->conteudo }}</textarea><br>
                        
                        <input type="hidden" name="id" value="{{ $noticiaEditar->id }}"><br><br>
                        <input type="submit" class="btn btn-primary" value="Editar"><br><br>
                    </form>
                @else
                    <b>Divulgar noticia</b>
                    <form class="form-post" role="form" method="POST" action="{{ route('noticias.nova_noticia') }}">
                        {{ csrf_field() }}
                        Título:<br>
                        <input type="text" name="assunto" maxlength="256" required><br><br>
                        Notícia:<br>
                        <textarea rows="6" cols="70" name="conteudo" maxlength="65535" required></textarea><br>
                        <input type="submit" class="btn btn-primary" value="Divulgar"><br><br>
                    </form>
                @endif
            </div>
            <div class="col-sm-6 div-indice">
                <b>Criar nova board</b>
                <form class="form-post" role="form" method="POST" action="{{ route('boards.store') }}">
                    {{ csrf_field() }}
                    Nome da board:<br>
                    <input type="text" name="nome" maxlength="50" required><br><br>
                    /sigla/:<br>
                    <input type="text" name="sigla" maxlength="10" required><br><br>
                    Descrição:<br>
                    <input type="text" name="descricao" maxlength="300" required><br><br>
                    Ordem:<br>
                    <input type="number" name="ordem" max="32767" min="-32767" required><br><br>
                    <input type="submit" class="btn btn-primary" value="Criar board"><br><br>
                </form>
                
            </div>
            
        </div>
        <div class="row" >
            <div class="col-sm-6 div-indice">
                <b>Definir nova regra</b>
                <form class="form-post" role="form" method="POST" action="{{ route('regras.regra') }}">
                    {{ csrf_field() }}
                    Descrição:<br>
                    <input type="text" name="descricao" maxlength="256" required><br><br>
                    Board:<br>
                    <select name="board_name">
                        <option value="todas" selected>todas</option>
                        @foreach($boards as $board => $boardnome)
                        <option value="{{ $board }}"> {{ $board }}</option>
                        @endforeach
                    </select>
                    <br>
                    <br>
                    <input type="submit" class="btn btn-primary" value="Criar regra"><br><br>
                </form>
            </div>
            <div class="col-sm-6 div-indice">
                <b>Alterar senha</b>
                <form class="form-post" role="form" method="POST" action="{{ route('users.update_password') }}">
                    {{ csrf_field() }}
                    Senha antiga:<br>
                    <input type="password" name="old_password" maxlength="25" required><br><br>
                    Nova senha:<br>
                    <input type="password" name="password" maxlength="25" required><br><br>
                    Confirmação nova senha:<br>
                    <input type="password" name="confirm_password" maxlength="25" required><br><br>
                    <br>
                    <br>
                    <input type="submit" class="btn btn-primary" value="Alterar senha"><br><br>
                </form>
            </div>
        </div>
        
    </div> <!-- container-fluid -->
@endsection
