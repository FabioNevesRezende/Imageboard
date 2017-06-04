
@foreach($posts->getCollection()->all() as $post)
@if($post->lead_id === NULL)
<div class="fio">
<strong class="assunto">{{ $post->assunto }}</strong> | Nro {{ $post->id }} | <a href="/{{ $nomeBoard }}/{{ $post->id }}" target="_blank">Responder</a> <br>
    @foreach ($post->arquivos as $arq)
    <a href="/storage/{{ $arq->filename }}" target="_blank"><img class="img-responsive img-thumbnail" src="{{ \Storage::url($arq->filename) }}" width="200px" height="200px" ></a>
    @endforeach

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