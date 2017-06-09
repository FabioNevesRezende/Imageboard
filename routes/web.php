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

Route::group(['middleware'=>['auth']], function(){
    
    Route::get('/deletepost/{post_id}', ['uses' => 'PostController@destroy'])->where('post_id', '[0-9]+');
    Route::get('/deleteimg/{nomeBoard}/{filename}', ['uses' => 'PostController@destroyArqDb'])->where('filename', '[0-9\-]+\.[a-zA-Z]+')->where('nomeBoard', '(int|b|news)');
    Route::get('/userban/{nomeBoard}/{post_id}', ['uses' => 'Controller@banirUsuario'])->where('nomeBoard', '(int|b|news)')->where('post_id', '[0-9]+');
    
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
