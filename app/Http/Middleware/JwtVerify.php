<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = explode(' ', $request->header('Authorization'));
        $head = isset($authorizationHeader[0]) ? $authorizationHeader[0] : false;
        $jwt = isset($authorizationHeader[1]) ? $authorizationHeader[1] : false;

        if(!$head || !$jwt){
            return response()->json([
                'status' => false,
                'reply' => 'Geçersiz kullanıcı!',
                'errorCode' => 'JWT_INVALID_HEADER',
            ]);
        }

        try
        {
            $secretKey = config('app.jwt.secret');
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

            // Kafa karıştırmaması için jwt keyini siliyoruz
            if (isset($decoded->jwt)) {
                unset($decoded->jwt);
            }

            $request->merge(['decoded' => $decoded, 'jwt' => $jwt]);
            return $next($request);
        }
        catch (ExpiredException $e)
        {
            return response()->json([
                'status' => false,
                'message' => 'Süresi dolmuş token!',
                'errorCode' => 'JWT_EXPIRED',
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => 'Geçersiz Kullanıcı!',
                'error' => $e->getMessage(),
                'errorCode' => 'JWT_EXCEPTION',
                'errorLine' => $e->getLine(),
            ], 400);
        }
    }
}
