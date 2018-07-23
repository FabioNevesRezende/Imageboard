<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Configuracao;
use Illuminate\Http\Request;
use Cache;
use Auth;
use Redirect;

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
    
    public function toggleCaptcha($val){
        if(Auth::check()){
            $config = Configuracao::find(1);
            if($val === "1" || $val === "0"){
                $config->captcha_ativado = $val;
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
