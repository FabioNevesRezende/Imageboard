<?php

namespace Ibbr\Http\Middleware;

use Closure;
use Ibbr\Http\Controllers\Controller;
use Ibbr\Helpers\Funcoes;

class VerificaCookieArquivo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Funcoes::consolelog('VerificaCookieArquivo::handle');
        if ((new Controller)->temBiscoito()) {
            return $next($request);
        }

        Funcoes::consolelog('VerificaCookieArquivo::handle redirecionando para o cancro:');
        return redirect('https://www.facebook.com');
    }
    
}
