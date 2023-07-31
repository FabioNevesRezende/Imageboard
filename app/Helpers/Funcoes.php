<?php

namespace Ibbr\Helpers;

use Ibbr\Http\Controllers\BoardController;

class Funcoes
{
    public static function geraRegexBoards() {
        $result = '(';
        foreach (BoardController::getAll() as $board) {
            $result .= $board->sigla . '|';
        }
        $result = substr($result, 0, strlen($result) - 1); // retira o último caracter |
        $result .= ')';
        return $result;
    }

    public static function trataFilesize($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function consolelog(...$args): void{
        foreach ($args as $arg) {
            if (is_object($arg) || is_array($arg) || is_resource($arg)) {
                $output = print_r($arg, true);
            } else {
                $output = (string) $arg;
            }

            fwrite(STDOUT, $output . "\n");
         }
    }
}
