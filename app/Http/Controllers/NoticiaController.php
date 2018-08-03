<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Http\Request;
use Ibbr\Noticia;
use Cache;
use Auth;
use Redirect;
use Purifier;

class NoticiaController extends Controller
{
    public static function getAll()
    {
        if(Cache::has('noticias'))
            return Cache::get('noticias');

        $noticias = Noticia::orderBy('created_at', 'desc')->get();
        $noticias = Controller::transformaDatasPortugues($noticias);
        Cache::forever('noticias', $noticias);
        return $noticias;
    }
    
    public function store(Request $request)
    {
        if(Auth::check())
        {
            $noticia = new Noticia;
            
            $noticia->assunto = strip_tags(Purifier::clean($request->assunto)); 
            $noticia->conteudo = strip_tags(Purifier::clean($request->conteudo));
            if( strlen($noticia->assunto) > 256 || strlen($noticia->conteudo) > 65535)
                abort(400);

            $noticia->autor_id = Auth::id();
            
            $noticia->save();
            Cache::forget('noticias');
            
        }
        return Redirect('/');
    }
    
    public function destroy($id)
    {
        {
            $noticia = Noticia::find($id);
            if($noticia && $noticia->autor_id === Auth::id() || Auth::id() === 1)
            {
                $noticia->delete();
                Cache::forget('noticias');
            }
        }
        return Redirect('/');
    }
    
    public function edit($id)
    {
        if(Auth::check())
        {
            $noticia = Noticia::find($id);
            if($noticia && $noticia->autor_id === Auth::id() || Auth::id() === 1)
            {
                return (new PagesController)->getAdmPage($noticia);
            }
        }
        return Redirect('/');
    }
    
    public function update(Request $request)
    {
        if(Auth::check())
        {
            $noticia = Noticia::find($request->id);
            if($noticia && $noticia->autor_id === Auth::id() || Auth::id() === 1)
            {
                
                $noticia->id = strip_tags(Purifier::clean($request->id)); 
                $noticia->assunto = strip_tags(Purifier::clean($request->assunto)); 
                $noticia->conteudo = strip_tags(Purifier::clean($request->conteudo));
                if( strlen($noticia->assunto) > 256 || strlen($noticia->conteudo) > 65535)
                    abort(400);
                
                $noticia->update();
                Cache::forget('noticias');
            }
        }
        return Redirect('/');
    }
    
}
