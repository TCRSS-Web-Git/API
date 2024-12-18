<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalizationHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = '';
        if ($request->expectsJson()) {
            $locale = $request->header('X-Localization');
        } else {
            $locale = $request->cookie('X-Localization');
        }
        // When there is wrong locale set to default thai language
        if (! in_array($locale, ['en', 'th'])) {
            $locale = 'th';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
