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
    Route::get('/{nomeBoard}', ['uses' => 'PagesController@getBoard'])->where('nomeBoard', '(int|b|news)');
    //Route::get('/{nomeBoard}/{nroPagina?}', ['uses' => 'PagesController@getBoard'])->where('nomeBoard', '(int|b|news)')->where('nroPagina', '[0-9]+');

    
    Route::get('/{nomeBoard}/{thread}', ['as' => 'post.single', 'uses' => 'PagesController@getThread'])->where('nomeBoard', '(int|b|news)')->where('thread', '[0-9]+');
    
    Route::resource('posts', 'PostController');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
