@foreach($posts as $ind=>$post)
@if($ind === 0) <div id="{{ $post->id }}" class="fio">
@else <div id="{{ $post->id }}" class="fio-subpost"> @endif

@if($post->pinado) <span class="glyphicon glyphicon-pushpin"></span> @endif 
@if($post->trancado) <span class="glyphicon glyphicon-lock"></span> @endif 
@if($post->modpost)   <p class="modpost">### Administrador ###</p> @else <span class="anonpost-title">Anônimo</span> @endif  
@if($post->sage) <span class="sage-text">[sage]</span> @endif
@if($post->anao->countrycode)
  <img src="/storage/res/flags/{{ $post->anao->countrycode }}.png" alt="{{ $post->anao->countrycode }}"> 
@endif 
<strong class="assunto">{{ $post->assunto }} </strong> 
 <i>{{ $post->data_post }}</i>
 <u>Nro <a class="a-nro-post">{{ $post->id }}</a></u>
 
<a class="mini-btn btn-report" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalReport"><span data-toggle="tooltip" data-placement="top" title="Denunciar" class="glyphicon glyphicon-exclamation-sign"></span></a> 
@if($ind === 0) 
<a data-toggle="tooltip" data-placement="top" title="Voltar" class="mini-btn" href="/{{ $siglaBoard }}"><span class="glyphicon glyphicon-circle-arrow-left"></span></a>  
@endif 
     <a data-toggle="tooltip" data-placement="top" title="Deletar post" href="/deletepost/{{ $siglaBoard }}/{{ $post->id }}" class="mini-btn"><span class="glyphicon glyphicon-trash"></span></a> 
     
@if(Auth::check()) 
    @if($ind === 0) 
        
        @if($post->pinado)
            <a data-toggle="tooltip" data-placement="top" title="Despinar fio" href="/pinarpost/{{ $post->board }}/{{ $post->id }}/0" class="mini-btn"><span class="glyphicon glyphicon-pushpin"></span></a>
        @elseif(!$post->pinado)
        <a data-toggle="tooltip" data-placement="top" title="Pinar fio" href="/pinarpost/{{ $post->board }}/{{ $post->id }}/1" class="mini-btn"><span class="glyphicon glyphicon-pushpin"></span></a> 
        @endif
         
        @if($post->trancado)
            <a data-toggle="tooltip" data-placement="top" title="Destrancar fio" href="/trancarpost/{{ $post->board }}/{{ $post->id }}/0" class="mini-btn"><span class="glyphicon glyphicon-lock"></span></a>
        @elseif(!$post->trancado)
        <a data-toggle="tooltip" data-placement="top" title="Trancar fio" href="/trancarpost/{{ $post->board }}/{{ $post->id }}/1" class="mini-btn"><span class="glyphicon glyphicon-lock"></span></a> 
        @endif
    @endif
    <a data-toggle="tooltip" data-placement="top" title="Banir usuário" class="mini-btn btn-ban" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalBan"><span class="glyphicon glyphicon-ban-circle"></span></a> 
    <a class="mini-btn btn-mover-post" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalMoverPost"><span data-toggle="tooltip" data-placement="top" title="Mover postagem" class="glyphicon glyphicon-circle-arrow-right"></span></a> 

@endif <br>
<br>
@foreach ($post->arquivos as $arq)

<div class="fio-img-div">
    <span data-toggle="tooltip" 
        data-placement="top" 
        title="@if($arq->filesize)Tamanho: {{ Config::get('funcoes.trataFilesize')($arq->filesize) }}  @endif{{ $arq->original_filename }}">
        {{ substr($arq->original_filename,0,10) }}
    </span><br>
@if($arq->mime === 'image/jpeg' || $arq->mime === 'image/png' || $arq->mime === 'image/gif' )
        <a href="/storage/{{ $arq->filename }}" target="_blank">
        <img class="img-responsive img-thumbnail" 
        @if($arq->spoiler) src="{{ \Storage::url('res/spoiler.png') }}"
        @else src="{{ \Storage::url($arq->filename) }}" 
        @endif
        width="200px" height="200px" ></a>
    @elseif($arq->mime === 'video/mp4')
     <video width="320" controls>
        <source src="/storage/{{ $arq->filename }}" type="video/mp4">
      </video>
    @elseif($arq->mime === 'video/webm')
     <video width="320" controls>
        <source src="/storage/{{ $arq->filename }}" type="video/webm">
      </video>
    @elseif($arq->mime === 'audio/mpeg')
     <audio controls>
        <source src="/storage/{{ $arq->filename }}" type="audio/mpeg">
     </audio>
    @endif
@if(Auth::check()) <a data-toggle="tooltip" data-placement="top" title="Deletar arquivo" href="/deleteimg/{{ $siglaBoard }}/{{ $arq->filename }}" class="mini-btn"><span class="glyphicon glyphicon-trash"></span></a> @endif
</div>
@endforeach

@foreach ($post->ytanexos as $anx)
    <iframe width="220" height="220"
        src="https://www.youtube.com/embed/{{ $anx->ytcode }}">
    </iframe> 
@endforeach

{!! $post->conteudo !!}
@if($post->ban) <p class="ban-msg">({{ $post->ban->motivo }})</p>  @endif
@if($ind !== 0) </div> @endif
@endforeach
</div>