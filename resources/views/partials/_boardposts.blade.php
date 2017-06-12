
@foreach($posts as $post)
@if($post->lead_id === NULL)
<div id="{{ $post->id }}" class="fio">
    @if($post->pinado === 's') <span class="glyphicon glyphicon-pushpin"></span> @endif @if($post->modpost === 's') <p class="modpost">### Administrador ###</p>  @else Anônimo @endif | <strong class="assunto">{{ $post->assunto }}</strong> | {{ $post->created_at->toDayDateTimeString() }} | Nro <a class="a-nro-post">{{ $post->id }}</a> |  <button type="button" class="btn btn-report" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalReport">Denunciar</button> | <a href="/{{ $nomeBoard }}/{{ $post->id }}" target="_blank">Responder</a> 
    @if(Auth::check()) 
    | <a href="/deletepost/{{ $post->id }}"><button class="btn">Deletar post</button> </a> 
    | <a href="/pinarpost/{{ $post->id }}"><button class="btn">Pinar post</button> </a> 
    | <button type="button" class="btn btn-ban" data-id-post="{{ $post->id }}" data-toggle="modal" data-target="#modalBan">Banir usuário</button> 
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
    @if(Auth::check()) <a href="/deleteimg/{{ $nomeBoard }}/{{ $arq->filename }}"><button class="btn">Deletar Arquivo</button> </a> @endif
    </div>
    @endforeach
    </div>

{!! substr($post->conteudo, 0, 500) !!}
<br><br>

    @foreach($subPosts as $subpost)
        @if($subpost->lead_id === $post->id)
            <div class="fio-subpost">
                @if($subpost->modpost === 's') <p class="modpost">### Administrador ###</p>  @else Anônimo @endif | <strong class="assunto">{{ $subpost->assunto }}</strong> | | {{ $subpost->created_at->toDayDateTimeString() }}  | Nro <a class="a-nro-post">{{ $subpost->id }}</a> |  <button type="button" class="btn btn-report" data-id-post="{{ $subpost->id }}" data-toggle="modal" data-target="#modalReport">Denunciar</button> | <br>
                @foreach ($subpost->arquivos as $sbarq)
                <a href="/storage/{{ $sbarq->filename }}" target="_blank"><img class="img-responsive img-thumbnail" src="{{ \Storage::url($sbarq->filename) }}" width="150px" height="150px" ></a>
                @endforeach
                {!! substr($subpost->conteudo, 0, 500) !!}
                <br>
            </div>
        @endif
    @endforeach

</div>
<hr class="fio-divisor">
@endif
@endforeach

{{ $paginador }}