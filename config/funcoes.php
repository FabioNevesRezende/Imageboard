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
    },
    'trataFilesize' => function($bytes){
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;

    }
    
];
