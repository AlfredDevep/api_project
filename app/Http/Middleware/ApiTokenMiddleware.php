<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // obtener el token de la cabecera Authorization
        $token = $request->bearerToken();

        if(!$token){
            // si el token no existe, responder con un código de error 401 (Unauthorized)
            return response()->json(['error' => 'Token not provided'], 401);
        }
        // buscar el token en la base de datos
        $accessToken = PersonalAccessToken::findToken($token);

        // si el token no existe, responder con un código de error 401 (Unauthorized)
        if (!$accessToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Definir el tiempo de expiración
        $expirationMinutes = config('sanctum.token_expiration', 30); // 30 minutos

        // verificsr si el token ha expirado
        $tokenExpiry = $accessToken->created_at->addMinutes($expirationMinutes);

        // si el token ha expirado, responder con un código de error 401 (Unauthorized)
        if (Carbon::now()->greaterThan($tokenExpiry)) {
            // Token ha expirado, eliminarlo
            $accessToken->delete();
            return response()->json(['error' => 'Token expired'], 401);
        }

        // Autenticar al usuario usando setUserResolver para que $request->user() funcione
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        return $next($request);
    }
}

