<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Ibbr\Post;
use Ibbr\Report;
use Ibbr\Configuracao;
use Ibbr\Http\Controllers\BoardController;
use Cache;
use Config;
use Auth;
use Carbon\Carbon;

class PagesController extends Controller
{
    
    protected function reordenaPostsPinados($posts){
        
        $postsFinal = array();
        
        foreach($posts->getCollection()->all() as $post){
            if($post->pinado){
                array_push($postsFinal, $post);
            }
        }
        
        foreach($posts->getCollection()->all() as $post){
            if(!($post->pinado)){
                array_push($postsFinal, $post);
            }
        }
        return $postsFinal;
    }
    
    public function getIndex(){
        $this->setaBiscoito();
        
        $regras = RegraController::getAll();
        $noticias = NoticiaController::getAll();
        return view('pages.indice')
                ->with('regras', $regras)
                ->with('noticias', $noticias);
    }
    
    public function getBoard($nomeBoard){
        
        $this->setaBiscoito();
        if(in_array($nomeBoard, array_keys(BoardController::getAll()) )){
            
            $posts = PostController::pegaPostsBoard($nomeBoard);
            $subposts = PostController::pegaSubPostsBoard($nomeBoard);
            
            return view('pages.board', ['posts' => $this->reordenaPostsPinados($posts)])
                    ->with('nomeBoard', $nomeBoard)
                    ->with('descrBoard', BoardController::getAll()[$nomeBoard])
                    ->with('insidePost', 'n')
                    ->with('subPosts', $subposts)
                    ->with('paginador', $posts->appends(\Request::except('page'))->links())
                    ->with('captchaImage', captcha_img())
                    ->with('captchaSize', Config::get('captcha.default.length'));
            
        } else{
            return view('pages.indice'); 
        }
        
    }
    
    public function getThread($nomeBoard, $thread){
        $this->setaBiscoito();
        
        $configuracaos = ConfiguracaoController::getAll();
        $ver = Post::find($thread);
        if($ver){
            if($ver->lead_id){
                abort(404);
            }
        } else abort(404);
        
        $posts = PostController::pegaPostsThread($thread);
        
        return view('pages.postshow')
                ->withPosts($posts)
                ->with('nomeBoard', $nomeBoard)
                ->with('descrBoard', BoardController::getAll()[$nomeBoard])
                ->with('insidePost', $thread)
                ->with('captchaImage', captcha_img())
                ->with('captchaSize', Config::get('captcha.default.length'));
        
    }
        
    public function getAdmPage($noticiaEditar = null){
        
        if(!(\Auth::check()) || !$this->temBiscoitoAdmin()) return view('pages.indice');
        
        $reports = PostController::pegaReports();
        
        return view('pages.admin')
        ->withReports($reports)
        ->with('noticiaEditar', $noticiaEditar);
    }
    
    public function getCatalogo()
    {
        
        $this->setaBiscoito();
        $posts = PostController::pegaPostsCatalogo();
        return view('pages.catalogo')
        ->withPosts($posts);
    }
    
    public function getLogin()
    {
        $this->setaBiscoito();
        if($this->temBiscoitoAdmin()){
            return view('auth.login');
        } else abort(404);
    }
    
    public function logout()
    {
        if(Auth::check())
            Auth::logout();
        return $this->getIndex();
    }
    
    public function getArquivo($filename)
    {
        $fullpath = "app/public/" . $filename;
        return response()->download(storage_path($fullpath), null, [], null);
    }
    
    public static function return404()
    {
        return view('pages.notfound');
    }
}
