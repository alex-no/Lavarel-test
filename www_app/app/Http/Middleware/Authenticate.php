<?php

namespace App\Http\Middleware;
use Illuminate\Auth\AuthenticationException;


class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Default behavior (for web UI) — throws the exception:
        // throw $exception;

        // Optional fallback (in case you're not using web login pages):
        abort(401, 'Unauthenticated');
    }
}
