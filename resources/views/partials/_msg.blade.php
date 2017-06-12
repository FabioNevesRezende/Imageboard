
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
