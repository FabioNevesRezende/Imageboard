<?php


return [
    'geraRegexBoards' => function (){
        $result = '(';
        foreach(\Config::get('constantes.boards') as $board => $boardnome){
            $result .= $board . '|';
        }
        $result = substr($result, 0, strlen($result)-1); // retira o último caracter |
        $result .= ')';
        return $result;
    }
    
];
