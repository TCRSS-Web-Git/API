<?php

namespace App\Http\Middleware;

use App\Models\Invite;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response | JsonResponse)  $next
     */
    public function handle($request, Closure $next, ...$args): Response|JsonResponse
    {
        [$relative, $ignore] = $this->parseArguments($args);

        if (! $request->hasValidSignatureWhileIgnoring($ignore, ! $relative)) {
            throw new InvalidSignatureException;
        }

        $token = $request->query('token');

        if ($token) {
            $invite = Invite::where('token', $token)->first();

            if (! $invite) {
                throw new InvalidSignatureException;
            }
        }

        return $next($request);
    }
}
