<?php

namespace Ibbr\Http\Controllers;

//use Illuminate\Http\Request;
use Ibbr\Post;

class PagesController extends Controller
{
    public function getIndex(){
        return view('pages.indice');
    }    
    
    public function getBoard($nomeBoard){
        if($nomeBoard === 'b' || $nomeBoard === 'int' || $nomeBoard === 'news'){
            $posts = Post::orderBy('created_at', 'desc')->where('board', $nomeBoard)->where('lead_id', null)->paginate(10);
            $subposts = Post::orderBy('created_at', 'asc')->where('board', $nomeBoard)->where('lead_id', '<>', null)->get();
                
            return view('pages.board')->with('nomeBoard', $nomeBoard)->with('insidePost', 'n')->withPosts($posts)->with('subPosts', $subposts);
            
        } else{
            return view('pages.indice'); 
        }
        
    }
    
    public function getThread($nomeBoard, $thread){
        
        //$posts = \DB::select('select * from posts where id = ? or lead_id = ? order by created_at desc ', [$thread, $thread]);
        $posts = Post::orderBy('created_at', 'asc')->where('id', $thread)->orWhere('lead_id', $thread)->get();
                       
        return view('pages.postshow')->withPosts($posts)->with('nomeBoard', $nomeBoard)->with('insidePost', $thread);
        
    }
    
    
    
}
