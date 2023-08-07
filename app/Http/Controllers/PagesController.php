<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Ibbr\Post;
use Ibbr\Http\Controllers\BoardController;
use Config;
use Auth;
use Ibbr\Helpers\Funcoes;

class PagesController extends Controller
{
    public function getIndex(){
        Funcoes::consolelog('PagesController::getIndex');
        $this->setaBiscoito();
        
        $regras = RegraController::getAll();
        $noticias = NoticiaController::getAll();
        return view('pages.indice')
                ->with('regras', $regras)
                ->with('noticias', $noticias)
                ->with('nomeib', ConfiguracaoController::getAll()->nomeib);
    }
    
    public function getBoard($siglaBoard){
        $this->setaBiscoito();
        $board = $this->boardExiste($siglaBoard);
        if($board){
            $posts = PostController::pegaPostsBoard($siglaBoard);
            $subposts = PostController::pegaSubPostsBoard($siglaBoard);
            
            Funcoes::consolelog('PagesController::getBoard retornando board ' . $siglaBoard);
            return view('pages.board', ['posts' => $posts])
                    ->with('siglaBoard', $siglaBoard)
                    ->with('descrBoard', $board->descricao)
                    ->with('insidePost', 'n')
                    ->with('subPosts', $subposts)
                    ->with('paginador', $posts->appends(\Request::except('page'))->links())
                    ->with('captchaImage', captcha_img())
                    ->with('captchaSize', Config::get('captcha.default.length'));
            
        } else{
            Funcoes::consolelog('PagesController::getBoard Board não encontrada');
            abort(404);
        }
        
    }
    
    public function getThread($siglaBoard, $thread){
        Funcoes::consolelog('PagesController::getThread');
        $this->setaBiscoito();
        
        $board = $this->boardExiste($siglaBoard);
        if(!$board){
            Funcoes::consolelog('PagesController::getThread Board não encontrada');
            abort(404);
        }

        $ver = Post::find($thread);
        if($ver){
            if($ver->lead_id || $ver->board != $siglaBoard){
                Funcoes::consolelog('PagesController::getThread requisição inconsistente');
                abort(404);
            }
        } else {
            Funcoes::consolelog('PagesController::getThread fio não encontrado');
            abort(404);
        }
        
        $posts = PostController::pegaPostsThread($thread);
        
        Funcoes::consolelog('PagesController::getThread retornando fio ' . $thread . ' da board ' . $siglaBoard);
        return view('pages.postshow')
                ->withPosts($posts)
                ->with('siglaBoard', $siglaBoard)
                ->with('descrBoard', BoardController::getAll()->where('sigla', '=', $siglaBoard)->first()->descricao)
                ->with('insidePost', $thread)
                ->with('captchaImage', captcha_img())
                ->with('captchaSize', Config::get('captcha.default.length'));
        
    }
        
    public function getAdmPage($noticiaEditar = null){
        Funcoes::consolelog('PagesController::getAdmPage');
        if(!(\Auth::check()) || !$this->temBiscoitoAdmin()){ 
            Funcoes::consolelog('PagesController::getAdmPage erro: não autenticado ou não tem biscoito admin');
            abort(404);
        }
        
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
        Funcoes::consolelog('PagesController::getCatalogo');
        $this->setaBiscoito();
        $posts = PostController::pegaPostsCatalogo();
        return view('pages.catalogo')->with('nomeib', ConfiguracaoController::getAll()->nomeib)
        ->withPosts($posts);
    }
    
    public function getLogin(){
        $this->setaBiscoito();
        if($this->temBiscoitoAdmin()){
            Funcoes::consolelog('PagesController::getLogin');
            return view('auth.login')
            ->with('nomeib', ConfiguracaoController::getAll()->nomeib);
        } else {
            Funcoes::consolelog('PagesController::getLogin erro: não tem biscoito admin');
            abort(404);
        }
    }
    
    public function logout(){
        if(Auth::check()){
            Funcoes::consolelog('PagesController::logout');
            Auth::logout();
            return $this->getIndex();
        } else {
            Funcoes::consolelog('PagesController::logout erro: não está autenticado');
            abort(404);
        }
    }
    
    public function getArquivo($filename){
        Funcoes::consolelog('PagesController::getArquivo ' . $filename);
        $fullpath = "app/public/" . $filename;
        return response()->download(storage_path($fullpath), null, [], null);
    }
        
    public function getPhpInfo(){
        Funcoes::consolelog('PagesController::getPhpInfo');
        return view('pages.phpinfo');
    }
}
