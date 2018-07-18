<?php

namespace Ibbr\Http\Middleware;

use Closure;
use Ibbr\Http\Controllers\Controller;

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
        if ($this->temBiscoito()) {
            return $next($request);
        }

        return redirect('/');
    }

    private function temBiscoito(){
        return (new Controller)->temBiscoito();
    }
    
    
    
}
