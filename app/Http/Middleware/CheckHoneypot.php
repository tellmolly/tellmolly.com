<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHoneypot
{
    /**
     * Handle an incoming request.
     *
     * With "honeypot:timed", the form must also have been on screen for a few
     * seconds: scripts submit instantly, people don't.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ?string $mode = null): Response
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

        if ($mode === 'timed') {
            $shownAt = $this->shownAt($request);

            // Missing or tampered with: not our form.
            if ($shownAt === null) {
                return $this->emptyResponse();
            }

            // Too fast for a person. Say so, in case it was one.
            if (now()->getTimestamp() - $shownAt < config('honeypot.minimum_seconds')) {
                return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                    'email' => 'That was very quick. Please check your details and send the form again.',
                ]);
            }
        }

        return $next($request);
    }

    private function shownAt(Request $request): ?int
    {
        try {
            $value = decrypt((string) $request->input(config('honeypot.time_field_name')));
        } catch (DecryptException) {
            return null;
        }

        return is_int($value) ? $value : null;
    }

    protected function emptyResponse(): \Illuminate\Http\Response
    {
        return response('');
    }
}
