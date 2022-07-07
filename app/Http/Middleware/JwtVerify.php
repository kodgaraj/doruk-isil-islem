<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
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
                'durum' => false,
                'mesaj' => 'Geçersiz kullanıcı!',
                'hataKodu' => 'JWT_INVALID_HEADER',
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

            // $request->merge(['decoded' => $decoded, 'jwt' => $jwt]);
            return $next($request);
        }
        catch (ExpiredException $e)
        {
            $kullanici = User::find($decoded->id);

            if (!$kullanici) {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Kullanıcı bulunamadı',
                ]);
            }

            $kullanici->jwt = null;
            $kullanici->pushToken = null;

            if (!$kullanici->save()) {
                return response()->json([
                    'durum' => false,
                    'mesaj' => 'Giriş yaparken bir hata oluştu',
                    "hataKodu" => "KULLANICI_TEMIZLEME",
                ]);
            }

            return response()->json([
                'durum' => false,
                'mesaj' => 'Oturum süreniz doldu! Lütfen tekrar giriş yapın.',
                'hataKodu' => 'JWT_EXPIRED',
            ], 400);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'durum' => false,
                'mesaj' => 'Geçersiz Kullanıcı!',
                'hata' => $e->getMessage(),
                'hataKodu' => 'JWT_EXCEPTION',
                'hataSatiri' => $e->getLine(),
            ], 400);
        }
    }
}
