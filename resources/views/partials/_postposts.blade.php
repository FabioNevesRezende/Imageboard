
@foreach($posts as $post)


<strong class="assunto">{{ $post->assunto }}</strong> | Nro {{ $post->id }} | <a href="/{{ $nomeBoard }}">Voltar</a> <br>
@foreach ($post->arquivos as $arq)
<img class="img-responsive img-thumbnail" src="{{ \Storage::url($arq->filename) }}" width="200px" height="200px" >
@endforeach

<br><br>
{{ $post->conteudo }}

@endforeach
