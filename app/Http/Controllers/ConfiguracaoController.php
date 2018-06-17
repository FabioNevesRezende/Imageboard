<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Configuracao;
use Illuminate\Http\Request;
use Cache;

class ConfiguracaoController extends Controller
{
    public static function getAll()
    {
        if(Cache::has('configs'))
            return Cache::get('configs');

        $configs = Configuracao::orderBy('id')->first();
                
        Cache::forever('configs', $configs);
        return $configs;
    }

}
