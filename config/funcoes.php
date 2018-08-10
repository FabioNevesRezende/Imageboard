<?php

use Ibbr\Http\Controllers\BoardController;

return [
    'geraRegexBoards' => function (){
        $result = '(';
        foreach(BoardController::getAll() as $board){
            $result .= $board->sigla . '|';
        }
        $result = substr($result, 0, strlen($result)-1); // retira o último caracter |
        $result .= ')';
        return $result;
    }
    
];
