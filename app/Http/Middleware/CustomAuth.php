<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // try {
        //     $user = JWTAuth::parseToken()->authenticate();
        // } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        //     return response()->json(['error' => 'Token expired'], 401);
        // } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        //     return response()->json(['error' => 'Token invalid'], 401);
        // } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        return $next($request);
    }
}
