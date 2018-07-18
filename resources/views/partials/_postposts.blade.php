<div class="fio">
@foreach($posts as $ind=>$post)
@if($ind !== 0) <div class="fio-subpost"> @endif

@if($post->pinado) 
<span class="glyphicon glyphicon-pushpin"></span> 
@endif 
@if($post->trancado) 
<span class="glyphicon glyphicon-lock"></span> 
@endif 
@if($post->modpost) 
    <p class="modpost">### Administrador ###</p>  
@else <span class="anonpost-title">Anônimo</span> 
@endif 
@if($post->anao->countrycode)
  <img src="/storage/flags/{{ $post->anao->countrycode }}.png" alt="{{ $post->anao->countrycode }}"> 
@endif 
<strong class="assunto">{{ $post->assunto }} </strong> 
 <i>{{ $post->created_at->toDayDateTimeString() }}</i>
 <u>Nro <a class="a-nro-post">{{ $post->id }}</a></u>
 
<a class="mini-btn btn-report" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalReport">Denunciar</a> 
@if($ind === 0) 
<a class="mini-btn" href="/{{ $nomeBoard }}">Voltar</a>  
@endif 
     <a href="/deletepost/{{ $nomeBoard }}/{{ $post->id }}" class="mini-btn">Deletar post</a> 
     
@if(Auth::check()) 
    @if($ind === 0) 
        
        @if($post->pinado)
            <a href="/pinarpost/{{ $post->board }}/{{ $post->id }}/0" class="mini-btn">Despinar post</a>
        @elseif(!$post->pinado)
        <a href="/pinarpost/{{ $post->board }}/{{ $post->id }}/1" class="mini-btn">Pinar post</a> 
        @endif
         
        @if($post->trancado)
            <a href="/trancarpost/{{ $post->board }}/{{ $post->id }}/0" class="mini-btn">Destrancar post</a>
        @elseif(!$post->trancado)
        <a href="/trancarpost/{{ $post->board }}/{{ $post->id }}/1" class="mini-btn">Trancar post</a> 
        @endif
    @endif
     <a class="mini-btn btn-ban" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalBan">Banir usuário</a> 
@endif <br>
<br>
@foreach ($post->arquivos as $arq)

@if($arq->mime === 'image/jpeg' || $arq->mime === 'image/png' || $arq->mime === 'image/gif' )
        <a href="/storage/{{ $arq->filename }}" target="_blank"><img class="img-responsive img-thumbnail" src="{{ \Storage::url($arq->filename) }}" width="200px" height="200px" ></a>
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

@if(Auth::check()) <a href="/deleteimg/{{ $nomeBoard }}/{{ $arq->filename }}" class="mini-btn">Deletar Arquivo</a> @endif
@endforeach

@foreach ($post->ytanexos as $anx)
    <iframe width="220" height="220"
        src="https://www.youtube.com/embed/{{ $anx->ytcode }}">
    </iframe> 
@endforeach

<br>
{!! $post->conteudo !!}
@if($post->ban) <p class="ban-msg">({{ $post->ban->motivo }})</p>  @endif
@if($ind !== 0) </div> @endif
@endforeach
</div>