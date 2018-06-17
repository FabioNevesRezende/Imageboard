<?php

namespace Ibbr\Http\Controllers;

use Ibbr\Board;
use Illuminate\Http\Request;
use Cache;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */     
    public function index()
    {
        //
    }

    /*
     * retorna o array de boards do tipo
     * ['sigla'=>'descricao'], exemplo:
     * ['b' => 'Aleatorio', 
        'mod' => 'Moderação',
        'pol' => 'Notícias e política',
        'tech' => 'Tecnologia e Computação',
        'w' => 'Wallpapers',
        'x' => 'Paranormal, Religiões, Teologia',
        'jo' => 'Jogos',
        'cu' => 'Cultura, música, televisão, literatura',
        'a' => 'Anime'
        ]
     * 
     * */
    public static function getAll()
    {
        if(Cache::has('boards'))
            return Cache::get('boards');

        $boards = Board::orderBy('ordem')->get();
        
        $retorno = array();
        
        foreach($boards as $b)
        {
            $retorno[$b->sigla] = $b->nome;
        }
        
        Cache::forever('boards', $retorno);
        return $retorno;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Ibbr\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function show(Board $board)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Ibbr\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function edit(Board $board)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Ibbr\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Board $board)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Ibbr\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Board $board)
    {
        //
    }
}
