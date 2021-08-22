<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Ibbr\Post;
use Ibbr\Http\Controllers\BoardController;
use Config;
use Auth;

class PagesController extends Controller
{
    public function getIndex(){
        $this->setaBiscoito();
        
        $regras = RegraController::getAll();
        $noticias = NoticiaController::getAll();
        return view('pages.indice')
                ->with('regras', $regras)
                ->with('noticias', $noticias);
    }
    
    public function getBoard($siglaBoard){
        $this->setaBiscoito();
        $board = $this->boardExiste($siglaBoard);
        if($board){
            $posts = PostController::pegaPostsBoard($siglaBoard);
            $subposts = PostController::pegaSubPostsBoard($siglaBoard);
            
            return view('pages.board', ['posts' => $posts])
                    ->with('siglaBoard', $siglaBoard)
                    ->with('descrBoard', $board->descricao)
                    ->with('insidePost', 'n')
                    ->with('subPosts', $subposts)
                    ->with('paginador', $posts->appends(\Request::except('page'))->links())
                    ->with('captchaImage', captcha_img())
                    ->with('captchaSize', Config::get('captcha.default.length'));
            
        } else{
            return view('pages.indice'); 
        }
        
    }
    
    public function getThread($siglaBoard, $thread){
        $this->setaBiscoito();
        
        $configuracaos = ConfiguracaoController::getAll();
        $ver = Post::find($thread);
        if($ver){
            if($ver->lead_id || $ver->board != $siglaBoard){
                abort(404);
            }
        } else abort(404);
        
        $posts = PostController::pegaPostsThread($thread);
        
        return view('pages.postshow')
                ->withPosts($posts)
                ->with('siglaBoard', $siglaBoard)
                ->with('descrBoard', BoardController::getAll()->where('sigla', '=', $siglaBoard)->first()->descricao)
                ->with('insidePost', $thread)
                ->with('captchaImage', captcha_img())
                ->with('captchaSize', Config::get('captcha.default.length'));
        
    }
        
    public function getAdmPage($noticiaEditar = null){
        if(!(\Auth::check()) || !$this->temBiscoitoAdmin()) abort(404);
        
        $reports = PostController::pegaReports();

        foreach($reports as $report){
            $post = Post::find($report->post_id);
            $report->lead_id = $post->lead_id;
            
        }
        
        return view('pages.admin')
        ->withReports($reports)
        ->with('noticiaEditar', $noticiaEditar);
    }
    
    public function getCatalogo(){
        $this->setaBiscoito();
        $posts = PostController::pegaPostsCatalogo();
        return view('pages.catalogo')
        ->withPosts($posts);
    }
    
    public function getLogin(){
        $this->setaBiscoito();
        if($this->temBiscoitoAdmin()){
            return view('auth.login');
        } else abort(404);
    }
    
    public function logout(){
        if(Auth::check()){
            Auth::logout();
            return $this->getIndex();
        } else abort(404);
    }
    
    public function getArquivo($filename){
        $fullpath = "app/public/" . $filename;
        return response()->download(storage_path($fullpath), null, [], null);
    }
        
    public function getPhpInfo(){
        return view('pages.phpinfo');
    }
}
