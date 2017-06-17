
@if (Session::has('post_criado'))

    <div class="alert alert-success" role="alert">
        <p><strong>Post criado:</strong> {!! Session::get('post_criado') !!}</p>
    </div>

@endif
@if (Session::has('erro_upload'))

    <div class="alert alert-warning" role="alert">
        <p><strong>Erro ao criar post:</strong> {{ Session::get('erro_upload') }}</p>
    </div>

@endif
@if (Session::has('ban'))

    <div class="alert alert-danger" role="alert">
        <p><strong>Erro ao criar post:</strong> {{ Session::get('ban') }}</p>
    </div>

@endif

@if (count($errors) > 0)

<div class="alert alert-danger" role="alert">
    <p><strong>Erro ao validar postagem:</strong></p>
    <ul>
        @foreach ($errors->all() as $error)
            @if(preg_match('/.*arquivos.*/s',$error)) <li>Mime type não permitido.</li> 
            @else <li>{{ $error }}</li>
            @endif
        @endforeach
    </ul>
</div>
@endif