<?php

namespace Ibbr\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function iniciaLog($nome) {
        return fopen($nome . "--" . date("Y-m-d") . ".tlog", "a+");
    }

    public function escreveLog($tag, $msg, $arq) {
        fwrite($arq, "tag=" . $tag . "-" . "data=" . date('Y/m/d-h:m:s-') . "LOG-MSG=" . $msg . "-|-\n");
    }

    public function terminaLog($logArq) {
        fclose($logArq);
    }

}
