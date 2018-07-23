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
use Ibbr\Anao;
use Ibbr\Report;
use Ibbr\Configuracao;
use Carbon\Carbon;
use Cache;
use Auth;

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
      
      
    private static function pegaMesPortugues($numMes)
    {
        switch($numMes)
        {
            case 1:
                return 'Janeiro';
            case 2:
                return 'Fevereiro';
            case 3:
                return 'Março';
            case 4:
                return 'Abril';
            case 5:
                return 'Maio';
            case 6:
                return 'Junho';
            case 7:
                return 'Julho';
            case 8:
                return 'Agosto';
            case 9:
                return 'Setembro';
            case 10:
                return 'Outubro';
            case 11:
                return 'Novembro';
            case 12:
                return 'Dezembro';
            default:
                return '';
        }
    }
    
    private static function transformaDatasPortugues($posts)
    {
        foreach($posts as $post)
        {
            $temp = strlen($post->created_at->day) === 1 ? '0' . $post->created_at->day : $post->created_at->day ;
            $temp .= ' de ';
            $temp .= PostController::pegaMesPortugues($post->created_at->month);
            $temp .= ' de ';
            $temp .= $post->created_at->year;
            $temp .= ' às ';
            $temp .= $post->created_at->hour;
            $temp .= ':';
            $temp .= strlen($post->created_at->minute) === 1 ? '0' . $post->created_at->minute : $post->created_at->minute ;
            
            $post->data_post = $temp;
        }
        return $posts;
    }

    public static function pegaPostsCatalogo()
    {
        $chave = 'posts_catalogo';
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $posts = Post::with(['arquivos', 'ytanexos'])->orderBy('updated_at', 'desc')->where('lead_id', null)->get();
        Cache::forever($chave, $posts);
        return $posts;
    }

    public static function pegaPostsBoard($nomeBoard)
    {
        $pagina = Controller::getPagina();
        $chave = 'posts_board_' . $nomeBoard . '_pag_' . $pagina;
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $posts = Post::with(['arquivos', 'ytanexos', 'anao', 'ban', 'board'])
        ->orderBy('updated_at', 'desc')
        ->where('board', $nomeBoard)->where('lead_id', null)->paginate(ConfiguracaoController::getAll()->num_posts_paginacao);
        $posts = PostController::transformaDatasPortugues($posts);
        Cache::forever($chave, $posts);
        return $posts;
    }
    
    public static function pegaSubPostsBoard($nomeBoard)
    {
        $pagina = Controller::getPagina();
        $chave = 'subposts_board_' . $nomeBoard . '_pag_' . $pagina;
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $subposts = Post::with(['arquivos', 'ytanexos', 'anao', 'ban', 'board'])->orderBy('created_at', 'asc')->where('board', $nomeBoard)->where('lead_id', '<>', null)->get();
        $subposts = PostController::transformaDatasPortugues($subposts);
        Cache::forever($chave, $subposts);
        return $subposts;
    }
    
    public static function pegaPostsThread($thread)
    {
        $chave = 'posts_thread_' . $thread;
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $posts = Post::with(['arquivos', 'ytanexos', 'anao', 'ban', 'board'])->orderBy('created_at', 'asc')->where('id', $thread)->orWhere('lead_id', $thread)->get();
        $posts = PostController::transformaDatasPortugues($posts);
        Cache::forever($chave, $posts);
        return $posts;
    }
    
    public static function pegaReports()
    {
        $chave = 'reports';
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $reports = Report::orderBy('id', 'desc')->get();
        Cache::forever($chave, $reports);
        return $reports;
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
        $configuracaos = ConfiguracaoController::getAll();
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
        $regras['captcha'] = $configuracaos->captcha_ativado ? 'required|captcha' : '';
        
        if($request->sage){
            $regras['sage'] = 'max:4';
        }
        
        if($request->insidepost){
            $regras['insidepost'] = 'max:25';
        }
        
        if($request->lead_id){
            $regras['lead_id'] = 'max:25';
        }
        
        if($request->modpost){
            $regras['modpost'] = 'max:7';
        }
        
        return $regras;
    }
    
    private function limpaCachePosts($board, $thread)
    {
        $num_paginas = 10;
        for($i = 0 ; $i < $num_paginas ; $i++ ){
            Cache::forget('posts_board_' . $board . '_pag_' . $i);
            Cache::forget('subposts_board_' . $board  . '_pag_' . $i);
        }
        Cache::forget('posts_thread_' . $thread);
        Cache::forget('posts_catalogo');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        // Verifica se a board requisitada para o post realmente existe
        if(!$this->verificaBoardLegitima(strip_tags(Purifier::clean($request->nomeboard)))){
            return Redirect::to('/');
        }
        
        // Verifica se o postador está banido da board em questão
        $bantime = $this->estaBanido(\Request::ip(), strip_tags(Purifier::clean($request->nomeboard)));
        if($bantime){
            return $this->redirecionaComMsg('ban', 
            'Seu IP ' . \Request::ip() . ' está banido da board ' . strip_tags(Purifier::clean($request->nomeboard)) . ' até: ' . $bantime->toDateTimeString() . ' e não pode postar.',
            '/' . strip_tags(Purifier::clean($request->nomeboard)));
     
        }
        
        // verifica se o postador esta banido para todas as boards
        $bantime = null;
        $bantime = $this->estaBanido(\Request::ip());
        if($bantime){
            return $this->redirecionaComMsg('ban',
            'Seu IP ' . \Request::ip() . ' está banido de todas as boards até: ' . $bantime->toDateTimeString() . ' e não pode postar.',
            '/' . strip_tags(Purifier::clean($request->nomeboard)));
        }
        
        $arquivos = $request->file('arquivos'); // salva os dados dos arquivos na variável $arquivos
        // valida os inputs
        $regras = $this->defineArrayValidacao($request);
        // validação caso haja link do youtube provido na postagem
        if($request->linkyoutube){
            if($request->file('arquivos')){
                return $this->redirecionaComMsg('erro_upload',
                'Sem anexo de arquivos quando há links de youtube',
                '/' . strip_tags(Purifier::clean($request->nomeboard)));
            }
            $this->validate($request, $regras);
            
        } else { // se não houver nenhum link do youtube
            if( (!$request->file('arquivos') && strip_tags(Purifier::clean($request->insidepost)) === 'n') || (is_array($arquivos) && sizeof($arquivos) < 1) ){
                return $this->redirecionaComMsg('erro_upload',
                'É necessário postar pelo menos com um arquivo ou um link do youtube',
                '/' . strip_tags(Purifier::clean($request->nomeboard)));
            }
            $this->validate($request, $regras);
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
        $post->sage = strip_tags(Purifier::clean($request->sage)) === 'sage'; // define se o post foi sageado ou não
        $post->pinado = false; // define se a thread está pinada, por padrão, não
        $post->lead_id = (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? null : strip_tags(Purifier::clean($request->insidepost))); // caso o post seja dentro de um fio, define qual fio "pai" da postagem
        $post->trancado = false; // define se o fio pode receber novos posts ou não
        
        
        
        if(!isset($_COOKIE[$this->nomeBiscoitoSessao]))
        {
            return $this->redirecionaComMsg('erro_upload', 
            'Erro ao postar. Você quer biscoito, amigo?',
            '/' . $post->board . '/' . ($post->lead_id ? $post->lead_id : ''));
        }
        
        $biscoito = strip_tags(Purifier::clean($_COOKIE[$this->nomeBiscoitoSessao]));
        $anao = Anao::where('biscoito', $biscoito)->first();
        
        if(!$anao)
        {
            return $this->redirecionaComMsg('erro_upload', 
            'Erro ao postar. Você quer biscoito, amigo?',
            '/' . $post->board . '/' . ($post->lead_id ? $post->lead_id : ''));    
        }
        
        $post->biscoito = $anao->biscoito;
        
        if($post->lead_id)
        {
            $lead_fio = Post::find($post->lead_id);
            if($lead_fio && $lead_fio->trancado)
            {
                return $this->redirecionaComMsg('erro_upload', 
                'Este fio já está trancado',
                '/' . $post->board . '/' . $post->lead_id);
            }
        }
        
        // flag "modpost" definido pelo mod
        if($request->modpost && Auth::check()){
            $post->modpost = strip_tags(Purifier::clean($request->modpost)) === 'modpost';
        }
        $num_max_arq_post = ConfiguracaoController::getAll()->num_max_arq_post;
        // verifica se há mais arquivos/links que o máximo permitido
        if((is_array($arquivos) && sizeof($arquivos) >  $num_max_arq_post) || ($links && sizeof($links) >  $num_max_arq_post ) ){
            return $this->redirecionaComMsg('erro_upload',
            'Número máximo de arquivos ou links do youtube permitidos: ' . $num_max_arq_post,
            '/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id ));
        }
        
        // salva o post em banco de dados
        $post->save();
        $this->limpaCachePosts($post->board, $post->lead_id);

        // caso haja arquivos, salva-os em disco e seus paths em banco
        if (!empty($arquivos)) {            
            foreach ($arquivos as $index => $arq) {
                if ($arq->isValid()) {
                                 
                    // define o filename baseado no nro da postagem concatenado com a qtdade de arquivos updados
                    // exemplo, se fio nro 1234 e a postagem tem 3 arquivos, gerará 3 filenames do tipo 1234-0, 1234-1, 1234-2 seguido da extensão do arquivo
                    $contador = 0;
                    do{
                        $nomeArquivo = $post->id . "-{$contador}"  . "." . $arq->extension();
                        
                        $contador++;
                    //}while(\File::exists(public_path() . '/storage/' . $nomeArquivo));
                    }while(Storage::disk('public')->exists($nomeArquivo));
                    
                    // salva em disco na pasta public/storage
                    //Storage::disk('disk2')->putFileAs('/storage', $arq, $nomeArquivo);
                    Storage::disk('public')->putFileAs('', $arq, $nomeArquivo);
                    
                    $spoilerVal =  $request->input('arquivos-spoiler-' . ($index+1)) !== null ? $request->input('arquivos-spoiler-' . ($index+1)) === 'spoiler' : false;
                    $post->arquivos()->save(new Arquivo(
                    ['filename' => $nomeArquivo, 
                     'mime' => $arq->getMimeType(), 
                     'spoiler' => $spoilerVal ,
                     'original_filename' => $arq->getClientOriginalName()
                     ]));
                    
                }
            }
        } else if($links){ // caso haja links, salva suas referências em banco
            foreach($links as $link){
                if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link, $match)){
                    $post->ytanexos()->save(new Ytanexo(['ytcode' => $match[1], 'post_id' => $post->id ]));
                    
                } else {
                    $this->postRollback($post);
                    return $this->redirecionaComMsg('erro_upload',
                    'Link inválido',
                    '/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id ));
                    
                }
            }
        }

        // se for post dentro de fio e não for sage, atualiza sua ultima atualização para que "bumpe"
        if($post->lead_id && !($post->sage)){
            $this->atualizaUpdatedAt($post->lead_id);
        }
        
        // verifica se ultrapassou o limite máximo de fios dentro da board
        $this->verificaLimitePosts($post->board);
        
        // prepara mensagem de aviso de post criado com sucesso
        $flashmsg = $post->lead_id ? 'Post número ' . $post->id . ' criado' : 'Post número <a target="_blank" href="/' . $post->board . '/' . $post->id . '">' . $post->id . '</a> criado';
        return $this->redirecionaComMsg('post_criado', $flashmsg,
        '/' . $post->board . (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? '' : '/' . $post->lead_id));
        
    }
    
    protected function postRollback($post){
        $this->destroy($post->board, $post->id);
    }

    // verifica se ultrapassou o nro máximo de posts para a board [configuracaos.num_max_fios]
    protected function verificaLimitePosts($nomeBoard){
        $posts = \DB::select('select * from posts where pinado = false and board = ? order by updated_at desc limit 1 offset ?;', [$nomeBoard, ConfiguracaoController::getAll()->num_max_fios ]);
        if(count($posts)>0){
            // destroyArq, destroyArqDb
            $post_a_deletar = $posts[0]->id;
            $arqs = \DB::table('arquivos')->where('post_id', $post_a_deletar)->get();
            foreach($arqs as $arq){
                $this->destroyArqDb($nomeBoard, $arq->filename, false);
            }
            $this->destroy($posts[0]->board, $post_a_deletar);
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
    public function pinarPost($nomeBoard, $post_id, $val){
        $post = Post::find($post_id);
        if($post){
            $post->pinado = $val;
            $post->save();
            $this->limpaCachePosts($nomeBoard, $post_id);
            return Redirect::to('/' . $post->board );
        }
        return Redirect::to('/');
    }
    
    // atualiza variável trancado fazendo que o post não possa mais ser respondido
    public function trancarPost($nomeBoard, $post_id, $val){
        $post = Post::find($post_id);
        if($post){
            $post->trancado = $val;
            $post->save();
            $this->limpaCachePosts($nomeBoard, $post_id);
            return Redirect::to('/' . $post->board );
        }
        return Redirect::to('/');
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
        Cache::forget('reports');
        
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

    protected function podeDeletarFio($postId){
        $post = Post::find($postId);
        $bisc = $this->temBiscoito();
        if(!$post || !$bisc)
        {
            return false;
        }
        if($post->biscoito === $bisc)
            return $post;
        else return false;
    }

    // deleta uma postagem e dados relacionados a ele (links, arquivos)
    public function destroy($nomeBoard, $postId) {
        $post = $this->podeDeletarFio($postId);
        if(Auth::check() || $post)
        {
            $arquivos = $post->arquivos;

            foreach($arquivos as $arq){
                $this->destroyArq($arq->filename);
                \DB::table('arquivos')->where('post_id', '=', $postId)->delete();
            }
            if($post->ytanexos){
                \DB::table('ytanexos')->where('post_id', '=', $postId)->delete();
            }
            
            if(!$post->lead_id)
            {
                $posts = Post::where('lead_id', $post->id)->get();
                foreach($posts as $p)
                {
                    $this->deletaUmPost($p);
                }
            }
            
            $post->delete();
            $this->limpaCachePosts($nomeBoard, $postId);
            return Redirect::to('/' . $nomeBoard );
            
        } else {
            return $this->redirecionaComMsg('ban', 'Não foi possível deletar este post', '/' . $nomeBoard);
        }
    }
    
    private function deletaUmPost($post)
    {
        $arquivos = $post->arquivos;

        foreach($arquivos as $arq){
            $this->destroyArq($arq->filename);
            \DB::table('arquivos')->where('post_id', '=', $post->id)->delete();
        }
        if($post->ytanexos){
            \DB::table('ytanexos')->where('post_id', '=', $post->id)->delete();
        }
        $post->delete();
    }
    
    // deleta arquivo da pasta pública
    public function destroyArq($filename){
        \File::delete(public_path() . '/storage/' . $filename);
    }
    
    // deleta arquivo da pasta pública e remove sua referência do banco de dados
    public function destroyArqDb($nomeBoard, $filename, $redirect=true){
        \File::delete(public_path() . '/storage/' . $filename);
        \DB::table('arquivos')->where('filename', '=', $filename)->delete();
        Cache::forget('posts');
    
        if($redirect) return Redirect::to('/' . $nomeBoard );
    }

}
