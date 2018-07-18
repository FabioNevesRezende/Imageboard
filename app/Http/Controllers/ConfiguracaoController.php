<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Configuracao;
use Illuminate\Http\Request;
use Cache;
use Auth;

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
    
    public function toggleCaptcha($status){
        if(Auth::check()){
            $config = Configuracao::find(1);
            if($status === 'ativado'){
                $config->captcha_ativado = false;
                $config->save();
                Cache::forget('configs');
                return Redirect::to('/admin');
            } elseif ($status === 'desativado'){
                $config->captcha_ativado = true;
                $config->save();
                Cache::forget('configs');
                return Redirect::to('/admin');
            } else {
                return 'input invalido';
            }
        } else {
            return Redirect::to('/');
        }
    }

}
