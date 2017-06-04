<div class="fio">
@foreach($posts as $ind=>$post)
@if($ind !== 0) <div class="fio-subpost"> @endif

<strong class="assunto">{{ $post->assunto }}</strong> | Nro {{ $post->id }} | @if($ind === 0) <a href="/{{ $nomeBoard }}">Voltar</a>  @endif <br>
@foreach ($post->arquivos as $arq)
<a href="/storage/{{ $arq->filename }}" target="_blank"><img class="img-responsive img-thumbnail" src="{{ \Storage::url($arq->filename) }}" width="200px" height="200px" ></a>
@endforeach

<br><br>
{!! $post->conteudo !!}
@if($ind !== 0) </div> @endif
@endforeach
</div>