<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Post;
use Illuminate\Http\Request;
use Purifier;
use Storage;
use Session;
use Config;
use Redirect;
use Ibbr\Arquivo;
use Ibbr\Ytanexo;
use Ibbr\Report;
use Carbon\Carbon;

class PostController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response

      public function create()
      {
      //
      } */

    
    
    protected function obtemCountryCode($ip){
        if($ip === '127.0.0.1') $ip = '139.82.255.255'; // se teste em localhost, retorna um ip do brasil
        $iptolocation = 'http://www.geoplugin.net/xml.gp?ip=' . $ip;
        $creatorlocation = simplexml_load_string(file_get_contents($iptolocation));
        return strtolower(preg_replace('/<geoplugin_countryCode>([a-zA-Z]+)<\/geoplugin_countryCode>/s', '$1', $creatorlocation->geoplugin_countryCode->asXML()));
        
        
    }
    
    
    protected function trataLinks($str){
        return preg_replace(
                // regex para um link https://stackoverflow.com/a/8234912/2414842
                '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/s', 
                '<a href="https://href.li/?$0" ref="nofollow" target="_blank">$0</a>', 
                $str
                );
    }
    
    protected function addRefPosts($url, $str){
        return preg_replace(
                
                '/&gt;&gt;([0-9]+)/s', 
                '<a href="' . $url . '#$1">&gt;&gt;$1</a>', 
                $str
                );
    }
    
    
    protected function addGreenText($str){
        return preg_replace(
                
                '/&gt;(.+)/m', 
                '<p class="green-text">&gt;$1</p>', 
                $str
                );
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        
        if(!preg_match(Config::get('funcoes.geraRegexBoards')(), strip_tags(Purifier::clean($request->nomeboard)))){
            Session::flash('erro_upload', 'Board inválida');
            return Redirect::to('/');
        }
        $bantime = $this->estaBanido(\Request::ip(), strip_tags(Purifier::clean($request->nomeboard)));
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido da board ' . strip_tags(Purifier::clean($request->nomeboard)) . ' até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
        
            return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        $bantime = null;
        $bantime = $this->estaBanido(\Request::ip());
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido de todas as boards até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
        
            return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        if($request->linkyoutube){
            if($request->file('arquivos')){
                Session::flash('erro_upload', 'Sem anexo de arquivos quando há links de youtube');
        
                return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
            }
            $this->validate($request, array(
                    'linkyoutube' => 'max:255',
                    'assunto' => 'max:255',
                    'conteudo' => 'required|max:65535'//,
                    //'g-recaptcha-response' => 'required|captcha'
                ));
            
        } else {
            if(!$request->file('arquivos') && strip_tags(Purifier::clean($request->insidepost)) === 'n'){
                Session::flash('erro_upload', 'É necessário postar pelo menos com um arquivo ou um link do youtube');
                return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
            }
            if(strip_tags(Purifier::clean($request->insidepost)) === 'n'){

                $this->validate($request, array(
                    'arquivos.*' => 'required|mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg',
                    'assunto' => 'max:255',
                    'conteudo' => 'required|max:65535'//,
                    //'g-recaptcha-response' => 'required|captcha'
                ));
            } else if( preg_match('/^[0-9]+$/s',strip_tags(Purifier::clean($request->insidepost))) ) {

                $this->validate($request, array(
                    'arquivos.*' => 'mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg',
                    'assunto' => 'max:255',
                    'conteudo' => 'max:65535'//,
                    //'g-recaptcha-response' => 'required|captcha'
                ));
            } else {
                Session::flash('erro_upload', 'Input inválido');

                return Redirect::to('/');
            }
        }
        
        $post = new Post;
        $arquivos = $request->file('arquivos');
        $links = null;
        if($request->linkyoutube){
            $links = explode('|' ,strip_tags(Purifier::clean($request->linkyoutube)));
        }
        $post->assunto = strip_tags(Purifier::clean($request->assunto));
        $post->board = strip_tags(Purifier::clean($request->nomeboard));
        
        $post->conteudo = $this->trataLinks(strip_tags(Purifier::clean($request->conteudo)));
        $post->conteudo = $this->addRefPosts(\URL::to('/') . '/' . $post->board, $post->conteudo);
        $post->conteudo = $this->addGreenText($post->conteudo);
        $post->sage = (strip_tags(Purifier::clean($request->sage)) === 'sage' ? 's' : 'n');
        $post->pinado = 'n';
        $post->lead_id = (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? null : strip_tags(Purifier::clean($request->insidepost)));
        
        $post->ipposter = \Request::ip();
        $post->countrycode = $this->obtemCountryCode($post->ipposter);
                
        if($request->modpost){
            $post->modpost = (strip_tags(Purifier::clean($request->modpost)) === 'modpost' ? 's' : 'n');
        
        }
        
        if(sizeof($arquivos) >  Config::get('constantes.num_max_files') || ($links && sizeof($links) >  Config::get('constantes.num_max_files') ) ){
            Session::flash('erro_upload', 'Número máximo de arquivos ou links do youtube permitidos: ' . Config::get('constantes.num_max_files') );
        
            return Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id ));
    
        }
        
        $post->save();

        if (!empty($arquivos)) {
            foreach ($arquivos as $arq) {
                if ($arq->isValid()) {
                                        
                    $contador = 0;
                    do{
                        $nomeArquivo = $post->id . "-{$contador}"  . "." . $arq->extension();
                        
                        $contador++;
                    }while(\File::exists(public_path() . '/storage/' . $nomeArquivo));
                    
                    Storage::disk('disk2')->putFileAs('/storage', $arq, $nomeArquivo);
                    
                    
                    $post->arquivos()->save(new Arquivo(['filename' => $nomeArquivo, 'mime' => $arq->getMimeType() ]));
                    
                }
            }
        } else if($links){
            foreach($links as $link){
                if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link, $match)){
                    $post->ytanexos()->save(new Ytanexo(['ytcode' => $match[1], 'post_id' => $post->id ]));
                    
                } else {
                    Session::flash('erro_upload', 'Link inválido');
                    $this->postRollback($post);
                    return Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id ));
    
                }
            }
        }

        if($post->lead_id && $post->sage !== 's'){
            $this->atualizaUpdatedAt($post->lead_id);
        }
        
        $this->verificaLimitePosts($post->board);
        
        $flashmsg = $post->lead_id ? 'Post número ' . $post->id . ' criado' : 'Post número <a target="_blank" href="/' . $post->board . '/' . $post->id . '">' . $post->id . '</a> criado';
        Session::flash('post_criado', $flashmsg);
        return Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id));
    }
    
    protected function postRollback($post){
        $this->destroy($post->id);
    }

    protected function verificaLimitePosts($nomeBoard){
        
        $posts = \DB::select('select * from posts where pinado <> \'s\' and board = ? order by updated_at desc limit 1 offset ?;', [$nomeBoard, Config::get('constantes.num_max_posts')]);
        if(count($posts)>0){
            // destroyArq, destroyArqDb
            $post_a_deletar = $posts[0]->id;
            $arqs = \DB::table('arquivos')->where('post_id', $post_a_deletar)->get();
            foreach($arqs as $arq){
                $this->destroyArqDb($nomeBoard, $arq->filename, false);
            }
            $this->destroy($post_a_deletar);
        }
    }


    protected function atualizaUpdatedAt($post_id){
        $post = Post::find($post_id);
        if($post){
            $post->updated_at = Carbon::now();
            $post->save();
            
        }
    }
    
    public function pinarPost($post_id){
        $post = Post::find($post_id);
        if($post){
            $post->pinado = 's';
            $post->save();
            return \Redirect::to('/' . $post->board );
        }
        return \Redirect::to('/');
    }
    
    public function report(Request $request){
        $this->validate($request, array(
                'motivo' => 'max:255'
            ));
        $report = new Report;
      
        $report->motivo = strip_tags(Purifier::clean($request->motivo));
        $report->post_id = strip_tags(Purifier::clean($request->idpost));
        $report->board = strip_tags(Purifier::clean($request->nomeboard));
        
        $report->save();
        
        return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));  
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \Ibbr\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Ibbr\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Ibbr\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post) {
        //
    }

    public function destroy($post_id) {
        
        $post = Post::find($post_id);
        $arquivos = $post->arquivos;

        foreach($arquivos as $arq){
            $this->destroyArq($arq->filename);
            \DB::table('arquivos')->where('post_id', '=', $post_id)->delete();
        }
        if($post->ytanexos){
            \DB::table('ytanexos')->where('post_id', '=', $post_id)->delete();
            
        }
        
        $post_board = $post->board;
        $post->delete();

        
        return \Redirect::to('/' . $post_board );
    
    }
    
    public function destroyArq($filename){
        \File::delete(public_path() . '/storage/' . $filename);
    }
    
    public function destroyArqDb($nomeBoard, $filename, $redirect=true){
        \File::delete(public_path() . '/storage/' . $filename);
        \DB::table('arquivos')->where('filename', '=', $filename)->delete();
    
        if($redirect) return \Redirect::to('/' . $nomeBoard );
    }

}
