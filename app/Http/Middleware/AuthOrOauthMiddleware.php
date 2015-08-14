<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware;

class AuthOrOauthMiddleware extends OAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $scopesString = null)
    {
        if(Auth::guest())
            parent::handle($request, $next, $scopesString = null);

        return $next($request);
    }
}
