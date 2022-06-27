<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Firebase\JWT\JWT;

class UpdateController extends Controller
{
    public function index($sifre)
    {
        if (!$sifre)
        {
            return '!';
        }

        if ($sifre !== 'K@22')
        {
            return "!!";
        }

        $sonuc = [];

        // Rolleri güncelleme
        $seeder = new RolesAndPermissionsSeeder();
        $seeder->run();
        $sonuc[] = 'Roller ve izinler güncellendi > ' . date('Y-m-d H:i:s');

        // Veritabanı güncelleme
        $seeder = new DatabaseSeeder();
        $seeder->run();
        $sonuc[] = 'Database güncellendi > ' . date('Y-m-d H:i:s');

        // Kullanıcıları JWT güncelleme
        $kullanicilar = User::all();
        foreach ($kullanicilar as $kullanici)
        {
            if (!$kullanici->jwt)
            {
                $kullanici->jwt = JWT::encode([
                    ...$kullanici->toArray(),
                    'exp' => time() + (60 * 60 * 24 * 30),
                    'iat' => time()
                ], config('app.jwt.secret'), 'HS256');
                $kullanici->save();
            }
        }

        $sonuc[] = 'JWT güncellendi > ' . date('Y-m-d H:i:s');

        return implode("<br /> <br />", $sonuc) ?? '!!!';
    }
}