<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Post;
use Illuminate\Http\Request;
use Purifier;
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

        $post->assunto = strip_tags(Purifier::clean($request->assunto));
        $post->board = strip_tags(Purifier::clean($request->nomeboard));
        $post->conteudo = $this->trataLinks(strip_tags(Purifier::clean($request->conteudo)));
        $post->sage = (strip_tags(Purifier::clean($request->sage)) === 'sage' ? 's' : 'n');
        $post->lead_id = (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? null : strip_tags(Purifier::clean($request->insidepost)));
        $post->ipposter = \Request::ip();
        $post->save();

        $arquivos = $request->file('arquivos');
        if (!empty($arquivos)) {
            foreach ($arquivos as $arq) {
                if ($arq->isValid()) {
                                        
                    $contador = 0;
                    do{
                        $nomeArquivo = $post->id . "-{$contador}"  . "." . $arq->extension();
                        
                        $contador++;
                    }while(\File::exists(public_path() . '/storage/' . $nomeArquivo));
                    
                    \Storage::putFileAs('/public', $arq, $nomeArquivo);
                    
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Ibbr\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post) {
        //
    }

}
