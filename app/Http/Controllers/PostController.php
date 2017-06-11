<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Post;
use Illuminate\Http\Request;
use Purifier;
use Storage;
use Session;
use Config;
use Ibbr\Arquivo;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        
        
        $bantime = $this->estaBanido2(\Request::ip(), strip_tags(Purifier::clean($request->nomeboard)));
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido da board ' . strip_tags(Purifier::clean($request->nomeboard)) . ' até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
        
            return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        $bantime = null;
        $bantime = $this->estaBanido2(\Request::ip());
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido de todas as boards até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
        
            return \Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        
        
        if(strip_tags(Purifier::clean($request->insidepost)) === 'n'){
            
            $this->validate($request, array(
                'arquivos.*' => 'required|mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg',
                'assunto' => 'max:255',
                'conteudo' => 'required|max:65535'//,
                //'g-recaptcha-response' => 'required|captcha'
            ));
        } else {
            
            $this->validate($request, array(
                'arquivos.*' => 'mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg',
                'assunto' => 'max:255',
                'conteudo' => 'required|max:65535'//,
                //'g-recaptcha-response' => 'required|captcha'
            ));
        }
        
        
        $post = new Post;
        $arquivos = $request->file('arquivos');

        $post->assunto = strip_tags(Purifier::clean($request->assunto));
        $post->board = strip_tags(Purifier::clean($request->nomeboard));
        $post->conteudo = $this->trataLinks(strip_tags(Purifier::clean($request->conteudo)));
        $post->conteudo = $this->addRefPosts(\URL::to('/') . '/' . $post->board, $post->conteudo);
        $post->sage = (strip_tags(Purifier::clean($request->sage)) === 'sage' ? 's' : 'n');
        $post->lead_id = (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? null : strip_tags(Purifier::clean($request->insidepost)));
        $post->ipposter = \Request::ip();
        
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
                    
                    Storage::putFileAs('/public', $arq, $nomeArquivo);
                    
                    $post->arquivos()->save(new Arquivo(['filename' => $nomeArquivo ]));
                    
                }
            }
        }
                

        //$post->datacriacao = Carbon::now();
        /*
          $logfile = $this->iniciaLog('logstore');
          if($post->save()){
          $this->escreveLog('info', 'registro inserido', $logfile);
          } else {
          $this->escreveLog('info', 'erro ao inserir registro', $logfile);
          } */

        //return redirect()->route('/{nomeBoard}', $request->nomeboard);

        Session::flash('post_criado', 'Post número ' . $post->id . ' criado');
        return \Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . strip_tags(Purifier::clean($request->insidepost))));
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
            $arq->delete();
        }
        $post_board = $post->board;
        $post->delete();

        
        return \Redirect::to('/' . $post_board );
    
    }
    
    public function destroyArq($filename){
        \File::delete(public_path() . '/storage/' . $filename);
    }
    
    public function destroyArqDb($nomeBoard, $filename){
        \File::delete(public_path() . '/storage/' . $filename);
        \DB::table('arquivos')->where('filename', '=', $filename)->delete();
    
        return \Redirect::to('/' . $nomeBoard );
    }

}
