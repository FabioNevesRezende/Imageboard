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
    
    protected function limpaCachePosts($board, $thread){
        $num_paginas = 10;
        for($i = 0 ; $i < $num_paginas ; $i++ ){
            Cache::forget('posts_board_' . $board . '_pag_' . $i);
            Cache::forget('subposts_board_' . $board  . '_pag_' . $i);
        }
        Cache::forget('posts_thread_' . $thread);
        Cache::forget('posts_catalogo');
    }
    
    public function banirUsuario(Request $request){
        
        $ban = new Ban;
        $ban->ip = \Ibbr\Post::find(strip_tags(Purifier::clean($request->idpost)))->anao->ip;
        $ban->exp_date = strip_tags(Purifier::clean($request->permaban)) === 'permaban' ?  Carbon::now()->addYears(100) : Carbon::now()->addHours(strip_tags(Purifier::clean($request->nro_horas)))->addDays(strip_tags(Purifier::clean($request->nro_dias)));
        $post = Post::find(strip_tags(Purifier::clean($request->idpost)));
        if(!$post)
            return $this->redirecionaComMsg('ban', 'Erro ao banir usuário: post inexistente', $request->headers->get('referer'));
        $ban->post_id = $post->id;
        
        if( strip_tags(Purifier::clean($request->board)) !== 'todas'){
            $ban->board = strip_tags(Purifier::clean($request->board));
            Cache::forget('bans_board_' . $ban->board);
        }
        
        $ban->motivo = strip_tags(Purifier::clean($request->motivo));
        
        $ban->save();
        Cache::forget('bans_gerais');
        $this->limpaCachePosts($request->board, $post->lead_id === null ? $post->id : $post->lead_id );
        
        return \Redirect::to($request->headers->get('referer'));
    }
    
    public function estaBanido($ip, $siglaBoard=null){
        if($siglaBoard===null){
            $chave = 'bans_gerais';
            if(Cache::has($chave)){
                $bans = Cache::get($chave);
            }
            else{
                $bans = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', '-')->orderBy('exp_date', 'desc')->get();
                Cache::forever($chave, $bans);
            }
            
        } else{
            $chave = 'bans_board_' . $siglaBoard;
            if(Cache::has($chave)){
                $bans = Cache::get($chave);
            }
            else{
                $bans = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', $siglaBoard)->orderBy('exp_date', 'desc')->get();
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
    
    public function temBiscoito(){
        if(isset($_COOKIE[$this->nomeBiscoitoSessao]))
            return $_COOKIE[$this->nomeBiscoitoSessao];
        else return false;
    }
    
    protected function setaBiscoito(){
        $request = \Request();
        if(!($this->temBiscoito())){
            $stringGerarBiscoito = $request->server('HTTP_USER_AGENT')
            . $request->server('REMOTE_ADDR')
            . ConfiguracaoController::getAll()->tempero_biscoito;
            $valorBiscoito = hash("sha512", $stringGerarBiscoito);
            (new AnaoController)->salvaAnao($valorBiscoito, $request->server('HTTP_USER_AGENT'), $request->server('REMOTE_ADDR'));
            setcookie($this->nomeBiscoitoSessao, $valorBiscoito);
        }
    }
    
    protected function temBiscoitoAdmin(){
        return isset($_COOKIE['biscoitoAdmin']) && 
                $_COOKIE['biscoitoAdmin'] === ConfiguracaoController::getAll()->biscoito_admin;
    }
    
    public static function getPagina(){
        if(isset($_GET['page'])){
            if(strlen($_GET['page']) > 3) return 1;
            return intval($_GET['page']);
        }
        else{
            return 1;
        }
    }
    
    public static function pegaMesPortugues($numMes){
        switch($numMes){
            case 1:
                return 'Janeiro';
            case 2:
                return 'Fevereiro';
            case 3:
                return 'Março';
            case 4:
                return 'Abril';
            case 5:
                return 'Maio';
            case 6:
                return 'Junho';
            case 7:
                return 'Julho';
            case 8:
                return 'Agosto';
            case 9:
                return 'Setembro';
            case 10:
                return 'Outubro';
            case 11:
                return 'Novembro';
            case 12:
                return 'Dezembro';
            default:
                return '';
        }
    }
    
    public static function transformaDatasPortugues($posts){
        foreach($posts as $post){
            $temp = strlen($post->created_at->day) === 1 ? '0' . $post->created_at->day : $post->created_at->day ;
            $temp .= ' de ';
            $temp .= PostController::pegaMesPortugues($post->created_at->month);
            $temp .= ' de ';
            $temp .= $post->created_at->year;
            $temp .= ' às ';
            $temp .= $post->created_at->hour;
            $temp .= ':';
            $temp .= strlen($post->created_at->minute) === 1 ? '0' . $post->created_at->minute : $post->created_at->minute ;
            
            $post->data_post = $temp;
        }
        return $posts;
    }
    
    public function boardExiste($siglaBoard){
        $boards = BoardController::getAll();
        foreach($boards as $board){
            if($board->sigla === $siglaBoard)
                return $board;
        }
        return false;
    }
    
}
