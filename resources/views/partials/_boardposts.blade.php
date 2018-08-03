<div class="container-fluid">
@foreach($posts as $post)
@if($post->lead_id === NULL)
<div id="{{ $post->id }}" class="fio">
    @if($post->pinado) <span class="glyphicon glyphicon-pushpin"></span> @endif 
    @if($post->trancado) <span class="glyphicon glyphicon-lock"></span> @endif 
    @if($post->modpost) <p class="modpost">### Administrador ###</p>  
    @else <span class="anonpost-title">Anônimo</span> @endif 
    @if($post->anao->countrycode)   <img src="/storage/res/flags/{{ $post->anao->countrycode }}.png" alt="{{ $post->anao->countrycode }}"> @endif 
     <strong class="assunto">{{ $post->assunto }}</strong>  
     <i>{{ $post->data_post }} </i>

     <u>Nro <a class="a-nro-post" href="/{{ $post->board }}/{{ $post->id }}">{{ $post->id }}</a></u>
     <a class="mini-btn btn-report" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalReport"><span data-toggle="tooltip" data-placement="top" title="Denunciar" class="glyphicon glyphicon-exclamation-sign"></span></a> 
     <a data-toggle="tooltip" data-placement="top" title="Responder" class="mini-btn" href="/{{ $nomeBoard }}/{{ $post->id }}" target="_blank"><span class="glyphicon glyphicon-pencil"></span></a> 
     
     <a data-toggle="tooltip" data-placement="top" title="Deletar post" href="/deletepost/{{ $nomeBoard }}/{{ $post->id }}" class="mini-btn"><span class="glyphicon glyphicon-trash"></span></a> 
     
    @if(Auth::check())
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
    
    <a data-toggle="tooltip" data-placement="top" title="Banir usuário" class="mini-btn btn-ban" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalBan"><span class="glyphicon glyphicon-ban-circle"></span></a> 
    @endif <br>
    
    <div class="fio-imgs-div">
    @foreach ($post->arquivos as $arq)
    <div class="fio-img-div">
    {{ $arq->original_filename }}<br>
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
    
    @if(Auth::check()) 
    <a data-toggle="tooltip" data-placement="top" title="Deletar arquivo" href="/deleteimg/{{ $nomeBoard }}/{{ $arq->filename }}" class="mini-btn"><span class="glyphicon glyphicon-trash"></span></a><br><br>@endif
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
                    <img src="/storage/res/flags/{{ $subpost->anao->countrycode }}.png" alt="{{ $subpost->anao->countrycode }}"> 
                @endif
                <strong class="assunto">{{ $subpost->assunto }}</strong> 
                <i>{{ $subpost->data_post }}</i>
                <u>Nro <a class="a-nro-post">{{ $subpost->id }}</a></u> 
                  <a class="mini-btn btn-report" data-id-post="{{ $subpost->id }}" data-toggle="modal" data-target="#modalReport"><span data-toggle="tooltip" data-placement="top" title="Denunciar" class="glyphicon glyphicon-exclamation-sign"></span></a>
                <br>
                @foreach ($subpost->arquivos as $sbarq)
                    <div class="fio-img-div">
                    {{ $arq->original_filename }}<br>
                    @if($sbarq->mime === 'image/jpeg' || $sbarq->mime === 'image/png' || $sbarq->mime === 'image/gif' )
                        <a href="/storage/{{ $sbarq->filename }}" target="_blank">
                        <img class="img-responsive img-thumbnail" 
                        @if($sbarq->spoiler) src="{{ \Storage::url('res/spoiler.png') }}"
                        @else src="{{ \Storage::url($sbarq->filename) }}" 
                        @endif
                        width="200px" height="200px" ></a>
                    @elseif($sbarq->mime === 'video/mp4')
                     <video width="320" controls>
                        <source src="/storage/{{ $sbarq->filename }}" type="video/mp4">
                      </video>
                    @elseif($sbarq->mime === 'video/webm')
                     <video width="320" controls>
                        <source src="/storage/{{ $sbarq->filename }}" type="video/webm">
                      </video>
                    @elseif($sbarq->mime === 'audio/mpeg')
                     <audio controls>
                        <source src="/storage/{{ $sbarq->filename }}" type="audio/mpeg">
                     </audio>
                    @endif
                    </div>
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
<hr>
@endif
@endforeach

@if(isset($paginador))
    <br>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 text-center">
            {{ $paginador }}    
        </div>
        <div class="col-sm-4"></div>
    </div>
@endif
</div>