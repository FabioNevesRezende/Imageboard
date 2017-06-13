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
    
    
    
    public function banirUsuario(Request $request){
        
        $ban = new Ban;
        $ban->ip = \Ibbr\Post::find(strip_tags(Purifier::clean($request->idpost)))->ipposter;
        $ban->exp_date = strip_tags(Purifier::clean($request->permaban)) === 'permaban' ?  Carbon::now()->addYears(100) : Carbon::now()->addHours(strip_tags(Purifier::clean($request->nro_horas)))->addDays(strip_tags(Purifier::clean($request->nro_dias)));
        $ban->post_id = strip_tags(Purifier::clean($request->idpost));
        
        if( strip_tags(Purifier::clean($request->board)) !== 'todas'){
            $ban->board = strip_tags(Purifier::clean($request->board));
        }
        
        $ban->motivo = strip_tags(Purifier::clean($request->motivo));
        
        $ban->save();
        
        return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)) );
    }
    
    public function estaBanido($ip, $nomeBoard=null){
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
