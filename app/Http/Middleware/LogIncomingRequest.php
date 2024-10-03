<?php

namespace App\Http\Middleware;

use App\Models\IncomingRequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogIncomingRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $incomingRequestLog = new IncomingRequestLog;
        $incomingRequestLog->method = $request->method();
        $incomingRequestLog->ip = $request->ip();
        $incomingRequestLog->uri = $request->getRequestUri();
        $incomingRequestLog->header = $request->header();
        $incomingRequestLog->body = $request->getContent();
        $incomingRequestLog->save();

        $response = $next($request);

        $incomingRequestLog->response_status = $response->getStatusCode();
        /** @phpstan-ignore-next-line */
        $incomingRequestLog->response_header = $response->headers;
        $incomingRequestLog->response = $response->getContent();
        $incomingRequestLog->save();

        return $response;
    }
}
