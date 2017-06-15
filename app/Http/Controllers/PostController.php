<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Post;
use Illuminate\Http\Request;
use Purifier;
use Storage;
use Session;
use Config;
use Ibbr\Arquivo;
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
        
        
        
        $bantime = $this->estaBanido(\Request::ip(), strip_tags(Purifier::clean($request->nomeboard)));
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido da board ' . strip_tags(Purifier::clean($request->nomeboard)) . ' até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
        
            return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        $bantime = null;
        $bantime = $this->estaBanido(\Request::ip());
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido de todas as boards até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
        
            return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        
        
        if(strip_tags(Purifier::clean($request->insidepost)) === 'n'){
            
            $this->validate($request, array(
                'arquivos.*' => 'required|mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg',
                'assunto' => 'max:255',
                'conteudo' => 'required|max:65535',
                'g-recaptcha-response' => 'required|captcha'
            ));
        } else {
            
            $this->validate($request, array(
                'arquivos.*' => 'mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg',
                'assunto' => 'max:255',
                'conteudo' => 'required|max:65535',
                'g-recaptcha-response' => 'required|captcha'
            ));
        }
        
        
        $post = new Post;
        $arquivos = $request->file('arquivos');

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
        
        if(sizeof($arquivos) >  Config::get('constantes.num_max_files') ){
            Session::flash('erro_upload', 'Número máximo de arquivos permitidos: ' . Config::get('constantes.num_max_files') );
        
            return \Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . strip_tags(Purifier::clean($request->insidepost))));
    
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
                    
                    //Storage::disk('disk2')->putFileAs('/storage', $arq, $nomeArquivo);
                    
                    move_uploaded_file($arq, $nomeArquivo);
                    
                    $post->arquivos()->save(new Arquivo(['filename' => $nomeArquivo, 'mime' => $arq->getMimeType() ]));
                    
                }
            }
        }

        if($post->lead_id){
            $this->atualizaUpdatedAt($post->lead_id);
        }
        
        $this->verificaLimitePosts($post->board);
        
        Session::flash('post_criado', 'Post número <a target="_blank" href="/' . $post->board . '/' . $post->id . '">' . $post->id . '</a> criado');
        return \Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . strip_tags(Purifier::clean($request->insidepost))));
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
        $post->updated_at = Carbon::now();
        $post->save();
    }
    
    public function pinarPost($post_id){
        $post = Post::find($post_id);
        $post->pinado = 's';
        $post->save();
        return \Redirect::to('/' . $post->board );
    
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
        
        return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));  
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
