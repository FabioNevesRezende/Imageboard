<div class="container-fluid ibnav">
    <div class="ibnavl">
        [
        <a href="{{ URL::action('PagesController@getBoard', 'b') }}">b</a> /
        <a href="{{ URL::action('PagesController@getBoard', 'int') }}">int</a> /
        <a href="{{ URL::action('PagesController@getBoard', 'news') }}">news</a> /
        ]
    </div>

    <div class="ibnavr">
        [
        <a href="/">Home</a>
        ]
    </div>
    <hr>
</div>