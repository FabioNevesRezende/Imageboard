
@foreach($posts->getCollection()->all() as $post)

@if($post->lead_id === NULL)
<strong class="assunto">{{ $post->assunto }}</strong> | Nro {{ $post->id }} | <a href="/{{ $nomeBoard }}/{{ $post->id }}" target="_blank">Responder</a> <br>
@foreach ($post->arquivos as $arq)
<img class="img-responsive img-thumbnail" src="{{ \Storage::url($arq->filename) }}" width="200px" height="200px" >
@endforeach

<br><br>
{{ $post->conteudo }}

@foreach($subPosts as $subpost)
    @if($subpost->lead_id === $post->id)
    <strong class="assunto">{{ $subpost->assunto }}</strong> | Nro {{ $subpost->id }} | <br>
        @foreach ($subpost->arquivos as $sbarq)
        <img class="img-responsive img-thumbnail" src="{{ \Storage::url($sbarq->filename) }}" width="200px" height="200px" >
        @endforeach
    @endif
@endforeach

<hr>
@endif

@endforeach

{{ $posts->appends(Request::except('page'))->links() }}