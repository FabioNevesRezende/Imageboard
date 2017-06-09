
@foreach($posts->getCollection()->all() as $post)
@if($post->lead_id === NULL)
<div class="fio">
    <strong class="assunto">{{ $post->assunto }}</strong> | Nro {{ $post->id }} | <a href="/{{ $nomeBoard }}/{{ $post->id }}" target="_blank">Responder</a> @if(Auth::check()) | <a href="/deletepost/{{ $post->id }}"><button class="btn">Deletar post</button> </a> | <a href="/userban/{{ $nomeBoard }}/{{ $post->id }}"><button class="btn">Banir usuário</button> </a>  @endif <br>
    
    <div class="fio-imgs-div">
    @foreach ($post->arquivos as $arq)
    <div class="fio-img-div">
    <a href="/storage/{{ $arq->filename }}" target="_blank"><img class="img-responsive img-thumbnail" src="{{ \Storage::url($arq->filename) }}" width="200px" height="200px" ></a>
    @if(Auth::check()) <a href="/deleteimg/{{ $nomeBoard }}/{{ $arq->filename }}"><button class="btn">Deletar Imagem</button> </a> @endif
    </div>
    @endforeach
    </div>

{!! substr($post->conteudo, 0, 500) !!}
<br><br>

    @foreach($subPosts as $subpost)
        @if($subpost->lead_id === $post->id)
            <div class="fio-subpost">
            <strong class="assunto">{{ $subpost->assunto }}</strong> | Nro {{ $subpost->id }} | <br>
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

{{ $posts->appends(Request::except('page'))->links() }}