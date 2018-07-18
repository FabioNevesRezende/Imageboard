<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Http\Request;
use Ibbr\Anao;

class AnaoController extends Controller
{
    // obtem código do país baseado no IP
    protected function obtemCountryCode($ip){
        if($ip === '127.0.0.1') $ip = '139.82.255.255'; // se teste em localhost, retorna um ip do brasil
        $iptolocation = 'http://www.geoplugin.net/xml.gp?ip=' . $ip;
        $creatorlocation = simplexml_load_string(file_get_contents($iptolocation));
        return strtolower(preg_replace('/<geoplugin_countryCode>([a-zA-Z]+)<\/geoplugin_countryCode>/s', '$1', $creatorlocation->geoplugin_countryCode->asXML()));
        
    }
    
    public function salvaAnao($biscoito, $userAgent, $ip)
    {
        $anao = new Anao;
        $anao->biscoito = $biscoito;
        $anao->ip = $ip; // ip do postador
        $anao->countrycode = $this->obtemCountryCode($ip); // country code do IP é armazenado para não ter que ficar recalculando em tempo de execução
        $anao->user_agent = $userAgent;
        try{
            $anao->save();
        }catch(\Illuminate\Database\QueryException $e)
        {
            
        }
    }
}
