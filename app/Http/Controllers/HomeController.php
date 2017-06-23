<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Auth;

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
    
    public function migrate(){
        if(Auth::check()){
            Artisan::call('migrate');
            
        }
    }
    
    public function migrateRefresh(){
        if(Auth::check()){
            Artisan::call('migrate:refresh');
        }
    }
    
}
