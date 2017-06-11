<div class="container-fluid ibnav">
    <div class="ibnavl">
        [
        @foreach(Config::get('constantes.boards') as $board => $boardnome)
        <a href="{{ URL::action('PagesController@getBoard', $board) }}">{{ $board }}</a> /
        @endforeach
        
        ]
    </div>

    <div class="ibnavr">
        [
        <a href="/">Home</a>
        ]
    </div>
    <hr>
</div>