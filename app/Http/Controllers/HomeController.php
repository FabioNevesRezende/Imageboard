<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Auth;
use Illuminate\Support\Facades\Redirect;

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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    
    public function seedar(){
        if(Auth::check() && Auth::id() === 1){
            Artisan::call('db:seed');
        }
        return Redirect::to('/admin');
    }
    
    public function migrate(){
        if(Auth::check() && Auth::id() === 1){
            Artisan::call('migrate');
        }
        return Redirect::to('/');
    }
    
    public function migrateRefresh(){
        if(Auth::check() && Auth::id() === 1){
            Artisan::call('migrate:refresh');
        }
        return Redirect::to('/');
    }
    
    public function toggleCaptcha($status){
        if(Auth::check()){
            $config = Configuracao::find(1);
            if($status === 'ativado'){
                $config->captchaativado = 'n';
                $config->save();
                return Redirect::to('/admin');
            } elseif ($status === 'desativado'){
                $config->captchaativado = 's';
                $config->save();
                return Redirect::to('/admin');
            } else {
                return 'input invalido';
            }
        } else {
            return Redirect::to('/');
        }
    }
    
}
