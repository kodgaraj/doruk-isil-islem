<?php

namespace App\Http\Controllers;

use App\Models\User;
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
                'status' => false,
                'message' => 'Kullanıcı bulunamadı',
            ]);
        }

        if (!Hash::check($sifre, $kullanici->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Şifre yanlış',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Giriş başarılı',
            'data' => $kullanici,
        ]);
    }
}
