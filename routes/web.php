<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>['web']], function(){
    Route::get('/', 'PagesController@getIndex');
    Route::get('/{nomeBoard}', ['uses' => 'PagesController@getBoard'])->where('nomeBoard', Config::get('funcoes.geraRegexBoards')());
    Route::get('/{nomeBoard}/{thread}', ['as' => 'post.single', 'uses' => 'PagesController@getThread'])->where('nomeBoard', Config::get('funcoes.geraRegexBoards')())->where('thread', '[0-9]+');
    
    Route::resource('posts', 'PostController');
    Route::post('/report', ['as' => 'posts.report', 'uses' => 'PostController@report']);
    
});
Auth::routes();

Route::group(['middleware'=>['auth']], function(){
    
    Route::get('/deletepost/{post_id}', ['uses' => 'PostController@destroy'])->where('post_id', '[0-9]+');
    Route::get('/pinarpost/{post_id}', ['uses' => 'PostController@pinarPost'])->where('post_id', '[0-9]+');
    Route::get('/deleteimg/{nomeBoard}/{filename}', ['uses' => 'PostController@destroyArqDb'])->where('filename', '[0-9\-]+\.[a-zA-Z]+')->where('nomeBoard', Config::get('funcoes.geraRegexBoards')());
    Route::get('/admin', 'PagesController@getAdmPage');
    Route::post('/userban', ['as' => 'bans.userban', 'uses' => 'Controller@banirUsuario']);
    
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
