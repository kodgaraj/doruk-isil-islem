<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController
{
    public function index()
    {
        return "api";
    }

    public function giris(Request $request)
    {
        $eposta = $request->email;
        $sifre = $request->password;

        $kullanici = User::where('email', $eposta)->first();

        if (!$kullanici) {
            return response()->json([
                'durum' => false,
                'mesaj' => 'Kullanıcı bulunamadı',
            ]);
        }

        if (!Hash::check($sifre, $kullanici->password)) {
            return response()->json([
                'durum' => false,
                'mesaj' => 'Şifre yanlış',
            ]);
        }

        $kullanici->jwt = $this->jwtUret($kullanici);

        if (!$kullanici->save()) {
            return response()->json([
                'durum' => false,
                'mesaj' => 'Giriş yaparken bir hata oluştu',
                "hataKodu" => "JWT_OLUSTUR",
            ]);
        }

        return response()->json([
            'durum' => true,
            'mesaj' => 'Giriş başarılı',
            'kullanici' => $kullanici,
        ]);
    }

    public function oturumKontrol()
    {
        return response()->json([
            'durum' => true,
            'mesaj' => 'Oturum başarılı',
        ]);
    }

    public function jwtUret($kullanici)
    {
        $jwt = JWT::encode([
            ...(is_array($kullanici) ? $kullanici : $kullanici->toArray()),
            'exp' => time() + (60 * 60 * 24 * 30),
            'iat' => time()
        ], config('app.jwt.secret'), 'HS256');

        return $jwt;
    }
}
