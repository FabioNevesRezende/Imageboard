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
use Ibbr\Configuracao;
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

    // obtem código do país baseado no IP
    protected function obtemCountryCode($ip){
        if($ip === '127.0.0.1') $ip = '139.82.255.255'; // se teste em localhost, retorna um ip do brasil
        $iptolocation = 'http://www.geoplugin.net/xml.gp?ip=' . $ip;
        $creatorlocation = simplexml_load_string(file_get_contents($iptolocation));
        return strtolower(preg_replace('/<geoplugin_countryCode>([a-zA-Z]+)<\/geoplugin_countryCode>/s', '$1', $creatorlocation->geoplugin_countryCode->asXML()));
        
        
    }
    
    // adiciona tags <a> em links
    protected function trataLinks($str){
        return preg_replace(
                // regex para um link https://stackoverflow.com/a/8234912/2414842
                '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/s', 
                '<a href="https://href.li/?$0" ref="nofollow" target="_blank">$0</a>', 
                $str
                );
    }
    
    // adiciona referencia aos posts, para o usuário clicar e ser direcionado ao mesmo
    protected function addRefPosts($url, $str){
        return preg_replace(
                
                '/&gt;&gt;([0-9]+)/s', 
                '<a href="' . $url . '#$1">&gt;&gt;$1</a>', 
                $str
                );
    }
    
    // adiciona verdetexto na postagem
    protected function addGreenText($str){
        return preg_replace(
                
                '/&gt;(.+)/m', 
                '<p class="green-text">&gt;$1</p>', 
                $str
                );
    }
    
    // true se $nomeboard for uma board que existe, caso contrário false
    public function verificaBoardLegitima($nomeboard){
        if(!preg_match(Config::get('funcoes.geraRegexBoards')(),  $nomeboard)){
            Session::flash('erro_upload', 'Board inválida');
            return false;
        } else return true;
    }
    
    // retorna array com regras de validação
    public function defineArrayValidacao($request){
        $configuracaos = Configuracao::orderBy('id', 'desc')->get()[0];
        $regras = array();
        
        if($request->linkyoutube){
            $regras['linkyoutube'] = 'max:255';
        }
        
        $regras['assunto'] = 'max:255';
        
        // caso seja uma nova postagem fora de um fio
        if(strip_tags(Purifier::clean($request->insidepost)) === 'n'){
            $regras['conteudo'] = 'required|max:65535';
            $regras['arquivos.*'] = 'required|mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg';
        }else if( preg_match('/^[0-9]+$/s',strip_tags(Purifier::clean($request->insidepost))) ) { // caso seja dentro de um fio, 
            if($request->conteudo){
                $regras['conteudo'] = 'max:65535';
                $regras['arquivos.*'] = 'mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg';
            } else {
                $regras['arquivos.*'] = 'required|mimetypes:image/jpeg,image/png,image/gif,video/webm,video/mp4,audio/mpeg';
            }
            
        }
        $regras['g-recaptcha-response'] = $configuracaos->captchaativado === 's' ? 'required|captcha' : '';
        
        if($request->sage){
            $regras['sage'] = 'max:4';
        }
        
        if($request->lead_id){
            $regras['lead_id'] = 'max:25';
        }
        
        if($request->ipposter){
            $regras['ipposter'] = 'max:15';
        }
        
        if($request->modpost){
            $regras['modpost'] = 'max:7';
        }
        
        return $regras;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->defineArrayValidacao($request);
        // Verifica se a board requisitada para o post realmente existe
        if(!$this->verificaBoardLegitima(strip_tags(Purifier::clean($request->nomeboard)))){
            return Redirect::to('/');
        }
        
        // Verifica se o postador está banido da board em questão
        $bantime = $this->estaBanido(\Request::ip(), strip_tags(Purifier::clean($request->nomeboard)));
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido da board ' . strip_tags(Purifier::clean($request->nomeboard)) . ' até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
        
            return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        // verifica se o postador esta banido para todas as boards
        $bantime = null;
        $bantime = $this->estaBanido(\Request::ip());
        if($bantime){
            Session::flash('ban', 'Seu IP ' . \Request::ip() . ' está banido de todas as boards até: ' . $bantime->toDateTimeString() . ' e não pode postar.');
            return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
        }
        
        $arquivos = $request->file('arquivos'); // salva os dados dos arquivos na variável $arquivos
        // valida os inputs
        $regras = $this->defineArrayValidacao($request);
        // validação caso haja link do youtube provido na postagem
        if($request->linkyoutube){
            if($request->file('arquivos')){
                Session::flash('erro_upload', 'Sem anexo de arquivos quando há links de youtube');
                return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
            }
            $this->validate($request, $regras);
            
        } else { // se não houver nenhum link do youtube
            if(!$request->file('arquivos') && strip_tags(Purifier::clean($request->insidepost)) === 'n'){
                Session::flash('erro_upload', 'É necessário postar pelo menos com um arquivo ou um link do youtube');
                return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
            }
            $this->validate($request, $regras);
            if(sizeof($arquivos) < 1){
                Session::flash('erro_upload', 'É necessário postar pelo menos com um arquivo ou um link do youtube');
                return Redirect::to('/' . strip_tags(Purifier::clean($request->nomeboard)));
                
            }
        }
        // termina validação dos inputs
        
        // salva os dados do post na variável $post
        $post = new Post;
        $links = null;
        if($request->linkyoutube){ // caso haja links do youtube, divida a strings por pipe characters | 
            $links = explode('|' ,strip_tags(Purifier::clean($request->linkyoutube)));
        }
        $post->assunto = strip_tags(Purifier::clean($request->assunto)); // assunto do post
        $post->board = strip_tags(Purifier::clean($request->nomeboard)); // board que o post pertence
        $post->conteudo = $this->trataLinks(strip_tags(Purifier::clean($request->conteudo))); // adiciona tags <a> ao conteudo das mensagens
        $post->conteudo = $this->addRefPosts(\URL::to('/') . '/' . $post->board, $post->conteudo); // adiciona referência a outros posts iniciados com '>'
        $post->conteudo = $this->addGreenText($post->conteudo); // add verdetexto após os símbolos '>>'
        $post->sage = (strip_tags(Purifier::clean($request->sage)) === 'sage' ? 's' : 'n'); // define se o post foi sageado ou não
        $post->pinado = 'n'; // define se a thread está pinada, por padrão, não
        $post->lead_id = (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? null : strip_tags(Purifier::clean($request->insidepost))); // caso o post seja dentro de um fio, define qual fio "pai" da postagem
        
        $post->ipposter = \Request::ip(); // ip do postador
        $post->countrycode = $this->obtemCountryCode($post->ipposter); // country code do IP é armazenado para não ter que ficar recalculando em tempo de execução
                
        // flag "modpost" definido pelo mod
        if($request->modpost){
            $post->modpost = (strip_tags(Purifier::clean($request->modpost)) === 'modpost' ? 's' : 'n');
        }
        
        // verifica se há mais arquivos/links que o máximo permitido
        if(sizeof($arquivos) >  Config::get('constantes.num_max_files') || ($links && sizeof($links) >  Config::get('constantes.num_max_files') ) ){
            Session::flash('erro_upload', 'Número máximo de arquivos ou links do youtube permitidos: ' . Config::get('constantes.num_max_files') );
            return Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id ));
        }
        
        // salva o post em banco de dados
        $post->save();

        // caso haja arquivos, salva-os em disco e seus paths em banco
        if (!empty($arquivos)) {
            foreach ($arquivos as $arq) {
                if ($arq->isValid()) {
                                 
                    // define o filename baseado no nro da postagem concatenado com a qtdade de arquivos updados
                    // exemplo, se fio nro 1234 e a postagem tem 3 arquivos, gerará 3 filenames do tipo 1234-0, 1234-1, 1234-2 seguido da extensão do arquivo
                    $contador = 0;
                    do{
                        $nomeArquivo = $post->id . "-{$contador}"  . "." . $arq->extension();
                        
                        $contador++;
                    }while(\File::exists(public_path() . '/storage/' . $nomeArquivo));
                    
                    // salva em disco na pasta public/storage
                    Storage::disk('disk2')->putFileAs('/storage', $arq, $nomeArquivo);
                    
                    // salva em banco
                    $post->arquivos()->save(new Arquivo(['filename' => $nomeArquivo, 'mime' => $arq->getMimeType() ]));
                    
                }
            }
        } else if($links){ // caso haja links, salva suas referências em banco
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

        // se for post dentro de fio e não for sage, atualiza sua ultima atualização para que "bumpe"
        if($post->lead_id && $post->sage !== 's'){
            $this->atualizaUpdatedAt($post->lead_id);
        }
        
        // verifica se ultrapassou o limite máximo de fios dentro da board
        $this->verificaLimitePosts($post->board);
        
        // prepara mensagem de aviso de post criado com sucesso
        $flashmsg = $post->lead_id ? 'Post número ' . $post->id . ' criado' : 'Post número <a target="_blank" href="/' . $post->board . '/' . $post->id . '">' . $post->id . '</a> criado';
        Session::flash('post_criado', $flashmsg);
        return Redirect::to('/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id));
    }
    
    protected function postRollback($post){
        $this->destroy($post->id);
    }

    // verifica se ultrapassou o nro máximo de posts para a board [constantes.num_max_posts]
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

    // atualiza campo responsável por "bumpar" o fio
    protected function atualizaUpdatedAt($post_id){
        $post = Post::find($post_id);
        if($post){
            $post->updated_at = Carbon::now();
            $post->save();
            
        }
    }
    
    // atualiza variável pinado fazendo que o post fique sempre no topo da primeira página entre outros pinados
    public function pinarPost($post_id){
        $post = Post::find($post_id);
        if($post){
            $post->pinado = 's';
            $post->save();
            return \Redirect::to('/' . $post->board );
        }
        return \Redirect::to('/');
    }
    
    // gera um report (denuncia)
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

    // deleta uma postagem e dados relacionados a ele (links, arquivos)
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
    
    // deleta arquivo da pasta pública
    public function destroyArq($filename){
        \File::delete(public_path() . '/storage/' . $filename);
    }
    
    // deleta arquivo da pasta pública e remove sua referência do banco de dados
    public function destroyArqDb($nomeBoard, $filename, $redirect=true){
        \File::delete(public_path() . '/storage/' . $filename);
        \DB::table('arquivos')->where('filename', '=', $filename)->delete();
    
        if($redirect) return \Redirect::to('/' . $nomeBoard );
    }

}
