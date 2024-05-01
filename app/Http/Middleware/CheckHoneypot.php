<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHoneypot
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ( ! config('honeypot.enabled')) {
            return $next($request);
        }

        // Field has to be present
        if ( ! $request->has(config('honeypot.field_name'))) {
            return $this->emptyResponse();
        }

        // Field must be empty
        if ( ! empty($request->input(config('honeypot.field_name')))) {
            return $this->emptyResponse();
        }

        return $next($request);
    }

    protected function emptyResponse(): \Illuminate\Http\Response
    {
        return response('');
    }
}
