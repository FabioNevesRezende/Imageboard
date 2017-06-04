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
        switch($nomeBoard){
            case 'b':
                
                $posts = Post::orderBy('created_at', 'desc')->where('board', 'b')->where('lead_id', null)->paginate(2);
                $subposts = Post::orderBy('created_at', 'asc')->where('board', 'b')->where('lead_id', '<>',null)->get();
                
                return view('pages.board')->with('nomeBoard', 'b')->with('insidePost', 'n')->withPosts($posts)->with('subPosts', $subposts);
                break;
            
            case 'int':
                $posts = Post::orderBy('created_at', 'desc')->where('board', 'int')->get();
                return view('pages.board')->with('nomeBoard', 'int')->with('insidePost', 'n')->withPosts($posts);
                break;
            
            case 'news':
                $posts = Post::orderBy('created_at', 'desc')->where('board', 'news')->get();
                return view('pages.board')->with('nomeBoard', 'news')->with('insidePost', 'n')->withPosts($posts);
                break;
            
            default:
                return view('pages.indice');
                break;
        }
    }
    
    public function getThread($nomeBoard, $thread){
        
        //$posts = \DB::select('select * from posts where id = ? or lead_id = ? order by created_at desc ', [$thread, $thread]);
        $posts = Post::orderBy('created_at', 'asc')->where('id', $thread)->orWhere('lead_id', $thread)->get();
                       
        return view('pages.postshow')->withPosts($posts)->with('nomeBoard', $nomeBoard)->with('insidePost', $thread);
        
    }
    
    
}
