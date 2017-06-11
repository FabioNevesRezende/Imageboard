<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Ibbr\Ban;
use Purifier;
use Carbon\Carbon;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    protected function iniciaLog($nome) {
        return fopen($nome . "--" . date("Y-m-d") . ".tlog", "a+");
    }

    protected function escreveLog($tag, $msg, $arq) {
        fwrite($arq, "tag=" . $tag . "-" . "data=" . date('Y/m/d-h:m:s-') . "LOG-MSG=" . $msg . "-|-\n");
    }

    protected function terminaLog($logArq) {
        fclose($logArq);
    }
    
    protected function trataLinks($str){
        return preg_replace(
                // regex para um link https://stackoverflow.com/a/8234912/2414842
                '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/s', 
                '<a href="https://href.li/?$0" ref="nofollow" target="_blank">$0</a>', 
                $str
                );
    }
    
    public function banirUsuario(Request $request){
        
        $ban = new Ban;
        $ban->ip = \Ibbr\Post::find(strip_tags(Purifier::clean($request->idpost)))->ipposter;
        $ban->exp_date = strip_tags(Purifier::clean($request->permaban)) === 'permaban' ?  Carbon::now()->addYears(100) : Carbon::now()->addHours(strip_tags(Purifier::clean($request->nro_horas)))->addDays(strip_tags(Purifier::clean($request->nro_dias)));
        
        if( strip_tags(Purifier::clean($request->board)) !== 'todas'){
            $ban->board = strip_tags(Purifier::clean($request->board));
        }
        
        $ban->motivo = strip_tags(Purifier::clean($request->motivo));
        
        $ban->save();
        
        return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)) );
    }
        
    public function estaBanido($ip, $nomeBoard=null){
        if($nomeBoard===null){
            $ban = \DB::table('bans')->where('ip', '=', $ip)->orderBy('exp_date', 'desc')->first();
        } else{
            $ban = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', $nomeBoard)->orderBy('exp_date', 'desc')->first();
            $queryran = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', $nomeBoard)->orderBy('exp_date', 'desc')->toSql();
            $_SESSION['bans2'] = var_export($queryran, true);
            $_SESSION['params'] = var_export(\DB::table('bans')->where('ip', '=', $ip)->where('board', '=', $nomeBoard)->orderBy('exp_date', 'desc')->getBindings(), true);
            
        }
        $_SESSION['bans'] = var_export($ban, true);
        if($ban === null) {
            return false;
        }
        
        $banTime = Carbon::parse($ban->exp_date);
        
        //$_SESSION['bantime'] = $banTime->toDateTimeString();
        //$_SESSION['diffbantime'] = Carbon::now()->diffInSeconds($banTime);
            
        if( Carbon::now()->gt($banTime) ){
            return false;
        } else {
            dfsdfsd;
            return $banTime;
        }
        
    }

    
    public function estaBanido2($ip, $nomeBoard=null){
        if($nomeBoard===null){
            $bans = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', '-')->orderBy('exp_date', 'desc')->get();
        } else{
            $bans = \DB::table('bans')->where('ip', '=', $ip)->where('board', '=', $nomeBoard)->orderBy('exp_date', 'desc')->get();
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
    
}
