<div class="container-fluid">
@foreach($posts as $post)
@if($post->lead_id === NULL)
<div id="{{ $post->id }}" class="fio">
    @if($post->pinado) <span class="glyphicon glyphicon-pushpin"></span> @endif 
    @if($post->trancado) <span class="glyphicon glyphicon-lock"></span> @endif 
    @if($post->modpost) <p class="modpost">### Administrador ###</p>  
    @else <span class="anonpost-title">Anônimo</span> @endif 
    @if($post->sage) <span class="sage-text">[sage]</span> @endif
    @if($post->anao->countrycode)   <img src="/storage/res/flags/{{ $post->anao->countrycode }}.png" alt="{{ $post->anao->countrycode }}"> @endif 
     <strong class="assunto">{{ $post->assunto }}</strong>  
     <i>{{ $post->data_post }} </i>

     <u>Nro <a class="a-nro-post">{{ $post->id }}</a></u>
     <a class="mini-btn btn-report" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalReport"><span data-toggle="tooltip" data-placement="top" title="Denunciar" class="glyphicon glyphicon-exclamation-sign"></span></a> 
     <a data-toggle="tooltip" data-placement="top" title="Responder" class="mini-btn" href="/{{ $siglaBoard }}/{{ $post->id }}" target="_blank"><span class="glyphicon glyphicon-pencil"></span></a> 
     
     <a data-toggle="tooltip" data-placement="top" title="Deletar post" href="/deletepost/{{ $siglaBoard }}/{{ $post->id }}" class="mini-btn"><span class="glyphicon glyphicon-trash"></span></a> 
     
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

        <a class="mini-btn btn-ban" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalBan"><span data-toggle="tooltip" data-placement="top" title="Banir usuário" class="glyphicon glyphicon-ban-circle"></span></a> 
        <a class="mini-btn btn-mover-post" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalMoverPost"><span data-toggle="tooltip" data-placement="top" title="Mover postagem" class="glyphicon glyphicon-circle-arrow-right"></span></a> 
    
    @endif <br>
    
    <div class="fio-imgs-div">
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
    
    @if(Auth::check()) 
    <a data-toggle="tooltip" data-placement="top" title="Deletar arquivo" href="/deleteimg/{{ $siglaBoard }}/{{ $arq->filename }}" class="mini-btn"><span class="glyphicon glyphicon-trash"></span></a><br><br>@endif
    </div>
    @endforeach
    
    @foreach ($post->ytanexos as $anx)
    <iframe width="220" height="220"
        src="https://www.youtube.com/embed/{{ $anx->ytcode }}">
    </iframe> 
    @endforeach
    </div>

    <span class="post-conteudo">{!! substr($post->conteudo, 0, 500) !!}</span>
@if($post->ban) <p class="ban-msg">({{ $post->ban->motivo }})</p>  @endif
<br><br>

    @php
        $sbtemp = $subPosts->where('lead_id', '=', $post->id);
        $k = 0;
    @endphp
    @if($sbtemp)
        @foreach($sbtemp as $sb)
            @if($k > sizeof($sbtemp) - ($configuracaos->num_subposts_post + 1))
                <div id="{{ $sb->id }}" class="fio-subpost">
                    @if($sb->modpost) <p class="modpost">### Administrador ###</p>  
                    @else <span class="anonpost-title">Anônimo</span> 
                    @endif  
                    @if($sb->sage) <span class="sage-text">[sage]</span> @endif
                    @if($sb->anao->countrycode)  
                        <img src="/storage/res/flags/{{ $sb->anao->countrycode }}.png" alt="{{ $sb->anao->countrycode }}"> 
                    @endif
                    <strong class="assunto">{{ $sb->assunto }}</strong> 
                    <i>{{ $sb->data_post }}</i>
                    <u>Nro <a class="a-nro-post">{{ $sb->id }}</a></u> 
                    <a class="mini-btn btn-report" data-id-post="{{ $sb->id }}" data-toggle="modal" data-target="#modalReport">
                        <span data-toggle="tooltip" data-placement="top" title="Denunciar" class="glyphicon glyphicon-exclamation-sign"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Deletar post" href="/deletepost/{{ $siglaBoard }}/{{ $sb->id }}" class="mini-btn"><span class="glyphicon glyphicon-trash"></span></a>
                    @if(Auth::check())
                    <a class="mini-btn btn-ban" data-id-post="{{ $sb->id }}" data-toggle="modal" data-target="#modalBan"><span data-toggle="tooltip" data-placement="top" title="Banir usuário" class="glyphicon glyphicon-ban-circle"></span></a> 
                    <a class="mini-btn btn-mover-post" data-id-post="{{ $sb->id }}" data-toggle="modal" data-target="#modalMoverPost"><span data-toggle="tooltip" data-placement="top" title="Mover postagem" class="glyphicon glyphicon-circle-arrow-right"></span></a> 
                    @endif
                    <br>
                    @foreach ($sb->arquivos as $sbarq)
                        <div class="fio-img-div">
                            <span data-toggle="tooltip" 
                                data-placement="top" 
                                title="@if($sbarq->filesize)Tamanho: {{ Config::get('funcoes.trataFilesize')($sbarq->filesize) }}  @endif{{ $sbarq->original_filename }}">
                                {{ substr($sbarq->original_filename,0,10) }}
                            </span><br>
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

                    @foreach ($sb->ytanexos as $anx)
                    <iframe width="220" height="220"
                        src="https://www.youtube.com/embed/{{ $anx->ytcode }}">
                    </iframe> 
                    @endforeach

                    <span class="post-conteudo">{!! substr($sb->conteudo, 0, 500) !!}</span>
                    @if($sb->ban) <p class="ban-msg">({{ $sb->ban->motivo }})</p>  @endif
                    <br>
                </div>
            @endif
            @php
                $k += 1;
            @endphp
        @endforeach
    @endif
    
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