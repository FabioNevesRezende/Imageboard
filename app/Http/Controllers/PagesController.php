<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Ibbr\Post;
use Ibbr\Report;
use Ibbr\Configuracao;
use Ibbr\Http\Controllers\BoardController;
use Cache;

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
        
        $configuracaos = ConfiguracaoController::getAll();
        return view('pages.indice')->withBoards(BoardController::getAll())->withConfiguracaos($configuracaos);
    }    
    
    public function getBoard($nomeBoard){
        
        $this->setaBiscoito();
        if(in_array($nomeBoard, array_keys(BoardController::getAll()) )){
            
            $posts = PostController::pegaPostsBoard($nomeBoard);
            $subposts = PostController::pegaSubPostsBoard($nomeBoard);
            
            $configuracaos = ConfiguracaoController::getAll();
            return view('pages.board', ['posts' => $this->reordenaPostsPinados($posts)])
                    ->with('nomeBoard', $nomeBoard)
                    ->with('descrBoard', BoardController::getAll()[$nomeBoard])
                    ->with('insidePost', 'n')
                    //->withPosts($this->reordenaPostsPinados($posts))
                    ->with('subPosts', $subposts)
                    ->with('paginador', $posts->appends(\Request::except('page'))->links())
                    ->withConfiguracaos($configuracaos)
                    ->withBoards(BoardController::getAll());
            
        } else{
            return view('pages.indice'); 
        }
        
    }
    
    public function getThread($nomeBoard, $thread){
        $this->setaBiscoito();
        
        $ver = Post::find($thread);
        if($ver){
            if($ver->lead_id){
                return view('pages.indice'); 
            }
        } else return view('pages.indice');
        
        $posts = PostController::pegaPostsThread($thread);
        $configuracaos = ConfiguracaoController::getAll();
        
        return view('pages.postshow')
                ->withPosts($posts)
                ->with('nomeBoard', $nomeBoard)
                ->with('descrBoard', BoardController::getAll()[$nomeBoard])
                ->with('insidePost', $thread)
                ->withConfiguracaos($configuracaos)
                ->withBoards(BoardController::getAll());
        
    }
        
    public function getAdmPage(){
        
        if(!(\Auth::check())) return view('pages.indice');
        
        $reports = PostController::pegaReports();
        
        $configuracaos = ConfiguracaoController::getAll();
        return view('pages.admin')
        ->withReports($reports)
        ->withConfiguracaos($configuracaos)
        ->withBoards(BoardController::getAll());
    }
    
    public function getCatalogo()
    {
        
        $this->setaBiscoito();
        $posts = PostController::pegaPostsCatalogo();
        $configuracaos = ConfiguracaoController::getAll();
        return view('pages.catalogo')
        ->withBoards(BoardController::getAll())
        ->withPosts($posts)
        ->withConfiguracaos($configuracaos);
    }
    
}
