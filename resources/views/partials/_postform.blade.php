
<h1 class="board-header" id="id-board-{{ $siglaBoard }}"> /{{ $siglaBoard }}/ - {{ $descrBoard }} </h1>
@if(Auth::check() && Auth::id() === 1)
<a  data-toggle="modal" data-target="#modalDeleteBoard" class="btn btn-danger">Deletar board</a>
@endif
<br>
<form class="form-post" role="form" method="POST" enctype="multipart/form-data" action="{{ route('posts.store') }}">
{{ csrf_field() }}
<input type="hidden" name="siglaboard" value="{{ $siglaBoard }}">
<input type="hidden" name="insidepost" value="{{ $insidePost }}">
@if(Auth::check()) 
<div style="float: left; margin-bottom: 20px; margin-left: 15px;">
Modpost <input type="checkbox" class="novo-post-form-item" name="modpost" value="modpost">
</div>
@endif
<input type="text" class="novo-post-form-item form-control" maxlength="255" placeholder="Assunto" name="assunto" >
<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-11">
        <div id="form-post-file-input-div">
            <div class="form-post-file-input-box">
                <input class="novo-post-form-item form-post-file-input" name="arquivos[]" type="file" onchange="addNovoInputFile(this, {{ $configuracaos->num_max_arq_post }})">
                Spoiler <input name="arquivos-spoiler-1" type="checkbox" value="spoiler">
            </div>
        </div>
    </div>
</div>

<input type="text" class="novo-post-form-item form-control" maxlength="255" placeholder="Link(s) para vídeo(s) do youtube, separados por |" name="linkyoutube" >
<textarea class="novo-post-form-item form-control" id="novo-post-conteudo" placeholder="Mensagem" rows="5" maxlength="32768" name="conteudo"></textarea>
<p style="margin-left: 15px;">Mime types: image/jpeg, image/png, image/gif, video/webm, video/mp4, audio/mpeg</p>
<div class="row">
    <div class="col-sm-6">
        Sage <input type="checkbox" class="novo-post-form-item" name="sage" value="sage">
        
    </div>
    <div class="col-sm-6">
        <input type="submit" value="Postar" class="mini-btn form-control">
    </div>
</div>

@if($configuracaos->captcha_ativado)
<br>
<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-4 text-center">
    {!! $captchaImage !!}
    </div>
    <div class="col-sm-7">
        <input type="text" name="captcha" class="novo-post-form-item" maxlength="{{ $captchaSize }}" required>
    </div>
        
</div><br>
@endif

</form>