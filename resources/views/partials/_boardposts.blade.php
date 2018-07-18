@foreach($posts as $post)
@if($post->lead_id === NULL)
<div id="{{ $post->id }}" class="fio">
    @if($post->pinado) <span class="glyphicon glyphicon-pushpin"></span> @endif 
    @if($post->trancado) <span class="glyphicon glyphicon-lock"></span> @endif 
    @if($post->modpost) <p class="modpost">### Administrador ###</p>  
    @else <span class="anonpost-title">Anônimo</span> @endif 
    @if($post->anao->countrycode)   <img src="/storage/flags/{{ $post->anao->countrycode }}.png" alt="{{ $post->anao->countrycode }}"> @endif 
     <strong class="assunto">{{ $post->assunto }}</strong>  <i>{{ $post->created_at->toDayDateTimeString() }} </i>
     <u>Nro <a class="a-nro-post" href="/{{ $post->board }}/{{ $post->id }}">{{ $post->id }}</a></u>
     <a class="mini-btn btn-report" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalReport">Denunciar</a> 
     <a class="mini-btn" href="/{{ $nomeBoard }}/{{ $post->id }}" target="_blank">Responder</a> 
     
     <a href="/deletepost/{{ $nomeBoard }}/{{ $post->id }}" class="mini-btn">Deletar post</a> 
     
    @if(Auth::check())
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
    
    <a class="mini-btn btn-ban" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalBan">Banir usuário</a> 
    @endif <br>
    
    <div class="fio-imgs-div">
    @foreach ($post->arquivos as $arq)
    <div class="fio-img-div">
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
    @if(Auth::check()) 
    <a href="/deleteimg/{{ $nomeBoard }}/{{ $arq->filename }}" class="mini-btn">Deletar Arquivo</a><br><br>@endif
    </div>
    @endforeach
    
    @foreach ($post->ytanexos as $anx)
    <iframe width="220" height="220"
        src="https://www.youtube.com/embed/{{ $anx->ytcode }}">
    </iframe> 
    @endforeach
    </div>

{!! substr($post->conteudo, 0, 500) !!}
@if($post->ban) <p class="ban-msg">({{ $post->ban->motivo }})</p>  @endif
<br><br>

    @foreach($subPosts as $subpost)
        @if($subpost->lead_id === $post->id)
            <div class="fio-subpost">
                @if($subpost->modpost) <p class="modpost">### Administrador ###</p>  
                @else <span class="anonpost-title">Anônimo</span> 
                @endif  
                @if($post->anao->countrycode)  
                    <img src="/storage/flags/{{ $subpost->anao->countrycode }}.png" alt="{{ $subpost->anao->countrycode }}"> 
                @endif
                <strong class="assunto">{{ $subpost->assunto }}</strong> 
                <i>{{ $subpost->created_at->toDayDateTimeString() }}</i>
                <u>Nro <a class="a-nro-post">{{ $subpost->id }}</a></u> 
                  <a class="mini-btn btn-report" data-id-post="{{ $subpost->id }}" data-toggle="modal" data-target="#modalReport">Denunciar</a>
                <br>
                @foreach ($subpost->arquivos as $sbarq)
                <a href="/storage/{{ $sbarq->filename }}" target="_blank"><img class="img-responsive img-thumbnail" src="{{ \Storage::url($sbarq->filename) }}" width="150px" height="150px" ></a>
                @endforeach
                
                @foreach ($subpost->ytanexos as $anx)
                <iframe width="220" height="220"
                    src="https://www.youtube.com/embed/{{ $anx->ytcode }}">
                </iframe> 
                @endforeach
                
                {!! substr($subpost->conteudo, 0, 500) !!}
                @if($subpost->ban) <p class="ban-msg">({{ $subpost->ban->motivo }})</p>  @endif
                <br>
            </div>
        @endif
    @endforeach

</div>
<hr class="fio-divisor">
@endif
@endforeach

{{ $paginador }}