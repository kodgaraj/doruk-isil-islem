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
        $pushToken = $request->pushToken;

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

        $jwtsizKullanici = $kullanici->toArray();
        unset($jwtsizKullanici['jwt']);

        $kullanici->jwt = $this->jwtUret($jwtsizKullanici);
        $kullanici->pushToken = $pushToken;

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

    public function cikis(Request $request)
    {
        $kullanici = User::find($request->kullaniciId);

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
                'mesaj' => 'Çıkış yaparken bir hata oluştu',
                "hataKodu" => "KULLANICI_CIKIS",
            ]);
        }

        return response()->json([
            'durum' => true,
            'mesaj' => 'Çıkış başarılı',
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
