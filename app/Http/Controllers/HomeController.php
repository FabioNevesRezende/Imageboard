<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Auth;
use Illuminate\Support\Facades\Redirect;
use Cache;

use Ibbr\Configuracao;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function seedar(){
        if(Auth::check() && Auth::id() === 1){
            try{
                Artisan::call('db:seed');
                $this->limparCache();
            }catch(\Illuminate\Database\QueryException $e)
            {
                    
            }
        }
        return Redirect::to('/admin');
    }
    
    public function migrate(){
        if(Auth::check() && Auth::id() === 1){
            Artisan::call('migrate');
            $this->limparCache();
        }
        return Redirect::to('/');
    }
    
    public function migrateRefresh(){
        if(Auth::check() && Auth::id() === 1){
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
            $this->limparCache();
        }
        return Redirect::to('/login');
    }
    
    public function limparCache()
    {
        if(Auth::check() && Auth::id() === 1){
            Cache::flush();
            return Redirect::to('/admin');
        }
    }
    
}
