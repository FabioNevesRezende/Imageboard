<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Http\Request;
use Ibbr\Regra;
use Cache;
use Auth;
use Redirect;
use Purifier;


class RegraController extends Controller
{
    public static function getAll(){
        if(Cache::has('regras'))
            return Cache::get('regras');

        $regras = Regra::orderBy('id')->get();
        
        Cache::forever('regras', $regras);
        return $regras;
    }
    
    public function store(Request $request){
        if(Auth::check()){
            $regra = new Regra;
            
            $regra->descricao = strip_tags(Purifier::clean($request->descricao)); 
            if($request->board_name && $request->board_name !== 'todas')
                $regra->board_name = strip_tags(Purifier::clean($request->board_name));
                
            if( strlen($regra->descricao) > 256 || strlen($regra->board_name) > 10)
                abort(400);
            
            $regra->save();
            Cache::forget('regras');
            
        }
        return Redirect('/');
    }
    
    public function destroy($id){
        if(Auth::check()){
            $regra = Regra::find($id);
            if($regra){
                $regra->delete();
                Cache::forget('regras');
            }
        }
        return Redirect('/');
    }
}
