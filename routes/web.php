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
    Route::get('/{nomeBoard}', ['uses' => 'PagesController@getBoard'])->where('nomeBoard', Config::get('funcoes.geraRegexBoards')());
    Route::get('/{nomeBoard}/{thread}', ['as' => 'post.single', 'uses' => 'PagesController@getThread'])->where('nomeBoard', Config::get('funcoes.geraRegexBoards')())->where('thread', '[0-9]+');
    
    Route::resource('posts', 'PostController');
    Route::post('/report', ['as' => 'posts.report', 'uses' => 'PostController@report']);
    Route::get('/catalogo', 'PagesController@getCatalogo');
    Route::get('/deletepost/{nomeBoard}/{post_id}', ['uses' => 'PostController@destroy'])->where('nomeBoard', Config::get('funcoes.geraRegexBoards')())->where('post_id', '[0-9]+');
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
        
    Route::get('/admin', 'PagesController@getAdmPage');
    Route::post('/userban', ['as' => 'bans.userban', 'uses' => 'Controller@banirUsuario']);
    Route::get('/migrate', 'HomeController@migrate');
    Route::get('/seedar', 'HomeController@seedar');
    Route::get('/limparcache', 'HomeController@limparCache');
    Route::get('/migrate/refresh', 'HomeController@migrateRefresh');
    Route::get('/togglecaptcha/{val}', 'ConfiguracaoController@toggleCaptcha')
        ->where('val', '(1|0)');
});

Auth::routes();