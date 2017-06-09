<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Ibbr\Ban;
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
    
    public function banirUsuario($nomeBoard, $post_id){
        
        $ban = new Ban;
        $ban->ip = \Ibbr\Post::find($post_id)->ipposter;
        $ban->exp_date = Carbon::now()->addMinutes(2);
        
        $ban->save();
        
        return \Redirect::to('/' . $nomeBoard );
    }
    
    public function estaBanido($ip){
        $ban = \DB::table('bans')->orderBy('exp_date', 'desc')->first();
        if($ban == null) {
            return false;
        }
        $banTime = Carbon::parse($ban->exp_date);
        
        //$_SESSION['bantime'] = $banTime->toDateTimeString();
        //$_SESSION['diffbantime'] = Carbon::now()->diffInSeconds($banTime);
        if( Carbon::now()->gt($banTime) ){
            return false;
        } else {
            return true;
        }
        
    }

}
