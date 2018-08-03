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

Route::group(['middleware'=>['verificaCookieArquivo']], function(){
    Route::get('/storage/{filename}', 'PagesController@getArquivo');
});

Route::group(['middleware'=>['web']], function(){
    Route::get('/', 'PagesController@getIndex');
    
    Route::get('/{nomeBoard}', ['uses' => 'PagesController@getBoard'])
        ->where('nomeBoard', Config::get('funcoes.geraRegexBoards')());
        
    Route::get('/{nomeBoard}/{thread}', ['as' => 'post.single', 'uses' => 'PagesController@getThread'])
        ->where('nomeBoard', Config::get('funcoes.geraRegexBoards')())->where('thread', '[0-9]+');
    
    Route::post('/posts', ['as' => 'posts.store', 'uses' => 'PostController@store']);
    Route::post('/report', ['as' => 'posts.report', 'uses' => 'PostController@report']);
    Route::get('/catalogo', 'PagesController@getCatalogo');
    
    Route::get('/deletepost/{nomeBoard}/{post_id}', ['uses' => 'PostController@destroy'])
        ->where('nomeBoard', Config::get('funcoes.geraRegexBoards')())
        ->where('post_id', '[0-9]+');
        
    Route::get('/logout', 'PagesController@logout');
});

Route::group(['middleware'=>['auth']], function(){
    
    Route::get('/pinarpost/{nomeBoard}/{post_id}/{val}', ['uses' => 'PostController@pinarPost'])
        ->where('post_id', '[0-9]+')
        ->where('nomeBoard', Config::get('funcoes.geraRegexBoards')())
        ->where('val', '(1|0)');
        
    Route::get('/trancarpost/{nomeBoard}/{post_id}/{val}', ['uses' => 'PostController@trancarPost'])
        ->where('post_id', '[0-9]+')
        ->where('nomeBoard', Config::get('funcoes.geraRegexBoards')())
        ->where('val', '(1|0)');
    
    Route::get('/deleteimg/{nomeBoard}/{filename}', ['uses' => 'PostController@destroyArqDb'])
        ->where('filename', '[0-9\-]+\.[a-zA-Z]+')
        ->where('nomeBoard', Config::get('funcoes.geraRegexBoards')());
        
    Route::get('/deleteregra/{id}', ['uses' => 'RegraController@destroy'])
        ->where('id', '[0-9]+');
        
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
    Route::get('/migrate/refresh', 'HomeController@migrateRefresh');
    Route::get('/togglecaptcha/{val}', 'ConfiguracaoController@toggleCaptcha')
        ->where('val', '(1|0)');
});

Auth::routes();