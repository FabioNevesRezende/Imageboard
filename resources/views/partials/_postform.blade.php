
<h1 class="board-header"> /{{ $siglaBoard }}/ - {{ $descrBoard }} </h1>
@if(Auth::check() && Auth::id() === 1)
<a  data-toggle="modal" data-target="#modalDeleteBoard" class="btn btn-danger">Deletar board</a>
@endif
<br>
{!! Form::open(['route' => 'posts.store', 'enctype' => 'multipart/form-data', 'class'=>'form-post']) !!}
{{ csrf_field() }}
{{  Form::hidden('siglaboard', $siglaBoard) }}
{{  Form::hidden('insidepost', $insidePost) }}
@if(Auth::check()) 
<div style="float: left; margin-bottom: 20px; margin-left: 15px;">
Modpost {{ Form::checkbox('modpost', 'modpost', false,array('class'=>'novo-post-form-item', 'checked' => '')) }}
</div>
@endif
{{  Form::text('assunto', null, array('class' => 'novo-post-form-item form-control', 'maxlength' => '255', 'placeholder' => 'Assunto' )) }}
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

{{ Form::text('linkyoutube', null, array('class' => 'novo-post-form-item form-control', 'maxlength' => '255', 'placeholder' => 'Link(s) para vídeo(s) do youtube, separados por |' )) }}
{{  Form::textarea('conteudo', null, array('id' => 'novo-post-conteudo','class' => 'novo-post-form-item form-control', 'placeholder' => 'Mensagem', 'rows'=>'5', 'maxlength' => '65535')) }}
<p style="margin-left: 15px;">Mime types: image/jpeg, image/png, image/gif, video/webm, video/mp4, audio/mpeg</p>
<div class="row">
    <div class="col-sm-6">
        Sage {{ Form::checkbox('sage', 'sage', false,array('class'=>'novo-post-form-item')) }}
    </div>
    <div class="col-sm-6">
        {{ Form::submit('Postar', array('class' => 'mini-btn form-control') ) }}
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

{!! Form::close() !!}