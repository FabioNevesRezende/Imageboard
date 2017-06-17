<?php


return [
    'geraRegexBoards' => function (){
        $result = '(';
        foreach(\Config::get('constantes.boards') as $board => $boardnome){
            $result .= $board . '|';
        }
        $result = substr($result, 0, sizeof($result)-2); // retira o último caracter |
        $result .= ')';
        return $result;
    }
    
];