<div class="container-fluid ibnav">
@if(isset($boards))
    <div class="ibnavl">
        [
        @foreach($boards as $board)
            <a href="{{ URL::action('PagesController@getBoard', $board->sigla) }}">{{ $board->sigla }}</a> /
        @endforeach
        ]
    </div>

    <div class="ibnavr">
        [
        <a href="/">Home</a>
        / <a href="/catalogo">Catalogo</a>
        @if(Auth::check()) 
        / <a href="/admin">Admin</a>
        @endif
        ]
    </div>
    <hr>
    
@endif
</div>