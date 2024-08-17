<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotAuthUser
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
        // Если заголовок Authorization присутствует, то считаем, что пользователь авторизован
        if ($request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already logged in'
            ], 403);
        }

        // Если токена нет, пропускаем запрос дальше
        return $next($request);
    }
}
