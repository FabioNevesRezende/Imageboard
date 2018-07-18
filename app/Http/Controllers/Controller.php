<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Ibbr\Ban;
use Ibbr\Post;
use Purifier;
use Carbon\Carbon;
use Cookie;
use Cache;
use Redirect;
use Session;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;
    var $nomeBiscoitoSessao = "biscoito";

    protected function iniciaLog($nome) {
        return fopen($nome . "--" . date("Y-m-d") . ".tlog", "a+");
    }

    protected function escreveLog($tag, $msg, $arq) {
        fwrite($arq, "tag=" . $tag . "-" . "data=" . date('Y/m/d-h:m:s-') . "LOG-MSG=" . $msg . "-|-\n");
    }

    protected function terminaLog($logArq) {
        fclose($logArq);
    }
    
    
    protected function redirecionaComMsg($tagMsg, $msg, $enderecoRed='/')
    {
        Session::flash($tagMsg, $msg);
        return Redirect::to($enderecoRed);
    }
    
    public function banirUsuario(Request $request){
        
        $ban = new Ban;
        $ban->ip = \Ibbr\Post::find(strip_tags(Purifier::clean($request->idpost)))->anao->ip;
        $ban->exp_date = strip_tags(Purifier::clean($request->permaban)) === 'permaban' ?  Carbon::now()->addYears(100) : Carbon::now()->addHours(strip_tags(Purifier::clean($request->nro_horas)))->addDays(strip_tags(Purifier::clean($request->nro_dias)));
        $post = Post::find(strip_tags(Purifier::clean($request->idpost)));
        if(!$post)
            return $this->redirecionaComMsg('ban', 'Erro ao banir usuário: post inexistente', '/');
        $ban->post_id = $post->id;
        
        if( strip_tags(Purifier::clean($request->board)) !== 'todas'){
            $ban->board = strip_tags(Purifier::clean($request->board));
            Cache::forget('bans_board_' . $ban->board);
        }
        
        $ban->motivo = strip_tags(Purifier::clean($request->motivo));
        
        $ban->save();
        Cache::forget('bans_gerais');
        
        return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)) );
    }
    
    public function estaBanido($ip, $nomeBoard=null){
        if($nomeBoard===null){
            $chave = 'bans_gerais';
            if(Cache::has($chave)){
                $bans = Cache::get($chave);
            }
            else{
                $bans = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', '-')->orderBy('exp_date', 'desc')->get();
                Cache::forever($chave, $bans);
            }
            
        } else{
            $chave = 'bans_board_' . $nomeBoard;
            if(Cache::has($chave)){
                $bans = Cache::get($chave);
            }
            else{
                $bans = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', $nomeBoard)->orderBy('exp_date', 'desc')->get();
                Cache::forever($chave, $bans);
            }
        }
        if(count($bans)>0) {
            $banTime = Carbon::parse($bans[0]->exp_date);
            
            if( Carbon::now()->gt($banTime) ){
                return false;
            } else {
                return $banTime;
            }
            
        } else {
            return false;
        }
    }
    
    public function temBiscoito()
    {
        if(isset($_COOKIE[$this->nomeBiscoitoSessao]))
            return $_COOKIE[$this->nomeBiscoitoSessao];
        else return false;
    }
    
    protected function setaBiscoito(){
        $request = \Request();
        if(!$this->temBiscoito()){
            $stringGerarBiscoito = $request->server('HTTP_USER_AGENT')
            . $request->server('REMOTE_ADDR')
            . ConfiguracaoController::getAll()->tempero_biscoito;
            $valorBiscoito = hash("sha512", $stringGerarBiscoito);
            setcookie($this->nomeBiscoitoSessao, $valorBiscoito);
            (new AnaoController)->salvaAnao($valorBiscoito, $request->server('HTTP_USER_AGENT'), $request->server('REMOTE_ADDR'));
        }
    }
    
    public static function getPagina()
    {
        if(isset($_GET['page']))
        {
            if(strlen($_GET['page']) > 3) return 1;
            return intval($_GET['page']);
        }
        else
        {
            return 1;
        }
    }
}
