<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Http\Request;
use Ibbr\Anao;
use Ibbr\Helpers\Funcoes;
use Config;

class AnaoController extends Controller
{
    // obtem código do país baseado no IP
    protected function obtemCountryCode($ip) {
        if(preg_match('/^127\..+$/', $ip) 
        || preg_match('/^192\.168\..+$/', $ip)
        || preg_match('/^10\..+$/', $ip)
        ) return 'br'; // se teste em rede local/loopback, retorna brasil

        $url = "http://ip-api.com/json/{$ip}";
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        curl_close($ch);
    
        $data = json_decode($response, true);
    
        if (isset($data['proxy']) && $data['proxy']) {
            return 'on';
        }
    
        if (isset($data['countryCode'])) {
            return strtoupper($data['countryCode']);
        }
    
        return 'on';
    }
    
    public function salvaAnao($biscoito, $userAgent, $ip){
        $anao = new Anao;
        $anao->biscoito = $biscoito;
        $anao->ip = $ip; // ip do postador
        //$anao->countrycode = $this->obtemCountryCode($ip); // country code do IP é armazenado para não ter que ficar recalculando em tempo de execução
        $anao->countrycode = 'on'; // por enquanto apenas onion
        $anao->user_agent = $userAgent;
        try{
            $anao->save();
        }catch(\Illuminate\Database\QueryException $e)
        {
            Funcoes::consolelog('AnaoController::salvaAnao erro: ', $e->getMessage());
        }
    }
}
