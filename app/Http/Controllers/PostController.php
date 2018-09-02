<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Post;
use Illuminate\Http\Request;
use Purifier;
use Storage;
use Redirect;
use Ibbr\Arquivo;
use Ibbr\Ytanexo;
use Ibbr\Anao;
use Ibbr\Report;
use Carbon\Carbon;
use Cache;
use Auth;

class PostController extends Controller {

    public static function pegaPostsCatalogo(){
        $chave = 'posts_catalogo';
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $posts = Post::with(['arquivos', 'ytanexos'])->orderBy('updated_at', 'desc')->where('lead_id', null)->get();
        Cache::forever($chave, $posts);
        return $posts;
    }

    public static function pegaPostsBoard($siglaBoard){
        $pagina = Controller::getPagina();
        $chave = 'posts_board_' . $siglaBoard . '_pag_' . $pagina;
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $posts = Post::with(['arquivos', 'ytanexos', 'anao', 'ban', 'board'])
        ->orderBy('updated_at', 'desc')
        ->where('board', $siglaBoard)->where('lead_id', null)->paginate(ConfiguracaoController::getAll()->num_posts_paginacao);
        $posts = Controller::transformaDatasPortugues($posts);
        Cache::forever($chave, $posts);
        return $posts;
    }
    
    public static function pegaSubPostsBoard($siglaBoard){
        $pagina = Controller::getPagina();
        $chave = 'subposts_board_' . $siglaBoard . '_pag_' . $pagina;
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $subposts = Post::with(['arquivos', 'ytanexos', 'anao', 'ban', 'board'])->orderBy('created_at', 'asc')->where('board', $siglaBoard)->where('lead_id', '<>', null)->get();
        $subposts = Controller::transformaDatasPortugues($subposts);
        Cache::forever($chave, $subposts);
        return $subposts;
    }
    
    public static function pegaPostsThread($thread){
        $chave = 'posts_thread_' . $thread;
        if(Cache::has($chave))
            return Cache::get($chave);
            
        $posts = Post::with(['arquivos', 'ytanexos', 'anao', 'ban', 'board'])->orderBy('created_at', 'asc')->where('id', $thread)->orWhere('lead_id', $thread)->get();
        $posts = Controller::transformaDatasPortugues($posts);
        Cache::forever($chave, $posts);
        return $posts;
    }
    
    public static function pegaReports(){
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
                '/&gt;(.+)\n?/m', 
                '<span class="green-text">&gt;$1</span><br>', 
                $str
                );
    }
    
    private function verificaBoardLegitima($request){
        if(!$this->boardExiste(strip_tags(Purifier::clean($request->siglaboard)))){
            abort(400);
        }
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
        if($configuracaos->captcha_ativado){
            $regras['captcha'] = 'required|captcha';
        }
        
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
        
    private function verificaBanimentos($request){
        // Verifica se o postador está banido da board em questão
        $bantime = $this->estaBanido(\Request::ip(), strip_tags(Purifier::clean($request->siglaboard)));
        if($bantime){
            return 'Seu IP ' 
                    . \Request::ip() 
                    . ' está banido da board ' 
                    . strip_tags(Purifier::clean($request->siglaboard)) 
                    . ' até: ' 
                    . $bantime->toDateTimeString() 
                    . ' e não pode postar.'; 
        }
        
        // verifica se o postador esta banido para todas as boards
        $bantime = null;
        $bantime = $this->estaBanido(\Request::ip());
        if($bantime){
            return 'Seu IP ' 
                    . \Request::ip() 
                    . ' está banido de todas as boards até: ' 
                    . $bantime->toDateTimeString() 
                    . ' e não pode postar.';
        }
        return false;
    }
    
    private function validaRequest($request, $arquivos, $links){
        // valida os inputs
        $regras = $this->defineArrayValidacao($request);
        $this->validate($request, $regras);
        
        // validação caso haja link do youtube provido na postagem
        if($links){
            if($request->file('arquivos')){
                return 'Sem anexo de arquivos quando há links de youtube';
            }
            
            foreach($links as $link){
                if(!preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link)){
                    return 'Link inválido';
                } 
            }
        
        } else { // se não houver nenhum link do youtube
            if( (!$request->file('arquivos') && strip_tags(Purifier::clean($request->insidepost)) === 'n') || (is_array($arquivos) && sizeof($arquivos) < 1) ){
                return 'É necessário postar pelo menos com um arquivo ou um link do youtube';
            }
        }
        
        $num_max_arq_post = ConfiguracaoController::getAll()->num_max_arq_post;
        // verifica se há mais arquivos/links que o máximo permitido
        if((is_array($arquivos) && sizeof($arquivos) >  $num_max_arq_post) || ($links && sizeof($links) >  $num_max_arq_post ) ){
            return 'Número máximo de arquivos ou links do youtube permitidos: ' . $num_max_arq_post;
        }
        
        // termina validação dos inputs
        return false;
    }
    
    private function getLinksYoutube($request){
        if($request->linkyoutube){ // caso haja links do youtube, divida a strings por pipe characters | 
            return explode('|' ,strip_tags(Purifier::clean($request->linkyoutube)));
        }
        return null;
    }
    
    private function getObjetoPost($request){
        $post = new Post;
        $post->assunto = strip_tags(Purifier::clean($request->assunto)); // assunto do post
        $post->lead_id = (strip_tags(Purifier::clean($request->insidepost)) === 'n' ? null : strip_tags(Purifier::clean($request->insidepost))); // caso o post seja dentro de um fio, define qual fio "pai" da postagem
        $post->board = strip_tags(Purifier::clean($request->siglaboard)); // board que o post pertence
        $post->conteudo = $this->trataLinks(strip_tags(Purifier::clean($request->conteudo))); // adiciona tags <a> ao conteudo das mensagens
        $post->conteudo = $this->addRefPosts('/' . $post->board . ($post->lead_id ? '/' . $post->lead_id : ''), $post->conteudo); // adiciona referência a outros posts iniciados com '>'
        $post->conteudo = $this->addGreenText($post->conteudo); // add verdetexto após os símbolos '>>'
        $post->conteudo = $this->saltaLinhas($post->conteudo);
        $post->sage = strip_tags(Purifier::clean($request->sage)) === 'sage'; // define se o post foi sageado ou não
        $post->pinado = false; // define se a thread está pinada, por padrão, não
        $post->trancado = false; // define se o fio pode receber novos posts ou não
        // flag "modpost" definido pelo mod
        $post->modpost = $request->modpost && Auth::check() && strip_tags(Purifier::clean($request->modpost)) === 'modpost';
        
        return $post;
    }
    
    private function verificaBiscoitoPostar(){
        if(!isset($_COOKIE[$this->nomeBiscoitoSessao]))
            return false;
        
        $biscoito = strip_tags(Purifier::clean($_COOKIE[$this->nomeBiscoitoSessao]));
        $anao = Anao::find($biscoito);
        
        if(!$anao)
            return false;
        
        return $anao;
    }
    
    private function salvaArquivosDisco($request, $post, $arquivos){
        foreach ($arquivos as $index => $arq) {
            if ($arq->isValid()) {
                // define o filename baseado no nro da postagem concatenado com a qtdade de arquivos updados
                // exemplo, se fio nro 1234 e a postagem tem 3 arquivos, gerará 3 filenames do tipo 1234-0, 1234-1, 1234-2 seguido da extensão do arquivo
                $contador = 0;
                do{
                    $nomeArquivo = $post->id . "-{$contador}"  . "." . $arq->extension();
                        
                    $contador++;
                }while(Storage::disk('public')->exists($nomeArquivo));
                    
                // salva em disco na pasta public/storage
                Storage::disk('public')->putFileAs('', $arq, $nomeArquivo);
                    
                $spoilerVal =  $request->input('arquivos-spoiler-' . ($index+1)) !== null ? $request->input('arquivos-spoiler-' . ($index+1)) === 'spoiler' : false;
                $post->arquivos()->save(new Arquivo(
                ['filename' => $nomeArquivo, 
                 'mime' => $arq->getMimeType(), 
                 'spoiler' => $spoilerVal ,
                 'original_filename' => $arq->getClientOriginalName(),
                 'filesize' => $arq->getSize()
                ]));
                    
            }
        }
    }

    private function salvaLinksYoutube($request, $post, $links){
        foreach($links as $link){
            if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link, $match)){
                $post->ytanexos()->save(new Ytanexo(['ytcode' => $match[1], 'post_id' => $post->id ]));
                    
            } else {
                $this->postRollback($post);
                return $this->redirecionaComMsg('erro_upload',
                'Link inválido',
                $request->headers->get('referer'));
                    
            }
        }
    }
    
    protected function postRollback($post){
        $this->destroy($post->board, $post->id);
    }

    // verifica se ultrapassou o nro máximo de posts para a board [configuracaos.num_max_fios]
    protected function verificaLimitePosts($siglaBoard){
        $posts = Post::where('pinado', '=', false)
                ->where('board', '=', $siglaBoard)
                ->whereNull('lead_id')
                ->orderBy('updated_at', 'desc')
                ->offset(ConfiguracaoController::getAll()->num_max_fios)
                ->limit(1)
                ->get();
        
        if($posts && count($posts)>0){
            // se houver pelo menos um post retornado desta query
            // significa que a boarda atingiu o nro máximo de fios
            // então deleta o fio mais antigo
            $this->deletaUmPost($posts[0]);
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
    public function pinarPost($siglaBoard, $post_id, $val){
        $post = Post::find($post_id);
        if($post){
            $post->pinado = $val;
            $post->save();
            $this->limpaCachePosts($siglaBoard, $post_id);
            return Redirect::to('/' . $post->board );
        }
        return Redirect::to('/');
    }
    
    // atualiza variável trancado fazendo que o post não possa mais ser respondido
    public function trancarPost($siglaBoard, $post_id, $val){
        $post = Post::find($post_id);
        if($post){
            $post->trancado = $val;
            $post->save();
            $this->limpaCachePosts($siglaBoard, $post_id);
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
        $report->board = strip_tags(Purifier::clean($request->siglaboard));
        
        $report->save();
        Cache::forget('reports');
        
        return Redirect::to('/' . strip_tags(Purifier::clean($request->siglaboard)));  
    }
    
    protected function podeDeletarFio($postId){
        $post = Post::find($postId);
        $bisc = $this->temBiscoito();
        if(!$post || !$bisc){
            return false;
        }
        if(Auth::check() || $post->biscoito === $bisc)
            return $post;
        else return false;
    }

    // deleta uma postagem e dados relacionados a ele (links, arquivos)
    public function destroy($siglaBoard, $postId) {
        $post = $this->podeDeletarFio($postId);
        if($post){
            $arquivos = $post->arquivos;

            foreach($arquivos as $arq){
                $this->destroyArq($arq->filename);
                \DB::table('arquivos')->where('post_id', '=', $postId)->delete();
            }
            if($post->ytanexos){
                \DB::table('ytanexos')->where('post_id', '=', $postId)->delete();
            }
            
            if(!$post->lead_id){
                $posts = Post::where('lead_id', $post->id)->get();
                foreach($posts as $p){
                    $this->deletaUmPost($p);
                }
            }
            
            $post->delete();
            $this->limpaCachePosts($siglaBoard, $postId);
            return Redirect::to('/' . $siglaBoard );
            
        } else {
            return $this->redirecionaComMsg('ban', 'Não foi possível deletar este post', '/' . $siglaBoard);
        }
    }
    
    private function deletaUmPost($post){
        if($post){
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
    }
    
    // deleta arquivo da pasta pública
    public function destroyArq($filename){
        Storage::disk('public')->delete($filename);
    }
    
    // deleta arquivo da pasta pública e remove sua referência do banco de dados
    public function destroyArqDb($siglaBoard, $filename, $redirect=true){
        $arq = Arquivo::where('filename', '=', $filename)->first();
        if($arq){
            $thread = Post::where('id', '=', $arq->post_id)->first();
            if($thread){
                Storage::disk('public')->delete($filename);

                $arq->delete();
                $this->limpaCachePosts($siglaBoard, $thread->lead_id === null ? $thread->id : $thread->lead_id );

                return Redirect::to('/' . $redirect ? $siglaBoard : '' );
            } else abort(400);
        } else abort(400);
        
    }
    
    private function deveTrancarFio($postId)
    {
        return Post::where('lead_id', '=', $postId)->count() >= ConfiguracaoController::getAll()->num_max_posts_fio - 1;
        
    }
    
    public function destroyReport($id)
    {
        if(Auth::check())
        {
            $report = Report::find($id);
            if($report){
                $report->delete();
                Cache::forget('reports');
                return $this->redirecionaComMsg('sucesso_admin', 'Report ' . $id . ' deletado com sucesso', '/admin');
            }
            else
            {
                return $this->redirecionaComMsg('erro_admin', 'Report ' . $id . ' deletado não encontrado', '/admin');
            }
        }
        abort(400);
    }
    
    public function movePost(Request $request)
    {
        if(Auth::check())
        {
            $postMover = Post::find($request->idpost);
            if($postMover){
                if($request->novopost > $request->idpost)
                {
                    return $this->redirecionaComMsg('erro_admin', 'O ID do novo post tem que ser menor do post a ser movido', '/' . $request->novaboard);
                }
                $velhaBoard = $postMover->board;
                if($postMover->lead_id !== null)
                {
                    $postsFio = Post::where('lead_id', '=', $request->idpost)->get();
                    foreach($postsFio as $postFio)
                    {
                        $postFio->lead_id = $request->novopost;
                        $postFio->board = $request->novaboard;
                        $postFio->save();
                    }
                }
                $postMover->lead_id = $request->novopost;
                $postMover->board = $request->novaboard;
                $postMover->pinado = false;
                $postMover->trancado = false;
                $postMover->save();
                
                $this->limpaCachePosts($velhaBoard, $postMover->lead_id);
                $this->limpaCachePosts($request->novaboard, $request->novopost);
                return $this->redirecionaComMsg('sucesso_admin', 'Post ' . $request->idpost . ' movido com sucesso para /' . $request->novaboard . '/' . $request->novopost, '/' . $request->novaboard);
            }
            return $this->redirecionaComMsg('erro_admin', 'Post ' . $request->idpost . ' não encontrado', '/' . $request->novaboard);
        }
        abort(400);
    }

    /**
     * Valida e cria uma nova postagem
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        // Verifica se a board requisitada para o post realmente existe
        // se não existe, aborta com http 400
        $this->verificaBoardLegitima($request);
        
        //se houver banimentos, retorna
        $msgBan = $this->verificaBanimentos($request);
        if($msgBan){
            return $this->redirecionaComMsg('ban', 
             $msgBan,
            $request->headers->get('referer'));
        }
        $arquivos = $request->file('arquivos'); // salva os dados dos arquivos na variável $arquivos
        $links = $this->getLinksYoutube($request); // pega links do youtube se tiverem
        
        // valida inputs
        $msgValidacao = $this->validaRequest($request, $arquivos, $links);
        if($msgValidacao){
            return $this->redirecionaComMsg('erro_upload', 
            $msgValidacao,
            $request->headers->get('referer'));
        }
        
        $post = $this->getObjetoPost($request); // transforma os campos do form da request num objeto Post
        
        // verifica se tem biscoito para postar
        $anao = $this->verificaBiscoitoPostar();
        if($anao){
            $post->biscoito = $anao->biscoito;
        }
        else{
            return $this->redirecionaComMsg('erro_upload', 
            'Erro ao postar. Você quer biscoito, amigo?',
            $request->headers->get('referer'));
        }
        
        if($post->lead_id){
            $lead_fio = Post::find($post->lead_id);
            if($lead_fio && $lead_fio->trancado){
                return $this->redirecionaComMsg('erro_upload', 
                'Este fio já está trancado',
                $request->headers->get('referer'));
            } elseif($lead_fio 
                    && !$lead_fio->trancado
                    && $this->deveTrancarFio($lead_fio->id)){
                $lead_fio->trancado = true;
                $lead_fio->save();
                
            }
        }
                
        // salva o post em banco de dados
        $post->save();
        $this->limpaCachePosts($post->board, $post->lead_id);

        // caso haja arquivos, salva-os em disco e seus paths em banco
        if (!empty($arquivos)) {
            $this->salvaArquivosDisco($request, $post, $arquivos);
        } else if($links){ // caso haja links, salva suas referências em banco
            $this->salvaLinksYoutube($request, $post, $links);
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
        $request->headers->get('referer'));
        
    }    
}
