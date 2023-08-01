<?php

use Ibbr\Helpers\Funcoes;

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

Route::group(['middleware'=>['verificaCookieArquivo']], function(){
    Route::get('/storage/{filename}', 'PagesController@getArquivo');
});

Route::group(['middleware'=>['xFrameOptionsHeader']], function(){
    Route::get('/', 'PagesController@getIndex');
    
    Route::get('/{siglaBoard}', ['uses' => 'PagesController@getBoard'])
        ->where('siglaBoard', '[a-zA-Zç]{1,10}');
        
    Route::get('/{siglaBoard}/{thread}', ['as' => 'post.single', 'uses' => 'PagesController@getThread'])
        ->where('siglaBoard', '[a-zA-Zç]{1,10}')->where('thread', '[0-9]+');
    
    Route::post('/posts', ['as' => 'posts.store', 'uses' => 'PostController@store']);
    Route::post('/report', ['as' => 'posts.report', 'uses' => 'PostController@report']);
    Route::get('/catalogo', 'PagesController@getCatalogo');
    
    Route::get('/deletepost/{siglaBoard}/{post_id}', ['uses' => 'PostController@destroy'])
        ->where('siglaBoard', '[a-zA-Zç]{1,10}')
        ->where('post_id', '[0-9]+');
        
    Route::get('/logout', 'PagesController@logout');
    Route::get('/login', 'PagesController@getLogin');
});

Route::group(['middleware'=>['auth']], function(){
    
    Route::get('/phpinfo', 'PagesController@getPhpInfo');
    
    Route::get('/pinarpost/{siglaBoard}/{post_id}/{val}', ['uses' => 'PostController@pinarPost'])
        ->where('post_id', '[0-9]+')
        ->where('siglaBoard', '[a-zA-Zç]{1,10}')
        ->where('val', '(1|0)');
        
    Route::get('/trancarpost/{siglaBoard}/{post_id}/{val}', ['uses' => 'PostController@trancarPost'])
        ->where('post_id', '[0-9]+')
        ->where('siglaBoard', '[a-zA-Zç]{1,10}')
        ->where('val', '(1|0)');
    
    Route::get('/deleteimg/{siglaBoard}/{filename}', ['uses' => 'PostController@destroyArqDb'])
        ->where('filename', '[0-9\-]+\.[a-zA-Z]+')
        ->where('siglaBoard', '[a-zA-Zç]{1,10}');
        
    Route::get('/deleteregra/{id}', ['uses' => 'RegraController@destroy'])
        ->where('id', '[0-9]+');
        
    Route::get('/deleteboard/{id}', ['uses' => 'BoardController@destroy'])
        ->where('id', '[a-zA-Zç]{1,10}');
        
    Route::get('/deletenoticia/{id}', ['uses' => 'NoticiaController@destroy'])
        ->where('id', '[0-9]+');
        
    Route::get('/editnoticia/{id}', ['uses' => 'NoticiaController@edit'])
        ->where('id', '[0-9]+');
        
    Route::get('/admin', 'PagesController@getAdmPage');
    Route::post('/userban', ['as' => 'bans.userban', 'uses' => 'Controller@banirUsuario']);
    
    Route::post('/nova_noticia', ['as' => 'noticias.nova_noticia', 'uses' => 'NoticiaController@store']);
    Route::post('/update_noticia', ['as' => 'noticias.update_noticia', 'uses' => 'NoticiaController@update']);
    
    Route::post('/update_password', ['as' => 'users.update_password', 'uses' => 'UserController@updatePassword']);
    
    Route::post('/regra', ['as' => 'regras.regra', 'uses' => 'RegraController@store']);
    Route::get('/migrate', 'HomeController@migrate');
    Route::get('/seedar', 'HomeController@seedar');
    Route::get('/limparcache', 'HomeController@limparCache');
    //Route::get('/migrate/refresh', 'HomeController@migrateRefresh');
    Route::get('/togglecaptcha/{val}', 'ConfiguracaoController@toggleCaptcha')
        ->where('val', '(1|0)');
    
    Route::get('/togglepostsblock/{val}', 'ConfiguracaoController@togglePostsBlock')
        ->where('val', '(1|0)');
    
    Route::post('/boards', ['as' => 'boards.store', 'uses' => 'BoardController@store']);
    
    Route::get('/deletereport/{id}', ['uses' => 'PostController@destroyReport'])
        ->where('id', '[0-9]+');
    
    Route::post('/movepost', ['as' => 'posts.mover', 'uses' => 'PostController@movePost']);
});

Auth::routes();