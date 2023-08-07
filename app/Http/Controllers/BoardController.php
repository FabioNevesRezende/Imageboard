<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Board;
use Ibbr\Post;
use Ibbr\Regra;
use Illuminate\Http\Request;
use Cache;
use Auth;
use Redirect;
use Purifier;
use Session;
use Ibbr\Helpers\Funcoes;

class BoardController extends Controller
{
    public static function getAll(){
        if(Cache::has('boards'))
            return Cache::get('boards');
        
        $boards = Board::orderBy('ordem')->get();
        
        Cache::forever('boards', $boards);
        return $boards;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        Funcoes::consolelog('BoardController::store');
        if(Auth::check()){
            $board = new Board;
            
            $board->sigla = strip_tags(Purifier::clean($request->sigla));
            $board->nome = strip_tags(Purifier::clean($request->nome));
            $board->descricao = strip_tags(Purifier::clean($request->descricao));
            $board->ordem = strip_tags(Purifier::clean($request->ordem));

            if( strlen($board->nome) > 50 
                    || strlen($board->sigla) > 10
                    || strlen($board->descricao) > 300
                    || $board->ordem > 32767
                    || $board->ordem < -32767
                    || !preg_match("/^[a-zA-Z0-9\-_]+$/", $board->sigla)
                    || !preg_match("/^[a-zA-Z0-9\-_]+$/", $board->descricao)
                    || !preg_match("/^[a-zA-Z0-9\-_]+$/", $board->nome))
                abort(400);
            
            try{
                Funcoes::consolelog('BoardController::store salvando nova board: ' . $board->sigla);
                $board->save();
            }
            catch(\Illuminate\Database\QueryException $e){
                Session::flash('erro_admin', 'Erro ao armazenar board: sigla já existente');
                return Redirect('/admin');
            }
            catch(Exception $e){
                Session::flash('erro_admin', 'Erro ao armazenar board');
                return Redirect('/admin');
            }
            
            Cache::forget('boards');
            return Redirect('/admin');
            
        }
        return Redirect('/');
    }
    
    public function destroy($id){
        if(Auth::check() && Auth::id() === 1){
            $board = Board::where('sigla', '=', $id)->first();
            if($board){
                $this->deletaPostsBoard($board);
                $this->deletaRegrasBoard($board);
                
                Funcoes::consolelog('BoardController::destroy: ' . $board->sigla);
                $board->delete();
                Cache::forget('boards');
            }
        }
        return Redirect('/');
    }
    
    private function deletaPostsBoard($board){
        $posts = Post::where('board', '=', $board->sigla)->whereNull('lead_id')->get();
        $postController = new PostController();
        
        if($posts && count($posts) > 0){
            foreach($posts as $post){
                $postController->destroy($board->sigla, $post->id);
                
            }
        }
    }
    
    private function deletaRegrasBoard($board){
        $regras = Regra::where('board_name', '=', $board->sigla)->get();
        $regraController = new RegraController();
        
        if($regras && count($regras) > 0){
            foreach($regras as $regra){
                $regraController->destroy($regra->id);
            }
        }
    }
}
